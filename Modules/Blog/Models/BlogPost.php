<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BlogPost",
    title: "Blog Post Model",
    description: "Main blog post content",
    properties: [
        new OA\Property(property: "id", type: "integer", readOnly: true),
        new OA\Property(property: "title", type: "string"),
        new OA\Property(property: "slug", type: "string"),
        new OA\Property(property: "content", type: "string"),
        new OA\Property(property: "status", type: "string"),
    ]
)]
class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'content_md',
        'content_html',
        'thumbnail',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'views_count',
        'likes_count',
        'comments_count',
        'reading_time',
        'is_published',
        'is_featured',
        'author',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && $post->content_md) {
                $post->excerpt = Str::limit(strip_tags($post->content_md), 180);
            }
            if ($post->content_md) {
                $wordCount = str_word_count(strip_tags($post->content_md));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });
        static::updating(function ($post) {
            if ($post->content_md) {
                $wordCount = str_word_count(strip_tags($post->content_md));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'blog_post_id', 'blog_tag_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->where('is_approved', true);
    }

    public function allComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function getPublishedAtFormattedAttribute(): string
    {
        if ($this->published_at) {
            return $this->published_at->format('d/m/Y');
        }

        return $this->created_at ? $this->created_at->format('d/m/Y') : date('d/m/Y');
    }
}
