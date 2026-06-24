<?php

namespace App\Modules\AgriVerse\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Services\SeoService;

class SeoMiddleware
{
    protected SeoService $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function handle(Request $request, Closure $next): mixed
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

        // Product page
        if ($route && $route->getName() === 'agriverse.shop.products.show') {
            $product = $request->route('product');
            if ($product) {
                $seo = $this->seoService->productMeta($product);
            }
        }

        // Category page
        if ($route && $route->getName() === 'agriverse.shop.products.index') {
            $category = $request->query('category');
            if ($category) {
                $catModel = \App\Modules\AgriVerse\Models\Category::where('slug', $category)->first();
                if ($catModel) {
                    $seo = $this->seoService->categoryMeta($catModel);
                }
            }
        }

        // Store page
        if ($route && $route->getName() === 'agriverse.shop.stores.show') {
            $store = $request->route('store');
            if ($store) {
                $seo = $this->seoService->storeMeta($store);
            }
        }

        return $seo;
    }
}
