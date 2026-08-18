<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController
{
    public function index(Request $request)
    {
        $query = Store::with('owner')->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stores = $query->latest()->paginate(15);

        return Inertia::render('Admin/Stores/Index', [
            'stores' => $stores,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Stores/Form', [
            'store' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['owner_id'] = $request->input('owner_id', auth()->id());

        Store::create($validated);

        return redirect()->route('admin.agriverse.stores.index')
            ->with('success', 'Cửa hàng đã được tạo.');
    }

    public function edit(Store $store)
    {
        return Inertia::render('Admin/Stores/Form', [
            'store' => $store,
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $store->update($validated);

        return redirect()->route('admin.agriverse.stores.index')
            ->with('success', 'Cửa hàng đã được cập nhật.');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->route('admin.agriverse.stores.index')
            ->with('success', 'Cửa hàng đã được xóa.');
    }
}
