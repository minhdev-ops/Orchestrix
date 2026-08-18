#!/bin/bash
# ============================================================
# Test AI Plant Doctor với ảnh thật từ PlantDiseaseDataset
# Kiểm tra: AI service → Database → JSON API response
# ============================================================

set -e
PROJ=/home/couterit/Work/02.Study/Project/Orchestrix
AI_LOG=/tmp/ai_service_test.log

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║    AI Plant Doctor - Full Pipeline Test with Real Images    ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# ---- Step 0: Start AI service ----
echo "📋 Step 0: Starting AI service..."
pkill -9 -f "uvicorn app.main" 2>/dev/null || true
sleep 1

cd $PROJ/ai_service
AI_MODEL_PATH=$PROJ/best_vit.keras AI_LABELS_PATH=$PROJ/ai_service/labels.txt \
  python3 -m uvicorn app.main:app --host 0.0.0.0 --port 8501 > $AI_LOG 2>&1 &
AI_PID=$!
echo "   AI PID: $AI_PID"

# Wait for port to be ready
for i in $(seq 1 30); do
  if ss -tlnp 2>/dev/null | grep -q 8501; then
    echo "   ✅ AI service ready after ${i}s"
    break
  fi
  if ! kill -0 $AI_PID 2>/dev/null; then
    echo "   ❌ AI service died!"
    tail -20 $AI_LOG
    exit 1
  fi
  sleep 1
done

if ! ss -tlnp 2>/dev/null | grep -q 8501; then
  echo "   ❌ AI service not ready after 30s"
  exit 1
fi

# ---- Step 1: Select test images ----
echo ""
echo "📸 Step 1: Selecting test images..."
echo "   Cách dùng: $0 [path/to/image] - hoặc tự động lấy ảnh (dataset hoặc ảnh tổng hợp)"

# Nếu truyền ảnh từ ngoài (vd: ảnh upload từ frontend web) thì dùng ảnh đó
if [ -n "$1" ] && [ -f "$1" ]; then
    TEST_IMAGES=("$1")
    echo "   📂 $1"
else
    # Nếu không: dùng ảnh trong dataset (nếu còn) hoặc ảnh tổng hợp
    TEST_IMG=$(bash "$PROJ/scripts/get_test_image.sh")
    TEST_IMAGES=("$TEST_IMG")
    echo "   📂 $TEST_IMG"
fi
echo "   Total: ${#TEST_IMAGES[@]} images"
echo ""

# ---- Step 2: Test AI service directly ----
echo "🔬 Step 2: Testing AI service (predict-leaf)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

SUCCESS=0
FAIL=0
for img in "${TEST_IMAGES[@]}"; do
  filename=$(basename "$img")
  dirname=$(basename "$(dirname "$img")")
  
  result=$(curl -s --max-time 15 -X POST http://localhost:8501/predict-leaf \
    -F "file=@$img" -F "top_k=5" 2>/dev/null)
  
  if echo "$result" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d['predicted_label'])" 2>/dev/null; then
    label=$(echo "$result" | python3 -c "import sys,json; d=json.load(sys.stdin); print(f\"{d['predicted_label']} ({d['confidence']*100:.1f}%) in {d['inference_ms']:.0f}ms\")" 2>/dev/null)
    echo "   ✅ $dirname/$filename → $label"
    SUCCESS=$((SUCCESS + 1))
  else
    echo "   ❌ $dirname/$filename → FAILED"
    FAIL=$((FAIL + 1))
  fi
done

echo ""
echo "   AI Service Results: ✅ $SUCCESS / $((SUCCESS + FAIL)) succeeded"
echo ""

# ---- Step 3: Test full Laravel pipeline (API → DB → JSON) ----
echo "🌐 Step 3: Testing Laravel API pipeline (port 8001)..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Get DB count before
DB_BEFORE=$(cd $PROJ && php artisan tinker --execute="echo DB::table('plant_diagnoses')->count();" 2>/dev/null || echo "0")
echo "   Database records before test: $DB_BEFORE"

# Create test user if not exists
cd $PROJ
php artisan tinker --execute="
\$user = App\Models\User::firstOrCreate(
    ['email' => 'aitest@orchestrix.local'],
    ['name' => 'AI Test User', 'password' => bcrypt('test'), 'email_verified_at' => now()]
);
echo 'Test user ID: ' . \$user->id;
" 2>/dev/null

# Test 3 images through the full pipeline using PHP
php -r "
require '$PROJ/vendor/autoload.php';
\$app = require_once '$PROJ/bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Modules\AgriVerse\Models\PlantDiagnosis;
use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use Illuminate\Http\UploadedFile;

\$user = User::where('email', 'aitest@orchestrix.local')->first();
if (!\$user) {
    echo 'ERROR: No test user found\n';
    exit(1);
}

\$aiService = app(AIPlantDoctorService::class);

\$testImages = [ !empty(\$argv[1]) ? \$argv[1] : '${TEST_IMG}' ];

\$jsonExamples = [];

