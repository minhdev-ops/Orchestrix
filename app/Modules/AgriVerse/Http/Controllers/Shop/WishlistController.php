<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Wishlist;
use Inertia\Inertia;

class WishlistController
{
    public function index()
    {
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20)
            ->through(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'description' => $item->product->description,
                    'image' => $item->product->image,
                ] : null,
            ]);

        return Inertia::render('Marketplace/Wishlist/Index', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    public function toggle(Product $product)
    {
        if (! auth()->check()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }

            return redirect()->guest(route('login'));
        }

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

    public function remove(Wishlist $wishlist)
    {

        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $wishlist->product()->exists()) {
            abort(404, 'Sản phẩm không tồn tại.');
        }

        $wishlist->delete();

        return back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }
}
