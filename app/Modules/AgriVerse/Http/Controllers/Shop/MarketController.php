<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketController
{
    /**
     * Trang Flash Deal — dữ liệu thời gian thực nhận qua /ws/market.
     * Server chỉ chọn deal đang hoạt động (nếu Redis trả đầy đủ),
     * MỘT số lịch sử flash deal lấy từ Redis khi client không connect.
     */
    public function flashDealIndex()
    {
        return Inertia::render('Marketplace/FlashDeal/Index');
    }

    /**
     * Trang Đề xuất giá (Offer) — kèm danh sách sản phẩm của người bán khác
     * để người mua có thể đề xuất giá ngay khi vào trang.
     */
    public function offerIndex()
    {
        $userId = auth()->id();

        $pickable = Product::published()
            ->where('user_id', '!=', $userId)
            ->limit(50)
            ->get(['id', 'name', 'image', 'price', 'user_id'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'image' => $p->image,
                'price' => $p->price,
                'seller_id' => $p->user_id,
                'seller_name' => $p->user->name ?? 'Người bán',
            ])
            ->values();

        return Inertia::render('Marketplace/Offer/Index', [
            'pickableProducts' => $pickable,
        ]);
    }

    public function pickableProducts()
    {
        $userId = auth()->id();

        $products = Product::published()
            ->where('user_id', '!=', $userId)
            ->limit(50)
            ->get(['id', 'name', 'image', 'price', 'user_id'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'image' => $p->image,
                'price' => $p->price,
                'seller_id' => $p->user_id,
                'seller_name' => $p->user->name ?? 'Người bán',
            ])
            ->values();

        return response()->json(['products' => $products]);
    }
}