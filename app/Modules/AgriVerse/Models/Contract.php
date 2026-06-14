<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'order_id', 'contract_number', 'content', 'content_hash',
        'file_path', 'signed_by_buyer', 'signed_by_seller', 'signed_at',
        'status', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'signed_by_buyer' => 'boolean',
            'signed_by_seller' => 'boolean',
            'signed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($contract) {
            if (empty($contract->uuid)) {
                $contract->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
