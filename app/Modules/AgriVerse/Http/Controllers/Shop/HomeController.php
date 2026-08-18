<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Wishlist;
use Inertia\Inertia;

class HomeController
{
    public function index()
    {
        $productsCount = Product::published()->count();
        $categories = Category::active()->root()->withCount('products')->get()->map(function ($cat) {
            $nameCount = Product::published()->where('category', $cat->slug)->count();
            $cat->products_count = ($cat->products_count ?? 0) + $nameCount;

            return $cat;
        })->filter(fn ($cat) => $cat->products_count > 0)
            ->sortByDesc('products_count')
            ->values();
        $featuredProducts = Product::published()->inStock()->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])->latest()->take(8)->get();
        $wishlistedIds = auth()->check()
            ? Wishlist::where('user_id', auth()->id())->pluck('product_id')->toArray()
            : [];
        $stores = Store::where('status', 'active')->withCount('products')->latest()->take(6)->get();
        $commitmentImage = Product::published()->where('category', 'bonsai-co-thu')
            ->whereNotNull('image')->value('image') ?? '/images/hero-bonsai.jpg';

        return Inertia::render('Marketplace/Home', [
            'productsCount' => $productsCount,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts->map(fn ($p) => $p->setAttribute('wishlisted', in_array($p->id, $wishlistedIds))),
            'stores' => $stores,
            'commitmentImage' => $commitmentImage,
        ]);
    }
}
