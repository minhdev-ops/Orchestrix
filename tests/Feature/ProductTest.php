<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('product-api');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();
});

it('can list products', function () {
    $user = User::factory()->create(['role' => 'buyer']);
    $user->assignRole('buyer');
    $user->assignRole(Role::findByName('buyer', 'api'));
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Passport::actingAs($user);

    Product::factory()->count(5)->create(['status' => 'published']);

    $response = $this->getJson('/api/products');

    $response->assertOk();
});

it('can search products', function () {
    $user = User::factory()->create(['role' => 'buyer']);
    $user->assignRole('buyer');
    $user->assignRole(Role::findByName('buyer', 'api'));
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Passport::actingAs($user);

    Product::factory()->create(['name' => 'Cay bonsai mini', 'status' => 'published']);
    Product::factory()->create(['name' => 'Chau dat nung', 'status' => 'published']);

    $response = $this->getJson('/api/products?search=bonsai');

    $response->assertOk();
});

it('seller can create product', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $seller->assignRole('seller');
    $seller->assignRole(Role::findByName('seller', 'api'));
    $seller->givePermissionTo(['product.view', 'product.create', 'product.edit', 'product.delete']);
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Passport::actingAs($seller);

    $response = $this->postJson('/api/products', [
        'name' => 'Cay bonsai moi',
        'description' => 'Mo ta san pham',
        'price' => 250000,
        'stock' => 10,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('products', ['name' => 'Cay bonsai moi']);
});

it('seller can update own product', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $seller->assignRole('seller');
    $seller->assignRole(Role::findByName('seller', 'api'));
    $seller->givePermissionTo(['product.view', 'product.create', 'product.edit', 'product.delete']);
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Passport::actingAs($seller);

    $product = Product::factory()->create(['user_id' => $seller->id]);

    $response = $this->putJson("/api/products/{$product->id}", [
        'name' => 'Ten moi',
    ]);

    $response->assertOk();
});

it('seller can delete own product', function () {
    $seller = User::factory()->create(['role' => 'seller']);
    $seller->assignRole('seller');
    $seller->assignRole(Role::findByName('seller', 'api'));
    $seller->givePermissionTo(['product.view', 'product.create', 'product.edit', 'product.delete']);
    app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    Passport::actingAs($seller);

    $product = Product::factory()->create(['user_id' => $seller->id]);

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertOk();
});
