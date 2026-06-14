<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghn_provinces', function (Blueprint $table) {
            $table->id();
            $table->integer('province_id')->unique();
            $table->string('province_name', 100);
            $table->string('code', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('ghn_districts', function (Blueprint $table) {
            $table->id();
            $table->integer('district_id')->unique();
            $table->string('district_name', 100);
            $table->integer('province_id');
            $table->string('code', 20)->nullable();
            $table->timestamps();

            $table->index('province_id');
        });

        Schema::create('ghn_wards', function (Blueprint $table) {
            $table->id();
            $table->string('ward_code', 20)->unique();
            $table->string('ward_name', 100);
            $table->integer('district_id');
            $table->timestamps();

            $table->index('district_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghn_wards');
        Schema::dropIfExists('ghn_districts');
        Schema::dropIfExists('ghn_provinces');
    }
};
