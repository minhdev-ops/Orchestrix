<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SellerStoreController
{
    public function edit()
    {
        $user = auth()->user();
        $store = Store::where('owner_id', $user->id)->firstOrFail();

        return Inertia::render('Marketplace/Seller/Store/Edit', [
            'store' => $store,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $store = Store::where('owner_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
        ]);

        $store->update($validated);

        return back()->with('success', 'Thông tin cửa hàng đã được cập nhật.');
    }
}
