<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ThreeDAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = '3d_assets';

    protected $fillable = [
        'uuid', 'user_id', 'product_id',
        'original_filename', 'original_path',
        'compressed_filename', 'compressed_path',
        'format', 'asset_type',
        'compression_status', 'compression_settings',
        'file_size', 'compressed_file_size',
        'thumbnail_path', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'compressed_file_size' => 'integer',
            'compression_settings' => 'array',
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->uuid)) {
                $asset->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
