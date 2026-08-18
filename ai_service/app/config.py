"""Cấu hình runtime cho AI Inference Service (đọc từ env)."""
from __future__ import annotations

from functools import lru_cache
from pathlib import Path

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_prefix="AI_", env_file=".env", extra="ignore")

    # Đường dẫn tới model & labels
    model_path: Path = Path("/models/best_vit.keras")
    labels_path: Path = Path("/models/labels.txt")

    # Tham số inference
    input_size: int = 224  # ViT của bạn nhận (224, 224, 3)
    top_k: int = 5
    max_image_mb: int = 10

    # Device: "auto" | "cpu" | "gpu"
    device: str = "auto"

    # Bỏ hẳn layer data_augmentation khỏi model (mặc định False vì model.predict
    # đã đặt training=False, layer augmentation tự identity). Bật True nếu muốn
    # giảm overhead và 100% chắc không có biến đổi nào chạy.
    strip_augmentation: bool = False

    # Token bảo vệ endpoint (rỗng = không kiểm tra)
    auth_token: str = ""

    # Tên service cho healthcheck
    service_name: str = "agriverse-vit-inference"
    service_version: str = "0.1.0"


@lru_cache
def get_settings() -> Settings:
    return Settings()
