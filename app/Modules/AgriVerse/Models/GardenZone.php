<?php
namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GardenZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'garden_id', 'name', 'icon', 'plant_count', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'plant_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function garden(): BelongsTo
    {
        return $this->belongsTo(Garden::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(GardenPlant::class, 'zone_id');
    }
}
