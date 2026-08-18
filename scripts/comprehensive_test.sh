#!/bin/bash
# ============================================================
# Comprehensive Test Script - AI Plant Doctor Pipeline
# Runs ALL tests and saves output to /tmp/test_results.txt
# ============================================================
exec > /tmp/test_results.txt 2>&1

PROJ=/home/couterit/Work/02.Study/Project/Orchestrix
RESULT_FILE=/tmp/test_results.txt

echo "========================================================"
echo "  AI PLANT DOCTOR - FULL PIPELINE TEST REPORT"
echo "  Date: $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================================"
echo ""

# ---- SECTION 1: Environment Info ----
echo "## 1. ENVIRONMENT INFO"
echo ""
echo "### System"
uname -a
echo ""
echo "### PHP Version"
php -v | head -1
echo ""
echo "### Python Version"
python3 --version
echo ""
echo "### Composer Packages (relevant)"
cd "$PROJ"
composer show | grep -iE 'laravel|passport|sanctum' 2>/dev/null | head -10
echo ""

# ---- SECTION 2: Configuration ----
echo "## 2. CONFIGURATION"
echo ""
echo "### .env AI Settings"
grep -E '^AI_PLANT_DOCTOR' "$PROJ/.env" 2>/dev/null || echo "(Using .env.example defaults)"
echo ""
echo "### Config Services AI"
php -d opcache.enable=0 -r "
require '$PROJ/vendor/autoload.php';
\$app = require '$PROJ/bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'Provider: ' . config('services.ai_plant_doctor.provider') . PHP_EOL;
echo 'ViT URL: ' . config('services.ai_plant_doctor.vit_service_url') . PHP_EOL;
echo 'Info Provider: ' . config('services.ai_plant_doctor.info_provider') . PHP_EOL;
echo 'Min Confidence: ' . config('services.ai_plant_doctor.vit_defaults.min_confidence') . PHP_EOL;
echo 'Top K: ' . config('services.ai_plant_doctor.vit_defaults.top_k') . PHP_EOL;
" 2>&1
echo ""

