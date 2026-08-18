<?php

namespace Tests;

use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PlantDoctorHybridTest extends TestCase
{
    protected AIPlantDoctorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.ai_plant_doctor.provider', 'vit_local');
        config()->set('services.ai_plant_doctor.info_provider', 'gemini');
        config()->set('services.ai_plant_doctor.gemini_api_key', 'test-gemini-key');
        config()->set('services.ai_plant_doctor.gemini_model', 'gemini-2.0-flash');
        config()->set('services.ai_plant_doctor.vit_service_url', 'http://localhost:8501');
        config()->set('services.ai_plant_doctor.vit_token', '');
        config()->set('services.ai_plant_doctor.vit_auto_detect', true);
        config()->set('services.ai_plant_doctor.vit_defaults.timeout', 30);
        config()->set('services.ai_plant_doctor.vit_defaults.top_k', 5);
        config()->set('services.ai_plant_doctor.vit_defaults.min_confidence', 0.45);

        Cache::flush();

        $this->service = app(AIPlantDoctorService::class);
    }

    public function test_hybrid_success_enriches_with_gemini_and_cross_validation(): void
    {
        $this->fakeVitService();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Đốm vi khuẩn',
                            'agrees' => true,
                            'gemini_confidence' => 0.9,
                            'severity' => 'high',
                            'description' => 'Cây cà chua bị đốm vi khuẩn do Xanthomonas, tạo đốm nâu viền vàng trên lá.',
                            'treatments' => ['Phun thuốc chứa đồng', 'Loại bỏ lá bệnh'],
                            'prevention' => ['Luân canh cây trồng', 'Tránh tưới lên lá'],
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), 'Lá có đốm nâu');

        $this->assertEquals('vit_local_enhanced+gemini', $result['provider']);
        $this->assertEquals('Cà chua', $result['plant_name']);
        $this->assertEquals('Đốm vi khuẩn', $result['disease_name']);
        $this->assertEquals('high', $result['severity']);
        $this->assertStringContainsString('Xanthomonas', $result['description']);
        $this->assertCount(2, $result['treatments']);

        $this->assertArrayHasKey('cross_validation', $result);
        $this->assertTrue($result['cross_validation']['agrees']);
        $this->assertEquals(0.9, $result['cross_validation']['cloud_confidence']);
        $this->assertSame('Bacterial spot (Tomato)', $result['cross_validation']['vi_predict']);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_gemini_failure_falls_back_to_knowledge_base(): void
    {
        $this->fakeVitService([
            'disease-info' => [
                'found_in_kb' => true,
                'disease_name' => 'Đốm vi khuẩn',
                'plant_name' => 'Tomato',
                'description' => 'Mô tả từ knowledge base nội bộ.',
                'treatments' => ['Xử lý bằng đồng', 'Vệ sinh vườn'],
                'prevention' => ['Luân canh', 'Tưới gốc'],
                'severity_factors' => ['Độ ẩm cao'],
            ],
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Internal Server Error', 500),
        ]);

        $result = $this->service->diagnose($this->makeImage(), 'Lá vàng');

        $this->assertEquals('vit_local_enhanced+kb', $result['provider']);
        $this->assertEquals('Mô tả từ knowledge base nội bộ.', $result['description']);
        $this->assertContains('Xử lý bằng đồng', $result['treatments']);
        $this->assertNull($result['cross_validation']);
    }

    public function test_no_gemini_api_key_falls_back_to_knowledge_base(): void
    {
        config()->set('services.ai_plant_doctor.gemini_api_key', '');

        $this->fakeVitService([
            'disease-info' => [
                'found_in_kb' => true,
                'description' => 'Fallback từ knowledge base.',
                'treatments' => ['Biện pháp 1'],
                'prevention' => ['Phòng ngừa 1'],
            ],
        ]);

        $result = $this->service->diagnose($this->makeImage(), null);

        $this->assertEquals('vit_local_enhanced+kb', $result['provider']);
        $this->assertEquals('Fallback từ knowledge base.', $result['description']);

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_gemini_markdown_fenced_json_is_parsed(): void
    {
        $this->fakeVitService();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => "```json\n" . json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Đốm vi khuẩn',
                            'agrees' => true,
                            'description' => 'Mô tả từ Gemini.',
                            'treatments' => ['Phun đồng'],
                            'prevention' => ['Vệ sinh'],
                        ]) . "\n```",
                    ]]],
                ]],
            ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), null);

        $this->assertEquals('vit_local_enhanced+gemini', $result['provider']);
        $this->assertEquals('Mô tả từ Gemini.', $result['description']);
    }

    public function test_gemini_invalid_json_retries_once_then_succeeds(): void
    {
        $this->fakeVitService();

        $valid = json_encode([
            'plant_name' => 'Cà chua',
            'disease_name' => 'Đốm vi khuẩn',
            'agrees' => true,
            'description' => 'Mô tả sau retry.',
            'treatments' => [],
            'prevention' => [],
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::sequence()
                ->push([
                    'candidates' => [[
                        'content' => ['parts' => [['text' => 'xin lỗi tôi không hiểu ảnh']]],
                    ]],
                ], 200)
                ->push([
                    'candidates' => [[
                        'content' => ['parts' => [['text' => $valid]]],
                    ]],
                ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), null);

        $this->assertEquals('Mô tả sau retry.', $result['description']);
        $this->assertEquals('vit_local_enhanced+gemini', $result['provider']);

        $geminiRequests = collect(Http::recorded())
            ->filter(fn ($pair) => str_contains($pair[0]->url(), 'generativelanguage.googleapis.com'))
            ->count();
        $this->assertEquals(2, $geminiRequests, 'Gemini phải được gọi 2 lần (1 fail + 1 retry)');
    }

    public function test_gemini_disagrees_takes_cloud_result(): void
    {
        $this->fakeVitService();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Bệnh thán thư',
                            'agrees' => false,
                            'gemini_confidence' => 0.6,
                            'severity' => 'medium',
                            'description' => 'Tôi thấy dấu hiệu thán thư rõ hơn.',
                            'treatments' => [],
                            'prevention' => [],
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), 'Đốm đen trên quả');

        $this->assertEquals('Bệnh thán thư', $result['disease_name'], 'Ưu tiên kết quả AI cloud khi không đồng ý với ViT');
        $this->assertEquals('medium', $result['severity']);
        $this->assertStringContainsString('[Lưu ý]', $result['description']);
        $this->assertFalse($result['cross_validation']['agrees']);
        $this->assertEquals('Bệnh thán thư', $result['cross_validation']['cloud_disease']);
        $this->assertSame('Bacterial spot (Tomato)', $result['cross_validation']['vi_predict']);
    }

    public function test_enrichment_is_cached_per_label_and_symptoms(): void
    {
        $this->fakeVitService();

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Đốm vi khuẩn',
                            'agrees' => true,
                            'description' => 'Mô tả từ Gemini.',
                            'treatments' => [],
                            'prevention' => [],
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $image = $this->makeImage();

        $this->service->diagnose($image, 'Đốm nâu');
        $this->service->diagnose($this->makeImage(), 'Đốm nâu');

        $geminiRequests = collect(Http::recorded())
            ->filter(fn ($pair) => str_contains($pair[0]->url(), 'generativelanguage.googleapis.com'))
            ->count();
        $this->assertEquals(1, $geminiRequests, 'Gọi thứ 2 cùng label + symptoms phải dùng cache');
    }

    public function test_healthy_plant_still_calls_gemini_and_cloud_verdict_wins(): void
    {
        $this->fakeVitService([
            'predict-leaf' => [
                'predicted_label' => 'healthy (Tomato)',
                'confidence' => 0.95,
                'top' => [
                    ['label' => 'healthy (Tomato)', 'confidence' => 0.95],
                ],
            ],
        ]);

        // Gemini đồng ý cây khỏe mạnh
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Khỏe mạnh',
                            'agrees' => true,
                            'gemini_confidence' => 0.9,
                            'severity' => 'none',
                            'description' => 'Lá xanh tốt, không dấu hiệu bệnh.',
                            'treatments' => [],
                            'prevention' => ['Kiểm tra định kỳ'],
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), null);

        $this->assertEquals('Khỏe mạnh', $result['disease_name']);
        $this->assertEquals('none', $result['severity'], 'Cây khỏe phải có severity none');
        $this->assertEquals('vit_local_enhanced+gemini', $result['provider']);
        $this->assertNotNull($result['cross_validation']);
        $this->assertTrue($result['cross_validation']['agrees']);

        // Gemini vẫn phải được gọi kể cả khi ViT kết luận khỏe mạnh
        Http::assertSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_vit_says_healthy_but_gemini_finds_disease(): void
    {
        $this->fakeVitService([
            'predict-leaf' => [
                'predicted_label' => 'healthy (Tomato)',
                'confidence' => 0.95,
                'top' => [
                    ['label' => 'healthy (Tomato)', 'confidence' => 0.95],
                ],
            ],
        ]);

        // Gemini phát hiện bệnh dù ViT nói khỏe -> kết quả theo cloud
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'plant_name' => 'Cà chua',
                            'disease_name' => 'Bệnh đốm lá',
                            'agrees' => false,
                            'gemini_confidence' => 0.8,
                            'severity' => 'medium',
                            'description' => 'Lá có đốm nâu lan rộng.',
                            'treatments' => ['Phun đồng'],
                            'prevention' => ['Vệ sinh vườn'],
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $result = $this->service->diagnose($this->makeImage(), null);

        $this->assertEquals('Bệnh đốm lá', $result['disease_name'], 'Cloud phát hiện bệnh phải được ưu tiên');
        $this->assertEquals('medium', $result['severity']);
        $this->assertStringContainsString('[Lưu ý]', $result['description']);
        $this->assertFalse($result['cross_validation']['agrees']);
    }

    private function fakeVitService(array $overrides = []): void
    {
        $detect = $overrides['detect-leaves'] ?? [
            'bboxes' => [
                ['xmin' => 10, 'ymin' => 10, 'xmax' => 120, 'ymax' => 120],
                ['xmin' => 130, 'ymin' => 10, 'xmax' => 240, 'ymax' => 120],
            ],
            'leaf_count' => 2,
        ];

        $crop = $overrides['crop'] ?? [
            'status' => 'ok',
            'image_hex' => 'ffd8ffe000104a46494600010100000100010000ffdb004300',
            'cropped_size' => ['width' => 110, 'height' => 110],
        ];

        $predictLeaf = $overrides['predict-leaf'] ?? [
            'predicted_label' => 'Bacterial spot (Tomato)',
            'confidence' => 0.87,
            'top' => [
                ['label' => 'Bacterial spot (Tomato)', 'confidence' => 0.87],
                ['label' => 'Early blight (Tomato)', 'confidence' => 0.12],
            ],
        ];

        $diseaseInfo = $overrides['disease-info'] ?? [
            'found_in_kb' => true,
            'disease_name' => 'Đốm vi khuẩn',
            'plant_name' => 'Tomato',
            'description' => 'Bệnh do vi khuẩn Xanthomonas gây ra.',
            'treatments' => ['Phun thuốc chứa đồng'],
            'prevention' => ['Luân canh cây trồng'],
            'severity_factors' => ['Độ ẩm cao'],
        ];

        Http::fake([
            'http://localhost:8501/detect-leaves' => Http::response($detect),
            'http://localhost:8501/crop' => Http::response($crop),
            'http://localhost:8501/predict-leaf*' => Http::response($predictLeaf),
            'http://localhost:8501/disease-info/*' => Http::response($diseaseInfo),
        ]);
    }

    private function makeImage(): UploadedFile
    {
        $path = storage_path('app/temp/hybrid_test.jpg');
        // 1x1 JPEG hợp lệ tối thiểu — service không decode ảnh (HTTP đều được fake)
        $jpeg = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AVN//2Q==');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $jpeg);

        return new UploadedFile($path, 'test_plant.jpg', 'image/jpeg', null, true);
    }
}
