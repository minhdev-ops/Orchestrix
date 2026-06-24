<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Order;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        Product::factory()->count(10)->create(['is_active' => true]);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function test_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Cây bonsai mini', 'is_active' => true]);
        Product::factory()->create(['name' => 'Chậu đất nung', 'is_active' => true]);

        $response = $this->getJson('/api/products?search=bonsai');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_get_product_detail(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'name' => $product->name,
                    'price' => $product->price,
                ],
            ]);
    }

    public function test_can_create_product(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        $category = Category::factory()->create();

        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/products', [
            'name' => 'Cây bonsai mới',
            'description' => 'Mô tả sản phẩm',
            'price' => 250000,
            'stock' => 10,
            'category_ids' => [$category->id],
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Cây bonsai mới']);

        $this->assertDatabaseHas('products', ['name' => 'Cây bonsai mới']);
    }

    public function test_cannot_create_product_without_auth(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test',
            'price' => 100000,
        ]);

        $response->assertUnauthorized();
    }

    public function test_can_update_product(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api');

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Tên mới',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Tên mới']);
    }

    public function test_can_delete_product(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api');

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
