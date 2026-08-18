<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->paginate(20);

        return JsonResource::collection($coupons);
    }

    public function show(Coupon $coupon): JsonResource
    {
        return JsonResource::make($coupon);
    }

    public function validate(Request $request): JsonResource
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $data['code'])->first();

        if (! $coupon) {
            abort(404, 'Coupon not found.');
        }

        if (! $coupon->isValid()) {
            abort(422, 'Coupon is expired or inactive.');
        }

        if ($data['order_amount'] < $coupon->min_order_amount) {
            abort(422, 'Minimum order amount is '.number_format($coupon->min_order_amount, 0).'đ');
        }

        $discount = $coupon->calculateDiscount($data['order_amount']);

        return JsonResource::make([
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => $data['order_amount'] - $discount,
        ]);
    }
}
