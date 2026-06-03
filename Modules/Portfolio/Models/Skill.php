<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Model Skill - Đại diện cho một kỹ năng kỹ thuật.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $slug
 * @property string      $category
 * @property string|null $description
 * @property string|null $content_md
 * @property string|null $icon
 * @property string|null $icon_url
 * @property int         $level      (0-100)
 * @property bool        $is_visible
 * @property bool        $is_featured
 * @property int         $sort_order
 */
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Skill",
    title: "Portfolio Skill Model",
    description: "Technical or soft skill",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "level", type: "integer"),
    ]
)]
class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'portfolio_skills';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'content_md',
        'icon',
        'icon_url',
        'level',
        'is_visible',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_visible'  => 'boolean',
        'is_featured' => 'boolean',
        'level'       => 'integer',
        'sort_order'  => 'integer',
    ];

    protected $attributes = [
        'level'      => 80,
        'is_visible' => true,
        'is_featured' => false,
        'sort_order' => 0,
    ];

    // =========================================================================
    // Boot - Auto-generate slug
    // =========================================================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Skill $skill): void {
            if (empty($skill->slug)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }

    // =========================================================================
    // Route Model Binding
    // =========================================================================

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Lấy tất cả categories kỹ năng.
     */
    public static function getAllCategories(): array
    {
        return static::visible()->distinct()->pluck('category')->sort()->values()->toArray();
    }

    /**
     * Lấy skills nhóm theo category.
     */
    public static function getGroupedByCategory(): \Illuminate\Support\Collection
    {
        return static::visible()->ordered()->get()->groupBy('category');
    }
}
