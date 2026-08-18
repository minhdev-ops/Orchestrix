<?php
/**
 * Test Full Pipeline: AI Predict (via HTTP) → DB Save → JSON Response
 * 
 * Bypasses the PHP service class method issue by calling AI service directly,
 * then saving to DB using the Eloquent model.
 */

ini_set('opcache.enable', '0');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Modules\AgriVerse\Models\PlantDiagnosis;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "  FULL PIPELINE TEST: AI → DB → JSON\n";
echo "  (Direct HTTP to AI service)\n";
echo "========================================\n\n";

$provider = config('services.ai_plant_doctor.provider');
$vitUrl = rtrim(config('services.ai_plant_doctor.vit_service_url', 'http://localhost:8501'), '/');
echo "Provider: {$provider}\n";
echo "AI Service URL: {$vitUrl}\n\n";

// Verify AI service is alive
$healthCheck = Http::timeout(3)->get("{$vitUrl}/docs");
if (!$healthCheck->successful()) {
    // Try predict-leaf as health check
    echo "⚠️  AI service may not be running on {$vitUrl}\n";
    echo "Attempting to continue anyway...\n\n";
}

// Create test user
$user = User::firstOrCreate(
    ['email' => 'test-pipeline@agriverse.com'],
    ['name' => 'Pipeline Test User', 'password' => bcrypt('test123')]
);
echo "Test User ID: {$user->id}\n";

// Count records before test
$countBefore = PlantDiagnosis::where('user_id', $user->id)->count();
echo "DB records before test: {$countBefore}\n\n";

// Lấy ảnh test: ưu tiên --image=path, kế đến env TEST_IMAGE, rồi ảnh trong dataset (nếu còn),
// cuối cùng dùng ảnh tổng hợp tạo bởi scripts/get_test_image.sh
$datasetPath = __DIR__ . '/../PlantDiseaseDataset/plant_disease_dataset/val';
$allImages = is_dir($datasetPath) ? glob("$datasetPath/*/*.jpg") : [];
$testImages = [];

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--image=')) {
        $candidate = substr($arg, strlen('--image='));
        if (is_file($candidate)) {
            $testImages[] = $candidate;
        }
    }
}

if (empty($testImages)) {
    $envImage = getenv('TEST_IMAGE');
    if ($envImage && is_file($envImage)) {
        $testImages[] = $envImage;
    }
}

if (empty($testImages) && $allImages) {
    $testImages = array_slice($allImages, 0, 5);
}

if (empty($testImages)) {
    $synthetic = shell_exec(__DIR__ . '/get_test_image.sh');
    $synthetic = trim((string) $synthetic);
    if ($synthetic && is_file($synthetic)) {
        $testImages[] = $synthetic;
    }
}

echo "Found " . count($testImages) . " test images (dataset: " . count($allImages) . ")\n\n";

$results = [];
$jsonResponse = null;

