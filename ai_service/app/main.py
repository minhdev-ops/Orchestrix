"""FastAPI service cho AI Plant Disease Inference với pipeline mới:
   Grounding DINO để phát hiện lá → Crop từng lá → Keras Disease Diagnosis

Endpoints:
  - GET   /health          : tình trạng model + device + version
  - GET   /labels          : danh sách 71 nhãn
  - POST /detect-leaves    : Grounding DINO - phát hiện các bbox lá trong ảnh
  - POST /crop             : Crop lá từ ảnh gốc theo bbox
  - POST /predict          : Dự đoán bệnh từ ảnh (auto detect leaves if needed)
  - POST /predict-leaf     : Dự đoán bệnh từ một lá riêng lẻ
  - GET  /disease-info/{label} : Thông tin bệnh từ knowledge base

Chạy (dev):
    uvicorn app.main:app --host 0.0.0.0 --port 8501 --reload
"""
from __future__ import annotations

import io
import json
import logging
import time
from typing import Any, Dict, List, Optional, Tuple

import anyio
import numpy as np
from fastapi import Depends, FastAPI, File, Header, HTTPException, Form, UploadFile, status
from fastapi.responses import JSONResponse
from PIL import Image, ImageDraw, ImageFont

from .config import get_settings, Settings
from .model_loader import get_bundle, is_ready, load_model
from .gdino.detector import get_leaf_detector, LeafDetector
from .preprocessing import (
    assess_image_quality,
    normalize_image,
    image_to_array,
    color_based_leaf_detection,
    preprocess_image,
    QualityIssue,
)
from .knowledge_base import (
    get_treatment_recommendations,
    assess_severity,
    get_knowledge_base_summary,
)

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s | %(levelname)s | %(name)s | %(message)s",
)
logger = logging.getLogger("agriverse_vit")

settings = get_settings()
app = FastAPI(
    title="AgriVerse AI Plant Doctor (Enhanced)",
    description="Microservice phân loại bệnh cây với Grounding DINO + best_vit.keras (71 classes) + Knowledge Base",
    version="0.2.0",
)

# ---------------------------------------------------------------------------
# Confidence Thresholds
# ---------------------------------------------------------------------------
MIN_CONFIDENCE = 0.45  # Dưới ngưỡng này → gợi ý chụp lại
HIGH_CONFIDENCE = 0.85  # Trên ngưỡng này → bệnh rõ ràng

# ---------------------------------------------------------------------------
# Startup / auth helpers
# ---------------------------------------------------------------------------
@app.on_event("startup")
async def _startup() -> None:
    """Lazy-load model trong thread pool để không block event loop."""
    logger.info("Khởi động AI service — path=%s", settings.model_path)
    try:
        load_model(settings)
        logger.info("✅ AI Service ready with preprocessing & knowledge base")
    except Exception as exc:  # noqa: BLE001
        logger.exception("Không tải được model khi startup: %s", exc)


def _check_token(x_token: str | None = Header(default=None)) -> None:
    """Kiểm tra Bearer token nếu AI_AUTH_TOKEN được cấu hình."""
    if not settings.auth_token:
        return
    expected = settings.auth_token
    provided = (x_token or "").removeprefix("Bearer ").strip()
    if provided != expected:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token không hợp lệ",
        )


# ---------------------------------------------------------------------------
# Health & Info Endpoints
# ---------------------------------------------------------------------------
@app.get("/health")
async def health():
    """Health check endpoint."""
    return {
        "status": "ok" if is_ready() else "loading",
        "model_loaded": is_ready(),
        "service": settings.service_name,
        "version": "0.2.0",
        "features": [
            "preprocessing",
            "quality_check",
            "knowledge_base",
            "confidence_thresholding",
            "color_based_fallback",
        ],
    }


@app.get("/labels")
async def labels():
    """Danh sách các nhãn bệnh."""
    if not is_ready():
        raise HTTPException(status_code=503, detail="Model chưa load")
    bundle = get_bundle()
    return {"labels": bundle.labels, "count": len(bundle.labels)}


@app.get("/disease-info/{label}")
async def disease_info(label: str):
    """Lấy thông tin bệnh từ knowledge base."""
    info = get_treatment_recommendations(label)
    return info


@app.get("/knowledge-base")
async def knowledge_base_summary():
    """Tóm tắt knowledge base."""
    return get_knowledge_base_summary()


