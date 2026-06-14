<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sustainability_reports', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('hero_image_url')->nullable();
            $table->string('quote')->nullable();
            $table->string('quote_author')->nullable();
            $table->text('ethical_description')->nullable();
            $table->text('circular_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sustainability_reports', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'hero_image_url', 'quote', 'quote_author', 'ethical_description', 'circular_description']);
        });
    }
};