foreach ($testImages as $index => $imagePath) {
    $filename = basename($imagePath);
    $category = basename(dirname($imagePath));
    
    echo str_repeat('-', 55) . "\n";
    echo "TEST " . ($index + 1) . ": {$category}\n";
    echo "File: {$filename}\n";
    
    if (!file_exists($imagePath)) {
        echo "  ❌ File not found! Skipping.\n\n";
        continue;
    }
    
    $imageSize = filesize($imagePath);
    echo "  Size: " . round($imageSize / 1024, 1) . " KB\n";
    
    try {
        // ===== STEP 1: Call AI Service via HTTP =====
        $start = microtime(true);
        
        $response = Http::timeout(30)->attach(
            'file',
            file_get_contents($imagePath),
            $filename,
            ['Content-Type' => 'image/jpeg']
        )->post("{$vitUrl}/predict", [
            'top_k' => '5',
            'auto_detect' => 'false',
        ]);
        
        $elapsed = round((microtime(true) - $start) * 1000);
        
        if (!$response->successful()) {
            throw new \RuntimeException("AI service returned HTTP {$response->status()}: {$response->body()}");
        }
        
        $aiResult = $response->json();
        $predictedLabel = $aiResult['predicted_label'] ?? 'Unknown';
        $confidence = (float) ($aiResult['confidence'] ?? 0);
        
        echo "  ✅ AI Predict: {$predictedLabel}\n";
        echo "     Confidence: " . round($confidence * 100, 1) . "%\n";
        echo "     Inference: {$elapsed}ms\n";
        
        // Parse plant/disease from label like "Bacterial spot (Tomato)"
        $plantName = 'Không xác định';
        $diseaseName = $predictedLabel;
        if (preg_match('/^(.*?)\s*\((.+?)\)\s*$/', $predictedLabel, $m)) {
            $diseaseName = trim($m[1]);
            $plantName = str_replace([',', '  '], ['', ' '], trim($m[2]));
        }
        
        $isHealthy = stripos($predictedLabel, 'healthy') !== false;
        $severity = $confidence >= 0.85 ? 'high' : ($confidence >= 0.65 ? 'medium' : ($confidence >= 0.45 ? 'low' : 'unknown'));
        
        // Description
        if ($isHealthy) {
            $description = 'Cây có vẻ khỏe mạnh, không phát hiện dấu hiệu bệnh.';
            $treatments = [];
            $prevention = ['Duy trì tưới nước, ánh sáng và dinh dưỡng hợp lý.'];
        } else {
            $description = "Mô hình phát hiện dấu hiệu của '{$predictedLabel}' trên ảnh. Confidence: " . round($confidence * 100, 1) . "%. ";
            $description .= 'Khuyến nghị tham khảo chuyên gia nông nghiệp.';
            $treatments = [
                'Cách ly cây bị bệnh.',
                'Loại bỏ lá/cành bệnh nặng.',
                'Tham khảo chuyên gia để chọn thuốc đặc trị.',
            ];
            $prevention = [
                'Theo dõi cây thường xuyên.',
                'Đảm bảo thông gió và ánh sáng hợp lý.',
            ];
        }
        
        // ===== STEP 2: Save to DB =====
        $dbStart = microtime(true);
        
        $diagnosis = PlantDiagnosis::create([
            'user_id' => $user->id,
            'image_path' => "plant-diagnoses/{$filename}",
            'plant_name' => $plantName,
            'disease_name' => $isHealthy ? 'Khỏe mạnh' : $diseaseName,
            'confidence' => $confidence,
            'severity' => $severity,
            'description' => $description,
            'treatments' => $treatments,
            'prevention' => $prevention,
            'raw_response' => $aiResult,
            'provider' => 'vit_local',
        ]);
        
        $dbTime = round((microtime(true) - $dbStart) * 1000);
        
        echo "  ✅ DB Saved: ID={$diagnosis->id}, UUID={$diagnosis->uuid}\n";
        echo "     DB Write: {$dbTime}ms\n";
        
        // ===== STEP 3: Build JSON Response (like controller) =====
        $jsonResponse = [
            'diagnosis' => [
                'id' => $diagnosis->id,
                'uuid' => $diagnosis->uuid,
                'image_url' => asset('storage/' . $diagnosis->image_path),
                'plant_name' => $diagnosis->plant_name,
                'disease_name' => $diagnosis->disease_name,
                'confidence' => $diagnosis->confidence,
                'severity' => $diagnosis->severity,
                'description' => $diagnosis->description,
                'treatments' => $diagnosis->treatments,
                'prevention' => $diagnosis->prevention,
                'provider' => $diagnosis->provider,
                'created_at' => $diagnosis->created_at->toIso8601String(),
            ],
        ];
        
        echo "  ✅ JSON Response built\n";
        echo "     Description: " . mb_substr($description, 0, 70) . "...\n";
        
        $results[] = [
            'image' => $category,
            'disease' => $isHealthy ? 'Khỏe mạnh' : $diseaseName,
            'plant' => $plantName,
            'confidence' => round($confidence * 100, 1),
            'db_id' => $diagnosis->id,
            'time_ms' => $elapsed,
            'success' => true,
        ];
        
    } catch (\Throwable $e) {
        echo "  ❌ ERROR: {$e->getMessage()}\n";
        $results[] = [
            'image' => $category,
            'error' => $e->getMessage(),
            'success' => false,
        ];
    }
    
    echo "\n";
}

// ===== SUMMARY =====
echo "========================================\n";
echo "  SUMMARY\n";
echo "========================================\n";

$successCount = count(array_filter($results, fn($r) => $r['success'] ?? false));
$totalCount = count($testImages);

echo "Results: {$successCount}/{$totalCount} passed\n\n";

echo str_pad('Category', 30) . str_pad('Disease', 25) . str_pad('Conf', 8) . str_pad('DB ID', 8) . "Time\n";
echo str_repeat('-', 90) . "\n";

foreach ($results as $r) {
    if ($r['success'] ?? false) {
        echo str_pad($r['image'], 30) 
           . str_pad($r['disease'], 25) 
           . str_pad($r['confidence'] . '%', 8) 
           . str_pad($r['db_id'], 8) 
           . $r['time_ms'] . "ms\n";
    } else {
        echo str_pad($r['image'], 30) . "❌ {$r['error']}\n";
    }
}

// DB verification
$countAfter = PlantDiagnosis::where('user_id', $user->id)->count();
echo "\n📊 DB Verification:\n";
echo "   Records before: {$countBefore}\n";
echo "   Records after: {$countAfter}\n";
echo "   New records: " . ($countAfter - $countBefore) . "\n";

// Verify last record
$lastRecord = PlantDiagnosis::where('user_id', $user->id)->latest()->first();
if ($lastRecord) {
    echo "\n   Last record verification:\n";
    echo "   - ID: {$lastRecord->id}\n";
    echo "   - UUID: {$lastRecord->uuid}\n";
    echo "   - Plant: {$lastRecord->plant_name}\n";
    echo "   - Disease: {$lastRecord->disease_name}\n";
    echo "   - Confidence: {$lastRecord->confidence}\n";
    echo "   - Provider: {$lastRecord->provider}\n";
    echo "   - Created: {$lastRecord->created_at}\n";
}

if ($jsonResponse) {
    echo "\n========================================\n";
    echo "  SAMPLE JSON RESPONSE\n";
    echo "========================================\n";
    echo json_encode($jsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n✅ Pipeline test complete!\n";