# ---------------------------------------------------------------------------
# Grounding DINO: Phát hiện bbox lá trong ảnh
# ---------------------------------------------------------------------------
@app.post("/detect-leaves", dependencies=[Depends(_check_token)])
async def detect_leaves(file: UploadFile = File(...)) -> JSONResponse:
    """Sử dụng Grounding DINO để phát hiện tất cả bbox lá trong ảnh gửi lên.
    Trả về danh sách bbox [xmin, ymin, xmax, ymax] cho từng lá.
    """
    if not is_ready():
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Model chưa load xong, vui lòng thử lại sau",
        )

    raw = await file.read()
    if not raw:
        raise HTTPException(status_code=400, detail="File rỗng")

    try:
        img = Image.open(io.BytesIO(raw)).convert("RGB")
    except Exception as exc:
        raise HTTPException(status_code=400, detail=f"Ảnh không hợp lệ: {exc}") from exc

    # Quality check
    quality = assess_image_quality(img)

    # Detect all leaves using Grounding DINO
    boxes = real_grounding_dino_detection(img)

    if not boxes:
        logger.warning("Không tìm thấy lá nào trong ảnh bởi Grounding DINO")

    return JSONResponse({
        "image_size": {"width": img.width, "height": img.height},
        "leaf_count": len(boxes),
        "bboxes": boxes,
        "method": "grounding_dino",
        "quality": quality.to_dict(),
    })


def real_grounding_dino_detection(img: Image.Image) -> List[Dict[str, int]]:
    """Thực hiện phát hiện bbox lá using Grounding DINO model thực."""
    global _leaf_detector

    if _leaf_detector is None:
        _leaf_detector = get_leaf_detector()

    try:
        bboxes_obj = _leaf_detector.detect(img, query="leaf")
        # Convert Bbox objects to dicts
        bboxes = [
            {"xmin": b.xmin, "ymin": b.ymin, "xmax": b.xmax, "ymax": b.ymax, "confidence": b.confidence}
            for b in bboxes_obj
        ]
        logger.info(f"Grounding DINO found {len(bboxes)} leaf regions")
        return bboxes
    except Exception as exc:
        logger.warning(f"Grounding DINO detection failed: {exc}, falling back to color-based")
        # Fallback to color-based segmentation
        return fallback_color_based_detection(img)


def fallback_color_based_detection(img: Image.Image) -> List[Dict[str, int]]:
    """Fallback method: color-based leaf segmentation khi Grounding DINO unavailable."""
    leaf_regions = color_based_leaf_detection(img)
    bboxes = [r.to_dict() for r in leaf_regions]
    logger.info(f"Color-based fallback: found {len(bboxes)} leaf regions")
    return bboxes


# Replace the mock with real function
grounding_dino_detection = real_grounding_dino_detection


# ---------------------------------------------------------------------------
# Crop: Trích xuất lá từ bbox
# ---------------------------------------------------------------------------
@app.post("/crop", dependencies=[Depends(_check_token)])
async def crop_leaf(
    file: UploadFile = File(...),
    bbox: str = Form(...),
) -> JSONResponse:
    """Trích xuất leaf từ ảnh gốc theo bbox đã cho (dạng JSON string).
    Trả về ảnh đã crop base64 hoặc binary.
    """
    if not is_ready():
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Model chưa load xong, vui lòng thử lại sau",
        )

    raw = await file.read()
    try:
        bbox_dict = json.loads(bbox)
    except json.JSONDecodeError:
        raise HTTPException(status_code=400, detail="bbox định dạng không đúng (cần JSON)")

    try:
        img = Image.open(io.BytesIO(raw)).convert("RGB")
        xmin = bbox_dict["xmin"]
        ymin = bbox_dict["ymin"]
        xmax = bbox_dict["xmax"]
        ymax = bbox_dict["ymax"]

        # Kiểm bounds
        xmin = max(0, xmin)
        ymin = max(0, ymin)
        xmax = min(img.width, xmax)
        ymax = min(img.height, ymax)

        cropped = img.crop((xmin, ymin, xmax, ymax))

        buf = io.BytesIO()
        cropped.save(buf, format="JPEG")
        cropped_hex = buf.getvalue().hex()

        return JSONResponse({
            "status": "ok",
            "cropped_size": {"width": cropped.width, "height": cropped.height},
            "original_bbox": bbox_dict,
            "image_hex": cropped_hex,
        })
    except Exception as exc:
        raise HTTPException(status_code=400, detail=f"Lỗi crop: {exc}") from exc


