<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['step', 'question_text', 'choice_type', 'choices', 'sort_order'];

    protected function casts(): array
    {
        return [
            'choices' => 'array',
        ];
    }
}
