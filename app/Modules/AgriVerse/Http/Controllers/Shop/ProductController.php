<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController
{
    public function index(Request $request)
    {
        $query = Product::published()->with(['store', 'user'])->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])]);
        $wishlistedIds = auth()->check()
            ? Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray()
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

        $products = $query->paginate(24)->appends($request->only(['search', 'category', 'min_price', 'max_price', 'in_stock', 'sort']))->through(fn ($product) => [
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
            'seller' => $product->user ? ['id' => $product->user->id, 'name' => $product->user->name] : null,
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
        $product->loadCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])]);
        $product->load(['store', 'passportLogs.performer', 'user']);
        $product->load(['reviews' => fn ($q) => $q->where('is_approved', true), 'reviews.user']);

        $wishlisted = auth()->check() && Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();

        $relatedProducts = Product::published()
            ->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->category) {
                    $q->where('category', $product->category);
                }
            })
            ->latest()
            ->take(4)
            ->get()
            ->map(fn ($p) => [
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
                'metadata' => $product->metadata,
                'model_3d_path' => $product->model_3d_path,
                'model_3d_url' => $product->model_3d_url,
                'store' => $product->store ? ['id' => $product->store->id, 'name' => $product->store->name] : null,
                'seller' => $product->user ? ['id' => $product->user->id, 'name' => $product->user->name] : null,
                'reviews' => $product->reviews->map(fn ($r) => [
                    'id' => $r->id,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->diffForHumans(),
                    'user' => $r->user ? ['id' => $r->user->id, 'name' => $r->user->name] : null,
                ]),
                'passport_logs' => $product->passportLogs->map(fn ($log) => [
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

    public function compare(Request $request)
    {
        $ids = $request->query('ids', '');

        $productIds = collect(explode(',', $ids))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->toArray();

        if (empty($productIds)) {
            return Inertia::render('Marketplace/Compare/Index', [
                'products' => [],
                'specs' => [],
            ]);
        }

        $products = Product::published()
            ->whereIn('id', $productIds)
            ->with(['store', 'manufacturer', 'productType'])
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'compare_price' => $p->compare_price,
                'image' => $p->image,
                'category' => $p->category,
                'description' => $p->description,
                'stock' => $p->stock,
                'status' => $p->status,
                'is_featured' => $p->is_featured,
                'sold_count' => $p->sold_count,
                'technical_specs' => $p->technical_specs,
                'metadata' => $p->metadata,
                'tags' => $p->tags,
                'store_name' => $p->store?->name,
                'manufacturer_name' => $p->manufacturer?->name,
                'product_type_name' => $p->productType?->name,
                'avg_rating' => $p->reviews_avg_rating ? round($p->reviews_avg_rating, 1) : null,
                'reviews_count' => $p->reviews_count,
                'has_variants' => $p->has_variants,
                'model_3d_url' => $p->model_3d_url,
            ]);

        $allSpecs = collect();
        foreach ($products as $product) {
            if ($product['technical_specs']) {
                foreach ($product['technical_specs'] as $key => $value) {
                    if (!$allSpecs->has($key)) {
                        $allSpecs->push($key);
                    }
                }
            }
        }

        return Inertia::render('Marketplace/Compare/Index', [
            'products' => $products,
            'specs' => $allSpecs->values(),
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
