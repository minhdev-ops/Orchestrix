"""Image preprocessing module for AI Plant Doctor Pipeline.

Provides:
- Image quality assessment (blur, brightness, size)
- Image normalization and resize
- Color-based leaf segmentation fallback (when Grounding DINO unavailable)
"""
from __future__ import annotations

import logging
from dataclasses import dataclass
from enum import Enum
from typing import List, Dict, Optional, Tuple

import numpy as np
from PIL import Image, ImageFilter, ImageStat

logger = logging.getLogger("agriverse_preprocessing")


class QualityIssue(str, Enum):
    """Các vấn đề chất lượng ảnh có thể gặp."""
    TOO_SMALL = "too_small"
    TOO_BLURRY = "too_blurry"
    TOO_DARK = "too_dark"
    TOO_BRIGHT = "too_bright"
    LOW_CONTRAST = "low_contrast"
    NONE = "none"


@dataclass
class QualityReport:
    """Báo cáo chất lượng ảnh."""
    is_acceptable: bool
    issues: List[QualityIssue]
    blur_score: float  # 0-100, higher = sharper
    brightness: float  # 0-255
    contrast: float  # 0-100
    resolution: Tuple[int, int]  # (width, height)
    message: str

    def to_dict(self) -> Dict:
        return {
            "is_acceptable": self.is_acceptable,
            "issues": [i.value for i in self.issues],
            "blur_score": round(self.blur_score, 1),
            "brightness": round(self.brightness, 1),
            "contrast": round(self.contrast, 1),
            "resolution": {"width": self.resolution[0], "height": self.resolution[1]},
            "message": self.message,
        }


@dataclass
class LeafRegion:
    """Vùng lá được phát hiện bằng color-based segmentation."""
    xmin: int
    ymin: int
    xmax: int
    ymax: int
    confidence: float
    area_ratio: float  # Tỷ lệ diện tích so với ảnh gốc

    def to_dict(self) -> Dict:
        return {
            "xmin": self.xmin,
            "ymin": self.ymin,
            "xmax": self.xmax,
            "ymax": self.ymax,
            "confidence": round(self.confidence, 3),
            "area_ratio": round(self.area_ratio, 3),
        }


# ---------------------------------------------------------------------------
# Image Quality Assessment
# ---------------------------------------------------------------------------

def assess_image_quality(
    img: Image.Image,
    min_width: int = 64,
    min_height: int = 64,
    min_blur_score: float = 30.0,
    min_brightness: float = 30.0,
    max_brightness: float = 225.0,
    min_contrast: float = 15.0,
) -> QualityReport:
    """Đánh giá chất lượng ảnh đầu vào.

    Args:
        img: PIL Image cần kiểm tra
        min_width/height: Kích thước tối thiểu
        min_blur_score: Điểm sharpness tối thiểu (Laplacian variance)
        min_brightness/max_brightness: Phạm vi brightness chấp nhận được
        min_contrast: Độ tương phản tối thiểu

    Returns:
        QualityReport với chi tiết các vấn đề
    """
    issues = []
    width, height = img.size

    # 1. Check resolution
    if width < min_width or height < min_height:
        issues.append(QualityIssue.TOO_SMALL)
        logger.warning(f"Ảnh quá nhỏ: {width}x{height} (cần tối thiểu {min_width}x{min_height})")

    # 2. Check blur (Laplacian variance)
    blur_score = _calculate_blur_score(img)
    if blur_score < min_blur_score:
        issues.append(QualityIssue.TOO_BLURRY)
        logger.warning(f"Ảnh quá mờ: blur_score={blur_score:.1f} (cần tối thiểu {min_blur_score})")

    # 3. Check brightness
    brightness = _calculate_brightness(img)
    if brightness < min_brightness:
        issues.append(QualityIssue.TOO_DARK)
        logger.warning(f"Ảnh quá tối: brightness={brightness:.1f}")
    elif brightness > max_brightness:
        issues.append(QualityIssue.TOO_BRIGHT)
        logger.warning(f"Ảnh quá sáng: brightness={brightness:.1f}")

    # 4. Check contrast
    contrast = _calculate_contrast(img)
    if contrast < min_contrast:
        issues.append(QualityIssue.LOW_CONTRAST)
        logger.warning(f"Ảnh thiếu tương phản: contrast={contrast:.1f}")

    # Generate message
    is_acceptable = len(issues) == 0 or issues == [QualityIssue.NONE]
    message = _generate_quality_message(issues, blur_score, brightness, contrast)

    return QualityReport(
        is_acceptable=is_acceptable,
        issues=issues if issues else [QualityIssue.NONE],
        blur_score=blur_score,
        brightness=brightness,
        contrast=contrast,
        resolution=(width, height),
        message=message,
    )


