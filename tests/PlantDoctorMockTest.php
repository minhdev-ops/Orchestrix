<?php

namespace Tests;

use App\Models\User;
use App\Modules\AgriVerse\Models\PlantDiagnosis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PlantDoctorMockTest extends TestCase
{
    use RefreshDatabase;

    public function test_mocked_ai_diagnosis_saves_to_database_and_returns_api_response()
    {
        // ===============================
        // STEP 1: Setup test user
        // ===============================
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        Passport::actingAs($user);

        // ===============================
        // STEP 2: Create a test image
        // ===============================
        $imagePath = storage_path('app/temp/test_plant.jpg');
        $this->createTestImage($imagePath, 800, 600);

        // ===============================
        // STEP 3: Mock the AI service
        // ===============================
        $mockedResult = [
            'plant_name' => 'Tomato',
            'disease_name' => 'Bacterial spot (Tomato)',
            'confidence' => 0.873014,
            'severity' => 'high',
            'description' => 'Cây cà chua bị đốm vi khuẩn trên lá. Bệnh thường xuất hiện khi độ ẩm cao, thoáng khí kém. Cần xử lý ngay để tránh lây lan.',
            'treatments' => [
                'Cắt bỏ các lá bị bệnh và tiêu hủy',
                'Phun thuốc bảo vệ thực vật có chứa đồng (Copper hydroxide) 2-3 lần, cách nhau 7-10 ngày',
                'Tăng cường thông gió, giảm độ ẩm cho cây',
                'Bón phân cân đối, tránh bón quá nhiều đạm'
            ],
            'prevention' => [
                'Chọn giống kháng bệnh',
                'Xoay vụ trồng hợp lý',
                'Vệ sinh công cụ làm vườn',
                'Theo dõi cây thường xuyên để phát hiện sớm'
            ],
            'raw_response' => [
                'vit' => [
                    'aggregated' => [
                        'predicted_label' => 'Bacterial spot (Tomato)',
                        'confidence' => 0.873014,
                        'leaf_count' => 3,
                        'dominant_vote' => 'Bacterial spot (Tomato) (3/3 leaves)',
                        'top_k_aggregated' => [
                            ['label' => 'Bacterial spot (Tomato)', 'avg_confidence' => 0.873014, 'votes' => 3],
                            ['label' => 'Early blight (Tomato)', 'avg_confidence' => 0.123456, 'votes' => 0]
                        ]
                    ],
                    'leaf_predictions' => [
                        ['leaf_id' => 0, 'predicted_label' => 'Bacterial spot (Tomato)', 'confidence' => 0.89],
                        ['leaf_id' => 1, 'predicted_label' => 'Bacterial spot (Tomato)', 'confidence' => 0.87],
                        ['leaf_id' => 2, 'predicted_label' => 'Bacterial spot (Tomato)', 'confidence' => 0.85]
                    ],
                    'bboxes_count' => 3
                ],
                'symptoms' => 'Lây xanh trên lá, có đốm nâu'
            ],
            'provider' => 'vit_local_enhanced'
        ];

        $this->mock(\App\Modules\AgriVerse\Services\AIPlantDoctorService::class, function ($mock) use ($mockedResult) {
            $mock->shouldReceive('diagnose')
                ->once()
                ->andReturn($mockedResult);
        });

        // ===============================
        // STEP 4: Call the API endpoint
        // ===============================
        $response = $this->postJson('/api/plant-doctor/diagnose', [
            'image' => new \Illuminate\Http\UploadedFile($imagePath, 'test.jpg', 'image/jpeg', null, true),
            'symptoms' => 'Lây xanh trên lá, có đốm nâu',
        ]);

        // ===============================
        // STEP 5: Assertions
        // ===============================
        $response->assertSuccessful();
        $data = $response->json();

        // 5a. Check API response structure
        $this->assertArrayHasKey('diagnosis', $data);
        $diagnosis = $data['diagnosis'];

        $this->assertArrayHasKey('id', $diagnosis);
        $this->assertArrayHasKey('uuid', $diagnosis);
        $this->assertArrayHasKey('image_url', $diagnosis);
        $this->assertArrayHasKey('plant_name', $diagnosis);
        $this->assertArrayHasKey('disease_name', $diagnosis);
        $this->assertArrayHasKey('confidence', $diagnosis);
        $this->assertArrayHasKey('severity', $diagnosis);
        $this->assertArrayHasKey('description', $diagnosis);
        $this->assertArrayHasKey('treatments', $diagnosis);
        $this->assertArrayHasKey('prevention', $diagnosis);
        $this->assertArrayHasKey('provider', $diagnosis);
        $this->assertArrayHasKey('created_at', $diagnosis);

        // 5b. Verify response values match mock
        $this->assertEquals('Tomato', $diagnosis['plant_name']);
        $this->assertEquals('Bacterial spot (Tomato)', $diagnosis['disease_name']);
        $this->assertEquals(0.873014, $diagnosis['confidence']);
        $this->assertEquals('high', $diagnosis['severity']);
        $this->assertEquals('vit_local_enhanced', $diagnosis['provider']);

        // 5c. Verify database record was created
        $stored = PlantDiagnosis::where('uuid', $diagnosis['uuid'])->first();
        $this->assertNotNull($stored, 'Database record should exist');
        $this->assertEquals($user->id, $stored->user_id);
        $this->assertNotEmpty($stored->image_path);
        $this->assertEquals('Tomato', $stored->plant_name);
        $this->assertEquals('Bacterial spot (Tomato)', $stored->disease_name);
        $this->assertEqualsWithDelta(0.873014, $stored->confidence, 0.000001);
        $this->assertEquals('high', $stored->severity);
        $this->assertStringContainsString('đốm vi khuẩn', $stored->description);
        $this->assertIsArray($stored->treatments);
        $this->assertCount(4, $stored->treatments);
        $this->assertIsArray($stored->prevention);
        $this->assertCount(4, $stored->prevention);
        $this->assertIsArray($stored->raw_response);
        $this->assertEquals('vit_local_enhanced', $stored->provider);

        // 5d. Verify raw_response contains leaf detection info
        $this->assertArrayHasKey('vit', $stored->raw_response);
        $this->assertArrayHasKey('aggregated', $stored->raw_response['vit']);
        $this->assertEquals(3, $stored->raw_response['vit']['aggregated']['leaf_count']);

        // Cleanup
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    public function test_healthy_plant_returns_correct_response()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'healthy@example.com',
            'password' => bcrypt('password'),
        ]);
        Passport::actingAs($user);

        $imagePath = storage_path('app/temp/healthy_plant.jpg');
        $this->createTestImage($imagePath, 400, 300);

        $mockedResult = [
            'plant_name' => 'Cà chua',
            'disease_name' => 'Khỏe mạnh',
            'confidence' => 0.95,
            'severity' => 'none',
            'description' => 'Cây có vẻ khỏe mạnh, không phát hiện dấu hiệu bệnh trên các lá đã phân tích.',
            'treatments' => [],
            'prevention' => [
                'Duy trì tưới nước, ánh sáng và dinh dưỡng hợp lý.',
                'Kiểm tra định kỳ để phát hiện sớm dấu hiệu bất thường.',
            ],
            'raw_response' => [
                'vit' => [
                    'aggregated' => [
                        'predicted_label' => 'healthy (Tomato)',
                        'confidence' => 0.95,
                        'leaf_count' => 2,
                        'dominant_vote' => 'healthy (Tomato) (2/2 leaves)',
                        'top_k_aggregated' => [
                            ['label' => 'healthy (Tomato)', 'avg_confidence' => 0.95, 'votes' => 2]
                        ]
                    ],
                    'leaf_predictions' => [
                        ['leaf_id' => 0, 'predicted_label' => 'healthy (Tomato)', 'confidence' => 0.96],
                        ['leaf_id' => 1, 'predicted_label' => 'healthy (Tomato)', 'confidence' => 0.94]
                    ],
                    'bboxes_count' => 2
                ],
                'symptoms' => null
            ],
            'provider' => 'vit_local_enhanced'
        ];

        $this->mock(\App\Modules\AgriVerse\Services\AIPlantDoctorService::class, function ($mock) use ($mockedResult) {
            $mock->shouldReceive('diagnose')
                ->once()
                ->andReturn($mockedResult);
        });

        $response = $this->postJson('/api/plant-doctor/diagnose', [
            'image' => new \Illuminate\Http\UploadedFile($imagePath, 'test.jpg', 'image/jpeg', null, true),
            'symptoms' => null,
        ]);

        $response->assertSuccessful();
        $data = $response->json();

        $diagnosis = $data['diagnosis'];
        $this->assertEquals('Cà chua', $diagnosis['plant_name']);
        $this->assertEquals('Khỏe mạnh', $diagnosis['disease_name']);
        $this->assertEquals('none', $diagnosis['severity']);
        $this->assertEmpty($diagnosis['treatments']);
        $this->assertNotEmpty($diagnosis['prevention']);

        $stored = PlantDiagnosis::where('uuid', $diagnosis['uuid'])->first();
        $this->assertEquals('Khỏe mạnh', $stored->disease_name);
        $this->assertEquals('none', $stored->severity);
        $this->assertEmpty($stored->treatments);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private function createTestImage(string $path, int $width, int $height): void
    {
        // 1x1 JPEG hợp lệ tối thiểu — test này mock toàn bộ AI service nên không cần ảnh thật
        $jpeg = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AVN//2Q==');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $jpeg);
    }
}