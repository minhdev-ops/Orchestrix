<?php

namespace App\Modules\AgriVerse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'store_id' => 'nullable|integer|exists:stores,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
