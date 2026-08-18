<?php

namespace Tests;

use App\Models\User;
use App\Modules\AgriVerse\Models\PlantDiagnosis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PlantDoctorPipelineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Integration test: gọi endpoint thật, yêu cầu ViT service (port 8501)
     * và Gemini API key đang chạy. Bỏ qua nếu service không khả dụng.
     */
    public function test_complete_pipeline_saves_diagnosis_to_database()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        Passport::actingAs($user);

        $imagePath = storage_path('app/temp/test_image.jpg');
        if (!file_exists($imagePath)) {
            $this->createTestImage($imagePath);
        }

        $response = $this->postJson('/api/plant-doctor/diagnose', [
            'image' => new \Illuminate\Http\UploadedFile($imagePath, 'test.jpg', 'image/jpeg', null, true),
            'symptoms' => 'Lây xanh trên lá, có đốm nâu',
        ]);

        if ($response->status() === 500) {
            $this->markTestSkipped('ViT service hoặc Gemini không khả dụng: ' . ($response->json('error') ?? ''));
        }

        $response->assertSuccessful();
        $data = $response->json();
        $this->assertArrayHasKey('diagnosis', $data);
        $diagnosis = $data['diagnosis'];

        $this->assertNotEmpty($diagnosis['id']);
        $this->assertNotEmpty($diagnosis['uuid']);
        $this->assertNotEmpty($diagnosis['image_url']);
        $this->assertNotEmpty($diagnosis['plant_name']);
        $this->assertNotEmpty($diagnosis['disease_name']);
        $this->assertGreaterThanOrEqual(0, $diagnosis['confidence']);
        $this->assertLessThanOrEqual(1, $diagnosis['confidence']);
        $this->assertNotEmpty($diagnosis['severity']);
        $this->assertNotEmpty($diagnosis['description']);
        $this->assertIsArray($diagnosis['treatments']);
        $this->assertIsArray($diagnosis['prevention']);
        $this->assertNotEmpty($diagnosis['provider']);

        $stored = PlantDiagnosis::where('uuid', $diagnosis['uuid'])->first();
        $this->assertNotNull($stored);
        $this->assertEquals($user->id, $stored->user_id);
        $this->assertNotEmpty($stored->image_path);
        $this->assertNotEmpty($stored->plant_name);
        $this->assertNotEmpty($stored->disease_name);
        $this->assertNotNull($stored->confidence);
        $this->assertNotNull($stored->severity);
        $this->assertNotNull($stored->description);
        $this->assertIsArray($stored->treatments);
        $this->assertIsArray($stored->prevention);
        $this->assertIsArray($stored->raw_response);
        $this->assertNotNull($stored->provider);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private function createTestImage(string $path): void
    {
        $jpeg = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AVN//2Q==');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $jpeg);
    }
}
