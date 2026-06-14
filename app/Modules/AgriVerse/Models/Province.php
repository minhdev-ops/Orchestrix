<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'ghn_provinces';

    protected $fillable = [
        'province_id',
        'province_name',
        'code',
    ];

    public function districts()
    {
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }
}
