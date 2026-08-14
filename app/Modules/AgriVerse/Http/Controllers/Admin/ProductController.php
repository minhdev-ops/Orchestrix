<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'user']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $query->orderByRaw("CASE WHEN status = 'pending_review' THEN 0 ELSE 1 END")->latest();

        $products = $query->paginate(15);
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'stock' => 'required|integer|min:0',
        ]);

        $validated['user_id'] = $request->input('user_id', auth()->id());

        Product::create($validated);

        return redirect()->route('admin.agriverse.products.index')
            ->with('success', 'Sản phẩm đã được tạo.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->load(['store', 'user', 'categories', 'images', 'reviews.user']);

        return Inertia::render('Admin/Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::active()->get();

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('admin.agriverse.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.agriverse.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }

    public function upload3dModel(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'model' => 'required|file|mimes:glb,gltf,zip|max:51200',
        ]);

        $userId = auth()->id();
        $filename = uniqid().'_'.$request->file('model')->getClientOriginalName();
        $path = $request->file('model')->storeAs("users/{$userId}/products/{$product->id}", $filename, 'public');

        if ($product->model_3d_path) {
            Storage::disk('public')->delete($product->model_3d_path);
        }

        $product->update(['model_3d_path' => $path]);

        return redirect()->route('admin.agriverse.products.edit', $product)
            ->with('success', 'Mô hình 3D đã được tải lên.');
    }

    public function delete3dModel($id)
    {
        $product = Product::findOrFail($id);
        if ($product->model_3d_path) {
            Storage::disk('public')->delete($product->model_3d_path);
            $product->update(['model_3d_path' => null]);
        }

        return redirect()->route('admin.agriverse.products.edit', $product)
            ->with('success', 'Mô hình 3D đã được xóa.');
    }

    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'status' => 'published',
            'reject_reason' => null,
        ]);

        DigitalPassportLog::create([
            'product_id' => $product->id,
            'action' => 'product_approved',
            'data' => [
                'approved_by' => auth()->id(),
                'approved_at' => now()->toDateTimeString(),
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.agriverse.products.index')
            ->with('success', 'Sản phẩm đã được duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $product->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
        ]);

        DigitalPassportLog::create([
            'product_id' => $product->id,
            'action' => 'product_rejected',
            'data' => [
                'rejected_by' => auth()->id(),
                'rejected_at' => now()->toDateTimeString(),
                'reason' => $validated['reject_reason'],
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.agriverse.products.index')
            ->with('success', 'Sản phẩm đã bị từ chối.');
    }
}
