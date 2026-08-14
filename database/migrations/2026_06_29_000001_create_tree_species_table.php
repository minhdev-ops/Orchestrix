<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tree_species', function (Blueprint $table) {
            $table->id();
            $table->string('species_vn');
            $table->string('species_latin')->nullable();
            $table->string('family')->nullable();
            $table->string('category_type')->nullable();
            $table->string('type')->nullable();
            $table->string('origin')->nullable();
            $table->integer('max_height')->nullable()->comment('Chiều cao tối đa (m)');
            $table->text('light_requirements')->nullable();
            $table->integer('min_temp')->nullable()->comment('Nhiệt độ tối thiểu (°C)');
            $table->text('watering_needs')->nullable();
            $table->string('soil_ph')->nullable();
            $table->string('repotting_freq')->nullable();
            $table->text('propagation_methods')->nullable();
            $table->text('common_pests')->nullable();
            $table->string('flower_color')->nullable();
            $table->string('bloom_season')->nullable();
            $table->text('fertilizing_guide')->nullable();
            $table->text('pruning_wiring')->nullable();
            $table->string('indoor_outdoor')->nullable();
            $table->text('meaning_fengshui')->nullable();
            $table->json('image_urls')->nullable();
            $table->text('description')->nullable();
            $table->text('key_features')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tree_species');
    }
};
