<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->integer('province_id')->nullable()->after('phone');
            $table->integer('district_id')->nullable()->after('province_id');
            $table->string('ward_code', 20)->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'district_id', 'ward_code']);
        });
    }
};
