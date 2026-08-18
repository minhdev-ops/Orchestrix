<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Product;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('checkout');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->buyer->assignRole('buyer');
});

it('creates order and clears cart', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $product = Product::factory()->create([
        'user_id' => $seller->id,
        'price' => 150000,
        'stock' => 10,
    ]);

    Cart::create([
        'user_id' => $this->buyer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($this->buyer);

    $response = $this->post('/agriverse/api/checkout/process', [
        'shipping_address' => '123 Ha Noi',
        'shipping_fee' => 30000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'buyer_id' => $this->buyer->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'status' => 'pending',
    ]);
    $this->assertSoftDeleted('carts', [
        'user_id' => $this->buyer->id,
    ]);
});

it('decrements stock', function () {
    $product = Product::factory()->create([
        'user_id' => $this->buyer->id,
        'price' => 100000,
        'stock' => 5,
    ]);

    Cart::create([
        'user_id' => $this->buyer->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $this->actingAs($this->buyer);

    $this->post('/agriverse/api/checkout/process', [
        'shipping_address' => '456 HCM',
    ]);

    $product->refresh();
    expect($product->stock)->toBe(2);
});

it('fails when stock insufficient', function () {
    $product = Product::factory()->create([
        'user_id' => $this->buyer->id,
        'price' => 100000,
        'stock' => 1,
    ]);

    Cart::create([
        'user_id' => $this->buyer->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->actingAs($this->buyer);

    $response = $this->post('/agriverse/api/checkout/process', [
        'shipping_address' => '789 Da Nang',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('orders', [
        'buyer_id' => $this->buyer->id,
    ]);
});

it('rolls back on validation error', function () {
    $product = Product::factory()->create([
        'user_id' => $this->buyer->id,
        'price' => 100000,
        'stock' => 3,
    ]);

    Cart::create([
        'user_id' => $this->buyer->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($this->buyer);

    $this->post('/agriverse/api/checkout/process', [
        'shipping_address' => '',
    ]);

    $product->refresh();
    expect($product->stock)->toBe(3);
});

it('returns error for empty cart', function () {
    $this->actingAs($this->buyer);

    $response = $this->post('/agriverse/api/checkout/process', [
        'shipping_address' => '123 Address',
    ]);

    $response->assertSessionHas('error');
});
