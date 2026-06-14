<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\ForumCategory;
use Illuminate\Support\Str;

class ForumCategoryController
{
    public function index()
    {
        $categories = ForumCategory::withCount('posts')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'is_active' => $c->is_active,
                'posts_count' => $c->posts_count,
            ]);

        return Inertia::render('Admin/ForumCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:forum_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        ForumCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', 'Đã thêm danh mục');
    }

    public function update(Request $request, ForumCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:forum_categories,name,' . $category->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? $category->is_active,
        ]);

        return back()->with('success', 'Đã cập nhật danh mục');
    }

    public function destroy(ForumCategory $category)
    {
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có bài viết');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục');
    }
}
