<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Review;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerReviewController
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $productIds = Product::where('user_id', $user->id)->pluck('id');

        $reviews = Review::with(['user', 'product'])
            ->whereIn('product_id', $productIds)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Review::whereIn('product_id', $productIds)->count(),
            'average_rating' => Review::whereIn('product_id', $productIds)->avg('rating'),
            'pending' => Review::whereIn('product_id', $productIds)->where('is_approved', false)->count(),
        ];

        return Inertia::render('Marketplace/Seller/Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }
}
