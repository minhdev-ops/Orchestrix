<?php
namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GardenPlant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'garden_id', 'zone_id', 'product_id', 'order_id',
        'name', 'species', 'image_url', 'stage', 'planted_at',
        'hydration_value', 'nutrient_value',
        'last_watered_at', 'last_fertilized_at',
        'health_status', 'position_x', 'position_y',
    ];

    protected function casts(): array
    {
        return [
            'planted_at' => 'datetime',
            'last_watered_at' => 'datetime',
            'last_fertilized_at' => 'datetime',
            'hydration_value' => 'integer',
            'nutrient_value' => 'integer',
            'position_x' => 'integer',
            'position_y' => 'integer',
        ];
    }

    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(GardenZone::class, 'zone_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
