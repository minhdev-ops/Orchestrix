#!/bin/bash
# Full Pipeline Test: Start AI Service → Run PHP Test → Output Results
# All-in-one script to avoid process management issues

set -e
PROJ=/home/couterit/Work/02.Study/Project/Orchestrix
cd "$PROJ"

echo "========================================"
echo "  FULL PIPELINE TEST"
echo "========================================"
echo ""

# STEP 1: Start AI Service
echo "[1] Starting AI Service..."
pkill -9 -f 'uvicorn app.main' 2>/dev/null || true
sleep 1

cd "$PROJ/ai_service"
AI_MODEL_PATH="$PROJ/best_vit.keras" AI_LABELS_PATH="$PROJ/ai_service/labels.txt" \
    python3 -m uvicorn app.main:app --host 0.0.0.0 --port 8501 &
AI_PID=$!

# Wait for port
echo "    Waiting for model load..."
for i in $(seq 1 20); do
    if ss -tlnp 2>/dev/null | grep -q 8501; then
        echo "    ✅ AI Service ready on port 8501 (PID: $AI_PID)"
        break
    fi
    if ! kill -0 $AI_PID 2>/dev/null; then
        echo "    ❌ AI Service died! Check logs."
        exit 1
    fi
    sleep 2
done

# Verify port is listening
if ! ss -tlnp 2>/dev/null | grep -q 8501; then
    echo "    ❌ Port 8501 not listening after 40s"
    exit 1
fi

# STEP 2: Test AI endpoints directly
echo ""
echo "[2] Testing AI Endpoints..."

# Get test images (từ đối số hoặc dataset hoặc tạo ảnh tổng hợp)
if [ -n "$1" ]; then
    IMAGES=("$1")
else
    TEST_IMG=$(bash "$PROJ/scripts/get_test_image.sh")
    IMAGES=("$TEST_IMG")
fi
echo "    Found ${#IMAGES[@]} test images"

AI_SUCCESS=0
AI_FAIL=0

for IMG in "${IMAGES[@]}"; do
    CATEGORY=$(basename "$(dirname "$IMG")")
    # Test /predict-leaf (simple, proven to work)
    RESULT=$(curl -s --max-time 10 -X POST http://localhost:8501/predict-leaf \
        -F "file=@$IMG" -F 'top_k=3' 2>/dev/null)
    
    if echo "$RESULT" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['predicted_label'], d['confidence'])" 2>/dev/null; then
        AI_SUCCESS=$((AI_SUCCESS + 1))
    else
        echo "    ❌ FAILED: $CATEGORY"
        AI_FAIL=$((AI_FAIL + 1))
    fi
done

echo "    AI Results: $AI_SUCCESS succeeded, $AI_FAIL failed"

# STEP 3: Test /predict endpoint (the one used by PHP service)
echo ""
echo "[3] Testing /predict endpoint..."
IMG="${IMAGES[0]}"
PREDICT_RESULT=$(curl -s --max-time 10 -X POST http://localhost:8501/predict \
    -F "file=@$IMG" -F 'top_k=3' -F 'auto_detect=false' 2>/dev/null)

if echo "$PREDICT_RESULT" | python3 -c "import sys,json; d=json.load(sys.stdin); print(f'OK: {d[\"predicted_label\"]} ({d[\"confidence\"]*100:.1f}%)')" 2>/dev/null; then
    PREDICT_OK=true
else
    echo "    ❌ /predict endpoint failed"
    PREDICT_OK=false
fi

# STEP 4: Run PHP Pipeline Test (AI → DB → JSON)
echo ""
echo "[4] Running PHP Pipeline Test..."
cd "$PROJ"
php -d opcache.enable=0 scripts/test_full_pipeline.php 2>&1

# Cleanup
echo ""
echo "[5] Cleaning up..."
kill $AI_PID 2>/dev/null || true

echo ""
echo "========================================"
echo "  TEST COMPLETE"
echo "========================================"
