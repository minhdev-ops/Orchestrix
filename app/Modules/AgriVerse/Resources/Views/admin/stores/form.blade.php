@extends('layouts.admin')

@section('page-title', 'AgriVerse — ' . (isset($store) ? 'Sửa Cửa hàng' : 'Thêm Cửa hàng'))

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.stores.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">{{ isset($store) ? 'Sửa cửa hàng' : 'Thêm cửa hàng mới' }}</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">{{ isset($store) ? 'Cập nhật thông tin vườn ươm.' : 'Đăng ký vườn ươm mới trên nền tảng AgriVerse.' }}</p>
    </div>

    <form action="{{ isset($store) ? route('admin.agriverse.stores.update', $store->id) : route('admin.agriverse.stores.store') }}" method="POST" class="space-y-8">
        @csrf
        @if(isset($store)) @method('PUT') @endif

        <div class="card-premium space-y-6">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin cửa hàng</h3>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tên cửa hàng</label>
                <input type="text" name="name" value="{{ old('name', $store->name ?? '') }}" required
                    class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                @error('name') <p class="mt-1.5 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Mô tả</label>
                <textarea name="description" rows="4" class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('description', $store->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Logo URL</label>
                    <input type="text" name="logo" value="{{ old('logo', $store->logo ?? '') }}"
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Trạng thái</label>
                    <select name="status" required class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="active" {{ old('status', $store->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $store->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $store->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.agriverse.stores.index') }}" class="px-8 py-3.5 rounded-2xl border border-outline-variant text-on-surface text-sm font-black hover:bg-surface-container-low transition-all">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary text-sm font-black shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                {{ isset($store) ? 'Cập nhật' : 'Tạo cửa hàng' }}
            </button>
        </div>
    </form>
</div>
@endsection
