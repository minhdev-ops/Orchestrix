<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController
{
    private function cartOwnerId(): ?int
    {
        return auth()->id();
    }

    private function cartSessionId(): ?string
    {
        return auth()->check() ? null : session()->getId();
    }

    public function index()
    {
        $cartItems = Cart::with(['product', 'store'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                ] : null,
            ]);

        return Inertia::render('Marketplace/Cart/Index', [
            'cartItems' => $cartItems,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->status !== 'published') {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Sản phẩm không khả dụng.'], 422);
            }

            return redirect()->back()->with('error', 'Sản phẩm không khả dụng.');
        }

        $quantity = $validated['quantity'] ?? 1;
        $userId = $this->cartOwnerId();
        $sessionId = $this->cartSessionId();

        $cart = Cart::withTrashed()
            ->where($userId ? 'user_id' : 'session_id', $userId ?? $sessionId)
            ->where('product_id', $product->id)
            ->first();

        $currentQty = $cart ? ($cart->trashed() ? 0 : $cart->quantity) : 0;
        $newQty = $cart ? ($cart->trashed() ? $quantity : $currentQty + $quantity) : $quantity;

        if ($newQty > $product->stock) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Số lượng vượt quá tồn kho ('.$product->stock.').'], 422);
            }

            return redirect()->back()->with('error', 'Số lượng vượt quá tồn kho ('.$product->stock.').');
        }

        if ($cart) {
            if ($cart->trashed()) {
                $cart->restore();
                $cart->update(['quantity' => $quantity, 'store_id' => $product->store_id]);
            } else {
                $cart->update(['quantity' => $newQty, 'store_id' => $product->store_id]);
            }
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'store_id' => $product->store_id,
                'quantity' => $quantity,
            ]);
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request, Cart $cart)
    {
        $cart->load('product');
        $userId = $this->cartOwnerId();

        if ($userId) {
            if ($cart->user_id !== $userId) {
                abort(403);
            }
        } else {
            if ($cart->session_id !== $this->cartSessionId()) {
                abort(403);
            }
        }

        $quantity = max(1, (int) $request->quantity);

        if ($cart->product && $quantity > $cart->product->stock) {
            return redirect()->back()->with('error', 'Số lượng vượt quá tồn kho ('.$cart->product->stock.').');
        }

        $cart->update(['quantity' => $quantity]);

        return redirect()->back()->with('success', 'Đã cập nhật số lượng.');
    }

    public function remove(Cart $cart)
    {
        $userId = $this->cartOwnerId();

        if ($userId) {
            if ($cart->user_id !== $userId) {
                abort(403);
            }
        } else {
            if ($cart->session_id !== $this->cartSessionId()) {
                abort(403);
            }
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ.');
    }

    public function buyNow(Request $request, Product $product)
    {
        if ($product->status !== 'published') {
            return redirect()->back()->with('error', 'Sản phẩm không khả dụng.');
        }

        if ($product->stock < 1) {
            return redirect()->back()->with('error', 'Sản phẩm đã hết hàng.');
        }

        $quantity = min((int) ($request->quantity ?? 1), $product->stock);

        $userId = $this->cartOwnerId();
        $sessionId = $this->cartSessionId();

        $cart = Cart::withTrashed()
            ->where($userId ? 'user_id' : 'session_id', $userId ?? $sessionId)
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            if ($cart->trashed()) {
                $cart->restore();
            }
            $cart->update(['quantity' => $quantity, 'store_id' => $product->store_id]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'store_id' => $product->store_id,
                'quantity' => $quantity,
            ]);
        }

        return $userId
            ? redirect()->route('agriverse.shop.checkout.index')
            : redirect()->back()->with('success', 'Đã thêm vào giỏ hàng.');
    }
}
