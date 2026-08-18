<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specimen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'code', 'location', 'image_url', 'status',
        'hydration_value', 'hydration_label', 'hydration_error',
        'nutrient_value', 'nutrient_label', 'nutrient_error', 'nutrient_muted',
    ];

    protected function casts(): array
    {
        return [
            'hydration_error' => 'boolean',
            'nutrient_error' => 'boolean',
            'nutrient_muted' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
