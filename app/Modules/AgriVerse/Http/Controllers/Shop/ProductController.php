<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;

class ProductController
{
    public function index(Request $request)
    {
        $query = Product::published()->with(['store'])->withCount(['orders as sold_count' => fn($q) => $q->whereIn('status', ['completed', 'delivered'])]);
        $wishlistedIds = auth()->check()
            ? \App\Modules\AgriVerse\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray()
            : [];

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(24)->appends($request->only(['search', 'category', 'min_price', 'max_price', 'in_stock', 'sort']))->through(fn($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'compare_price' => $product->compare_price,
            'image' => $product->image,
            'category' => $product->category,
            'stock' => $product->stock,
            'sold_count' => (int) $product->sold_count,
            'model_3d_path' => $product->model_3d_path,
            'wishlisted' => in_array($product->id, $wishlistedIds),
            'store' => $product->store ? ['id' => $product->store->id, 'name' => $product->store->name] : null,
        ]);

        $categories = Category::active()->root()->get();

        return Inertia::render('Marketplace/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'min_price', 'max_price', 'in_stock', 'sort']),
        ]);
    }

    public function show(Product $product)
    {
        $product->loadCount(['orders as sold_count' => fn($q) => $q->whereIn('status', ['completed', 'delivered'])]);
        $product->load(['store', 'reviews.user', 'passportLogs.performer', 'user']);

        $wishlisted = auth()->check() && \App\Modules\AgriVerse\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();

        $relatedProducts = Product::published()
            ->withCount(['orders as sold_count' => fn($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->category) {
                    $q->where('category', $product->category);
                }
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'compare_price' => $p->compare_price,
                'image' => $p->image,
                'model_3d_path' => $p->model_3d_path,
                'sold_count' => $p->sold_count,
            ]);

        return Inertia::render('Marketplace/Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
                'image' => $product->image,
                'category' => $product->category,
                'description' => $product->description,
                'stock' => $product->stock,
                'sold_count' => $product->sold_count,
                'wishlisted' => $wishlisted,
                'technical_specs' => $product->technical_specs,
                'model_3d_path' => $product->model_3d_path,
                'model_3d_url' => $product->model_3d_url,
                'store' => $product->store ? ['id' => $product->store->id, 'name' => $product->store->name] : null,
                'seller' => $product->user ? ['id' => $product->user->id, 'name' => $product->user->name] : null,
                'reviews' => $product->reviews->map(fn($r) => [
                    'id' => $r->id,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->diffForHumans(),
                    'user' => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
                ]),
                'passport_logs' => $product->passportLogs->map(fn($log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'data' => $log->data,
                    'created_at' => $log->created_at->diffForHumans(),
                    'performer' => $log->performer ? ['name' => $log->performer->name] : null,
                ]),
            ],
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function categories()
    {
        $categories = Category::active()->withCount('products')->with('children')->root()->get();

        return Inertia::render('Marketplace/Categories/Index', [
            'categories' => $categories,
        ]);
    }
}
