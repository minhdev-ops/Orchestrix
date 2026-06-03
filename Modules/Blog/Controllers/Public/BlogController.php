<?php

namespace Modules\Blog\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/blog/latest",
     *     tags={"Blog Public"},
     *     summary="Get latest blog posts",
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function latest()
    {
        $posts = BlogPost::published()
            ->with('category')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'date' => $post->published_at_formatted,
                    'excerpt' => $post->excerpt,
                    'category' => $post->category?->name ?? 'System',
                    'image' => $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=1200',
                    'url' => route('blog.show', [$post->category->slug, $post->slug]),
                ];
            });

        return response()->json($posts);
    }

    public function index()
    {
        $posts = BlogPost::published()->with('category')->orderBy('published_at', 'desc')->paginate(10);
        $featured = BlogPost::published()->with('category')->featured()->take(2)->get();
        $categories = BlogCategory::withCount('posts')->get();
        $featuredPost = $featured->first();

        return view('blog::index', compact('posts', 'featured', 'categories', 'featuredPost'));
    }

    public function show(string $category_slug, string $post_slug)
    {
        $post = BlogPost::where('slug', $post_slug)->firstOrFail();
        $post->load('category');
        $post->incrementViews();
        $relatedPosts = BlogPost::published()
            ->with('category')
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->take(3)
            ->get();

        return view('blog::show', compact('post', 'relatedPosts'));
    }

    public function byCategory(BlogCategory $category)
    {
        $posts = $category->posts()->published()->with('category')->paginate(10);
        $categories = BlogCategory::withCount('posts')->get();
        $featured = BlogPost::published()->with('category')->featured()->take(2)->get();
        $featuredPost = $featured->first();

        return view('blog::index', compact('posts', 'category', 'categories', 'featured', 'featuredPost'));
    }

    public function like(BlogPost $blog_post)
    {
        $blog_post->incrementLikes();
        return response()->json(['likes' => $blog_post->likes_count]);
    }

    public function comment(Request $request, BlogPost $blog_post)
    {
        // Placeholder for comment logic
        return back()->with('success', 'Bình luận của bạn đang được duyệt.');
    }
}
