<?php

namespace App\Modules\AgriVerse\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gte:price',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'image' => 'nullable|string|max:500',
            'technical_specs' => 'nullable|array',
            'status' => 'nullable|in:draft,pending_review,published',
        ];
    }
}
