<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    private function isMySQL(): bool
    {
        return DB::getDriverName() === 'mysql';
    }

    public function up(): void
    {
        if (! $this->isMySQL()) {
            Log::info('Skipping MySQL-specific ALTER TABLE MODIFY statements');

            return;
        }

        try {
            DB::statement('ALTER TABLE `journal_articles` MODIFY `hero_image_url` TEXT');
            DB::statement('ALTER TABLE `journal_articles` MODIFY `pdf_url` TEXT');
            DB::statement('ALTER TABLE `specimens` MODIFY `image_url` TEXT');
        } catch (\Exception $e) {
            Log::warning('Migration ALTER TABLE MODIFY failed: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        if (! $this->isMySQL()) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `journal_articles` MODIFY `hero_image_url` VARCHAR(255)');
            DB::statement('ALTER TABLE `journal_articles` MODIFY `pdf_url` VARCHAR(255)');
            DB::statement('ALTER TABLE `specimens` MODIFY `image_url` VARCHAR(255)');
        } catch (\Exception $e) {
            Log::warning('Migration rollback ALTER TABLE MODIFY failed: '.$e->getMessage());
        }
    }
};
