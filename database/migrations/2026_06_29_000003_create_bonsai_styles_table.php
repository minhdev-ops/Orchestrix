<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonsai_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name_vn');
            $table->string('name_jp')->nullable();
            $table->text('description')->nullable();
            $table->string('section')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonsai_styles');
    }
};
