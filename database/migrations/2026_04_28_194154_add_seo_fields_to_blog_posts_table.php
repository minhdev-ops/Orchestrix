<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $blueprint) {
            $blueprint->string('seo_title')->nullable()->after('title');
            $blueprint->text('seo_description')->nullable()->after('excerpt');
            $blueprint->string('seo_keywords')->nullable()->after('seo_description');
            $blueprint->text('content_html')->nullable()->after('content_md');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['seo_title', 'seo_description', 'seo_keywords', 'content_html']);
        });
    }
};
