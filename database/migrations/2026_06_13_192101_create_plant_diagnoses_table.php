<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plant_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('plant_name')->nullable();
            $table->string('disease_name');
            $table->float('confidence')->default(0);
            $table->enum('severity', ['none', 'low', 'medium', 'high', 'critical', 'unknown'])->default('unknown');
            $table->text('description')->nullable();
            $table->json('treatments')->nullable();
            $table->json('prevention')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('provider')->default('gemini');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_diagnoses');
    }
};
