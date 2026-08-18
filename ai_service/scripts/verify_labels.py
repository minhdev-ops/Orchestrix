#!/usr/bin/env python3
"""Kiểm chứng thứ tự labels.txt có khớp với output index của model hay không.

Cách dùng:
    python scripts/verify_labels.py --dataset ../PlantDiseaseDataset/plant_disease_dataset/train
    python scripts/verify_labels.py --dataset <train_dir> --sample-per-class 5

Duyệt các subfolder của `train_dir` theo thứ tự (C-locale alphabetical),
lấy 1 -> sample_per_class ảnh mỗi lớp, chạy model, so sánh argmax với folder name.
Nếu labels.txt khớp đúng thứ tự train -> accuracy ~ cao.
Nếu lệch -> in ra confusion matrix rút gọn + gợi ý đảo/reorder.
"""
from __future__ import annotations

import argparse
import logging
import sys
from pathlib import Path

import numpy as np
from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

from app.model_loader import load_model  # noqa: E402
from app.config import Settings  # noqa: E402

logging.basicConfig(level=logging.INFO, format="%(levelname)s %(message)s")


def main() -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--dataset", type=Path, required=True,
                   help="Thư mục train (chứa 71 subfolder lớp)")
    p.add_argument("--labels", type=Path, default=ROOT / "labels.txt")
    p.add_argument("--model", type=Path, default=Path("../best_vit.keras"))
    p.add_argument("--sample-per-class", type=int, default=3,
                   help="Số ảnh test mỗi lớp")
    p.add_argument("--input-size", type=int, default=224)
    args = p.parse_args()

    if not args.dataset.exists():
        print(f"❌ Không tìm thấy dataset: {args.dataset}")
        return 1

    settings = Settings(
        model_path=args.model, labels_path=args.labels,
        input_size=args.input_size,
    )
    bundle = load_model(settings)

    # Lấy danh sách lớp theo C-locale alphabetical (giống image_dataset_from_directory)
    classes = sorted(
        [d.name for d in args.dataset.iterdir() if d.is_dir()],
        key=lambda s: s.encode("utf-8"),
    )
    print(f"\nDataset: {len(classes)} lớp (labels.txt có {bundle.num_classes})")
    if len(classes) != bundle.num_classes:
        print("⚠ Số lớp dataset khác số labels -> có thể sai mapping!")

    correct = 0
    total = 0
    mismatches: list[tuple[str, str, str]] = []  # (truth, predicted, file)

    for class_idx, class_name in enumerate(classes):
        expected_label = bundle.labels[class_idx] if class_idx < bundle.num_classes else "?"

        class_dir = args.dataset / class_name
        files = list(class_dir.glob("*"))
        if not files:
            continue
        step = max(1, len(files) // args.sample_per_class)
        sample = files[::step][: args.sample_per_class]

        for fp in sample:
            try:
                img = Image.open(fp).convert("RGB").resize(
                    (bundle.input_size, bundle.input_size), Image.BILINEAR,
                )
                arr = np.expand_dims(np.asarray(img, dtype=np.float32), axis=0)
                probs = bundle.model.predict(arr, verbose=0)[0]
                pred_idx = int(np.argmax(probs))
            except Exception as exc:  # noqa: BLE001
                print(f"⚠ skip {fp}: {exc}")
                continue

            total += 1
            pred_label = bundle.labels[pred_idx] if pred_idx < bundle.num_classes else "?"
            if pred_idx == class_idx:
                correct += 1
            else:
                mismatches.append((expected_label, pred_label, fp.name))

    print(f"\n=== Kết quả verify ===")
    print(f"Total samples: {total}")
    print(f"Match (index):  {correct} ({correct/max(1,total)*100:.1f}%)")
    print(f"Mismatch:       {len(mismatches)}")

    if mismatches:
        # Top mismatched classes
        from collections import Counter
        cm = Counter((t, p) for t, p, _ in mismatches)
        print("\nTop confusion (truth → predicted):")
        for (t, p), count in cm.most_common(15):
            print(f"  {count:>3}x  '{t}' → '{p}'")
        print("\n⚠ Nếu accuracy thấp → labels.txt đang lệch thứ tự.")
        print("   Hãy chạy verify trên ít mẫu (sample-per-class nhỏ) để xác nhận,")
        print("   rồi tạo lại labels.txt đúng thứ tự training hoặc đảo index.")
        return 2

    print("\n✅ labels.txt khớp đúng thứ tự output của model.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
