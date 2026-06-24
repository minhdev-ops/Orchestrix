<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;

class SearchService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Search products with full-text
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 24): mixed
    {
        $cacheKey = md5(json_encode(['query' => $query, 'filters' => $filters, 'page' => $filters['page'] ?? 1]));

        return $this->cacheService->cacheSearchResults($query, $filters, function () use ($query, $filters, $perPage) {
            $searchQuery = Product::query()
                ->with(['categories', 'store'])
                ->where('is_active', true);

            if (!empty($query)) {
                // Use MySQL FULLTEXT if available, fallback to LIKE
                if ($this->hasFulltextIndex()) {
                    $searchQuery->whereRaw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [$this->prepareFulltextQuery($query)]);
                } else {
                    $searchQuery->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('sku', 'like', "%{$query}%");
                    });
                }
            }

            // Apply filters
            $searchQuery = $this->applyFilters($searchQuery, $filters);

            // Sorting
            $searchQuery = $this->applySorting($searchQuery, $filters['sort'] ?? 'relevance', $query);

            return $searchQuery->paginate($perPage);
        });
    }

    /**
     * Autocomplete suggestions
     */
    public function autocomplete(string $query, int $limit = 10): array
    {
        if (mb_strlen($query) < 2) {
            return [];
        }

        $cacheKey = "autocomplete:" . md5($query);

        return Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            $products = Product::where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name', 'price', 'image')
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'text' => $p->name,
                    'price' => $p->price,
                    'image' => $p->image,
                    'type' => 'product',
                ]);

            $categories = Category::where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name', 'slug')
                ->limit(3)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'text' => $c->name,
                    'slug' => $c->slug,
                    'type' => 'category',
                ]);

            $stores = Store::where('is_active', true)
                ->where('name', 'like', "%{$query}%")
                ->select('id', 'name')
                ->limit(3)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'text' => $s->name,
                    'type' => 'store',
                ]);

            return array_merge($products->toArray(), $categories->toArray(), $stores->toArray());
        });
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): array
    {
        $cacheKey = "popular_searches";

        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            // Get from search_logs table if exists, otherwise return defaults
            if (DB::getSchemaBuilder()->hasTable('search_logs')) {
                return DB::table('search_logs')
                    ->select('query', DB::raw('COUNT(*) as count'))
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('query')
                    ->orderByDesc('count')
                    ->limit($limit)
                    ->pluck('query', 'count')
                    ->toArray();
            }

            return [
                'cây cảnh' => 150,
                'bonsai' => 120,
                'phân bón' => 90,
                'chậu cây' => 80,
                'đất trồng' => 70,
                'cây indoor' => 60,
                'phòng trừ sâu bệnh' => 50,
            ];
        });
    }

    /**
     * Save search history
     */
    public function saveSearchHistory(int $userId, string $query): void
    {
        if (DB::getSchemaBuilder()->hasTable('search_logs')) {
            DB::table('search_logs')->insert([
                'user_id' => $userId,
                'query' => $query,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Get user search history
     */
    public function getUserSearchHistory(int $userId, int $limit = 10): array
    {
        if (DB::getSchemaBuilder()->hasTable('search_logs')) {
            return DB::table('search_logs')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->pluck('query')
                ->toArray();
        }

        return [];
    }

    /**
     * Apply filters
     */
    protected function applyFilters($query, array $filters): mixed
    {
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->where('stock', '>', 0);
        }

        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (!empty($filters['rating'])) {
            $query->whereHas('reviews', function ($q) use ($filters) {
                $q->havingRaw('AVG(rating) >= ?', [$filters['rating']]);
            });
        }

        return $query;
    }

    /**
     * Apply sorting
     */
    protected function applySorting($query, string $sort, string $searchQuery = ''): mixed
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->latest(),
            'popular' => $query->orderByDesc('views_count'),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'relevance' => $searchQuery
                ? $query->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["%{$searchQuery}%"])
                : $query->latest(),
            default => $query->latest(),
        };
    }

    /**
     * Check if FULLTEXT index exists
     */
    protected function hasFulltextIndex(): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM products WHERE Index_type = 'FULLTEXT'");
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Prepare query for FULLTEXT search
     */
    protected function prepareFulltextQuery(string $query): string
    {
        $words = explode(' ', $query);
        $prepared = array_map(fn ($word) => "+{$word}*", $words);
        return implode(' ', $prepared);
    }
}
