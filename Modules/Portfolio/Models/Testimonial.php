<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Testimonial - Lời chứng thực từ khách hàng/đồng nghiệp.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $position
 * @property string|null $company
 * @property string|null $avatar
 * @property string      $content
 * @property int         $rating
 * @property string|null $linkedin_url
 * @property bool        $is_visible
 * @property int         $sort_order
 */
class Testimonial extends Model
{
    protected $table = 'portfolio_testimonials';

    protected $fillable = [
        'name',
        'position',
        'company',
        'avatar',
        'content',
        'rating',
        'linkedin_url',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
