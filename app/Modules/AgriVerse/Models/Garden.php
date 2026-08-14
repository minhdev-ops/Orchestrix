<?php
namespace App\Modules\AgriVerse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Garden extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'total_plants', 'health_score', 'grade',
    ];

    protected function casts(): array
    {
        return [
            'total_plants' => 'integer',
            'health_score' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(GardenZone::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(GardenPlant::class);
    }
}
