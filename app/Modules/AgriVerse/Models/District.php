<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'ghn_districts';

    protected $fillable = [
        'district_id',
        'district_name',
        'province_id',
        'code',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_id', 'district_id');
    }
}