def _calculate_blur_score(img: Image.Image) -> float:
    """Tính điểm blur sử dụng Laplacian variance.
    Giá trị cao = ảnh sắc nét, giá trị thấp = ảnh mờ.
    """
    # Convert to grayscale
    gray = img.convert("L")

    # Apply Laplacian filter
    laplacian = gray.filter(ImageFilter.Kernel(
        size=(3, 3),
        kernel=[-1, -1, -1, -1, 8, -1, -1, -1, -1],
        scale=1,
        offset=0,
    ))

    # Calculate variance
    stat = ImageStat.Stat(laplacian)
    variance = stat.var[0] if stat.var else 0

    return float(variance)


def _calculate_brightness(img: Image.Image) -> float:
    """Tính brightness trung bình của ảnh (0-255)."""
    gray = img.convert("L")
    stat = ImageStat.Stat(gray)
    return float(stat.mean[0]) if stat.mean else 0.0


def _calculate_contrast(img: Image.Image) -> float:
    """Tính contrast của ảnh (standard deviation của brightness)."""
    gray = img.convert("L")
    stat = ImageStat.Stat(gray)
    return float(stat.stddev[0]) if stat.stddev else 0.0


def _generate_quality_message(
    issues: List[QualityIssue],
    blur_score: float,
    brightness: float,
    contrast: float,
) -> str:
    """Tạo message hướng dẫn người dùng dựa trên các vấn đề chất lượng."""
    if not issues or issues == [QualityIssue.NONE]:
        return "Ảnh đủ chất lượng để phân tích."

    messages = []

    if QualityIssue.TOO_SMALL in issues:
        messages.append("Ảnh quá nhỏ. Vui lòng chụp ảnh rõ nét hơn với kích thước tối thiểu 64x64 pixels.")

    if QualityIssue.TOO_BLURRY in issues:
        messages.append(f"Ảnh bị mờ (điểm sắc nét: {blur_score:.0f}/100). "
                       "Vui lòng giữ ổn định máy ảnh khi chụp và đảm bảo tiêu cự đúng.")

    if QualityIssue.TOO_DARK in issues:
        messages.append("Ảnh quá tối. Vui lòng chụp ảnh ở nơi có đủ ánh sáng hoặc bật đèn flash.")

    if QualityIssue.TOO_BRIGHT in issues:
        messages.append("Ảnh quá sáng. Vui lòng chụp ảnh ở nơi có ánh sáng nhẹ, tránh ánh nắng trực tiếp.")

    if QualityIssue.LOW_CONTRAST in issues:
        messages.append("Ảnh thiếu tương phản. Vui lòng chụp ảnh với ánh sáng đều hơn.")

    return " ".join(messages)


# ---------------------------------------------------------------------------
# Image Normalization
# ---------------------------------------------------------------------------

def normalize_image(
    img: Image.Image,
    target_size: Tuple[int, int] = (224, 224),
    enhance_contrast: bool = True,
) -> Image.Image:
    """Chuẩn hóa ảnh cho model inference.

    Args:
        img: PIL Image gốc
        target_size: Kích thước mục tiêu (width, height)
        enhance_contrast: Có tăng contrast không

    Returns:
        PIL Image đã chuẩn hóa
    """
    # 1. Convert to RGB if needed
    if img.mode != "RGB":
        img = img.convert("RGB")

    # 2. Enhance contrast if needed
    if enhance_contrast:
        from PIL import ImageEnhance
        enhancer = ImageEnhance.Contrast(img)
        img = enhancer.enhance(1.2)  # Tăng 20%

    # 3. Resize to target size
    img = img.resize(target_size, Image.BILINEAR)

    return img


def image_to_array(img: Image.Image) -> np.ndarray:
    """Chuyển PIL Image sang numpy array cho model inference.

    Returns:
        numpy array shape (1, H, W, 3), dtype float32
    """
    arr = np.asarray(img, dtype=np.float32)
    arr = np.expand_dims(arr, axis=0)
    return arr


# ---------------------------------------------------------------------------
# Color-based Leaf Segmentation (Fallback)
# ---------------------------------------------------------------------------

