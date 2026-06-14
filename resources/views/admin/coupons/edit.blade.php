@extends('layouts.admin')
@section('page-title', 'Sửa mã giảm giá')
@section('content')
<div class="max-w-2xl space-y-6 pb-20">
    <div>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Sửa mã giảm giá</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Cập nhật thông tin mã giảm giá <strong>{{ $coupon->code }}</strong>.</p>
    </div>

    <div class="glass-panel rounded-3xl p-8">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Mã giảm giá *</label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Tên *</label>
                    <input type="text" name="name" value="{{ old('name', $coupon->name) }}" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Mô tả</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">{{ old('description', $coupon->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Loại *</label>
                    <select name="type" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Cố định (VNĐ)</option>
                        <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giá trị *</label>
                    <input type="number" name="value" value="{{ old('value', $coupon->value) }}" step="0.01" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Đơn hàng tối thiểu</label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giảm tối đa</label>
                    <input type="number" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" step="0.01" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="0" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Kích hoạt</label>
                    <select name="is_active" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                        <option value="1" {{ old('is_active', $coupon->is_active) ? 'selected' : '' }}>Có</option>
                        <option value="0" {{ !old('is_active', $coupon->is_active) ? 'selected' : '' }}>Không</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Ngày bắt đầu</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Ngày hết hạn</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Cập nhật</button>
                <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
