<?php

namespace Tests\Feature\AgriVerse;

use Tests\TestCase;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;
use App\Models\User;

class MarketplaceTest extends TestCase
{
    public function test_home_page_returns_200()
    {
        $response = $this->get('/agriverse');
        $response->assertStatus(200);
    }

    public function test_products_page_returns_200()
    {
        $response = $this->get('/agriverse/san-pham');
        $response->assertStatus(200);
    }

    public function test_stores_page_returns_200()
    {
        $response = $this->get('/agriverse/cua-hang');
        $response->assertStatus(200);
    }

    public function test_categories_page_returns_200()
    {
        $response = $this->get('/agriverse/danh-muc');
        $response->assertStatus(200);
    }

    public function test_product_detail_page_returns_200_for_existing_product()
    {
        $product = Product::published()->first();
        if (!$product) {
            $this->markTestSkipped('No published products found');
        }
        $response = $this->get("/agriverse/san-pham/{$product->id}");
        $response->assertStatus(200);
    }

    public function test_store_detail_page_returns_200_for_existing_store()
    {
        $store = Store::where('status', 'active')->first();
        if (!$store) {
            $this->markTestSkipped('No active stores found');
        }
        $response = $this->get("/agriverse/cua-hang/{$store->id}");
        $response->assertStatus(200);
    }

    public function test_cart_page_requires_auth()
    {
        $response = $this->get('/agriverse/gio-hang');
        $response->assertRedirect('/login');
    }

    public function test_checkout_page_requires_auth()
    {
        $response = $this->get('/agriverse/thanh-toan');
        $response->assertRedirect('/login');
    }

    public function test_orders_page_requires_auth()
    {
        $response = $this->get('/agriverse/don-hang');
        $response->assertRedirect('/login');
    }

    public function test_wishlist_page_requires_auth()
    {
        $response = $this->get('/agriverse/yeu-thich');
        $response->assertRedirect('/login');
    }

    public function test_home_page_returns_inertia_response()
    {
        $response = $this->get('/agriverse');
        $response->assertSuccessful();
        $this->assertStringContainsString('Inertia', $response->headers->get('Content-Type') ?? '');
    }

    public function test_products_page_with_filters()
    {
        $response = $this->get('/agriverse/san-pham?sort=price_asc&min_price=10000&max_price=500000');
        $response->assertStatus(200);
    }

    public function test_products_page_with_category_filter()
    {
        $category = Category::active()->first();
        if (!$category) {
            $this->markTestSkipped('No categories found');
        }
        $response = $this->get("/agriverse/san-pham?category={$category->slug}");
        $response->assertStatus(200);
    }
}
