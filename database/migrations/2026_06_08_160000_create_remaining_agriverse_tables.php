<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MANUFACTURERS
        if (!Schema::hasTable('manufacturers')) {
            Schema::create('manufacturers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique()->nullable();
                $table->string('origin_country')->nullable();
                $table->text('description')->nullable();
                $table->string('logo')->nullable();
                $table->string('website')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. PRODUCT TYPES (wine type: red, white, sparkling, etc.)
        if (!Schema::hasTable('product_types')) {
            Schema::create('product_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique()->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. TAGS
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. PIVOT: product_tag
        if (!Schema::hasTable('product_tag')) {
            Schema::create('product_tag', function (Blueprint $table) {
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
                $table->primary(['product_id', 'tag_id']);
            });
        }

        // 5. PRODUCT IMAGES (replaces web.sql p_img)
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->string('alt_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->index(['product_id', 'is_primary']);
                $table->index(['product_id', 'sort_order']);
            });
        }

        // 6. PIVOT: coupon_product (replaces web.sql dis_process)
        if (!Schema::hasTable('coupon_product')) {
            Schema::create('coupon_product', function (Blueprint $table) {
                $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->primary(['coupon_id', 'product_id']);
            });
        }

        // 7. USER VOUCHERS (user-claimed coupons)
        if (!Schema::hasTable('user_vouchers')) {
            Schema::create('user_vouchers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_used')->default(false);
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'coupon_id']);
                $table->index(['user_id', 'is_used']);
            });
        }

        // 8. BANNERS
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('image_url');
                $table->string('link_url')->nullable();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('is_active');
            });
        }

        // 9. FEEDBACK
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('status')->default('pending');
                $table->timestamps();
                $table->softDeletes();
                $table->index('status');
            });
        }

        // 12. ADD COLUMNS TO PRODUCTS
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'manufacturer_id')) {
                $table->foreignId('manufacturer_id')->nullable()->constrained()->nullOnDelete()->after('category');
            }
            if (!Schema::hasColumn('products', 'product_type_id')) {
                $table->foreignId('product_type_id')->nullable()->constrained()->nullOnDelete()->after('manufacturer_id');
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manufacturer_id', 'product_type_id', 'is_featured', 'image']);
        });
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('user_vouchers');
        Schema::dropIfExists('coupon_product');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('manufacturers');
    }
};
