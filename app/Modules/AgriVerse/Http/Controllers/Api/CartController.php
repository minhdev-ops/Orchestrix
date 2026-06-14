<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Http\Resources\CartResource;
use App\Modules\AgriVerse\Http\Requests\StoreCartRequest;
use App\Modules\AgriVerse\Http\Requests\UpdateCartRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = Cart::with('product.store', 'store')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return CartResource::collection($items);
    }

    public function store(StoreCartRequest $request): CartResource
    {
        $item = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ],
            [
                'store_id' => $request->store_id,
                'quantity' => $request->quantity,
            ]
        );

        $item->load('product.store', 'store');

        return CartResource::make($item);
    }

    public function update(UpdateCartRequest $request, Cart $cart): CartResource
    {
        if ($cart->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $cart->update(['quantity' => $request->quantity]);
        $cart->load('product.store', 'store');

        return CartResource::make($cart);
    }

    public function destroy(Request $request, Cart $cart)
    {
        if ($cart->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }
}
