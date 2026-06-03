<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng about_experiences - kinh nghiệm làm việc và học vấn.
     * type: 'work' | 'education' | 'certification'
     */
    public function up(): void
    {
        Schema::create('portfolio_about_experiences', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['work', 'education', 'certification'])->default('work');
            $table->string('title');                        // "Senior DevOps Engineer"
            $table->string('organization');                 // "Google", "HCMUT"
            $table->string('location')->nullable();
            $table->text('description')->nullable();

            // Duration
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);

            // Extras
            $table->string('badge_url')->nullable();        // Certification badge image
            $table->string('certificate_url')->nullable();  // Certificate link

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_about_experiences');
    }
};
