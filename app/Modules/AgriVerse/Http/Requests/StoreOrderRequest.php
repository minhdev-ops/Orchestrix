<?php

namespace App\Modules\AgriVerse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'seller_id' => 'required|integer|exists:users,id',
            'store_id' => 'nullable|integer|exists:stores,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string|max:50|exists:coupons,code',
        ];
    }
}
