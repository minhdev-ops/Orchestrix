<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng about_stats - các số liệu thống kê hiển thị trên trang About.
     * Ví dụ: "5+ Years Experience", "20+ Projects", "10+ Clients".
     */
    public function up(): void
    {
        Schema::create('portfolio_about_stats', function (Blueprint $table) {
            $table->id();

            $table->string('label');        // "Years Experience"
            $table->string('value');        // "5+"
            $table->string('icon')->nullable();    // Material icon class
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_about_stats');
    }
};
