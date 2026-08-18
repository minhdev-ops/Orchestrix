<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\ForumPost;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController
{
    public function index(Request $request)
    {
        $query = ForumPost::with(['user:id,name', 'category:id,name'])
            ->withCount(['comments', 'likes']);

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);

        $posts->getCollection()->transform(fn ($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'category' => $p->category->name ?? '',
            'user' => ['id' => $p->user->id, 'name' => $p->user->name],
            'status' => $p->status,
            'is_pinned' => $p->is_pinned,
            'comments_count' => $p->comments_count,
            'likes_count' => $p->likes_count,
            'created_at' => $p->created_at?->toIso8601String(),
        ]);

        return Inertia::render('Admin/Forum/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(ForumPost $post)
    {
        $post->load(['user:id,name', 'category:id,name']);
        $post->loadCount(['comments', 'likes']);

        return Inertia::render('Admin/Forum/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category->name ?? '',
                'user' => ['id' => $post->user->id, 'name' => $post->user->name],
                'status' => $post->status,
                'is_pinned' => $post->is_pinned,
                'comments_count' => $post->comments_count,
                'likes_count' => $post->likes_count,
                'reject_reason' => $post->reject_reason,
                'created_at' => $post->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function approve(ForumPost $post)
    {
        $post->update([
            'status' => 'approved',
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Trạng thái bài viết đã được cập nhật.');
    }

    public function reject(Request $request, ForumPost $post)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $post->update([
            'status' => 'rejected',
            'reject_reason' => $data['reason'],
        ]);

        return back()->with('success', 'Bài viết đã bị từ chối.');
    }

    public function pin(ForumPost $post)
    {
        $post->update(['is_pinned' => ! $post->is_pinned]);

        return back()->with('success', $post->is_pinned ? 'Bài viết đã được ghim.' : 'Bài viết đã bỏ ghim.');
    }

    public function destroy(ForumPost $post)
    {
        $post->delete();

        return redirect()->route('admin.agriverse.forum.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}
