<?php

namespace Tests\Feature\AgriVerse;

use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private $buyer;
    private $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->buyer = User::factory()->create(['role' => 'buyer']);
        $this->buyer->assignRole('buyer');
        $this->buyer->assignRole(Role::findByName('buyer', 'api'));

        $this->seller = User::factory()->create(['role' => 'seller']);
        $this->seller->assignRole('seller');
        $this->seller->assignRole(Role::findByName('seller', 'api'));
        $this->seller->givePermissionTo(['product.view', 'product.create', 'product.edit', 'product.delete']);
    }

    public function test_api_products_list()
    {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_api_categories_list()
    {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
    }

    public function test_api_stores_list()
    {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/stores');
        $response->assertStatus(200);
    }

    public function test_api_cart_requires_auth()
    {
        $response = $this->getJson('/api/cart');
        $response->assertStatus(401);
    }

    public function test_api_orders_requires_auth()
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    public function test_api_wishlist_requires_auth()
    {
        $response = $this->getJson('/api/wishlist');
        $response->assertStatus(401);
    }

    public function test_api_authenticated_cart()
    {
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/cart');
        $response->assertStatus(200);
    }

    public function test_api_product_detail()
    {
        $product = Product::factory()->create(['status' => 'published', 'user_id' => $this->seller->id]);
        Passport::actingAs($this->buyer);
        $response = $this->getJson("/api/products/{$product->id}");
        $response->assertStatus(200);
    }

    public function test_api_product_search()
    {
        Product::factory()->create(['name' => 'Cay bonsai', 'status' => 'published', 'user_id' => $this->seller->id]);
        Passport::actingAs($this->buyer);
        $response = $this->getJson('/api/products?search=bonsai');
        $response->assertStatus(200);
    }

    public function test_api_coupons_validate()
    {
        Passport::actingAs($this->buyer);
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'INVALID',
            'order_amount' => 100000,
        ]);
        $response->assertStatus(404);
    }
}
