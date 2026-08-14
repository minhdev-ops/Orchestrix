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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete()->after('product_id');
            }
            if (! Schema::hasColumn('orders', 'quantity')) {
                $table->integer('quantity')->default(1)->after('store_id');
            }
            if (! Schema::hasColumn('orders', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0)->after('quantity');
            }
            if (! Schema::hasColumn('orders', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->after('unit_price');
            }
            if (! Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('commission_fee');
            }
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('shipping_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['store_id', 'quantity', 'unit_price', 'total_price', 'shipping_address', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
