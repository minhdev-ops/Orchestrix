"""Grounding DINO module for leaf detection.

Uses the pretrained model from IDEA Research:
https://huggingface.co/IDEA-Research/grounding-dino-base

Usage:
    from gdino.detector import LeafDetector
    detector = LeafDetector()
    bboxes = detector.detect(image)  # list of Bbox objects
"""

from __future__ import annotations

import logging
from dataclasses import dataclass
from typing import List, Optional

import torch
from PIL import Image

logger = logging.getLogger("agriverse_gdino")

# Model chuẩn trên HuggingFace (IDEA-Research). Các model "IDEA/CCNL-*" không tồn tại.
DEFAULT_MODEL = "IDEA-Research/grounding-dino-base"


@dataclass
class Bbox:
    xmin: int
    ymin: int
    xmax: int
    ymax: int
    confidence: float


class LeafDetector:
    """Wrapper for Grounding DINO model to detect leaves in images."""

    def __init__(self, model_name: str = DEFAULT_MODEL):
        self.model_name = model_name
        self.model = None
        self.processor = None
        self._load_model()

    def _load_model(self):
        """Load Grounding DINO model and processor lazily."""
        if self.model is not None:
            return

        try:
            from transformers import AutoProcessor, AutoModelForZeroShotObjectDetection

            logger.info(f"Loading Grounding DINO model: {self.model_name}")
            self.processor = AutoProcessor.from_pretrained(self.model_name)
            self.model = AutoModelForZeroShotObjectDetection.from_pretrained(self.model_name)
            logger.info("Grounding DINO model loaded successfully")
        except Exception as exc:
            logger.warning(f"Could not load Grounding DINO: {exc}, using fallback mock")
            self.model = None
            self.processor = None

    def detect(self, image: Image.Image, query: str = "leaf") -> List[Bbox]:
        """Detect all leaves in the image using Grounding DINO.

        Args:
            image: PIL Image in RGB mode
            query: Text query for grounding (default: "leaf")

        Returns:
            List of Bbox objects with xmin, ymin, xmax, ymax, confidence
        """
        if self.model is None:
            # Fallback: return empty list (will be handled by caller)
            logger.warning("Using mock Grounding DINO - no real model available")
            return []

        # Text query phải lowercase và kết thúc bằng dấu chấm (yêu cầu của Grounding DINO)
        text = query.strip().lower()
        if not text.endswith("."):
            text += "."

        inputs = self.processor(images=image, text=text, return_tensors="pt")

        with torch.no_grad():
            outputs = self.model(**inputs)

        results = self.processor.post_process_grounded_object_detection(
            outputs,
            inputs.input_ids,
            threshold=0.2,
            text_threshold=0.2,
            target_sizes=[(image.height, image.width)],
        )[0]

        boxes = results.get("boxes", [])
        scores = results.get("scores", [])
        labels = results.get("labels", results.get("text_labels", []))

        bboxes: List[Bbox] = []
        for i, bbox in enumerate(boxes):
            xmin, ymin, xmax, ymax = [float(v) for v in bbox.tolist()]
            conf = float(scores[i]) if len(scores) > i else 0.0
            label = labels[i] if len(labels) > i else ""
            # Chỉ nhận vùng được gán nhãn liên quan đến lá
            if "leaf" in label.lower():
                bboxes.append(Bbox(
                    xmin=int(xmin),
                    ymin=int(ymin),
                    xmax=int(xmax),
                    ymax=int(ymax),
                    confidence=conf,
                ))

        logger.info(f"Detected {len(bboxes)} leaf regions (query='{text}')")
        return bboxes


# Global singleton instance
_leaf_detector: Optional[LeafDetector] = None


def get_leaf_detector() -> LeafDetector:
    """Get the global LeafDetector singleton."""
    global _leaf_detector
    if _leaf_detector is None:
        _leaf_detector = LeafDetector()
    return _leaf_detector
