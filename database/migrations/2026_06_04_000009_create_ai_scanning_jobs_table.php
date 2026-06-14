<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_scanning_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('3d_assets')->nullOnDelete();
            $table->string('source_video_url')->nullable();
            $table->string('status')->default('pending');
            $table->json('result')->nullable();
            $table->foreignId('result_asset_id')->nullable()->constrained('3d_assets')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_scanning_jobs');
    }
};
