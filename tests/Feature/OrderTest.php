<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Transaction;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_create_order(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create([
            'user_id' => $seller->id,
            'price' => 250000,
            'stock' => 10,
        ]);

        $this->actingAs($buyer, 'api');

        $response = $this->postJson('/api/orders', [
            'product_id' => $product->id,
            'quantity' => 2,
            'shipping_address' => '123 Đường ABC, Hà Nội',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['buyer_id' => $buyer->id]);
    }

    public function test_buyer_can_cancel_order(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'status' => 'pending',
        ]);

        $this->actingAs($buyer, 'api');

        $response = $this->postJson("/api/orders/{$order->id}/cancel");

        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_seller_can_confirm_order(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $order = Order::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'pending',
        ]);

        $this->actingAs($seller, 'api');

        $response = $this->postJson("/api/orders/{$order->id}/confirm");

        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_buyer_can_complete_order(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'status' => 'delivered',
        ]);

        $this->actingAs($buyer, 'api');

        $response = $this->postJson("/api/orders/{$order->id}/complete");

        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    public function test_cannot_cancel_completed_order(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'status' => 'completed',
        ]);

        $this->actingAs($buyer, 'api');

        $response = $this->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(422);
    }
}