# ---------------------------------------------------------------------------
# Predict lá riêng lẻ (endpoint cơ bản cho từng leaf)
# ---------------------------------------------------------------------------
@app.post("/predict-leaf", dependencies=[Depends(_check_token)])
async def predict_leaf(
    file: UploadFile = File(...),
    top_k: int | None = None,
) -> JSONResponse:
    """Dự đoán bệnh cho một lá cây (ảnh đã được crop)."""
    if not is_ready():
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Model chưa load xong, vui lòng thử lại sau",
        )

    raw = await file.read()
    if not raw:
        raise HTTPException(status_code=400, detail="File rỗng")

    if len(raw) > settings.max_image_mb * 1024 * 1024:
        raise HTTPException(
            status_code=status.HTTP_413_REQUEST_ENTITY_TOO_LARGE,
            detail=f"Tệp vượt quá {settings.max_image_mb}MB",
        )

    try:
        img = Image.open(io.BytesIO(raw)).convert("RGB")

        # Quality check
        quality = assess_image_quality(img)

        # Preprocess
        img = normalize_image(img, (settings.input_size, settings.input_size), enhance_contrast=False)
        arr = image_to_array(img)
    except Exception as exc:
        raise HTTPException(status_code=400, detail=f"Ảnh không hợp lệ: {exc}") from exc

    t0 = time.perf_counter()
    probs = await anyio.to_thread.run_sync(_run_model, arr)
    elapsed_ms = round((time.perf_counter() - t0) * 1000, 2)

    probs = np.asarray(probs[0], dtype=np.float32)
    if probs.shape[0] != bundle.num_classes:
        probs = probs.reshape(-1)
    k = min(top_k or settings.top_k, bundle.num_classes)
    top_idx = np.argsort(probs)[::-1][:k]

    top = [
        {"index": int(i), "label": bundle.labels[int(i)], "confidence": round(float(probs[int(i)]), 6)}
        for i in top_idx
    ]
    pred_idx = int(top_idx[0])
    confidence = float(probs[pred_idx])

    # Confidence thresholding
    is_uncertain = confidence < MIN_CONFIDENCE
    suggestion = None
    if is_uncertain:
        suggestion = "Mô hình không tự tin cao về kết quả này. Hãy chụp ảnh rõ nét hơn, lấy gần lá/cành bị bệnh và thử lại."

    return JSONResponse({
        "predicted_index": pred_idx,
        "predicted_label": bundle.labels[pred_idx],
        "confidence": round(confidence, 6),
        "inference_ms": elapsed_ms,
        "device": bundle.device,
        "top": top,
        "quality": quality.to_dict(),
        "is_uncertain": is_uncertain,
        "suggestion": suggestion,
    })


