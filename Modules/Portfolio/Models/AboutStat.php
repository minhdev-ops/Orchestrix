<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AboutStat - Số liệu thống kê trong trang About.
 * Ví dụ: "5+ Years Experience", "20+ Projects".
 *
 * @property int         $id
 * @property int|null    $about_id
 * @property string      $label
 * @property string      $value
 * @property string|null $icon
 * @property int         $sort_order
 */
class AboutStat extends Model
{
    protected $table = 'portfolio_about_stats';

    protected $fillable = [
        'about_id',
        'label',
        'value',
        'icon',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function about(): BelongsTo
    {
        return $this->belongsTo(About::class, 'about_id');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
