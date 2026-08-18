"""Singleton loader cho best_vit.keras.

Model được train kèm layer `data_augmentation` (chỉ hợp lệ ở train time) và
`Rescaling` (1/255) nằm sẵn trong pipeline. Khi inference:
- Bỏ layer `data_augmentation` để kết quả ổn định mỗi lần gọi.
- Giữ `Rescaling` nguyên trong model, service chỉ resize ảnh về 224x224.

Tải 1 lần duy nhất khi startup, cache cho toàn bộ app (FastAPI process).
"""
from __future__ import annotations

import json
import logging
import os
from pathlib import Path
from typing import Optional

import numpy as np

from .config import Settings

logger = logging.getLogger("agriverse_vit")


class ModelBundle:
    """Bọc model + labels + metadata."""

    def __init__(self, model, labels: list[str], settings: Settings):
        self.model = model
        self.labels = labels
        self.settings = settings
        self.input_size = settings.input_size

    @property
    def num_classes(self) -> int:
        return len(self.labels)


_bundle: Optional[ModelBundle] = None


def _select_device(requested: str) -> str:
    """Trả về 'GPU' nếu có và được phép, ngược lại 'CPU'."""
    if requested == "cpu":
        return "CPU"
    try:
        import tensorflow as tf  # noqa: WPS433 (lazy import)
        gpus = tf.config.list_physical_devices("GPU")
        if gpus:
            for g in gpus:
                try:
                    tf.config.experimental.set_memory_growth(g, True)
                except Exception:  # noqa: BLE001
                    pass
            return "GPU"
    except Exception:  # noqa: BLE001
        pass
    return "CPU"


def _load_labels(path: Path) -> list[str]:
    if not path.exists():
        raise FileNotFoundError(f"Labels file không tồn tại: {path}")
    lines = [
        line.strip()
        for line in path.read_text(encoding="utf-8").splitlines()
        if line.strip()
    ]
    if not lines:
        raise ValueError(f"Labels file rỗng: {path}")
    return lines


def _strip_data_augmentation(model):
    """Loại bỏ layer `data_augmentation` khỏi Functional model.

    Lưu ý KHÔNG cần gọi hàm này khi inference bình thường: `model.predict()`
    thiết lập training=False nên các layer augmentation (RandomFlip, ...)
    tự động chuyển sang identity. Hàm này chỉ dùng khi bạn muốn bỏ hẳn layer
    để giảm overhead/đảm bảo 100% không có biến đổi.

    Rebuild bằng `keras.Model.from_config` với config đã bỏ layer + sửa lại
    inbound node trỏ vào `input_layer`. Trọng số được copy từ model gốc.
    Trả về model mới nếu thành công, ngược lại ném exception (caller sẽ fallback).
    """
    import keras  # noqa: WPS433

    cfg = json.loads(model.to_json())
    skip_names = {n for cfg_layer in cfg["config"]["layers"]
                  if "augmentation" in (n := cfg_layer["config"]["name"])}

    new_layers_cfg = []
    for layer_cfg in cfg["config"]["layers"]:
        name = layer_cfg["config"]["name"]
        if name in skip_names:
            logger.info("Bỏ layer '%s' khi inference", name)
            continue
        # Sửa inbound node trỏ vào augmentation -> input_layer
        for node in layer_cfg.get("inbound_nodes", []) or []:
            for part in node:
                if isinstance(part, list) and part and part[0] in skip_names:
                    part[0] = "input_layer"
        new_layers_cfg.append(layer_cfg)

    new_cfg = {
        "name": cfg["config"]["name"] + "_inference",
        "layers": new_layers_cfg,
        "input_layers": cfg["config"].get("input_layers", [["input_layer", 0, 0]]),
        "output_layers": cfg["config"].get("output_layers", []),
    }
    rebuilt = keras.Model.from_config({"class_name": "Functional", "config": new_cfg})

    # Copy weights khớp tên layer
    src_by_name = {layer.name: layer for layer in model.layers}
    for layer in rebuilt.layers:
        src = src_by_name.get(layer.name)
        if src is not None and layer.get_weights():
            try:
                layer.set_weights(src.get_weights())
            except Exception as exc:  # noqa: BLE001
                logger.warning("Skip copy weights cho '%s': %s", layer.name, exc)
    return rebuilt


def load_model(settings: Settings) -> ModelBundle:
    """Tải model + labels + khởi tạo device. Chỉ nên gọi 1 lần."""
    global _bundle
    if _bundle is not None:
        return _bundle

    if not settings.model_path.exists():
        raise FileNotFoundError(f"Model file không tồn tại: {settings.model_path}")

    logger.info("Đang tải model từ %s ...", settings.model_path)
    import keras  # noqa: WPS433 (lazy import để startup import nhanh hơn)

    model = keras.saving.load_model(str(settings.model_path))
    logger.info("Model đã load. Tổng layers: %d", len(model.layers))

    # Bỏ augmentation nếu cấu hình bật (mặc định tắt vì predict đã training=False)
    aug_names = {layer.name for layer in model.layers if "augmentation" in layer.name}
    if aug_names:
        logger.info(
            "Model có layer augmentation: %s (training=False trong predict -> identity)",
            ", ".join(sorted(aug_names)),
        )
        if settings.strip_augmentation:
            try:
                model = _strip_data_augmentation(model)
                logger.info("Đã strip augmentation. Layers còn lại: %d", len(model.layers))
            except Exception as exc:  # noqa: BLE001
                logger.warning("Strip augmentation thất bại (giữ nguyên model): %s", exc)

    labels = _load_labels(settings.labels_path)
    logger.info("Labels: %d lớp", len(labels))

    device = _select_device(settings.device)
    logger.info("Device: %s", device)

    # Validate số lượng output classes khớp labels
    try:
        out_shape = model.outputs[0].shape
        n_out = int(out_shape[-1])
        if n_out != len(labels):
            logger.warning(
                "Model output (%d) != số labels (%d). Có thể sai thứ tự labels.txt!",
                n_out, len(labels),
            )
    except Exception as exc:  # noqa: BLE001
        logger.warning("Không kiểm được output shape: %s", exc)

    _bundle = ModelBundle(model=model, labels=labels, settings=settings)
    _bundle.device = device  # type: ignore[attr-defined]
    return _bundle


def get_bundle() -> ModelBundle:
    if _bundle is None:
        raise RuntimeError("Model chưa được load. Gọi load_model() khi startup.")
    return _bundle


def is_ready() -> bool:
    return _bundle is not None
