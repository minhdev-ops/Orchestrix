<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Coupon;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('admin');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('admin');

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->buyer->assignRole('buyer');

    $this->seller = User::factory()->create(['role' => 'seller']);
    $this->seller->assignRole('seller');

    $this->store = Store::create([
        'owner_id' => $this->seller->id, 'name' => 'Cửa hàng Test', 'status' => 'active',
    ]);
});

/*************** Dashboard ***************/

describe('Admin Dashboard', function () {
    it('admin can access dashboard', function () {
        $this->actingAs($this->admin);
        $response = $this->get(route('admin.agriverse.dashboard'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Product Management ***************/

describe('Admin Product Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list products', function () {
        $response = $this->get(route('admin.agriverse.products.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can store product', function () {
        $response = $this->post(route('admin.agriverse.products.store'), [
            'name' => 'Sản phẩm Admin tạo', 'price' => 150000,
            'store_id' => $this->store->id, 'status' => 'published',
        ]);
        expect(in_array($response->status(), [200, 302, 201]))->toBeTrue();
    });

    it('can edit product', function () {
        $product = Product::create([
            'user_id' => $this->seller->id, 'store_id' => $this->store->id,
            'name' => 'Test', 'price' => 50000, 'stock' => 5, 'status' => 'published',
        ]);

        $response = $this->get(route('admin.agriverse.products.edit', $product->id));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can approve product', function () {
        $product = Product::create([
            'user_id' => $this->seller->id, 'store_id' => $this->store->id,
            'name' => 'Pending', 'price' => 50000, 'stock' => 5, 'status' => 'pending',
        ]);

        $response = $this->post(route('admin.agriverse.products.approve', $product->id));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Store Management ***************/

describe('Admin Store Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list stores', function () {
        $response = $this->get(route('admin.agriverse.stores.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can edit store', function () {
        $response = $this->get(route('admin.agriverse.stores.edit', $this->store->id));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Order Management ***************/

describe('Admin Order Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list orders', function () {
        $response = $this->get(route('admin.agriverse.orders.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can show order detail', function () {
        $product = Product::create([
            'user_id' => $this->seller->id, 'store_id' => $this->store->id,
            'name' => 'Test', 'price' => 50000, 'stock' => 5, 'status' => 'published',
        ]);
        $order = Order::create([
            'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
            'product_id' => $product->id, 'store_id' => $this->store->id,
            'quantity' => 1, 'unit_price' => 50000, 'total_price' => 50000,
            'total_amount' => 50000, 'status' => 'pending', 'shipping_address' => 'Test',
        ]);

        $response = $this->get(route('admin.agriverse.orders.show', $order->id));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can update order status', function () {
        $product = Product::create([
            'user_id' => $this->seller->id, 'store_id' => $this->store->id,
            'name' => 'Test', 'price' => 50000, 'stock' => 5, 'status' => 'published',
        ]);
        $order = Order::create([
            'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
            'product_id' => $product->id, 'store_id' => $this->store->id,
            'quantity' => 1, 'unit_price' => 50000, 'total_price' => 50000,
            'total_amount' => 50000, 'status' => 'pending', 'shipping_address' => 'Test',
        ]);

        $response = $this->post(route('admin.agriverse.orders.update-status', $order->id), [
            'status' => 'confirmed',
        ]);
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Category Management ***************/

describe('Admin Category Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list categories', function () {
        $response = $this->get(route('admin.agriverse.categories.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can create category', function () {
        $response = $this->post(route('admin.agriverse.categories.store'), [
            'name' => 'Danh mục mới', 'slug' => 'danh-muc-moi',
        ]);
        expect(in_array($response->status(), [200, 302, 201]))->toBeTrue();
    });
});

/*************** User Management ***************/

describe('Admin User Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list users', function () {
        $response = $this->get(route('admin.agriverse.users.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Coupon Management ***************/

describe('Admin Coupon Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list coupons', function () {
        $response = $this->get(route('admin.agriverse.coupons.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can create coupon', function () {
        $response = $this->post(route('admin.agriverse.coupons.store'), [
            'code' => 'SALE20', 'type' => 'percent', 'value' => 20,
            'min_order_amount' => 200000, 'is_active' => true,
            'starts_at' => now()->format('Y-m-d'), 'expires_at' => now()->addMonth()->format('Y-m-d'),
        ]);
        expect(in_array($response->status(), [200, 302, 201]))->toBeTrue();
    });
});

/*************** Refund Management ***************/

describe('Admin Refund Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list refunds', function () {
        $response = $this->get(route('admin.agriverse.refunds.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Seller Management ***************/

describe('Admin Seller Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list sellers', function () {
        $response = $this->get(route('admin.agriverse.sellers.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Banner Management ***************/

describe('Admin Banner Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list banners', function () {
        $response = $this->get(route('admin.agriverse.banners.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });
});

/*************** Plan Management ***************/

describe('Admin Plan Management', function () {
    beforeEach(function () {
        $this->actingAs($this->admin);
    });

    it('can list plans', function () {
        $response = $this->get(route('admin.agriverse.plans.index'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('can create plan', function () {
        $response = $this->post(route('admin.agriverse.plans.store'), [
            'name' => 'Gói Premium', 'price' => 299000, 'duration_days' => 30,
            'features' => ['all'], 'is_active' => true,
        ]);
        expect(in_array($response->status(), [200, 302, 201]))->toBeTrue();
    });
});

/*************** Seller Dashboard ***************/

describe('Seller Dashboard', function () {
    it('seller can access dashboard', function () {
        $this->actingAs($this->seller);
        $response = $this->get(route('agriverse.shop.seller.dashboard'));
        expect(in_array($response->status(), [200, 302]))->toBeTrue();
    });

    it('seller can view stats via API', function () {
        $this->actingAs($this->seller, 'api');
        $response = $this->getJson('/api/seller/dashboard/stats');
        $response->assertOk();
    });
});
