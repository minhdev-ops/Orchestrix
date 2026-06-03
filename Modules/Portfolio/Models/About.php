<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model About - Singleton: thông tin giới thiệu bản thân.
 * Chỉ có 1 record trong bảng. Sử dụng About::getSingleton() để lấy.
 *
 * Relationships:
 *  - stats()       HasMany AboutStat
 *  - experiences() HasMany AboutExperience
 *
 * @property int         $id
 * @property string|null $full_name
 * @property string|null $title
 * @property string|null $subtitle
 * @property string|null $bio
 * @property string|null $description
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $location
 * @property string|null $github_url
 * @property string|null $linkedin_url
 * @property string|null $twitter_url
 * @property string|null $avatar
 * @property string|null $resume_url
 * @property bool        $is_available
 */
class About extends Model
{
    protected $table = 'portfolio_abouts';

    protected $fillable = [
        'full_name',
        'title',
        'subtitle',
        'bio',
        'description',
        'email',
        'phone',
        'location',
        'github_url',
        'linkedin_url',
        'twitter_url',
        'website_url',
        'avatar',
        'resume_url',
        'is_available',
        'availability_status',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // =========================================================================
    // Singleton Pattern
    // =========================================================================

    /**
     * Lấy record About duy nhất, tạo mới nếu chưa có.
     */
    public static function getSingleton(): static
    {
        return static::first() ?? static::create([
            'title' => 'Portfolio',
        ]);
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Các số liệu thống kê trên trang About.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(AboutStat::class, 'about_id');
    }

    /**
     * Các kinh nghiệm làm việc/học vấn.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(AboutExperience::class, 'about_id');
    }
}
