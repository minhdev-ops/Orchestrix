<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Http\Requests\Shop\StoreAddressRequest;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Models\UserAddress;
use Inertia\Inertia;

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
        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);

        return Inertia::render('Account/Addresses/Form', [
            'address' => null,
            'provinces' => $provinces,
        ]);
    }

    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

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
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $provinces = Province::orderBy('province_name')->get(['province_id', 'province_name', 'code']);

        return Inertia::render('Account/Addresses/Form', [
            'address' => $address,
            'provinces' => $provinces,
        ]);
    }

    public function update(StoreAddressRequest $request, UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        if ($data['is_default'] ?? false) {
            UserAddress::where('user_id', auth()->id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('agriverse.shop.addresses.index')
            ->with('success', 'Đã cập nhật địa chỉ.');
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('agriverse.shop.addresses.index')
            ->with('success', 'Đã xóa địa chỉ.');
    }

    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định.');
    }
}
