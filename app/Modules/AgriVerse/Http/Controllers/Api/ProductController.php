<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Http\Resources\ProductResource;
use App\Modules\AgriVerse\Http\Requests\StoreProductRequest;
use App\Modules\AgriVerse\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()->with(['user', 'store', 'assets']);

        if ($request->user()->hasRole('seller')) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->user()->hasRole('employee')) {
            $storeIds = $request->user()->stores->pluck('id');
            $query->whereIn('store_id', $storeIds);
        }

        if (!$request->user()->hasRole('admin')) {
            $query->where('status', 'published');
        }

        if ($request->filled('status') && $request->user()->hasRole('admin')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sortField = in_array($request->sort, ['price', 'name', 'created_at']) ? $request->sort : 'created_at';
        $sortDir = $request->dir === 'asc' ? 'asc' : 'desc';

        $products = $query->orderBy($sortField, $sortDir)->paginate($request->per_page ?? 15);

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        if ($product->status !== 'published' && !$request->user()->hasRole('admin')) {
            if ($request->user()->hasRole('seller') && $product->user_id !== $request->user()->id) {
                abort(404);
            }
            if ($request->user()->hasRole('buyer')) {
                abort(404);
            }
        }

        $product->load(['user', 'store', 'assets']);
        return ProductResource::make($product);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'category' => $request->category,
            'tags' => $request->tags,
            'status' => $request->status ?? 'draft',
            'metadata' => $request->metadata,
            'technical_specs' => $request->technical_specs,
            'store_id' => $request->store_id,
            'stock' => $request->stock ?? 0,
        ]);

        return ProductResource::make($product->load('user'));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        if ($request->user()->hasRole('seller') && $product->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $product->update($request->validated());

        return ProductResource::make($product->load('user'));
    }

    public function destroy(Request $request, Product $product)
    {
        if ($request->user()->hasRole('seller') && $product->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
