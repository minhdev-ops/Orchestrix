<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\ForumCategory;
use App\Modules\AgriVerse\Models\ForumPost;
use App\Modules\AgriVerse\Models\ForumComment;
use App\Modules\AgriVerse\Models\ForumLike;
use App\Modules\AgriVerse\Services\ForumService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForumController
{
    public function __construct(
        protected ForumService $forumService,
    ) {}

    public function index(Request $request)
    {
        $categories = ForumCategory::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug]);

        $query = ForumPost::with(['user:id,name', 'category:id,name'])
            ->withCount(['comments', 'likes'])
            ->where('status', 'approved');

        if ($categorySlug = $request->category) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $sort = $request->sort ?? 'latest';
        if ($sort === 'popular') {
            $query->orderBy('likes_count', 'desc');
        } else {
            $query->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(12);

        $posts->getCollection()->transform(fn ($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'content' => Str::limit(strip_tags($p->content), 200),
            'category' => $p->category ? ['id' => $p->category->id, 'name' => $p->category->name] : null,
            'user' => ['id' => $p->user->id, 'name' => $p->user->name],
            'comments_count' => $p->comments_count,
            'likes_count' => $p->likes_count,
            'is_pinned' => $p->is_pinned,
            'created_at' => $p->created_at->diffForHumans(),
        ]);

        return Inertia::render('Marketplace/Forum/Index', [
            'categories' => $categories,
            'posts' => $posts,
            'filters' => [
                'category' => $request->category,
                'search' => $request->search,
                'sort' => $sort,
            ],
        ]);
    }

    public function show($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->status !== 'approved') {
            if (!auth()->check() || auth()->id() !== $post->user_id) {
                abort(404);
            }
        }

        $post->load(['user:id,name', 'category:id,name']);
        $post->loadCount(['comments', 'likes']);

        $comments = ForumComment::with('user:id,name')
            ->where('post_id', $post->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'content' => $c->content,
                'user' => ['id' => $c->user->id, 'name' => $c->user->name],
                'created_at' => $c->created_at->diffForHumans(),
            ]);

        $isLiked = auth()->check() && $post->isLikedBy(auth()->id());

        return Inertia::render('Marketplace/Forum/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category ? ['id' => $post->category->id, 'name' => $post->category->name] : null,
                'user' => ['id' => $post->user->id, 'name' => $post->user->name],
                'comments_count' => $post->comments_count,
                'likes_count' => $post->likes_count,
                'is_liked' => $isLiked,
                'created_at' => $post->created_at->diffForHumans(),
                'created_at_raw' => $post->created_at->toIso8601String(),
            ],
            'comments' => $comments,
        ]);
    }

    public function create()
    {
        $categories = ForumCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Marketplace/Forum/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:200',
            'content' => 'required|string',
        ]);

        $post = ForumPost::create([
            'category_id' => $data['category_id'],
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => 'pending',
        ]);

        return redirect()->route('agriverse.shop.forum.show', $post)
            ->with('success', 'Bài viết đã được gửi và chờ admin duyệt.');
    }

    public function edit($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
        if ($post->created_at->diffInHours(Carbon::now()) > 24) {
            return redirect()->route('agriverse.shop.forum.show', $post)
                ->with('error', 'Bài viết đã quá 24 giờ, không thể chỉnh sửa.');
        }

        $categories = ForumCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Marketplace/Forum/Create', [
            'categories' => $categories,
            'post' => [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'title' => $post->title,
                'content' => $post->content,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
        if ($post->created_at->diffInHours(Carbon::now()) > 24) {
            return redirect()->route('agriverse.shop.forum.show', $post)
                ->with('error', 'Bài viết đã quá 24 giờ, không thể chỉnh sửa.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:200',
            'content' => 'required|string',
        ]);

        $post->update([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => 'pending',
        ]);

        return redirect()->route('agriverse.shop.forum.show', $post)
            ->with('success', 'Bài viết đã được cập nhật và gửi lại để duyệt.');
    }

    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('agriverse.shop.forum.index')
            ->with('success', 'Bài viết đã được xóa.');
    }

    public function storeComment(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->status !== 'approved') {
            abort(404);
        }

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = ForumComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        $comment->load('user:id,name');

        $this->forumService->publishComment(
            $post->id,
            auth()->id(),
            auth()->user()->name,
            $data['content']
        );

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => ['id' => $comment->user->id, 'name' => $comment->user->name],
                'created_at' => $comment->created_at->diffForHumans(),
            ]
        ], 201);
    }

    public function toggleLike(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->status !== 'approved') {
            abort(404);
        }

        $userId = auth()->id();
        $like = ForumLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ForumLike::create(['post_id' => $post->id, 'user_id' => $userId]);
            $liked = true;
        }

        $likeCount = $post->likes()->count();

        $this->forumService->publishLike(
            $post->id,
            $userId,
            auth()->user()->name,
            $likeCount
        );

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likeCount,
        ]);
    }
}
