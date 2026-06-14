<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Review;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController
{
    public function index(Request $request, Product $product): AnonymousResourceCollection
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->approved()
            ->latest()
            ->paginate($request->per_page ?? 10);

        return ReviewResource::collection($reviews);
    }

    public function store(Request $request, Product $product): ReviewResource
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'images' => 'nullable|array',
            'images.*' => 'string|max:500',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'order_id' => $data['order_id'] ?? null,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'images' => $data['images'] ?? null,
            'is_approved' => $request->user()->hasRole('admin'),
        ]);

        $review->load('user');

        return ReviewResource::make($review);
    }

    public function destroy(Request $request, Review $review)
    {
        if ($review->user_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            abort(403, 'Forbidden');
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted.']);
    }
}
