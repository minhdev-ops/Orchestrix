<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Attributes
        if (!Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->default('select'); // select, color, size
                $table->json('options')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Product Variants
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('sku')->unique();
                $table->string('name');
                $table->decimal('price', 15, 2);
                $table->decimal('compare_price', 15, 2)->nullable();
                $table->integer('stock')->default(0);
                $table->decimal('weight', 8, 2)->nullable();
                $table->string('image')->nullable();
                $table->json('attributes')->nullable(); // {"Trọng lượng": "1kg", "Cấp hạng": "A"}
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index('product_id');
                $table->index('sku');
            });
        }

        // Product Attribute Pivot
        if (!Schema::hasTable('product_attribute_value')) {
            Schema::create('product_attribute_value', function (Blueprint $table) {
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
                $table->text('value')->nullable();
                $table->primary(['product_id', 'product_attribute_id']);
            });
        }

        // Affiliates
        if (!Schema::hasTable('affiliates')) {
            Schema::create('affiliates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('affiliate_code')->unique();
                $table->decimal('commission_rate', 5, 2)->default(5.00);
                $table->string('status')->default('active'); // active, suspended, banned
                $table->decimal('total_earnings', 15, 2)->default(0);
                $table->integer('total_referrals')->default(0);
                $table->string('payout_method')->nullable();
                $table->json('payout_info')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index('affiliate_code');
            });
        }

        // Referrals
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('affiliate_code');
                $table->string('status')->default('pending'); // pending, completed, expired
                $table->foreignId('first_order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->decimal('commission_earned', 15, 2)->default(0);
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
                $table->index(['affiliate_id', 'status']);
            });
        }

        // Commissions
        if (!Schema::hasTable('commissions')) {
            Schema::create('commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('order_amount', 15, 2);
                $table->decimal('commission_rate', 5, 2);
                $table->decimal('commission_amount', 15, 2);
                $table->string('status')->default('pending'); // pending, approved, paid, rejected
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['affiliate_id', 'status']);
            });
        }

        // Analytics Events
        if (!Schema::hasTable('analytics_events')) {
            Schema::create('analytics_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('session_id')->nullable();
                $table->string('event_type'); // page_view, product_view, add_to_cart, purchase, search
                $table->string('event_name')->nullable();
                $table->json('properties')->nullable();
                $table->string('page_url')->nullable();
                $table->string('referrer_url')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_type')->nullable(); // desktop, mobile, tablet
                $table->string('browser')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->timestamps();
                $table->index(['event_type', 'created_at']);
                $table->index(['user_id', 'created_at']);
                $table->index('session_id');
            });
        }

        // Recently Viewed
        if (!Schema::hasTable('recently_viewed')) {
            Schema::create('recently_viewed', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('session_id')->nullable();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('view_count')->default(1);
                $table->timestamp('viewed_at');
                $table->timestamps();
                $table->unique(['user_id', 'product_id'], 'recently_viewed_user_product');
                $table->unique(['session_id', 'product_id'], 'recently_viewed_session_product');
                $table->index('viewed_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recently_viewed');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('product_attribute_value');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attributes');
    }
};
