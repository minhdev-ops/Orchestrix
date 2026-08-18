<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_guides', function (Blueprint $table) {
            $table->id();
            $table->string('guide_topic');
            $table->string('guide_url')->nullable();
            $table->string('vietnamese_title')->nullable();
            $table->text('key_content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_guides');
    }
};