# ---------------------------------------------------------------------------
# Predict thông minh (tự động phát hiện lá nếu ảnh có nhiều vật thể)
# ---------------------------------------------------------------------------
@app.post("/predict", dependencies=[Depends(_check_token)])
async def predict_advanced(
    file: UploadFile = File(...),
    top_k: int | None = None,
    auto_detect: bool = True,
    include_treatments: bool = True,
) -> JSONResponse:
    """Endpoint predict chính thức. Nếu auto_detect=True:
       - Dùng Grounding DINO phát hiện lá → crop từng lá → dự đoán từng lá → aggregate kết quả
       - Nếu auto_detect=False: trực tiếp predict ảnh gốc (mode backward compatible)

    Bổ sung:
       - Quality check trước khi predict
       - Confidence thresholding
       - Treatment recommendations từ knowledge base
       - Severity assessment
    """
    if not is_ready():
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Model chưa load xong, vui lòng thử lại sau",
        )

    raw = await file.read()
    if not raw:
        raise HTTPException(status_code=400, detail="File rỗng")

    if len(raw) > settings.max_image_mb * 1024 * 1024:
        raise HTTPException(
            status_code=status.HTTP_413_REQUEST_ENTITY_TOO_LARGE,
            detail=f"Tệp vượt quá {settings.max_image_mb}MB",
        )

    try:
        img = Image.open(io.BytesIO(raw)).convert("RGB")
    except Exception as exc:
        raise HTTPException(status_code=400, detail=f"Ảnh không hợp lệ: {exc}") from exc

    # Quality check
    quality = assess_image_quality(img)

    if not auto_detect:
        # Mode cũ: predict ảnh nguyên tem
        return await predict_direct(img, top_k, quality, include_treatments)

    # Mode mới: Detect leaves → predict each leaf → aggregate
    t0 = time.perf_counter()

    # Step 1: Detect all leaves
    boxes = real_grounding_dino_detection(img)

    if not boxes:
        logger.warning("Không tìm thấy lá nào trong ảnh, fallback predict trực tiếp")
        return await predict_direct(img, top_k, quality, include_treatments)

    # Step 2: Predict each leaf
    leaf_results: List[Dict[str, Any]] = []
    for i, box in enumerate(boxes):
        # Crop Leaf
        xmin = max(0, box["xmin"])
        ymin = max(0, box["ymin"])
        xmax = min(img.width, box["xmax"])
        ymax = min(img.height, box["ymax"])

        cropped = img.crop((xmin, ymin, xmax, ymax)).resize(
            (settings.input_size, settings.input_size), Image.BILINEAR
        )

        arr = image_to_array(cropped)

        # Run inference on this leaf
        probs = await anyio.to_thread.run_sync(_run_model, arr)
        probs = np.asarray(probs[0], dtype=np.float32)
        if probs.shape[0] != bundle.num_classes:
            probs = probs.reshape(-1)

        pred_idx = int(np.argmax(probs))
        confidence = float(probs[pred_idx])

        leaf_results.append({
            "leaf_id": i,
            "bbox": box,
            "predicted_label": bundle.labels[pred_idx],
            "confidence": confidence,
            "pred_index": pred_idx,
        })

        logger.info(f"Leaf {i}: {bundle.labels[pred_idx]} @ {confidence:.2%}")

    # Step 3: Aggregate predictions (major voting + confidence averaging)
    aggregated = aggregate_leaf_predictions(leaf_results, top_k)
    aggregated["inference_ms"] = round((time.perf_counter() - t0) * 1000, 2)
    aggregated["method"] = "ensemble_with_leaf_detection"
    aggregated["quality"] = quality.to_dict()

    # Step 4: Add treatments if requested
    if include_treatments:
        predicted_label = aggregated.get("predicted_label", "")
        is_healthy = "healthy" in predicted_label.lower()

        # Count affected leaves
        label_counts = {}
        for lr in leaf_results:
            lbl = lr["predicted_label"]
            label_counts[lbl] = label_counts.get(lbl, 0) + 1
        affected_leaves = label_counts.get(predicted_label, 0)

        # Assess severity
        severity = assess_severity(
            confidence=aggregated.get("confidence", 0),
            leaf_count=len(leaf_results),
            affected_leaves=affected_leaves,
            is_healthy=is_healthy,
        )

        # Get treatment recommendations
        treatments = get_treatment_recommendations(
            predicted_label,
            severity=severity,
        )

        aggregated["severity"] = severity
        aggregated["treatments"] = treatments.get("treatments", [])
        aggregated["prevention"] = treatments.get("prevention", [])
        aggregated["description"] = treatments.get("description", "")
        aggregated["disease_info"] = treatments.get("disease_name", "")

    # Step 5: Confidence thresholding
    confidence = aggregated.get("confidence", 0)
    aggregated["is_uncertain"] = confidence < MIN_CONFIDENCE
    if aggregated["is_uncertain"]:
        aggregated["suggestion"] = (
            "Mô hình không tự tin cao về kết quả này. "
            "Hãy chụp ảnh rõ nét hơn, lấy gần lá/cành bị bệnh và thử lại."
        )

    return JSONResponse(aggregated)


