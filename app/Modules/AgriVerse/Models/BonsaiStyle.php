<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class BonsaiStyle extends Model
{
    protected $fillable = [
        'name_vn', 'name_jp', 'description', 'section',
    ];
}