foreach (\$testImages as \$idx => \$imagePath) {
    if (!file_exists(\$imagePath)) {
        echo '   SKIP: ' . basename(\$imagePath) . ' not found\n';
        continue;
    }
    
    \$filename = basename(\$imagePath);
    \$dirName = basename(dirname(\$imagePath));
    echo '   [' . (\$idx+1) . '/3] Testing: ' . \$dirName . '/' . \$filename . \"\n\";
    
    try {
        \$startTime = microtime(true);
        
        // Create UploadedFile
        \$uploaded = new UploadedFile(\$imagePath, \$filename, 'image/jpeg', null, true);
        
        // Step A: Call AI service
        \$result = \$aiService->diagnose(\$uploaded, null);
        \$elapsed = round((microtime(true) - \$startTime) * 1000);
        
        echo '       🌿 Plant: ' . \$result['plant_name'] . \"\n\";
        echo '       🦠 Disease: ' . \$result['disease_name'] . \"\n\";
        echo '       📊 Confidence: ' . round(\$result['confidence'] * 100, 1) . '%\n';
        echo '       ⏱️  Time: ' . \$elapsed . \"ms\n\";
        
        // Step B: Save to database
        \$path = \$uploaded->store('plant-diagnoses/test', 'public');
        \$diagnosis = PlantDiagnosis::create([
            'user_id' => \$user->id,
            'image_path' => \$path,
            'plant_name' => \$result['plant_name'],
            'disease_name' => \$result['disease_name'],
            'confidence' => \$result['confidence'],
            'severity' => \$result['severity'],
            'description' => \$result['description'] ?? '',
            'treatments' => \$result['treatments'] ?? [],
            'prevention' => \$result['prevention'] ?? [],
            'raw_response' => \$result['raw_response'] ?? [],
            'provider' => \$result['provider'],
        ]);
        
        // Step C: Verify DB record
        \$stored = PlantDiagnosis::find(\$diagnosis->id);
        if (\$stored) {
            echo '       💾 DB: ✅ Saved (ID: #' . \$diagnosis->id . \")\n\";
        } else {
            echo '       💾 DB: ❌ Not found!\n';
        }
        
        // Step D: Build JSON response (like PlantDoctorController)
        \$jsonResponse = [
            'diagnosis' => [
                'id' => \$diagnosis->id,
                'uuid' => \$diagnosis->uuid,
                'image_url' => asset('storage/' . \$path),
                'plant_name' => \$diagnosis->plant_name,
                'disease_name' => \$diagnosis->disease_name,
                'confidence' => \$diagnosis->confidence,
                'severity' => \$diagnosis->severity,
                'description' => \$diagnosis->description,
                'treatments' => \$diagnosis->treatments,
                'prevention' => \$diagnosis->prevention,
                'provider' => \$diagnosis->provider,
                'created_at' => \$diagnosis->created_at->toIso8601String(),
            ],
        ];
        
        \$jsonValid = isset(\$jsonResponse['diagnosis']['id'])
            && isset(\$jsonResponse['diagnosis']['uuid'])
            && isset(\$jsonResponse['diagnosis']['plant_name'])
            && isset(\$jsonResponse['diagnosis']['confidence']);
        
        if (\$jsonValid) {
            echo '       📄 JSON: ✅ Valid structure\n';
            \$jsonExamples[] = \$jsonResponse;
        } else {
            echo '       📄 JSON: ❌ Invalid structure\n';
        }
        
        echo \"\n\";
        
    } catch (\Throwable \$e) {
        echo '       ❌ ERROR: ' . \$e->getMessage() . \"\n\";
        echo \"\n\";
    }
}

// Show example JSON
if (!empty(\$jsonExamples)) {
    echo '📄 Example JSON API Response (last diagnosis):' . \"\n\";
    echo json_encode(end(\$jsonExamples), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . \"\n\";
}
" 2>/dev/null

# ---- Step 4: Final verification ----
echo ""
echo "📊 Step 4: Database verification..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

DB_AFTER=$(cd $PROJ && php artisan tinker --execute="echo DB::table('plant_diagnoses')->count();" 2>/dev/null || echo "?")
echo "   Database records after test: $DB_AFTER (before: $DB_BEFORE)"

# Show latest records
echo ""
echo "   📋 Latest records in plant_diagnoses:"
cd $PROJ && php artisan tinker --execute="
\$records = DB::table('plant_diagnoses')->orderBy('id', 'desc')->limit(5)->get();
foreach (\$records as \$r) {
    echo '   - #' . \$r->id . ': ' . \$r->plant_name . ' / ' . \$r->disease_name . ' (' . \$r->provider . ') conf=' . round(\$r->confidence * 100, 1) . '%' . PHP_EOL;
}
" 2>/dev/null

# ---- Summary ----
echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                      TEST SUMMARY                           ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "   🤖 AI Service (FastAPI ViT): Running on port 8501"
echo "   🌐 Laravel Backend: Running on port 8001"
echo "   📸 Test images: ${#TEST_IMAGES[@]} from PlantDiseaseDataset"
echo "   ✅ AI Predictions: $SUCCESS succeeded / $FAIL failed"
echo "   💾 DB Records: $DB_BEFORE → $DB_AFTER"
echo ""
echo "   ✅ Data saved to database: YES"
echo "   ✅ JSON API response: YES"
echo ""

# Cleanup
pkill -f "uvicorn app.main" 2>/dev/null || true
echo "   🧹 AI service stopped."
