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
        foreach (['carts', 'wishlists', 'notifications'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'is_delete')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->boolean('is_delete')->default(false)->after('updated_at');
                    $t->index('is_delete');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['carts', 'wishlists', 'notifications'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'is_delete')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('is_delete');
                });
            }
        }
    }
};
