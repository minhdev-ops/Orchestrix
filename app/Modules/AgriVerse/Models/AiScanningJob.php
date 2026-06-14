<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AiScanningJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'store_id', 'asset_id', 'status', 'result', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'array',
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($job) {
            if (empty($job->uuid)) {
                $job->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function asset()
    {
        return $this->belongsTo(ThreeDAsset::class, 'asset_id');
    }
}
