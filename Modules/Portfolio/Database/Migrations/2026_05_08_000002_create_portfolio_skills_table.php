<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng skills cho Portfolio module.
     * Lưu trữ kỹ năng kỹ thuật: name, slug, category, level (0-100),
     * icon class, description, content_md, is_visible, is_featured.
     */
    public function up(): void
    {
        Schema::create('portfolio_skills', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');                     // Backend, Frontend, DevOps, etc.

            // Content
            $table->string('description')->nullable();
            $table->longText('content_md')->nullable();

            // Display
            $table->string('icon')->nullable();             // Material icon / FontAwesome class
            $table->string('icon_url')->nullable();         // Or an image URL
            $table->unsignedTinyInteger('level')->default(80); // 0-100

            // Visibility
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_visible', 'category']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_skills');
    }
};
