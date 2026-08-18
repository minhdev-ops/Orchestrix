<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class CareGuide extends Model
{
    protected $fillable = [
        'guide_topic', 'guide_url', 'vietnamese_title', 'key_content',
    ];
}
