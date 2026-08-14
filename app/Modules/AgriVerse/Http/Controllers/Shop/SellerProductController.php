<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Manufacturer;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\ProductType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerProductController
{
    public function index()
    {
        $products = Product::with('store')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return Inertia::render('Marketplace/Seller/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        return Inertia::render('Marketplace/Seller/Products/Form', [
            'product' => null,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'productTypes' => ProductType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technical_specs' => 'nullable|json',
            'metadata' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'model_3d_path' => 'nullable|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'manufacturer_id' => 'nullable|integer|exists:manufacturers,id',
            'is_featured' => 'boolean',
            'status' => 'nullable|string|in:draft,pending_review',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'pending_review';
        $validated['store_id'] = auth()->user()->stores()->first()?->id;

        if (! empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Decode JSON fields
        if (! empty($validated['technical_specs']) && is_string($validated['technical_specs'])) {
            $validated['technical_specs'] = json_decode($validated['technical_specs'], true);
        }
        if (! empty($validated['metadata']) && is_string($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        $product = Product::create($validated);

        if (! empty($validated['category'])) {
            $cat = Category::where('name', $validated['category'])->first();
            if ($cat) {
                $product->categories()->attach($cat->id);
            }
        }

        DigitalPassportLog::create([
            'product_id' => $product->id,
            'action' => 'product_submitted',
            'data' => [
                'submitted_by' => auth()->id(),
                'submitted_at' => now()->toDateTimeString(),
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('agriverse.shop.seller.products.index')
            ->with('success', 'Sản phẩm đã được gửi đi duyệt.');
    }

    public function edit(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Marketplace/Seller/Products/Form', [
            'product' => $product->load('categories'),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'productTypes' => ProductType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technical_specs' => 'nullable|json',
            'metadata' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'model_3d_path' => 'nullable|string|max:255',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'manufacturer_id' => 'nullable|integer|exists:manufacturers,id',
            'is_featured' => 'boolean',
            'status' => 'nullable|string|in:draft,pending_review',
        ]);

        if (! empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Decode JSON fields
        if (! empty($validated['technical_specs']) && is_string($validated['technical_specs'])) {
            $validated['technical_specs'] = json_decode($validated['technical_specs'], true);
        }
        if (! empty($validated['metadata']) && is_string($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        $validated['status'] = $validated['status'] ?? 'pending_review';
        $validated['reject_reason'] = null;

        $product->update($validated);

        if (! empty($validated['category'])) {
            $cat = Category::where('name', $validated['category'])->first();
            if ($cat) {
                $product->categories()->sync([$cat->id]);
            }
        }

        DigitalPassportLog::create([
            'product_id' => $product->id,
            'action' => 'product_resubmitted',
            'data' => [
                'resubmitted_by' => auth()->id(),
                'resubmitted_at' => now()->toDateTimeString(),
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('agriverse.shop.seller.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật và gửi lại duyệt.');
    }

    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('agriverse.shop.seller.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }
}
