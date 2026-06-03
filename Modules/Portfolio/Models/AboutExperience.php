<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AboutExperience - Kinh nghiệm làm việc / học vấn / chứng chỉ.
 *
 * @property int         $id
 * @property int|null    $about_id
 * @property string      $type         ('work'|'education'|'certification')
 * @property string      $title
 * @property string      $organization
 * @property string|null $location
 * @property string|null $description
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property bool        $is_current
 * @property string|null $badge_url
 * @property string|null $certificate_url
 * @property int         $sort_order
 */
class AboutExperience extends Model
{
    protected $table = 'portfolio_about_experiences';

    protected $fillable = [
        'about_id',
        'type',
        'title',
        'organization',
        'location',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'badge_url',
        'certificate_url',
        'sort_order',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
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
        return $query->orderBy('sort_order')->orderByDesc('start_date');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWorkExperiences($query)
    {
        return $this->scopeOfType($query, 'work');
    }

    public function scopeEducations($query)
    {
        return $this->scopeOfType($query, 'education');
    }

    public function scopeCertifications($query)
    {
        return $this->scopeOfType($query, 'certification');
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Hiển thị thời gian theo định dạng "Jan 2022 - Present" hoặc "Jan 2022 - Dec 2023".
     */
    public function getDurationAttribute(): string
    {
        $start = $this->start_date?->format('M Y') ?? '';
        $end   = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? '');

        return $start && $end ? "{$start} - {$end}" : $start;
    }
}
