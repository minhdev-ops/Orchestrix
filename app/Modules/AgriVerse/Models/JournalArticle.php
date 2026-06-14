<?php

namespace App\Modules\AgriVerse\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalArticle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'tag', 'hero_image_url',
        'author_name', 'author_role', 'author_institution',
        'abstract', 'content', 'blockquote_text', 'blockquote_author',
        'doi', 'is_peer_reviewed', 'read_time_minutes', 'citation_count',
        'citations', 'related_specimens', 'pdf_url', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_peer_reviewed' => 'boolean',
            'citations' => 'array',
            'related_specimens' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
