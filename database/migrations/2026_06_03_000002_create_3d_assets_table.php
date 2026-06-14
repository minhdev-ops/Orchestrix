<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('3d_assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('original_path');
            $table->string('compressed_filename')->nullable();
            $table->string('compressed_path')->nullable();
            $table->string('format')->default('glb');
            $table->string('asset_type')->default('360_View');
            $table->string('compression_status')->default('pending');
            $table->json('compression_settings')->nullable();
            $table->bigInteger('file_size')->unsigned()->default(0);
            $table->bigInteger('compressed_file_size')->unsigned()->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'compression_status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('3d_assets');
    }
};
