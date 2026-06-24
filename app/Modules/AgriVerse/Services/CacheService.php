<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;

class CacheService
{
    protected int $defaultTTL = 3600; // 1 hour
    protected int $shortTTL = 300; // 5 minutes
    protected int $longTTL = 86400; // 24 hours

    /**
     * Cache key prefixes
     */
    protected array $prefixes = [
        'product' => 'product',
        'category' => 'category',
        'store' => 'store',
        'home' => 'home',
        'search' => 'search',
    ];

    /**
     * Get cached product
     */
    public function getProduct(int $productId, callable $callback = null): ?Product
    {
        $key = "{$this->prefixes['product']}:{$productId}";

        return Cache::remember($key, $this->defaultTTL, function () use ($productId, $callback) {
            $query = Product::with(['categories', 'store', 'images'])
                ->where('id', $productId);

            if ($callback) {
                $query = $callback($query);
            }

            return $query->first();
        });
    }

    /**
     * Invalidate product cache
     */
    public function invalidateProduct(int $productId): void
    {
        $key = "{$this->prefixes['product']}:{$productId}";
        Cache::forget($key);

        // Also invalidate related caches
        $this->invalidateProductList();
    }

    /**
     * Get cached product list
     */
    public function getProductList(string $cacheKey, callable $callback, int $ttl = null): mixed
    {
        $key = "{$this->prefixes['product']}:list:{$cacheKey}";

        return Cache::remember($key, $ttl ?? $this->shortTTL, $callback);
    }

    /**
     * Invalidate product list caches
     */
    public function invalidateProductList(): void
    {
        $keys = Cache::get('product_list_keys') ?? [];

        foreach ($keys as $key) {
            Cache::forget("{$this->prefixes['product']}:list:{$key}");
        }

        // Clear homepage cache
        $this->invalidateHome();
    }

    /**
     * Get cached category
     */
    public function getCategory(int $categoryId): ?Category
    {
        $key = "{$this->prefixes['category']}:{$categoryId}";

        return Cache::remember($key, $this->longTTL, function () use ($categoryId) {
            return Category::with(['products' => function ($query) {
                $query->where('is_active', true)->limit(10);
            }])->find($categoryId);
        });
    }

    /**
     * Get cached categories tree
     */
    public function getCategoriesTree(): array
    {
        $key = "{$this->prefixes['category']}:tree";

        return Cache::remember($key, $this->longTTL, function () {
            return Category::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate category cache
     */
    public function invalidateCategory(int $categoryId = null): void
    {
        if ($categoryId) {
            Cache::forget("{$this->prefixes['category']}:{$categoryId}");
        }
        Cache::forget("{$this->prefixes['category']}:tree");
    }

    /**
     * Get cached store
     */
    public function getStore(int $storeId): ?Store
    {
        $key = "{$this->prefixes['store']}:{$storeId}";

        return Cache::remember($key, $this->defaultTTL, function () use ($storeId) {
            return Store::with(['owner', 'products' => function ($query) {
                $query->where('is_active', true)->limit(20);
            }])->find($storeId);
        });
    }

    /**
     * Invalidate store cache
     */
    public function invalidateStore(int $storeId): void
    {
        Cache::forget("{$this->prefixes['store']}:{$storeId}");
    }

    /**
     * Get cached homepage data
     */
    public function getHomeData(): array
    {
        $key = "{$this->prefixes['home']}:data";

        return Cache::remember($key, $this->shortTTL, function () {
            return [
                'featured_products' => Product::where('is_active', true)
                    ->with(['categories', 'store'])
                    ->inRandomOrder()
                    ->limit(12)
                    ->get(),
                'latest_products' => Product::where('is_active', true)
                    ->with(['categories', 'store'])
                    ->latest()
                    ->limit(12)
                    ->get(),
                'categories' => Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->withCount(['products' => function ($query) {
                        $query->where('is_active', true);
                    }])
                    ->orderBy('sort_order')
                    ->limit(8)
                    ->get(),
                'stores' => Store::where('is_active', true)
                    ->withCount('products')
                    ->orderByDesc('products_count')
                    ->limit(6)
                    ->get(),
            ];
        });
    }

    /**
     * Invalidate homepage cache
     */
    public function invalidateHome(): void
    {
        Cache::forget("{$this->prefixes['home']}:data");
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(string $query, array $filters, callable $callback, int $ttl = null): mixed
    {
        $cacheKey = $this->generateSearchCacheKey($query, $filters);
        $key = "{$this->prefixes['search']}:{$cacheKey}";

        return Cache::remember($key, $ttl ?? $this->shortTTL, $callback);
    }

    /**
     * Generate search cache key
     */
    protected function generateSearchCacheKey(string $query, array $filters): string
    {
        $data = json_encode(['query' => $query, 'filters' => $filters]);
        return md5($data);
    }

    /**
     * Clear all caches
     */
    public function clearAll(): void
    {
        Cache::tags(['products', 'categories', 'stores', 'home'])->flush();
    }

    /**
     * Get cache stats
     */
    public function getStats(): array
    {
        return [
            'product_count' => Cache::get('product_count') ?? Product::count(),
            'category_count' => Cache::get('category_count') ?? Category::count(),
            'store_count' => Cache::get('store_count') ?? Store::count(),
        ];
    }

    /**
     * Warm up cache
     */
    public function warmUp(): void
    {
        // Warm up categories
        $this->getCategoriesTree();

        // Warm up homepage
        $this->getHomeData();

        // Warm up popular products
        Product::where('is_active', true)
            ->with(['categories', 'store'])
            ->orderByDesc('views_count')
            ->limit(50)
            ->each(function ($product) {
                $this->getProduct($product->id);
            });
    }
}
