@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Cửa hàng')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Chỉnh sửa Cửa hàng</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">{{ $store->name }}</p>
        </div>
    </div>

    <div class="card-premium bg-white p-8 rounded-3xl shadow-xl shadow-black/[0.02]">
        <form action="{{ route('admin.stores.update', $store->id) }}" method="POST" class="max-w-xl space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tên cửa hàng</label>
                <input type="text" name="name" value="{{ old('name', $store->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Mô tả</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">{{ old('description', $store->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Trạng thái</label>
                <select name="status" required
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="active" {{ $store->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ $store->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.stores.index') }}" class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
