<?php

namespace App\Modules\AgriVerse\Http\Middleware;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Services\SeoService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    protected SeoService $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Share SEO data with all Inertia pages
        Inertia::share('seo', function () use ($request) {
            return $this->getSeoData($request);
        });

        return $next($request);
    }

    protected function getSeoData(Request $request): array
    {
        $route = $request->route();

        // Default SEO data
        $seo = $this->seoService->homeMeta();

        if ($route && $route->getName() === 'agriverse.shop.products.show') {
            $product = $request->route('product');
            if ($product) {
                if (! $product instanceof \App\Modules\AgriVerse\Models\Product) {
                    $product = \App\Modules\AgriVerse\Models\Product::find($product);
                }
                if ($product instanceof \App\Modules\AgriVerse\Models\Product) {
                    $seo = $this->seoService->productMeta($product);
                }
            }
        }

        // Category page
        if ($route && $route->getName() === 'agriverse.shop.products.index') {
            $category = $request->query('category');
            if ($category) {
                $catModel = Category::where('slug', $category)->first();
                if ($catModel) {
                    $seo = $this->seoService->categoryMeta($catModel);
                }
            }
        }

        // Store page
        if ($route && $route->getName() === 'agriverse.shop.stores.show') {
            $store = $request->route('store');
            if ($store) {
                if (! $store instanceof Store) {
                    $store = Store::find($store);
                }
                if ($store instanceof Store) {
                    $seo = $this->seoService->storeMeta($store);
                }
            }
        }

        return $seo;
    }
}
