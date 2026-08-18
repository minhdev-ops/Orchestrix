<?php

namespace App\Modules\AgriVerse\Console;

use App\Models\User;
use App\Modules\AgriVerse\Models\PlantDiagnosis;
use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

class TestPlantDoctorPipeline extends Command
{
    protected $name = 'plant-doctor:test-pipeline';
    protected $description = 'Test the complete AI Plant Doctor pipeline with image upload and database storage';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(AIPlantDoctorService $aiService): int
    {
        $testUser = $this->resolveTestUser();
        if (!$testUser) {
            $this->error('Could not find or create test user.');
            return self::FAILURE;
        }

        $this->info('========================================');
        $this->info('TESTING: Complete Plant Doctor Pipeline');
        $this->info('========================================');

        // Use dummy.jpg from public directory
        $imagePath = base_path('public/dummy.jpg');
        if (!file_exists($imagePath)) {
            $this->error("Test image not found: {$imagePath}");
            return self::FAILURE;
        }

        // Step 1: Validate image file
        $this->info('Step 1: Validating image file...');
        $uploaded = new UploadedFile(
            $imagePath,
            basename($imagePath),
            'image/jpeg',
            null,
            true
        );

        $this->info("Image: " . basename($imagePath));
        $this->info("Size: " . round(filesize($imagePath) / 1024, 2) . ' KB');
        $this->info('✓ Image validated');

        // Step 2: Call AI service diagnosis
        $this->info('Step 2: Calling AI Plant Doctor service...');
        try {
            $startTime = microtime(true);
            $result = $aiService->diagnose($uploaded, 'Lây xanh trên lá, có đốm nâu');
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->info("Diagnosis completed in {$executionTime}ms");
            $this->info("Provider: " . $result['provider']);
            $this->info("Plant: " . $result['plant_name']);
            $this->info("Disease: " . $result['disease_name']);
            $this->info("Confidence: " . round($result['confidence'] * 100, 2) . '%');
            $this->info("Severity: " . $result['severity']);
            $this->info('✓ AI Diagnosis completed');
        } catch (\Exception $e) {
            $this->error("Diagnosis failed: " . $e->getMessage());
            $this->line('');
            return self::FAILURE;
        }

        // Step 3: Store image in filesystem
        $this->info('Step 3: Storing image in filesystem...');
        try {
            $path = $uploaded->store('plant-diagnoses', 'public');
            $fullPath = storage_path('app/public/' . $path);
            copy($imagePath, $fullPath);

            $this->info("Storage path: $path");
            $this->info('✓ Image stored');
        } catch (\Exception $e) {
            $this->error("Failed to store image: " . $e->getMessage());
            return self::FAILURE;
        }

        // Step 4: Save to database
        $this->info('Step 4: Saving diagnosis to database...');
        try {
            $diagnosis = PlantDiagnosis::create([
                'user_id' => $testUser->id,
                'image_path' => $path,
                'plant_name' => $result['plant_name'],
                'disease_name' => $result['disease_name'],
                'confidence' => $result['confidence'],
                'severity' => $result['severity'],
                'description' => $result['description'] ?? 'No description provided',
                'treatments' => $result['treatments'] ?? [],
                'prevention' => $result['prevention'] ?? [],
                'raw_response' => $result['raw_response'] ?? [],
                'provider' => $result['provider'],
            ]);

            $this->info("Diagnosis ID: #{$diagnosis->id}");
            $this->info('✓ Saved to database');
        } catch (\Exception $e) {
            $this->error("Failed to save to database: " . $e->getMessage());
            return self::FAILURE;
        }

        // Step 5: Verify retrieval
        $this->info('Step 5: Verifying data retrieval from database...');
        try {
            $retrieved = PlantDiagnosis::find($diagnosis->id);

            if ($retrieved && $retrieved->id === $diagnosis->id) {
                $this->info("Retrieved diagnosis ID: #{$retrieved->id}");
                $this->info('✓ Data integrity verified');
            } else {
                $this->error('Failed to retrieve diagnosis from database');
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Verification failed: " . $e->getMessage());
            return self::FAILURE;
        }

        // Summary
        $this->line('');
        $this->info('========================================');
        $this->info('PIPELINE TEST SUMMARY');
        $this->info('========================================');
        $this->info("Test Image: " . basename($imagePath));
        $this->info("Total Execution Time: {$executionTime}ms");
        $this->info("AI Provider: " . $result['provider']);
        $this->info("Diagnosis Result: " . $result['disease_name']);
        $this->info("Database Record ID: #" . $diagnosis->id);
        $this->info('========================================');
        $this->info('✅ ALL STEPS COMPLETED SUCCESSFULLY!');
        $this->info('========================================');

        return self::SUCCESS;
    }

    private function resolveTestUser(): ?User
    {
        return User::where('email', 'test@example.com')->first() ?: User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
