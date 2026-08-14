<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\ForumCategory;
use App\Modules\AgriVerse\Models\ForumComment;
use App\Modules\AgriVerse\Models\ForumLike;
use App\Modules\AgriVerse\Models\ForumPost;
use App\Modules\AgriVerse\Services\ForumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForumController
{
    public function __construct(
        protected ForumService $forumService,
    ) {}

    public function categories(): JsonResponse
    {
        $categories = ForumCategory::where('is_active', true)
            ->withCount(['approvedPosts'])
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function posts(Request $request): JsonResponse
    {
        $query = ForumPost::with(['user:id,name,avatar', 'category:id,name,slug'])
            ->where('status', 'approved');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json($posts);
    }

    public function show(ForumPost $post): JsonResponse
    {
        if ($post->status !== 'approved' && auth()->id() !== $post->user_id && ! auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->load(['user:id,name,avatar', 'category:id,name,slug', 'comments.user:id,name,avatar']);
        $post->is_liked = $post->isLikedBy(auth()->id());

        return response()->json($post);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:200',
            'content' => 'required|string|max:10000',
        ]);

        $post = ForumPost::create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'post' => $post->load(['user:id,name,avatar', 'category:id,name,slug']),
            'message' => 'Bài viết đã được gửi, chờ duyệt',
        ], 201);
    }

    public function update(Request $request, ForumPost $post): JsonResponse
    {
        if (auth()->id() !== $post->user_id && ! auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:200',
            'content' => 'sometimes|string|max:10000',
            'category_id' => 'sometimes|exists:forum_categories,id',
        ]);

        $post->update($data);

        if (auth()->user()->hasRole('admin') && $request->has('status')) {
            $post->update(['status' => $request->status]);
        }

        return response()->json($post->load(['user:id,name,avatar', 'category:id,name,slug']));
    }

    public function destroy(ForumPost $post): JsonResponse
    {
        if (auth()->id() !== $post->user_id && ! auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Đã xóa bài viết']);
    }

    public function comments(Request $request, ForumPost $post): JsonResponse
    {
        $comments = ForumComment::where('post_id', $post->id)
            ->with('user:id,name,avatar')
            ->orderBy('created_at')
            ->paginate($request->per_page ?? 20);

        return response()->json($comments);
    }

    public function addComment(Request $request, ForumPost $post): JsonResponse
    {
        if ($post->status !== 'approved') {
            return response()->json(['error' => 'Post not available'], 404);
        }

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = ForumComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        $this->forumService->publishComment(
            $post->id,
            $request->user()->id,
            $request->user()->name,
            $data['content']
        );

        return response()->json([
            'comment' => $comment->load('user:id,name,avatar'),
        ], 201);
    }

    public function toggleLike(Request $request, ForumPost $post): JsonResponse
    {
        $userId = $request->user()->id;

        $like = ForumLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ForumLike::create(['post_id' => $post->id, 'user_id' => $userId]);
            $liked = true;
        }

        $likeCount = ForumLike::where('post_id', $post->id)->count();

        $this->forumService->publishLike(
            $post->id,
            $userId,
            $request->user()->name,
            $likeCount
        );

        return response()->json([
            'liked' => $liked,
            'like_count' => $likeCount,
        ]);
    }

    public function myPosts(Request $request): JsonResponse
    {
        $posts = ForumPost::where('user_id', $request->user()->id)
            ->with(['category:id,name,slug'])
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 15);

        return response()->json($posts);
    }
}
