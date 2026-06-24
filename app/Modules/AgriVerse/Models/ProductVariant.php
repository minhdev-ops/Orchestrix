<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'sku', 'name', 'price', 'compare_price', 'stock',
        'weight', 'image', 'attributes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'attributes' => 'array',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return $this->product->image_url;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }
        return round((1 - $this->price / $this->compare_price) * 100);
    }

    public function getAttributesDisplayAttribute(): string
    {
        if (!$this->attributes) return '';
        return collect($this->attributes)->map(fn($v, $k) => "{$k}: {$v}")->implode(', ');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
