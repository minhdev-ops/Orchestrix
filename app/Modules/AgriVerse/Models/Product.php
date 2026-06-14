<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'user_id', 'store_id', 'name', 'slug', 'image', 'description',
        'price', 'compare_price', 'category', 'tags',
        'status', 'is_featured', 'technical_specs', 'metadata', 'stock',
        'model_3d_path', 'manufacturer_id', 'product_type_id',
        'reject_reason',
    ];

    protected $appends = ['model_3d_url'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'tags' => 'array',
            'technical_specs' => 'array',
            'metadata' => 'array',
            'stock' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(6);
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

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function assets()
    {
        return $this->hasMany(ThreeDAsset::class, 'product_id');
    }

    public function passportLogs()
    {
        return $this->hasMany(DigitalPassportLog::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_product');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->approved()->avg('rating');
    }

    public function reviewsCount()
    {
        return $this->reviews()->approved()->count();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getModel3dUrlAttribute()
    {
        if (!$this->model_3d_path) {
            return null;
        }
        return '/storage/' . ltrim($this->model_3d_path, '/');
    }
}
