<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PlantDiagnosis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'image_path',
        'plant_name',
        'disease_name',
        'confidence',
        'severity',
        'description',
        'treatments',
        'prevention',
        'raw_response',
        'provider',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'treatments' => 'array',
            'prevention' => 'array',
            'raw_response' => 'array',
            'metadata' => 'array',
            'confidence' => 'float',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
