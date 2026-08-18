<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'offer_id')) {
                $table->string('offer_id')->nullable()->after('uuid');
                $table->index('offer_id');
            }
            if (! Schema::hasColumn('orders', 'confirm_deadline')) {
                $table->timestamp('confirm_deadline')->nullable()->after('estimated_delivery');
            }
            if (! Schema::hasColumn('orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('confirm_deadline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['offer_id', 'confirm_deadline', 'confirmed_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};