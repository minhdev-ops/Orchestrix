<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Model Project - Đại diện cho một dự án trong Portfolio.
 *
 * Relationships:
 *  - N/A (standalone entity)
 *
 * Scopes:
 *  - visible()  - Chỉ lấy projects đang hiển thị
 *  - featured() - Chỉ lấy projects được featured
 *
 * @property int         $id
 * @property string      $title
 * @property string      $slug
 * @property string      $category
 * @property string|null $description
 * @property string|null $content_md
 * @property string|null $image
 * @property string|null $link
 * @property string|null $github_url
 * @property array|null  $tech_stack
 * @property bool        $is_featured
 * @property bool        $is_visible
 * @property int         $sort_order
 * @property int         $view_count
 */
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Project",
    title: "Portfolio Project Model",
    description: "Work project showcase",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true),
        new OA\Property(property: "title", type: "string"),
        new OA\Property(property: "slug", type: "string"),
        new OA\Property(property: "description", type: "string"),
    ]
)]
class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'portfolio_projects';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'content_md',
        'image',
        'link',
        'github_url',
        'tech_stack',
        'is_featured',
        'is_visible',
        'sort_order',
        'view_count',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'tech_stack'   => 'array',
        'is_featured'  => 'boolean',
        'is_visible'   => 'boolean',
        'sort_order'   => 'integer',
        'view_count'   => 'integer',
        'started_at'   => 'date',
        'completed_at' => 'date',
    ];

    protected $attributes = [
        'is_featured'  => false,
        'is_visible'   => true,
        'sort_order'   => 0,
        'view_count'   => 0,
    ];

    // =========================================================================
    // Boot - Auto-generate slug
    // =========================================================================

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project): void {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function (Project $project): void {
            if ($project->isDirty('title') && ! $project->isDirty('slug')) {
                $project->slug = Str::slug($project->title);
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

    /**
     * Scope: chỉ lấy projects đang hiển thị.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope: chỉ lấy projects được đánh dấu featured.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: lọc theo category.
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: sắp xếp theo thứ tự hiển thị.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // =========================================================================
    // Accessors & Mutators
    // =========================================================================

    /**
     * Tăng view_count thêm 1.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Lấy danh sách các tech categories duy nhất.
     */
    public static function getAllCategories(): array
    {
        return static::distinct()->pluck('category')->sort()->values()->toArray();
    }
}
