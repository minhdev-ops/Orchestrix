<?php

namespace App\Modules\AgriVerse\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'category_id' => 'nullable|integer|exists:forum_categories,id',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50',
        ];
    }
}
