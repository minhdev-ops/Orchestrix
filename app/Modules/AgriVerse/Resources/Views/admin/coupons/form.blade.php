@extends('layouts.admin')

@section('page-title', 'AgriVerse — ' . (isset($coupon) ? 'Sửa Mã Giảm giá' : 'Thêm Mã Giảm giá'))

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.coupons.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">{{ isset($coupon) ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá mới' }}</h2>
    </div>

    <form action="{{ isset($coupon) ? route('admin.agriverse.coupons.update', $coupon->id) : route('admin.agriverse.coupons.store') }}" method="POST" class="space-y-8">
        @csrf
        @if(isset($coupon)) @method('PUT') @endif

        <div class="card-premium space-y-6">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin mã</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Mã giảm giá</label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono uppercase">
                    @error('code') <p class="mt-1.5 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Tên hiển thị</label>
                    <input type="text" name="name" value="{{ old('name', $coupon->name ?? '') }}" required
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Mô tả</label>
                <textarea name="description" rows="2" class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('description', $coupon->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Loại</label>
                    <select name="type" required class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="percent" {{ old('type', $coupon->type ?? '') === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Cố định (đ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giá trị</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value ?? '') }}" required
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Đơn hàng tối thiểu</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
                            class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">đ</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giảm tối đa</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                            class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">đ</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                    <p class="mt-1 text-xs text-on-surface-variant opacity-60">Để trống nếu không giới hạn</p>
                </div>
                <div class="flex items-center gap-3 pt-8">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-outline-variant text-emerald-600 focus:ring-emerald-500">
                    <label for="is_active" class="text-sm font-bold text-on-surface cursor-pointer">Kích hoạt</label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Ngày bắt đầu</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}"
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Ngày hết hạn</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}"
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.agriverse.coupons.index') }}" class="px-8 py-3.5 rounded-2xl border border-outline-variant text-on-surface text-sm font-black hover:bg-surface-container-low transition-all">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary text-sm font-black shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                {{ isset($coupon) ? 'Cập nhật' : 'Tạo mã' }}
            </button>
        </div>
    </form>
</div>
@endsection
