#!/usr/bin/env python3
"""CLI nhanh để test model mà cần tới HTTP.

Cách dùng:
    python scripts/predict_cli.py --image path/to/leaf.jpg
    python scripts/predict_cli.py --image img.jpg --top-k 3 --labels labels.txt \
        --model ../best_vit.keras

Tải model, bỏ augmentation, dự đoán và in top-k.
"""
from __future__ import annotations

import argparse
import json
import logging
import sys
import time
from pathlib import Path

import numpy as np
from PIL import Image

logging.basicConfig(level=logging.INFO, format="%(levelname)s %(message)s")

# Cho phép import package `app` khi chạy từ folder scripts/
ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from app.model_loader import load_model  # noqa: E402
from app.config import Settings  # noqa: E402


def main() -> int:
    p = argparse.ArgumentParser(description="Predict 1 image bằng best_vit.keras")
    p.add_argument("--image", required=True, type=Path, help="Đường dẫn ảnh")
    p.add_argument("--model", type=Path, default=None, help="best_vit.keras")
    p.add_argument("--labels", type=Path, default=ROOT / "labels.txt")
    p.add_argument("--top-k", type=int, default=5)
    p.add_argument("--input-size", type=int, default=224)
    args = p.parse_args()

    if not args.image.exists():
        print(f"❌ Không tìm thấy ảnh: {args.image}")
        return 1

    settings = Settings(
        model_path=args.model or Path("../best_vit.keras"),
        labels_path=args.labels,
        input_size=args.input_size,
        top_k=args.top_k,
    )
    bundle = load_model(settings)

    img = Image.open(args.image).convert("RGB").resize(
        (bundle.input_size, bundle.input_size), Image.BILINEAR,
    )
    arr = np.expand_dims(np.asarray(img, dtype=np.float32), axis=0)

    t0 = time.perf_counter()
    probs = bundle.model.predict(arr, verbose=0)[0]
    ms = round((time.perf_counter() - t0) * 1000, 2)

    probs = np.asarray(probs, dtype=np.float32).reshape(-1)
    top = np.argsort(probs)[::-1][: args.top_k]

    print(f"\n🖼  Ảnh: {args.image}")
    print(f"⚡ Inference: {ms} ms | device: {bundle.device}\n")
    print("Top-%d dự đoán:" % args.top_k)
    for i in top:
        print(f"  {i:>3} | {float(probs[i]):.4f} | {bundle.labels[int(i)]}")

    print("\n✅ OK")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