def color_based_leaf_detection(
    img: Image.Image,
    min_leaf_area_ratio: float = 0.02,
    max_leaf_area_ratio: float = 0.8,
    green_threshold: float = 0.3,
) -> List[LeafRegion]:
    """Phát hiện lá cây dựa trên màu sắc (fallback khi Grounding DINO unavailable).

    Sử dụng HSV color space để detect vùng màu xanh lá (green).
    Đây là method đơn giản, không cần model ML.

    Args:
        img: PIL Image RGB
        min_leaf_area_ratio: Tỷ lệ diện tích tối thiểu của một lá
        max_leaf_area_ratio: Tỷ lệ diện tích tối đa của một lá
        green_threshold: Ngưỡng phát hiện màu xanh (0-1)

    Returns:
        Danh sách LeafRegion
    """
    width, height = img.size
    img_array = np.asarray(img, dtype=np.float32) / 255.0

    # Convert to HSV-like using simple calculation
    # Green detection: G channel significantly higher than R and B
    r, g, b = img_array[:, :, 0], img_array[:, :, 1], img_array[:, :, 2]

    # Simple green mask: G > R * 1.1 AND G > B * 1.1 AND G > green_threshold
    green_mask = (
        (g > r * 1.1) &
        (g > b * 1.1) &
        (g > green_threshold)
    )

    # Find connected components (simple flood fill approach)
    # For simplicity, we'll use a grid-based approach
    regions = _find_green_regions(green_mask, width, height, min_leaf_area_ratio, max_leaf_area_ratio)

    # If no regions found, return the whole image as one region
    if not regions:
        logger.info("Không phát hiện được vùng lá nào, sử dụng toàn ảnh")
        return [LeafRegion(
            xmin=0,
            ymin=0,
            xmax=width,
            ymax=height,
            confidence=0.5,
            area_ratio=1.0,
        )]

    logger.info(f"Color-based detection: tìm thấy {len(regions)} vùng lá")
    return regions


def _find_green_regions(
    mask: np.ndarray,
    width: int,
    height: int,
    min_area_ratio: float,
    max_area_ratio: float,
) -> List[LeafRegion]:
    """Tìm các vùng lá từ binary mask."""
    regions = []
    total_area = width * height

    # Simple approach: divide image into grid and find green cells
    grid_size = 32  # pixels per cell
    rows = height // grid_size
    cols = width // grid_size

    # Find cells with high green ratio
    green_cells = []
    for r in range(rows):
        for c in range(cols):
            y1 = r * grid_size
            y2 = min((r + 1) * grid_size, height)
            x1 = c * grid_size
            x2 = min((c + 1) * grid_size, width)

            cell = mask[y1:y2, x1:x2]
            green_ratio = np.mean(cell)

            if green_ratio > 0.4:  # Cell is mostly green
                green_cells.append((r, c, green_ratio))

    if not green_cells:
        return []

    # Group adjacent green cells into regions
    # Simple approach: find bounding box of all green cells
    if green_cells:
        min_r = min(r for r, c, _ in green_cells)
        max_r = max(r for r, c, _ in green_cells)
        min_c = min(c for r, c, _ in green_cells)
        max_c = max(c for r, c, _ in green_cells)

        # Calculate bounding box
        xmin = max(0, min_c * grid_size)
        ymin = max(0, min_r * grid_size)
        xmax = min(width, (max_c + 1) * grid_size)
        ymax = min(height, (max_r + 1) * grid_size)

        area_ratio = ((xmax - xmin) * (ymax - ymin)) / total_area

        # Check if area is within bounds
        if min_area_ratio <= area_ratio <= max_area_ratio:
            avg_confidence = np.mean([g for _, _, g in green_cells])
            regions.append(LeafRegion(
                xmin=xmin,
                ymin=ymin,
                xmax=xmax,
                ymax=ymax,
                confidence=float(avg_confidence),
                area_ratio=float(area_ratio),
            ))

    return regions


# ---------------------------------------------------------------------------
# Combined Preprocessing Pipeline
# ---------------------------------------------------------------------------

def preprocess_image(
    img: Image.Image,
    target_size: Tuple[int, int] = (224, 224),
    check_quality: bool = True,
    enhance_contrast: bool = True,
) -> Tuple[Image.Image, Optional[QualityReport]]:
    """Pipeline preprocessing hoàn chỉnh.

    Args:
        img: PIL Image gốc
        target_size: Kích thước mục tiêu
        check_quality: Có kiểm tra chất lượng không
        enhance_contrast: Có tăng contrast không

    Returns:
        Tuple của (processed_image, quality_report)
    """
    quality_report = None

    # 1. Quality check
    if check_quality:
        quality_report = assess_image_quality(img)

    # 2. Normalize
    processed = normalize_image(img, target_size, enhance_contrast)

    return processed, quality_report
