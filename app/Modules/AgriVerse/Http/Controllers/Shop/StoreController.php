<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\SellerVerification;

class StoreController
{
    public function index()
    {
        $stores = Store::where('status', 'active')
            ->withCount('products')
            ->latest()
            ->paginate(12);

        return Inertia::render('Marketplace/Stores/Index', [
            'stores' => $stores,
        ]);
    }

    public function show($id)
    {
        $store = Store::findOrFail($id);

        $products = Product::published()
            ->where('store_id', $store->id)
            ->withCount(['orders as sold_count' => fn($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->latest()
            ->paginate(12);

        $store->loadCount('products');

        // Seller verification info
        $sellerVerifiedAt = null;
        $sellerType = null;
        $owner = $store->owner;
        if ($owner) {
            $sellerVerifiedAt = $owner->seller_verified_at;
            $sellerType = $owner->seller_type;
        }

        return Inertia::render('Marketplace/Stores/Show', [
            'store' => $store,
            'products' => $products,
            'sellerVerifiedAt' => $sellerVerifiedAt,
            'sellerType' => $sellerType,
        ]);
    }
}
