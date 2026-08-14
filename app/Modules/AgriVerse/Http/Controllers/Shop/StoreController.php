<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use Inertia\Inertia;

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

    public function show(Store $store)
    {

        $products = Product::published()
            ->where('store_id', $store->id)
            ->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])
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
