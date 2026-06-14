@extends('layouts.admin')
@section('page-title', 'Thêm mã giảm giá')
@section('content')
<div class="max-w-2xl space-y-6 pb-20">
    <div>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Thêm mã giảm giá</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Tạo mã giảm giá mới cho hệ thống.</p>
    </div>

    <div class="glass-panel rounded-3xl p-8">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Mã giảm giá *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                    @error('code') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Tên *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Mô tả</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Loại *</label>
                    <select name="type" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Cố định (VNĐ)</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giá trị *</label>
                    <input type="number" name="value" value="{{ old('value', 0) }}" step="0.01" required class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Đơn hàng tối thiểu</label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" step="0.01" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giảm tối đa (để trống nếu không giới hạn)</label>
                    <input type="number" name="max_discount" value="{{ old('max_discount') }}" step="0.01" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Giới hạn sử dụng (0 = không giới hạn)</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', 0) }}" min="0" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Kích hoạt</label>
                    <select name="is_active" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                        <option value="1" selected>Có</option>
                        <option value="0">Không</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Ngày bắt đầu</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-on-surface-variant opacity-60 mb-2">Ngày hết hạn</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-sm font-medium focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">Lưu</button>
                <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
