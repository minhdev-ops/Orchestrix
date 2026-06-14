<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\UserAddress;

class AddressController
{
    public function index()
    {
        $addresses = UserAddress::where('user_id', auth()->id())
            ->latest('is_default')
            ->latest()
            ->get();

        return Inertia::render('Account/Addresses/Index', [
            'addresses' => $addresses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Account/Addresses/Form', [
            'address' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'province_id' => 'nullable|integer',
            'district' => 'required|string|max:100',
            'district_id' => 'nullable|integer',
            'ghn_district_id' => 'nullable|integer',
            'ward' => 'required|string|max:100',
            'ward_code' => 'nullable|string|max:20',
            'ghn_ward_code' => 'nullable|string|max:20',
            'address_detail' => 'required|string|max:500',
            'is_default' => 'boolean',
        ]);

        $data['user_id'] = auth()->id();

        if ($data['is_default'] ?? false) {
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        UserAddress::create($data);

        return redirect()->route('agriverse.shop.addresses.index')
            ->with('success', 'Đã thêm địa chỉ mới.');
    }

    public function edit(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        return Inertia::render('Account/Addresses/Form', [
            'address' => $address,
        ]);
    }

    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        $data = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'province_id' => 'nullable|integer',
            'district' => 'required|string|max:100',
            'district_id' => 'nullable|integer',
            'ghn_district_id' => 'nullable|integer',
            'ward' => 'required|string|max:100',
            'ward_code' => 'nullable|string|max:20',
            'ghn_ward_code' => 'nullable|string|max:20',
            'address_detail' => 'required|string|max:500',
            'is_default' => 'boolean',
        ]);

        if ($data['is_default'] ?? false) {
            UserAddress::where('user_id', auth()->id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('agriverse.shop.addresses.index')
            ->with('success', 'Đã cập nhật địa chỉ.');
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        $address->delete();

        return redirect()->route('agriverse.shop.addresses.index')
            ->with('success', 'Đã xóa địa chỉ.');
    }

    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định.');
    }
}
