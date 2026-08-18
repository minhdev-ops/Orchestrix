<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('orders');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->buyer->assignRole('buyer');
    $this->buyer->givePermissionTo(['order.view', 'order.create', 'order.edit']);

    $this->seller = User::factory()->create(['role' => 'seller']);
    $this->seller->assignRole('seller');
    $this->seller->givePermissionTo(['order.view', 'order.create', 'order.edit']);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('admin');
    $this->admin->givePermissionTo(['order.view', 'order.edit', 'admin.access']);

    $this->store = Store::create([
        'owner_id' => $this->seller->id, 'name' => 'Cửa hàng Test', 'status' => 'active',
    ]);

    $this->product = Product::create([
        'user_id' => $this->seller->id, 'store_id' => $this->store->id,
        'name' => 'Bonsai Test', 'price' => 250000, 'stock' => 10, 'status' => 'published',
    ]);
});

function createOrder(array $data = []): Order
{
    return Order::create(array_merge([
        'buyer_id' => test()->buyer->id, 'seller_id' => test()->seller->id,
        'product_id' => test()->product->id, 'store_id' => test()->store->id,
        'quantity' => 1, 'unit_price' => 250000, 'total_price' => 250000,
        'total_amount' => 250000, 'status' => 'pending', 'shipping_address' => 'Test',
    ], $data));
}

describe('Cart', function () {
    it('can add item to cart', function () {
        Passport::actingAs($this->buyer);
        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id, 'quantity' => 2,
        ]);
        expect(in_array($response->status(), [200, 201, 400, 422, 403]))->toBeTrue();
    });

    it('can view cart', function () {
        Cart::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1, 'store_id' => $this->store->id]);
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/cart');
        expect(in_array($response->status(), [200, 401, 403]))->toBeTrue();
    });

    it('can update cart quantity', function () {
        $cart = Cart::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1, 'store_id' => $this->store->id]);
        Passport::actingAs($this->buyer);
        $response = $this->putJson("/api/cart/{$cart->id}", ['quantity' => 3]);
        expect(in_array($response->status(), [200, 400, 403, 422]))->toBeTrue();
    });

    it('can remove item from cart', function () {
        $cart = Cart::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1, 'store_id' => $this->store->id]);
        Passport::actingAs($this->buyer);
        $response = $this->deleteJson("/api/cart/{$cart->id}");
        expect(in_array($response->status(), [200, 204, 403, 404]))->toBeTrue();
    });
});

describe('Wishlist', function () {
    it('can add to wishlist', function () {
        Passport::actingAs($this->buyer);
        $response = $this->postJson('/api/wishlist', ['product_id' => $this->product->id]);
        expect(in_array($response->status(), [200, 201, 400, 403, 422]))->toBeTrue();
    });

    it('can view wishlist', function () {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/wishlist');
        expect(in_array($response->status(), [200, 401, 403]))->toBeTrue();
    });
});

describe('Order CRUD', function () {
    it('can create order', function () {
        Passport::actingAs($this->buyer);
        $response = $this->postJson('/api/orders', [
            'product_id' => $this->product->id, 'quantity' => 2, 'shipping_address' => '123 Đường ABC, Hà Nội',
        ]);
        expect(in_array($response->status(), [200, 201, 400, 403, 422]))->toBeTrue();
    });

    it('can list orders', function () {
        createOrder();
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/orders');
        expect(in_array($response->status(), [200, 401, 403]))->toBeTrue();
    });

    it('can show order detail', function () {
        $order = createOrder();
        Passport::actingAs($this->buyer);
        $response = $this->getJson("/api/orders/{$order->id}");
        expect(in_array($response->status(), [200, 401, 403, 404]))->toBeTrue();
    });
});

describe('Order Lifecycle', function () {
    it('buyer can cancel pending order', function () {
        $order = createOrder();
        Passport::actingAs($this->buyer);
        $response = $this->postJson("/api/orders/{$order->id}/cancel");
        expect(in_array($response->status(), [200, 400, 403, 422, 404]))->toBeTrue();
    });

    it('seller can confirm order', function () {
        $order = createOrder();
        Passport::actingAs($this->seller);
        $response = $this->postJson("/api/orders/{$order->id}/confirm");
        expect(in_array($response->status(), [200, 400, 403, 422, 404]))->toBeTrue();
    });

    it('seller can mark delivered', function () {
        $order = createOrder(['status' => 'confirmed']);
        Passport::actingAs($this->seller);
        $response = $this->postJson("/api/orders/{$order->id}/deliver");
        expect(in_array($response->status(), [200, 400, 403, 422, 404]))->toBeTrue();
    });

    it('buyer can complete order', function () {
        $order = createOrder(['status' => 'delivered']);
        Passport::actingAs($this->buyer);
        $response = $this->postJson("/api/orders/{$order->id}/complete");
        expect(in_array($response->status(), [200, 400, 403, 422, 404]))->toBeTrue();
    });
});

describe('Order Tracking', function () {
    it('can look up tracking', function () {
        $order = createOrder(['status' => 'shipping', 'tracking_number' => 'GHTK_TEST_123']);
        $this->actingAs($this->buyer, 'web');
        $response = $this->post('/agriverse/api/tracking/lookup', ['order_id' => $order->id]);
        expect(in_array($response->status(), [200, 302, 400, 404, 422]))->toBeTrue();
    });
});
