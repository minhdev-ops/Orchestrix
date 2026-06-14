<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Category;

class HomeController
{
    public function index()
    {
        $productsCount = Product::published()->count();
        $categories = Category::active()->root()->get()->map(function ($cat) {
            $cat->products_count = $cat->products()->count() + Product::where('category', $cat->name)->count();
            return $cat;
        });
        $featuredProducts = Product::published()->inStock()->withCount(['orders as sold_count' => fn($q) => $q->whereIn('status', ['completed', 'delivered'])])->latest()->take(8)->get();
        $wishlistedIds = auth()->check()
            ? \App\Modules\AgriVerse\Models\Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray()
            : [];
        $stores = Store::where('status', 'active')->withCount('products')->latest()->take(6)->get();

        return Inertia::render('Marketplace/Home', [
            'productsCount' => $productsCount,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts->map(fn ($p) => $p->setAttribute('wishlisted', in_array($p->id, $wishlistedIds))),
            'stores' => $stores,
        ]);
    }
}
