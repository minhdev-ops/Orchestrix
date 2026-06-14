<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `journal_articles` MODIFY `hero_image_url` TEXT');
        DB::statement('ALTER TABLE `journal_articles` MODIFY `pdf_url` TEXT');
        DB::statement('ALTER TABLE `specimens` MODIFY `image_url` TEXT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `journal_articles` MODIFY `hero_image_url` VARCHAR(255)');
        DB::statement('ALTER TABLE `journal_articles` MODIFY `pdf_url` VARCHAR(255)');
        DB::statement('ALTER TABLE `specimens` MODIFY `image_url` VARCHAR(255)');
    }
};
