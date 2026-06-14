<?php

namespace App\Modules\AgriVerse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThreeDAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'original_filename' => 'required|string|max:255',
            'file' => 'required|file|max:204800',
            'format' => 'nullable|string|max:50',
            'asset_type' => 'nullable|string|max:50',
        ];
    }
}
