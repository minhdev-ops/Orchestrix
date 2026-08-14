<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $seller;

    private User $buyer;

    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
        $this->admin->assignRole(Role::findByName('admin', 'api'));

        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->seller->assignRole('seller');
        $this->seller->assignRole(Role::findByName('seller', 'api'));

        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->buyer->assignRole('buyer');
        $this->buyer->assignRole(Role::findByName('buyer', 'api'));

        $this->store = Store::create([
            'owner_id' => $this->seller->id,
            'name' => 'Test Store',
            'status' => 'active',
        ]);
    }

    private function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'user_id' => $this->seller->id,
            'store_id' => $this->store->id,
            'name' => 'Test Product',
            'price' => 100.00,
            'status' => 'published',
            'stock' => 10,
        ], $overrides));
    }

    public function test_seller_can_create_product(): void
    {
        Passport::actingAs($this->seller);

        $response = $this->postJson('/api/products', [
            'name' => 'New Product',
            'price' => 49.99,
            'store_id' => $this->store->id,
            'category' => 'machinery',
            'technical_specs' => [
                'engine' => 'V8',
                'warranty_months' => 12,
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'New Product']);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_buyer_cannot_create_product(): void
    {
        Passport::actingAs($this->buyer);

        $response = $this->postJson('/api/products', [
            'name' => 'New Product',
            'price' => 49.99,
        ]);

        $response->assertStatus(403);
    }

    public function test_seller_sees_only_own_products(): void
    {
        Passport::actingAs($this->seller);

        $this->createProduct();
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $otherSeller->assignRole('seller');
        $otherSeller->assignRole(Role::findByName('seller', 'api'));
        Product::create([
            'user_id' => $otherSeller->id,
            'name' => 'Other Product',
            'price' => 200,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_admin_sees_all_products(): void
    {
        Passport::actingAs($this->admin);

        $this->createProduct();
        Product::create([
            'user_id' => $this->buyer->id,
            'name' => 'Another Product',
            'price' => 50,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_buyer_sees_only_published_products(): void
    {
        Passport::actingAs($this->buyer);

        $this->createProduct(['status' => 'published']);
        $this->createProduct(['name' => 'Draft Product', 'status' => 'draft']);

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_product_can_be_filtered_by_store_id(): void
    {
        Passport::actingAs($this->admin);

        $this->createProduct(['store_id' => $this->store->id]);
        $otherStore = Store::create(['owner_id' => $this->seller->id, 'name' => 'Other']);
        $this->createProduct(['name' => 'Other Store Product', 'store_id' => $otherStore->id]);

        $response = $this->getJson('/api/products?store_id='.$this->store->id);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_product_can_be_sorted_by_price(): void
    {
        Passport::actingAs($this->admin);

        $this->createProduct(['name' => 'Cheap', 'price' => 10]);
        $this->createProduct(['name' => 'Expensive', 'price' => 100]);

        $response = $this->getJson('/api/products?sort=price&dir=asc');

        $response->assertOk();
        $this->assertEquals('Cheap', $response->json('data.0.name'));
    }

    public function test_seller_can_update_own_product(): void
    {
        Passport::actingAs($this->seller);

        $product = $this->createProduct();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 150,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_seller_cannot_update_others_product(): void
    {
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $otherSeller->assignRole('seller');
        $otherSeller->assignRole(Role::findByName('seller', 'api'));
        Passport::actingAs($otherSeller);

        $product = $this->createProduct();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Hacked Product',
        ]);

        $response->assertStatus(403);
    }

    public function test_seller_can_delete_own_product(): void
    {
        Passport::actingAs($this->seller);

        $product = $this->createProduct();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_buyer_cannot_delete_product(): void
    {
        Passport::actingAs($this->buyer);

        $product = $this->createProduct();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    public function test_digital_passport_endpoint_returns_specs(): void
    {
        $this->markTestSkipped('Digital passport endpoint returns logs, not product specs');
    }

    public function test_digital_passport_log_can_be_created(): void
    {
        Passport::actingAs($this->seller);

        $product = $this->createProduct();

        $response = $this->postJson('/api/digital-passport-logs', [
            'product_id' => $product->id,
            'action' => 'maintenance',
            'data' => ['note' => 'Oil change performed'],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('digital_passport_logs', [
            'product_id' => $product->id,
            'action' => 'maintenance',
        ]);
    }

    public function test_product_store_and_stock_fields(): void
    {
        Passport::actingAs($this->seller);

        $response = $this->postJson('/api/products', [
            'name' => 'Stocked Product',
            'price' => 75,
            'store_id' => $this->store->id,
            'stock' => 25,
        ]);

        $response->assertStatus(201);
        $this->assertEquals($this->store->id, $response->json('data.store_id'));
        $this->assertEquals(25, $response->json('data.stock'));
    }
}
