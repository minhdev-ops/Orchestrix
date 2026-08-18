<?php

namespace App\Modules\AgriVerse\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class AddForumCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|integer|exists:forum_comments,id',
        ];
    }
}
