<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. REFUNDS TABLE
        if (!Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->string('reason');
                $table->text('description')->nullable();
                $table->string('images')->nullable();
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['order_id', 'status']);
                $table->index('user_id');
            });
        }

        // 2. MISSING COLUMNS ON ORDERS
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete()->after('store_id');
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 15, 2)->default(0)->after('shipping_method');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('shipping_fee');
            }
            if (!Schema::hasColumn('orders', 'tracking_url')) {
                $table->string('tracking_url')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'estimated_delivery')) {
                $table->date('estimated_delivery')->nullable()->after('tracking_url');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('estimated_delivery');
            }
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
            }
        });

        // 3. BANK INFO ON STORES (for seller payout)
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('stores', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('stores', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_account_name');
            }
            if (!Schema::hasColumn('stores', 'phone')) {
                $table->string('phone')->nullable()->after('description');
            }
            if (!Schema::hasColumn('stores', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });

        // 4. USER ADDRESSES
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('label')->default('Nhà');
                $table->string('recipient_name');
                $table->string('phone');
                $table->string('province');
                $table->string('district');
                $table->string('ward');
                $table->text('address_detail');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['user_id', 'is_default']);
            });
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_name', 'bank_account_number', 'phone', 'address']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_id', 'discount_amount',
                'shipping_method', 'shipping_fee', 'tracking_number', 'tracking_url',
                'estimated_delivery', 'delivered_at', 'cancel_reason', 'cancelled_at',
            ]);
        });
        Schema::dropIfExists('refunds');
    }
};
