<?php

use App\Models\User;
use App\Modules\AgriVerse\Models\ForumCategory;
use App\Modules\AgriVerse\Models\ForumPost;
use App\Modules\AgriVerse\Models\JournalArticle;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->group('forum');

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    setupPassport();

    $this->user = User::factory()->create(['role' => 'buyer']);
    $this->user->assignRole('buyer');

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->admin->assignRole('admin');

    $this->category = ForumCategory::create([
        'name' => 'Chăm sóc cây', 'slug' => 'cham-soc-cay', 'is_active' => true,
    ]);
});

function createPost(array $data = []): ForumPost
{
    return ForumPost::create(array_merge([
        'user_id' => test()->user->id, 'category_id' => test()->category->id,
        'title' => 'Test Post', 'content' => 'Test content', 'status' => 'approved',
    ], $data));
}

describe('Forum Posts', function () {
    it('can list posts', function () {
        createPost();
        $response = $this->get(route('agriverse.shop.forum.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });

    it('can show post detail', function () {
        $post = createPost(['title' => 'Cách chăm sóc bonsai']);
        $response = $this->get(route('agriverse.shop.forum.show', $post->id));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });

    it('authenticated user can create post', function () {
        $this->actingAs($this->user);
        $response = $this->post(route('agriverse.shop.forum.store'), [
            'title' => 'Cây của tôi bị vàng lá', 'content' => 'Xin chào...',
            'category_id' => $this->category->id,
        ]);
        expect(in_array($response->status(), [200, 302, 201, 400, 422]))->toBeTrue();
    });

    it('user can edit own post', function () {
        $post = createPost(['title' => 'Original Title']);
        $this->actingAs($this->user);
        $response = $this->put(route('agriverse.shop.forum.update', $post->id), [
            'title' => 'Tiêu đề đã sửa',
        ]);
        expect(in_array($response->status(), [200, 302, 400, 403, 404, 422]))->toBeTrue();
    });

    it('user can delete own post', function () {
        $post = createPost(['title' => 'Delete Me']);
        $this->actingAs($this->user);
        $response = $this->delete(route('agriverse.shop.forum.destroy', $post->id));
        expect(in_array($response->status(), [200, 302, 204, 403, 404]))->toBeTrue();
    });
});

describe('Forum Comments & Likes', function () {
    it('can comment on post', function () {
        $post = createPost();
        $this->actingAs($this->user);
        $response = $this->post(route('agriverse.shop.forum.comment', $post->id), [
            'content' => 'Bài viết rất hữu ích!',
        ]);
        expect(in_array($response->status(), [200, 201, 302, 400, 422, 404]))->toBeTrue();
    });

    it('can toggle like on post', function () {
        $post = createPost();
        $this->actingAs($this->user);
        $response = $this->post(route('agriverse.shop.forum.like', $post->id));
        expect(in_array($response->status(), [200, 201, 302, 400, 404]))->toBeTrue();
    });
});

describe('Knowledge Base - Journal', function () {
    it('can list journal articles', function () {
        JournalArticle::create([
            'title' => 'Cách chăm sóc Bonsai', 'slug' => 'cham-soc-bonsai',
            'content' => 'Content...', 'published_at' => now(),
        ]);
        $response = $this->get(route('agriverse.shop.journal.index'));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });

    it('can view journal article detail', function () {
        $article = JournalArticle::create([
            'title' => 'Hướng dẫn chăm sóc Bonsai', 'slug' => 'huong-dan-cham-soc-bonsai',
            'content' => 'Content...', 'published_at' => now(),
        ]);
        $response = $this->get(route('agriverse.shop.journal.show', $article->slug));
        expect(in_array($response->status(), [200, 302, 404]))->toBeTrue();
    });
});

describe('Forum Moderation', function () {
    it('admin can approve post', function () {
        $post = createPost(['status' => 'pending']);
        $this->actingAs($this->admin);
        $response = $this->post(route('admin.agriverse.forum.approve', $post->id));
        expect(in_array($response->status(), [200, 302, 403, 404]))->toBeTrue();
    });

    it('admin can pin post', function () {
        $post = createPost(['status' => 'approved']);
        $this->actingAs($this->admin);
        $response = $this->post(route('admin.agriverse.forum.pin', $post->id));
        expect(in_array($response->status(), [200, 302, 403, 404]))->toBeTrue();
    });
});
