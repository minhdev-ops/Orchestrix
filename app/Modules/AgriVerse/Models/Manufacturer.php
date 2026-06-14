<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Manufacturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'origin_country', 'description',
        'logo', 'website', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($manufacturer) {
            if (empty($manufacturer->slug)) {
                $manufacturer->slug = Str::slug($manufacturer->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
