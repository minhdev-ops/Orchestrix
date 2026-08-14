<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ownership_history')) {
            Schema::table('ownership_history', function (Blueprint $table) {
                if (! Schema::hasColumn('ownership_history', 'transaction_id')) {
                    $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
                }
                if (! Schema::hasColumn('ownership_history', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('ownership_history', function (Blueprint $table) {
            if (Schema::hasColumn('ownership_history', 'transaction_id')) {
                $table->dropForeign(['transaction_id']);
                $table->dropColumn('transaction_id');
            }
            if (Schema::hasColumn('ownership_history', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
