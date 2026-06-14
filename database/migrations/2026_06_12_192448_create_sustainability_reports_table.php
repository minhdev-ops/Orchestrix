<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sustainability_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->integer('carbon_offset_target')->default(0);
            $table->integer('carbon_offset_actual')->default(0);
            $table->string('carbon_trend')->nullable(); // e.g. "+12% YoY"
            $table->integer('reforestation_total')->default(0);
            $table->string('reforestation_status')->nullable();
            $table->decimal('packaging_sustainable_percent', 5, 1)->default(0);
            $table->integer('circularity_percent')->default(0);
            $table->string('supply_regions_tracked')->nullable(); // e.g. "24"
            $table->string('audit_rating')->nullable(); // e.g. "A+ (SGS)"
            $table->decimal('ev_delivery_percent', 5, 1)->nullable();
            $table->decimal('water_recovered_gallons', 12, 1)->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sustainability_reports');
    }
};
