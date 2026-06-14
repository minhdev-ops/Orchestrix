<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tag')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_role')->nullable();
            $table->string('author_institution')->nullable();
            $table->text('abstract')->nullable();
            $table->longText('content')->nullable();
            $table->text('blockquote_text')->nullable();
            $table->string('blockquote_author')->nullable();
            $table->string('doi')->nullable();
            $table->boolean('is_peer_reviewed')->default(true);
            $table->integer('read_time_minutes')->default(10);
            $table->integer('citation_count')->default(0);
            $table->json('citations')->nullable();
            $table->json('related_specimens')->nullable();
            $table->string('pdf_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_articles');
    }
};
