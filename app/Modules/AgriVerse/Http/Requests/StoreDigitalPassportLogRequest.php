<?php

namespace App\Modules\AgriVerse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDigitalPassportLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'action' => 'required|string|max:100',
            'data' => 'nullable|array',
        ];
    }
}
