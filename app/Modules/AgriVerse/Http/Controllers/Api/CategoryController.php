<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Http\Resources\CategoryResource;
use App\Modules\AgriVerse\Http\Resources\ProductResource;
use App\Modules\AgriVerse\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = Category::with('children')
            ->root()
            ->active()
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        $category->load('children', 'parent');

        return CategoryResource::make($category);
    }

    public function products(Request $request, Category $category): AnonymousResourceCollection
    {
        $products = $category->products()
            ->with('store', 'user')
            ->published()
            ->when($request->filled('sort'), function ($q) use ($request) {
                match ($request->sort) {
                    'price_asc' => $q->orderBy('price'),
                    'price_desc' => $q->orderBy('price', 'desc'),
                    'newest' => $q->latest(),
                    'best_seller' => $q->withCount('orders')->orderBy('orders_count', 'desc'),
                    default => $q->latest(),
                };
            }, fn ($q) => $q->latest())
            ->paginate($request->per_page ?? 12);

        return ProductResource::collection($products);
    }
}
