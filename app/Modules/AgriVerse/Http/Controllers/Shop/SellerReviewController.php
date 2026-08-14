<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Review;
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

        $stats = Review::whereIn('product_id', $productIds)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COALESCE(AVG(rating), 0) as average_rating')
            ->selectRaw('SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending')
            ->first();
        $stats = [
            'total' => (int) ($stats->total ?? 0),
            'average_rating' => (float) ($stats->average_rating ?? 0),
            'pending' => (int) ($stats->pending ?? 0),
        ];

        return Inertia::render('Marketplace/Seller/Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }
}
