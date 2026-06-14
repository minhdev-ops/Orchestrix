<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Wishlist;
use App\Modules\AgriVerse\Http\Resources\WishlistResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = Wishlist::with('product.store')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return WishlistResource::collection($items);
    }

    public function store(Request $request): JsonResource
    {
        $data = $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $item = Wishlist::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $data['product_id'],
            ]
        );

        $item->load('product.store');

        return WishlistResource::make($item);
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        if ($wishlist->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $wishlist->delete();

        return response()->json(['message' => 'Item removed from wishlist.']);
    }
}
