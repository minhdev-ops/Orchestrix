<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng settings - key-value store cho cấu hình Portfolio.
     * Hỗ trợ phân nhóm (group) và các loại giá trị (type).
     */
    public function up(): void
    {
        Schema::create('portfolio_settings', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');    // general, home, seo, social, etc.
            $table->enum('type', ['string', 'boolean', 'integer', 'json', 'text'])
                  ->default('string');

            // Metadata for admin UI
            $table->string('label')->nullable();            // Human readable label
            $table->string('description')->nullable();      // Hint for admin
            $table->boolean('is_public')->default(false);   // Exposed to public API

            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_settings');
    }
};
