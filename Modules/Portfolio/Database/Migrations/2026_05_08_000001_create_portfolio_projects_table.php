<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng projects cho Portfolio module.
     * Lưu trữ thông tin dự án: title, slug, description, content_md,
     * image, link, tech_stack (JSON), category, sort_order,
     * is_featured, is_visible, view_count.
     */
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Web Development');

            // Content
            $table->text('description')->nullable();
            $table->longText('content_md')->nullable();     // Markdown content

            // Media
            $table->string('image')->nullable();            // Storage path

            // Links
            $table->string('link')->nullable();             // Live demo URL
            $table->string('github_url')->nullable();       // GitHub repository

            // Tech stack stored as JSON array
            $table->json('tech_stack')->nullable();

            // Display control
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);

            // Date range (optional)
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['is_visible', 'is_featured']);
            $table->index('category');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_projects');
    }
};
