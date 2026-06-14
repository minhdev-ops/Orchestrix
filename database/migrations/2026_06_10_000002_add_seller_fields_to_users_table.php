<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'seller_type')) {
                $table->string('seller_type', 30)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'seller_verified_at')) {
                $table->timestamp('seller_verified_at')->nullable()->after('seller_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seller_type', 'seller_verified_at']);
        });
    }
};
