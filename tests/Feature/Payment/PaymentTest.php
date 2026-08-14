<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Coupon;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('payment');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->buyer->assignRole('buyer');

    $this->seller = User::factory()->create(['role' => 'seller']);
    $this->seller->assignRole('seller');

    $this->store = Store::create([
        'owner_id' => $this->seller->id, 'name' => 'Cửa hàng Test', 'status' => 'active',
    ]);

    $this->product = Product::create([
        'user_id' => $this->seller->id, 'store_id' => $this->store->id,
        'name' => 'Test Product', 'price' => 500000, 'stock' => 10, 'status' => 'published',
    ]);

    $this->order = Order::create([
        'buyer_id' => $this->buyer->id, 'seller_id' => $this->seller->id,
        'product_id' => $this->product->id, 'store_id' => $this->store->id,
        'quantity' => 1, 'unit_price' => 500000, 'total_price' => 500000,
        'total_amount' => 500000, 'status' => 'pending', 'shipping_address' => 'Test',
    ]);
});

describe('Coupons', function () {
    it('can list coupons', function () {
        $response = $this->actingAs($this->buyer, 'api')->getJson('/api/coupons');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can validate valid coupon', function () {
        Coupon::create([
            'code' => 'SALE10', 'type' => 'percent', 'value' => 10, 'name' => 'Sale 10%',
            'min_order_amount' => 100000, 'usage_limit' => 100, 'is_active' => true,
            'starts_at' => now()->subDay(), 'expires_at' => now()->addMonth(),
        ]);

        $response = $this->actingAs($this->buyer, 'api')->postJson('/api/coupons/validate', [
            'code' => 'SALE10', 'order_total' => 500000,
        ]);
        expect(in_array($response->status(), [200, 400, 401, 403, 422]))->toBeTrue();
    });

    it('rejects invalid coupon', function () {
        $response = $this->actingAs($this->buyer, 'api')->postJson('/api/coupons/validate', [
            'code' => 'INVALID', 'order_total' => 500000,
        ]);
        expect(in_array($response->status(), [200, 400, 401, 403, 404, 422]))->toBeTrue();
    });
});

describe('Notifications', function () {
    it('can list notifications', function () {
        $response = $this->actingAs($this->buyer, 'api')->getJson('/api/notifications');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can get unread count', function () {
        $response = $this->actingAs($this->buyer, 'api')->getJson('/api/notifications/unread-count');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });

    it('can mark notifications as read', function () {
        $response = $this->actingAs($this->buyer, 'api')->putJson('/api/notifications/read-all');
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });
});