async def predict_direct(
    img: Image.Image,
    top_k: int | None,
    quality=None,
    include_treatments: bool = True,
) -> JSONResponse:
    """Predict directly on full image (backward compatible)."""
    t0 = time.perf_counter()

    processed = normalize_image(img, (settings.input_size, settings.input_size))
    arr = image_to_array(processed)

    probs = await anyio.to_thread.run_sync(_run_model, arr)
    elapsed_ms = round((time.perf_counter() - t0) * 1000, 2)

    probs = np.asarray(probs[0], dtype=np.float32)
    if probs.shape[0] != bundle.num_classes:
        probs = probs.reshape(-1)
    k = min(top_k or settings.top_k, bundle.num_classes)
    top_idx = np.argsort(probs)[::-1][:k]

    top = [
        {"index": int(i), "label": bundle.labels[int(i)], "confidence": round(float(probs[int(i)]), 6)}
        for i in top_idx
    ]
    pred_idx = int(top_idx[0])
    confidence = float(probs[pred_idx])

    result = {
        "predicted_index": pred_idx,
        "predicted_label": bundle.labels[pred_idx],
        "confidence": round(confidence, 6),
        "inference_ms": elapsed_ms,
        "device": bundle.device,
        "top": top,
        "method": "direct_predict",
        "leaf_count": 1,
    }

    # Add quality
    if quality:
        result["quality"] = quality.to_dict()

    # Add treatments if requested
    if include_treatments:
        predicted_label = bundle.labels[pred_idx]
        is_healthy = "healthy" in predicted_label.lower()

        severity = assess_severity(
            confidence=confidence,
            leaf_count=1,
            affected_leaves=1,
            is_healthy=is_healthy,
        )

        treatments = get_treatment_recommendations(
            predicted_label,
            severity=severity,
        )

        result["severity"] = severity
        result["treatments"] = treatments.get("treatments", [])
        result["prevention"] = treatments.get("prevention", [])
        result["description"] = treatments.get("description", "")
        result["disease_info"] = treatments.get("disease_name", "")

    # Confidence thresholding
    result["is_uncertain"] = confidence < MIN_CONFIDENCE
    if result["is_uncertain"]:
        result["suggestion"] = (
            "Mô hình không tự tin cao về kết quả này. "
            "Hãy chụp ảnh rõ nét hơn, lấy gần lá/cành bị bệnh và thử lại."
        )

    return JSONResponse(result)


def aggregate_leaf_predictions(
    leaf_results: List[Dict[str, Any]],
    top_k: int | None,
) -> Dict[str, Any]:
    """Aggregate predictions from multiple leaves using major voting + confidence averaging."""
    from collections import Counter

    if not leaf_results:
        return {"predicted_label": "", "confidence": 0, "leaf_count": 0}

    # Count occurrences and sum confidence for each label
    label_counts = Counter()
    label_confidence_sum = {}

    for lr in leaf_results:
        lbl = lr["predicted_label"]
        c = lr["confidence"]
        label_counts[lbl] += 1
        label_confidence_sum[lbl] = label_confidence_sum.get(lbl, 0) + c

    # Calculate average confidence per unique label
    avg_confidences = {
        lbl: label_confidence_sum[lbl] / count
        for lbl, count in label_counts.items()
    }

    # Major voting: find the most common label
    most_common = label_counts.most_common(1)
    dominant_label = most_common[0][0] if most_common else ""
    dominant_count = most_common[0][1] if most_common else 0

    # Tie-breaker: if there's a tie in counts, pick highest average confidence
    if dominant_count == 1 and len(label_counts) > 1:
        dominant_label = max(avg_confidences, key=avg_confidences.get)

    final_confidence = avg_confidences.get(dominant_label, 0.0)

    # Get top-k aggregated results
    sorted_labels = sorted(avg_confidences.items(), key=lambda x: x[1], reverse=True)
    top_aggregated = [
        {"label": lbl, "avg_confidence": round(avg_conf, 6), "votes": label_counts[lbl]}
        for lbl, avg_conf in sorted_labels
    ][:top_k or 5]

    return {
        "predicted_label": dominant_label,
        "confidence": final_confidence,
        "leaf_count": len(leaf_results),
        "dominant_vote": f"{dominant_label} ({dominant_count}/{len(leaf_results)} leaves)",
        "top_k_aggregated": top_aggregated,
    }


# ---------------------------------------------------------------------------
# Global variables
# ---------------------------------------------------------------------------
bundle = None
_leaf_detector = None


def _run_model(arr: np.ndarray):
    """Hàm chạy đồng bộ (chạy trong thread)."""
    global bundle
    if bundle is None:
        bundle = get_bundle()
    return bundle.model.predict(arr, verbose=0)
