<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng testimonials - lời chứng thực từ khách hàng/đồng nghiệp.
     */
    public function up(): void
    {
        Schema::create('portfolio_testimonials', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('position')->nullable();         // "CTO at Acme Corp"
            $table->string('company')->nullable();
            $table->string('avatar')->nullable();

            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5 stars
            $table->string('linkedin_url')->nullable();

            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_testimonials');
    }
};
