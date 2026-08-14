<?php

namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnershipHistory extends Model
{
    protected $table = 'ownership_history';

    protected $fillable = [
        'product_id',
        'from_user_id',
        'to_user_id',
        'action',
        'tx_hash',
        'transfer_price',
        'metadata',
        'transfer_date',
    ];

    protected function casts(): array
    {
        return [
            'transfer_price' => 'decimal:2',
            'metadata' => 'array',
            'transfer_date' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
