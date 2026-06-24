<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('gateway_transaction_id')->nullable()->after('transaction_id');
            $table->json('gateway_response')->nullable()->after('payment_proof');
            $table->text('failure_reason')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway_transaction_id', 'gateway_response', 'failure_reason']);
        });
    }
};