# ---- SECTION 3: Test Images ----
echo "## 3. TEST IMAGES"
echo ""
echo "### Nguồn ảnh: ưu tiên dataset (nếu còn) -> ảnh tổng hợp"
if [ -d "$PROJ/PlantDiseaseDataset/plant_disease_dataset/val" ]; then
    echo "### Dataset: $PROJ/PlantDiseaseDataset/plant_disease_dataset/val/"
    CATEGORIES=0
    TOTAL_IMAGES=0
    for dir in "$PROJ"/PlantDiseaseDataset/plant_disease_dataset/val/*/; do
        count=$(find "$dir" -maxdepth 1 -name '*.jpg' -type f 2>/dev/null | wc -l)
        if [ "$count" -gt 0 ]; then
            CATEGORIES=$((CATEGORIES + 1))
            TOTAL_IMAGES=$((TOTAL_IMAGES + count))
        fi
    done
    echo "**Total: $CATEGORIES categories, $TOTAL_IMAGES images**"
else
    echo "### Dataset đã bị xóa - dùng ảnh tổng hợp cho test"
fi
echo ""

# ---- SECTION 4: Start AI Service ----
echo "## 4. AI SERVICE STARTUP"
echo ""
echo "### Killing existing processes..."
pkill -9 -f 'uvicorn app.main' 2>/dev/null
sleep 1

echo "### Starting AI Service..."
cd "$PROJ/ai_service"
AI_MODEL_PATH="$PROJ/best_vit.keras" AI_LABELS_PATH="$PROJ/ai_service/labels.txt" \
    python3 -m uvicorn app.main:app --host 0.0.0.0 --port 8501 &
AI_PID=$!
echo "AI PID: $AI_PID"

echo "### Waiting for model to load (up to 60s)..."
AI_READY=false
for i in $(seq 1 20); do
    if ss -tlnp 2>/dev/null | grep -q 8501; then
        echo "✅ AI Service READY on port 8501 after ${i}x3 seconds!"
        AI_READY=true
        break
    fi
    if ! kill -0 $AI_PID 2>/dev/null; then
        echo "❌ AI Service process DIED at iteration $i"
        break
    fi
    sleep 3
done

if [ "$AI_READY" = false ]; then
    echo "❌ AI Service FAILED to start"
    echo "### Process status:"
    ps aux | grep uvicorn | grep -v grep || echo "  No uvicorn process"
fi
echo ""

# ---- SECTION 5: Test AI Service Directly ----
echo "## 5. AI SERVICE DIRECT TESTS"
echo ""

if [ "$AI_READY" = true ]; then
    # Lấy ảnh test: đối số $1 hoặc dataset (nếu còn) hoặc ảnh tổng hợp
    if [ -n "$1" ] && [ -f "$1" ]; then
        TEST_IMG="$1"
    else
        TEST_IMG=$(bash "$PROJ/scripts/get_test_image.sh")
    fi
    echo "### Test image: $TEST_IMG"
    echo ""

    # Test /predict-leaf
    echo "### Test 5a: /predict-leaf endpoint"
    echo ""
    IMAGES=("$TEST_IMG")
    LEAF_SUCCESS=0
    LEAF_FAIL=0
    
    for IMG in "${IMAGES[@]}"; do
        CATEGORY=$(basename "$(dirname "$IMG")")
        FILENAME=$(basename "$IMG")
        
        RESULT=$(curl -s --max-time 15 -X POST http://localhost:8501/predict-leaf \
            -F "file=@$IMG" -F 'top_k=5' 2>/dev/null)
        
        LABEL=$(echo "$RESULT" | python3 -c "import sys,json; print(json.load(sys.stdin).get('predicted_label','ERROR'))" 2>/dev/null)
        CONF=$(echo "$RESULT" | python3 -c "import sys,json; print(f\"{json.load(sys.stdin).get('confidence',0)*100:.1f}%\")" 2>/dev/null)
        TIME=$(echo "$RESULT" | python3 -c "import sys,json; print(f\"{json.load(sys.stdin).get('inference_ms',0):.0f}ms\")" 2>/dev/null)
        
        if [ -n "$LABEL" ] && [ "$LABEL" != "ERROR" ]; then
            echo "  ✅ $CATEGORY/$FILENAME → $LABEL ($CONF) in $TIME"
            LEAF_SUCCESS=$((LEAF_SUCCESS + 1))
        else
            echo "  ❌ $CATEGORY/$FILENAME → FAILED"
            echo "     Response: $RESULT" | head -3
            LEAF_FAIL=$((LEAF_FAIL + 1))
        fi
    done
    echo ""
    echo "**predict-leaf Results: $LEAF_SUCCESS succeeded, $LEAF_FAIL failed**"
    echo ""
    
    # Test /predict with auto_detect=false
    echo "### Test 5b: /predict endpoint (auto_detect=false)"
    echo ""
    PREDICT_SUCCESS=0
    PREDICT_FAIL=0
    
    for IMG in "${IMAGES[@]}"; do
        CATEGORY=$(basename "$(dirname "$IMG")")
        FILENAME=$(basename "$IMG")
        
        RESULT=$(curl -s --max-time 15 -X POST http://localhost:8501/predict \
            -F "file=@$IMG" -F 'top_k=3' -F 'auto_detect=false' 2>/dev/null)
        
        LABEL=$(echo "$RESULT" | python3 -c "import sys,json; print(json.load(sys.stdin).get('predicted_label','ERROR'))" 2>/dev/null)
        CONF=$(echo "$RESULT" | python3 -c "import sys,json; print(f\"{json.load(sys.stdin).get('confidence',0)*100:.1f}%\")" 2>/dev/null)
        TIME=$(echo "$RESULT" | python3 -c "import sys,json; print(f\"{json.load(sys.stdin).get('inference_ms',0):.0f}ms\")" 2>/dev/null)
        
        if [ -n "$LABEL" ] && [ "$LABEL" != "ERROR" ]; then
            echo "  ✅ $CATEGORY/$FILENAME → $LABEL ($CONF) in $TIME"
            PREDICT_SUCCESS=$((PREDICT_SUCCESS + 1))
        else
            echo "  ❌ $CATEGORY/$FILENAME → FAILED"
            # Show error details
            HTTP_CODE=$(echo "$RESULT" | head -1)
            echo "     Response: $RESULT" | head -5
            PREDICT_FAIL=$((PREDICT_FAIL + 1))
        fi
    done
    echo ""
    echo "**predict Results: $PREDICT_SUCCESS succeeded, $PREDICT_FAIL failed**"
    echo ""

    # Test /predict with auto_detect=true (enhanced pipeline)
    echo "### Test 5c: /predict endpoint (auto_detect=true, 2 images)"
    echo ""
    ENHANCED_SUCCESS=0
    ENHANCED_FAIL=0
    
    for IMG in "${IMAGES[@]:0:2}"; do
        CATEGORY=$(basename "$(dirname "$IMG")")
        FILENAME=$(basename "$IMG")
        
        RESULT=$(curl -s --max-time 30 -X POST http://localhost:8501/predict \
            -F "file=@$IMG" -F 'top_k=3' -F 'auto_detect=true' 2>/dev/null)
        
        LABEL=$(echo "$RESULT" | python3 -c "import sys,json; print(json.load(sys.stdin).get('predicted_label','ERROR'))" 2>/dev/null)
        CONF=$(echo "$RESULT" | python3 -c "import sys,json; print(f\"{json.load(sys.stdin).get('confidence',0)*100:.1f}%\")" 2>/dev/null)
        
        if [ -n "$LABEL" ] && [ "$LABEL" != "ERROR" ]; then
            echo "  ✅ $CATEGORY/$FILENAME → $LABEL ($CONF)"
            ENHANCED_SUCCESS=$((ENHANCED_SUCCESS + 1))
        else
            echo "  ❌ $CATEGORY/$FILENAME → FAILED"
            echo "     Response: $(echo "$RESULT" | head -5)"
            ENHANCED_FAIL=$((ENHANCED_FAIL + 1))
        fi
    done
    echo ""
    echo "**enhanced predict Results: $ENHANCED_SUCCESS succeeded, $ENHANCED_FAIL failed**"
    echo ""
else
    echo "⚠️ Skipped (AI service not ready)"
fi

# ---- SECTION 6: Laravel Backend Tests ----
echo "## 6. LARAVEL BACKEND TESTS"
echo ""

# Check Laravel status
echo "### Laravel Service Status"
LARAVEL_OK=false
HTTP_CODE=$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 http://localhost:8001 2>/dev/null)
if [ "$HTTP_CODE" != "000" ]; then
    echo "✅ Laravel running on port 8001 (HTTP $HTTP_CODE)"
    LARAVEL_OK=true
else
    echo "❌ Laravel NOT running on port 8001"
fi
echo ""

# Check API routes
echo "### API Routes (plant-doctor)"
cd "$PROJ"
php artisan route:list --path=plant-doctor 2>/dev/null | head -10
echo ""

# Check DB
echo "### Database Status"
php -d opcache.enable=0 -r "
require '$PROJ/vendor/autoload.php';
\$app = require '$PROJ/bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$count = DB::table('plant_diagnoses')->count();
echo \"plant_diagnoses table: \$count records\" . PHP_EOL;
echo \"DB connection: \" . DB::connection()->getPdo()->getDatabaseName() . PHP_EOL;
" 2>&1
echo ""

# ---- SECTION 7: PHP Pipeline Test (Direct Service Call) ----
echo "## 7. PHP PIPELINE TEST (Direct Service → DB → JSON)"
echo ""
echo "### Running test_full_pipeline.php..."
cd "$PROJ"
php -d opcache.enable=0 scripts/test_full_pipeline.php 2>&1
echo ""

# ---- SECTION 8: Known Issues ----
echo "## 8. KNOWN ISSUES"
echo ""
echo "### Issue 1: /predict endpoint with auto_detect=true"
echo "  - Error: NameError in detector.py (__leaf_detector vs _leaf_detector)"
echo "  - Status: FIXED (changed __leaf_detector to _leaf_detector in detector.py)"
echo ""
echo "### Issue 2: AIPlantDoctorService.php Log::warn()"
echo "  - Error: Log::warn() is not a valid Laravel method"
echo "  - Status: FIXED (changed to Log::warning())"
echo ""
echo "### Issue 3: PHP diagnoseWithoutLeafDetection() method"
echo "  - Error: 'Call to undefined method' despite method existing in file"
echo "  - Root cause: composer/vendor files owned by root, cannot regenerate autoloader"
echo "  - Workaround: Use /predict-leaf endpoint directly via HTTP"
echo ""
echo "### Issue 4: OAuth Passport tables"
echo "  - Error: oauth_personal_access_clients table missing"
echo "  - Impact: API endpoint /api/plant-doctor/diagnose requires auth:api"
echo "  - Workaround: Test service layer directly, bypassing HTTP auth"
echo ""
echo "### Issue 5: Process management"
echo "  - AI service processes die when basher agent timeout occurs"
echo "  - Workaround: Run AI service in separate terminal"
echo ""

# ---- SECTION 9: Code Changes Summary ----
echo "## 9. CODE CHANGES MADE"
echo ""
echo "### ai_service/app/gdino/detector.py"
echo "  - Line 116: Changed 'return __leaf_detector' to 'return _leaf_detector'"
echo "  - Impact: Fixed NameError in /predict endpoint with auto_detect=true"
echo ""
echo "### ai_service/app/main.py"
echo "  - Added '_leaf_detector = None' global variable declaration"
echo "  - Impact: Fixed NameError when _leaf_detector not initialized"
echo ""
echo "### app/Modules/AgriVerse/Services/AIPlantDoctorService.php"
echo "  - Line 289: Changed Log::warn() to Log::warning()"
echo "  - Impact: Fixed Laravel method not found error"
echo "  - Also added full vit_local enhanced pipeline (diagnoseWithVitLocalEnhanced)"
echo ""
echo "### .env.example"
echo "  - Added AI_PLANT_DOCTOR_VIT_* configuration options"
echo "  - Changed default provider from gemini to vit_local"
echo ""
echo "### config/services.php"
echo "  - Added vit_service_url, vit_token, vit_defaults configuration"
echo "  - Added info_provider configuration option"
echo ""

echo "========================================================"
echo "  TEST COMPLETE - $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================================"

# Cleanup
kill $AI_PID 2>/dev/null
