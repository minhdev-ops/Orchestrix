#!/bin/bash
# Start AI Service + Laravel as persistent background processes
PROJ=/home/couterit/Work/02.Study/Project/Orchestrix

# Kill any existing instances
pkill -9 -f "uvicorn app.main:app" 2>/dev/null
pkill -9 -f "artisan serve" 2>/dev/null
sleep 1

# Start AI service with setsid (new process group, survives parent)
cd "$PROJ/ai_service"
setsid env AI_MODEL_PATH="$PROJ/best_vit.keras" AI_LABELS_PATH="$PROJ/ai_service/labels.txt" \
    python3 -m uvicorn app.main:app --host 0.0.0.0 --port 8501 \
    </dev/null >/tmp/ai_svc.log 2>&1 &
echo "AI service started"

# Start Laravel with setsid
cd "$PROJ"
setsid php artisan serve --host=0.0.0.0 --port=8001 \
    </dev/null >/tmp/laravel_svc.log 2>&1 &
echo "Laravel started"

echo "Waiting 20s for model load..."
sleep 20

# Quick verify
echo "=== PORTS ==="
ss -tlnp 2>/dev/null | grep -E '8001|8501' || echo "No ports listening yet"

echo "=== AI Health ==="
TEST_IMG=$(bash "$PROJ/scripts/get_test_image.sh")
echo "   Test image: $TEST_IMG"
curl -s --max-time 15 -X POST http://localhost:8501/predict-leaf \
    -F "file=@$TEST_IMG" \
    2>/dev/null | python3 -c "import sys,json; d=json.load(sys.stdin); print(f'OK: {d[\"predicted_label\"]} ({d[\"confidence\"]*100:.1f}%)')" 2>/dev/null \
    || echo "AI not ready yet"

echo "=== Laravel ==="
curl -s -o /dev/null -w 'HTTP %{http_code}\n' http://localhost:8001 2>/dev/null || echo "Laravel not ready"

echo "DONE"
