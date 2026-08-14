<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;

class SustainabilityReport extends Model
{
    protected $fillable = [
        'year', 'carbon_offset_target', 'carbon_offset_actual', 'carbon_trend',
        'reforestation_total', 'reforestation_status',
        'packaging_sustainable_percent', 'circularity_percent',
        'supply_regions_tracked', 'audit_rating',
        'ev_delivery_percent', 'water_recovered_gallons', 'pdf_url',
        'title', 'description', 'hero_image_url', 'quote', 'quote_author',
        'ethical_description', 'circular_description',
    ];

    protected $casts = [
        'year' => 'integer',
    ];
}
