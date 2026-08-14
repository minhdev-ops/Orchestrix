<?php

/**
 * @deprecated Use App\Modules\AgriVerse\Http\Controllers\Admin\StoreController instead.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('owner')->withCount('products')->latest()->paginate(15);

        return view('admin.stores.index', compact('stores'));
    }

    public function edit(Store $store)
    {
        $store->load('owner');

        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,suspended',
        ]);

        $store->update($request->only(['name', 'description', 'status']));

        return redirect()->route('admin.stores.index')->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->route('admin.stores.index')->with('success', 'Store deleted successfully.');
    }
}
