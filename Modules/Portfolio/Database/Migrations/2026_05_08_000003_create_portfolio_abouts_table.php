<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng about cho Portfolio module.
     * Singleton pattern - chỉ có 1 record: thông tin giới thiệu bản thân.
     */
    public function up(): void
    {
        Schema::create('portfolio_abouts', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('full_name')->nullable();
            $table->string('title')->nullable();            // "Software Engineer"
            $table->string('subtitle')->nullable();         // tagline
            $table->text('bio')->nullable();                // Short bio
            $table->longText('description')->nullable();    // Rich description

            // Contact info
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();

            // Social links
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('website_url')->nullable();

            // Assets
            $table->string('avatar')->nullable();           // Storage path
            $table->string('resume_url')->nullable();       // PDF URL

            // Availability
            $table->boolean('is_available')->default(true);
            $table->string('availability_status')->nullable(); // "Open to work"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_abouts');
    }
};
