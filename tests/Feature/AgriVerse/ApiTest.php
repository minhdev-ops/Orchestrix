<?php

namespace Tests\Feature\AgriVerse;

use Tests\TestCase;
use App\Models\User;
use App\Modules\AgriVerse\Models\Product;

class ApiTest extends TestCase
{
    public function test_api_products_list()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_api_categories_list()
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_api_stores_list()
    {
        $response = $this->getJson('/api/stores');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
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
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No users found');
        }
        $response = $this->actingAs($user, 'api')->getJson('/api/cart');
        $response->assertStatus(200);
    }

    public function test_api_product_detail()
    {
        $product = Product::published()->first();
        if (!$product) {
            $this->markTestSkipped('No published products found');
        }
        $response = $this->getJson("/api/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['id', 'name', 'price']]);
    }

    public function test_api_product_search()
    {
        $response = $this->getJson('/api/products?search=nông');
        $response->assertStatus(200);
    }

    public function test_api_coupons_validate()
    {
        $response = $this->postJson('/api/coupons/validate', [
            'code' => 'INVALID',
            'order_amount' => 100000,
        ]);
        $response->assertStatus(404);
    }
}
