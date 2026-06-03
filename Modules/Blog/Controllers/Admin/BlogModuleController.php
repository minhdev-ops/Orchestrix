<?php

namespace Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Blog\Models\BlogPost;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogModuleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/blog/blog",
     *     tags={"Blog Admin"},
     *     summary="List all blog posts",
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BlogPost")))
     * )
     */
    public function index()
    {
        $posts = BlogPost::with('category')->orderBy('created_at', 'desc')->paginate(10);
        return view('blog::admin.index', compact('posts'));
    }

    public function create()
    {
        $post = new BlogPost();
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return view('blog::admin.form', compact('post', 'categories', 'tags'));
    }

    /**
     * @OA\Post(
     *     path="/admin/blog/blog",
     *     tags={"Blog Admin"},
     *     summary="Create a new blog post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BlogPost")
     *     ),
     *     @OA\Response(response=201, description="Blog post created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content_html' => 'required',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'excerpt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

        $post = BlogPost::create($validated);
        return redirect()->route('admin.blog.index')->with('success', 'Bài viết đã được tạo!');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return view('blog::admin.form', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'content_html' => 'required',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'excerpt' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

        $post->update($validated);
        return redirect()->route('admin.blog.index')->with('success', 'Bài viết đã được cập nhật!');
    }

    /**
     * @OA\Delete(
     *     path="/admin/blog/blog/{blog}",
     *     tags={"Blog Admin"},
     *     summary="Delete a blog post",
     *     @OA\Parameter(name="blog", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Blog post deleted successfully")
     * )
     */
    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Bài viết đã được xóa!');
    }
}
