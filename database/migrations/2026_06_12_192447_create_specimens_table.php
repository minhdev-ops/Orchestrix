<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specimens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('location')->nullable();
            $table->string('image_url')->nullable();
            $table->string('status')->default('hydrated');
            $table->integer('hydration_value')->default(50);
            $table->string('hydration_label')->nullable();
            $table->boolean('hydration_error')->default(false);
            $table->integer('nutrient_value')->default(50);
            $table->string('nutrient_label')->nullable();
            $table->boolean('nutrient_error')->default(false);
            $table->boolean('nutrient_muted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specimens');
    }
};
