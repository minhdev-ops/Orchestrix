#!/bin/bash
# ============================================================
# get_test_image.sh - Lấy ảnh test cho AI Plant Doctor
# Ưu tiên: ảnh truyền vào ($1) -> ảnh trong dataset (nếu còn)
#          -> tạo ảnh tổng hợp tại /tmp (không phụ thuộc dataset)
# ============================================================
PROJ=/home/couterit/Work/02.Study/Project/Orchestrix

if [ -n "$1" ] && [ -f "$1" ]; then
    echo "$1"
    exit 0
fi

IMG=$(find "$PROJ/PlantDiseaseDataset" -type f \( -name '*.jpg' -o -name '*.jpeg' -o -name '*.png' \) 2>/dev/null | head -1)
if [ -n "$IMG" ]; then
    echo "$IMG"
    exit 0
fi

TMP=/tmp/plant_test_image.jpg
if [ ! -f "$TMP" ]; then
    python3 - "$TMP" <<'PYEOF'
import random
import sys
from PIL import Image, ImageDraw

img = Image.new('RGB', (512, 512), (70, 130, 70))
d = ImageDraw.Draw(img)
for i in range(40, 512, 90):
    d.ellipse([i, 70, i + 140, 300], fill=(90, 160, 80))
    d.line([i + 70, 300, i + 70, 490], fill=(50, 90, 50), width=12)
for _ in range(40):
    x, y = random.randint(0, 511), random.randint(0, 511)
    d.ellipse([x, y, x + 10, y + 10], fill=(45, 85, 45))
img.save(sys.argv[1], 'JPEG')
PYEOF
fi
echo "$TMP"
