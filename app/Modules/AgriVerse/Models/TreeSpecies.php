<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class TreeSpecies extends Model
{
    protected $fillable = [
        'species_vn', 'species_latin', 'family', 'category_type', 'type',
        'origin', 'max_height', 'light_requirements', 'min_temp',
        'watering_needs', 'soil_ph', 'repotting_freq', 'propagation_methods',
        'common_pests', 'flower_color', 'bloom_season', 'fertilizing_guide',
        'pruning_wiring', 'indoor_outdoor', 'meaning_fengshui',
        'image_urls', 'description', 'key_features', 'source_url',
    ];

    protected function casts(): array
    {
        return [
            'image_urls' => 'array',
            'max_height' => 'integer',
            'min_temp' => 'integer',
        ];
    }
}
