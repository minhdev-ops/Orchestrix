<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticSymptom extends Model
{
    protected $fillable = ['name', 'description', 'category', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
