<?php

namespace App\Modules\AgriVerse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'status' => 'nullable|in:draft,published,archived',
            'metadata' => 'nullable|array',
            'technical_specs' => 'nullable|array',
            'technical_specs.engine' => 'nullable|string|max:255',
            'technical_specs.dimensions' => 'nullable|string|max:255',
            'technical_specs.performance' => 'nullable|string|max:255',
            'technical_specs.classification' => 'nullable|string|max:255',
            'technical_specs.features' => 'nullable|array',
            'technical_specs.features.*' => 'string|max:255',
            'technical_specs.certifications' => 'nullable|array',
            'technical_specs.certifications.*' => 'string|max:255',
            'technical_specs.warranty_months' => 'nullable|integer|min:0|max:600',
            'technical_specs.year_of_manufacture' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'store_id' => 'nullable|integer|exists:stores,id',
            'stock' => 'nullable|integer|min:0',
        ];
    }
}
