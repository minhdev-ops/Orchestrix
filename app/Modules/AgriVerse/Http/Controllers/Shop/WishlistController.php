<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Wishlist;
use App\Modules\AgriVerse\Models\Product;

class WishlistController
{
    public function index()
    {
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                ] : null,
            ]);

        return Inertia::render('Marketplace/Wishlist/Index', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    public function toggle($id)
    {
        if (!auth()->check()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }
            return redirect()->guest(route('login'));
        }

        $product = Product::findOrFail($id);

        $existing = Wishlist::withTrashed()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $wishlisted = false;
        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $wishlisted = true;
            } else {
                $existing->delete();
            }
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $wishlisted = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['wishlisted' => $wishlisted]);
        }

        return back()->with('success', 'Đã cập nhật danh sách yêu thích.');
    }

    public function remove($id)
    {
        $wishlist = Wishlist::findOrFail($id);

        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }
}
