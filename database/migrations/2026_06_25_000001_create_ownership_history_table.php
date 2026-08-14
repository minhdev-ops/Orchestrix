<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action')->default('transfer'); // transfer, purchase, gift, auction
            $table->string('tx_hash', 100)->nullable()->unique();
            $table->decimal('transfer_price', 15, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('transfer_date');
            $table->timestamps();

            $table->index('product_id');
            $table->index('to_user_id');
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_history');
    }
};
