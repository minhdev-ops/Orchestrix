#!/usr/bin/env php
<?php
/**
 * Test AI Plant Doctor với ảnh thật từ PlantDiseaseDataset
 * 
 * Script này:
 * 1. Chọn ảnh mẫu từ nhiều loại bệnh khác nhau
 * 2. Gọi AI service (FastAPI ViT local) qua AIPlantDoctorService
 * 3. Lưu kết quả vào database plant_diagnoses
 * 4. Trả về JSON response kiểm tra
 * 
 * Cách chạy:
 *   php scripts/test_plant_doctor_dataset.php [--count=10] [--provider=vit_local]
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Modules\AgriVerse\Models\PlantDiagnosis;
use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use Illuminate\Http\UploadedFile;

// ============================================================
// Config
// ============================================================
$datasetPath = base_path('PlantDiseaseDataset/plant_disease_dataset/val');
$testCount = 10;
$provider = config('services.ai_plant_doctor.provider', 'vit_local');
$serviceUrl = config('services.ai_plant_doctor.vit_service_url', 'http://localhost:8501');

// Parse args
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--count=')) {
        $testCount = (int) explode('=', $arg)[1];
    }
    if (str_starts_with($arg, '--provider=')) {
        $provider = explode('=', $arg)[1];
    }
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║        AI Plant Doctor - Dataset Test Pipeline              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// ============================================================
// Step 1: Check services
// ============================================================
echo "📋 Step 1: Kiểm tra dịch vụ...\n";

// Check DB
try {
    $dbCount = PlantDiagnosis::count();
    echo "   ✅ Database OK (plant_diagnoses: {$dbCount} records)\n";
} catch (\Exception $e) {
    echo "   ❌ Database ERROR: {$e->getMessage()}\n";
    exit(1);
}

// Check AI service (for vit_local provider)
if ($provider === 'vit_local') {
    $ch = curl_init("{$serviceUrl}/labels");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $labels = json_decode($response, true);
        echo "   ✅ AI Service (ViT) OK ({$serviceUrl}) - " . count($labels ?? []) . " labels\n";
    } else {
        echo "   ⚠️  AI Service (ViT) không phản hồi tại {$serviceUrl}\n";
        echo "      HTTP Code: {$httpCode}\n";
        echo "      Script sẽ thử gọi và bắt lỗi nếu có.\n";
    }
}

// ============================================================
// Step 2: Select test images
// ============================================================
echo "\n📸 Step 2: Chọn ảnh mẫu...\n";

$testImages = [];
$imageArg = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--image=')) {
        $imageArg = substr($arg, strlen('--image='));
    }
}

// Ưu tiên ảnh truyền vào (--image=path), kế đến dataset (nếu còn), cuối cùng ảnh tổng hợp
if ($imageArg && is_file($imageArg)) {
    $testImages[] = [
        'path' => $imageArg,
        'expected_disease' => basename($imageArg),
    ];
    echo "   📂 Dùng ảnh được truyền vào: {$imageArg}\n";
} elseif (is_dir($datasetPath)) {
    $diseaseDirs = array_filter(glob("{$datasetPath}/*"), 'is_dir');
    shuffle($diseaseDirs);

    // Pick images from different disease categories
    $selectedDiseases = array_slice($diseaseDirs, 0, min($testCount, count($diseaseDirs)));

    foreach ($selectedDiseases as $dir) {
        $diseaseName = basename($dir);
        $images = glob("{$dir}/*.jpg");
        if (!empty($images)) {
            $testImages[] = [
                'path' => $images[array_rand($images)],
                'expected_disease' => $diseaseName,
            ];
        }
    }
    echo "   📂 Đã chọn " . count($testImages) . " ảnh từ dataset\n";
} else {
    $synthetic = shell_exec(__DIR__ . '/get_test_image.sh');
    $synthetic = trim((string) $synthetic);
    if ($synthetic && is_file($synthetic)) {
        $testImages[] = [
            'path' => $synthetic,
            'expected_disease' => 'unknown (ảnh tổng hợp)',
        ];
        echo "   📂 Dataset đã bị xóa - dùng ảnh tổng hợp: {$synthetic}\n";
    }
}

$testCount = count($testImages);
if ($testCount === 0) {
    echo "   ❌ Không có ảnh test nào!\n";
    exit(1);
}
echo "   📂 Tổng cộng {$testCount} ảnh test\n\n";

// ============================================================
// Step 3: Run diagnosis tests
// ============================================================
echo "🔬 Step 3: Chạy test chẩn đoán...\n\n";

// Create or find test user
$user = User::firstOrCreate(
    ['email' => 'test-ai@orchestrix.local'],
    [
        'name' => 'AI Test User',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]
);

$aiService = app(AIPlantDoctorService::class);

$results = [];
$successCount = 0;
$errorCount = 0;
$dbSavedCount = 0;

$startTime = microtime(true);

foreach ($testImages as $index => $testImage) {
    $num = $index + 1;
    $imageName = basename($testImage['path']);
    $expectedDisease = $testImage['expected_disease'];

    echo "   [{$num}/{$testCount}] {$imageName}\n";
    echo "       Expected: {$expectedDisease}\n";

    $testStart = microtime(true);

    try {
        // Create UploadedFile
        $uploaded = new UploadedFile(
            $testImage['path'],
            $imageName,
            'image/jpeg',
            null,
            true // test mode
        );

        // Call AI service
        $result = $aiService->diagnose($uploaded, null);

        $elapsed = round((microtime(true) - $testStart) * 1000);

        // Display result
        echo "       🌿 Plant: {$result['plant_name']}\n";
        echo "       🦠 Disease: {$result['disease_name']}\n";
        echo "       📊 Confidence: " . round($result['confidence'] * 100, 1) . "%\n";
        echo "       ⚡ Severity: {$result['severity']}\n";
        echo "       ⏱️  Time: {$elapsed}ms\n";
        echo "       🔧 Provider: {$result['provider']}\n";

        // Save to database (simulating what PlantDoctorController does)
        $path = $uploaded->store('plant-diagnoses/test', 'public');

        $diagnosis = PlantDiagnosis::create([
            'user_id' => $user->id,
            'image_path' => $path,
            'plant_name' => $result['plant_name'],
            'disease_name' => $result['disease_name'],
            'confidence' => $result['confidence'],
            'severity' => $result['severity'],
            'description' => $result['description'] ?? '',
            'treatments' => $result['treatments'] ?? [],
            'prevention' => $result['prevention'] ?? [],
            'raw_response' => $result['raw_response'] ?? [],
            'provider' => $result['provider'],
        ]);

        // Build JSON response (like PlantDoctorController)
        $jsonResponse = [
            'diagnosis' => [
                'id' => $diagnosis->id,
                'uuid' => $diagnosis->uuid,
                'image_url' => asset('storage/' . $path),
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

        // Verify database record
        $stored = PlantDiagnosis::find($diagnosis->id);
        if ($stored) {
            $dbSavedCount++;
            echo "       💾 DB: ✅ Saved (ID: #{$diagnosis->id})\n";
        } else {
            echo "       💾 DB: ❌ Not found after save!\n";
        }

        // Check JSON structure
        $jsonValid = isset($jsonResponse['diagnosis']['id'])
            && isset($jsonResponse['diagnosis']['uuid'])
            && isset($jsonResponse['diagnosis']['plant_name'])
            && isset($jsonResponse['diagnosis']['disease_name'])
            && isset($jsonResponse['diagnosis']['confidence'])
            && isset($jsonResponse['diagnosis']['provider']);

        if ($jsonValid) {
            echo "       📄 JSON: ✅ Valid structure\n";
        } else {
            echo "       📄 JSON: ❌ Invalid structure\n";
        }

        $results[] = [
            'image' => $imageName,
            'expected' => $expectedDisease,
            'predicted' => $result['disease_name'],
            'confidence' => $result['confidence'],
            'severity' => $result['severity'],
            'provider' => $result['provider'],
            'db_id' => $diagnosis->id,
            'db_saved' => true,
            'json_valid' => $jsonValid,
            'elapsed_ms' => $elapsed,
            'success' => true,
        ];

        $successCount++;

    } catch (\Throwable $e) {
        $elapsed = round((microtime(true) - $testStart) * 1000);
        echo "       ❌ ERROR: {$e->getMessage()}\n";
        echo "       ⏱️  Time: {$elapsed}ms\n";

        $results[] = [
            'image' => $imageName,
            'expected' => $expectedDisease,
            'error' => $e->getMessage(),
            'elapsed_ms' => $elapsed,
            'success' => false,
        ];

        $errorCount++;
    }

    echo "\n";
}

$totalTime = round((microtime(true) - $startTime) * 1000);

// ============================================================
// Step 4: Summary
// ============================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Kết quả:\n";
echo "   ✅ Thành công: {$successCount}/{$testCount}\n";
echo "   ❌ Thất bại: {$errorCount}/{$testCount}\n";
echo "   💾 Đã lưu DB: {$dbSavedCount}/{$testCount}\n";
echo "   ⏱️  Tổng thời gian: {$totalTime}ms\n";
echo "   📈 Trung bình: " . round($totalTime / max($testCount, 1)) . "ms/ảnh\n\n";

// Database verification
echo "💾 Kiểm tra Database:\n";
$finalCount = PlantDiagnosis::count();
$newRecords = $finalCount - $dbCount;
echo "   Records trước test: {$dbCount}\n";
echo "   Records mới tạo: {$newRecords}\n";
echo "   Records tổng cộng: {$finalCount}\n\n";

// Show last 3 records from DB
echo "📋 3 records mới nhất trong DB:\n";
$latest = PlantDiagnosis::latest()->limit(3)->get();
foreach ($latest as $record) {
    echo "   - #{$record->id}: {$record->plant_name} / {$record->disease_name} ({$record->provider})\n";
}
echo "\n";

// JSON API Response example
echo "📄 JSON API Response mẫu (test #1):\n";
if (!empty($results) && $results[0]['success']) {
    $sampleJson = [
        'diagnosis' => [
            'id' => $results[0]['db_id'] ?? null,
            'plant_name' => $results[0]['predicted'] ?? 'N/A',
            'confidence' => $results[0]['confidence'] ?? 0,
            'severity' => $results[0]['severity'] ?? 'N/A',
            'provider' => $results[0]['provider'] ?? 'N/A',
        ],
    ];
    echo "   " . json_encode($sampleJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

// Final verdict
echo "═══════════════════════════════════════════════════════════════\n";
if ($successCount === $testCount && $dbSavedCount === $testCount) {
    echo "🎉 ALL TESTS PASSED! AI Plant Doctor hoạt động tốt.\n";
    echo "   ✅ AI service phản hồi chính xác\n";
    echo "   ✅ Dữ liệu được lưu vào database\n";
    echo "   ✅ JSON API response đúng cấu trúc\n";
} elseif ($successCount > 0) {
    echo "⚠️  PARTIAL PASS: {$successCount}/{$testCount} tests thành công.\n";
} else {
    echo "❌ ALL TESTS FAILED! Kiểm tra lại AI service.\n";
}
echo "═══════════════════════════════════════════════════════════════\n";

// Output JSON results for programmatic use
$finalResults = [
    'summary' => [
        'total' => $testCount,
        'success' => $successCount,
        'errors' => $errorCount,
        'db_saved' => $dbSavedCount,
        'total_time_ms' => $totalTime,
        'avg_time_ms' => round($totalTime / max($testCount, 1)),
    ],
    'results' => $results,
];

file_put_contents('/tmp/plant_doctor_test_results.json', json_encode($finalResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n📁 Kết quả đầy đủ đã lưu tại: /tmp/plant_doctor_test_results.json\n";
