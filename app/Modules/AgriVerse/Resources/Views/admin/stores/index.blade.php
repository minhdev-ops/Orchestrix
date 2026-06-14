@extends('layouts.admin')

@section('page-title', 'AgriVerse — Quản lý Cửa hàng')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Cửa hàng</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý các vườn ươm cây cảnh trên nền tảng.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input type="text" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}"
                        class="pl-11 pr-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </form>
            <a href="{{ route('admin.agriverse.stores.create') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all whitespace-nowrap">
                <span class="material-symbols-outlined text-base align-text-bottom mr-1">add</span>
                Thêm cửa hàng
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-5 bg-emerald-50 border border-emerald-200/60 text-emerald-700 rounded-2xl font-bold flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02] rounded-3xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Tên cửa hàng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Chủ sở hữu</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stores as $store)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-sm">
                                {{ strtoupper(substr($store->name, 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-base font-black text-on-surface">{{ $store->name }}</span>
                                <p class="text-xs text-on-surface-variant opacity-60">{{ Str::limit($store->description ?? '', 50) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-on-surface font-semibold text-sm">{{ $store->owner?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6">
                        <span class="font-bold font-mono text-on-surface">{{ $store->products_count }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                            {{ $store->status === 'active' || !$store->status ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : '' }}
                            {{ $store->status === 'inactive' ? 'bg-amber-50 text-amber-600 border-amber-200/60' : '' }}
                            {{ $store->status === 'suspended' ? 'bg-red-50 text-red-600 border-red-200/60' : '' }}">
                            {{ $store->status ?? 'active' }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.agriverse.stores.edit', $store->id) }}" class="p-2.5 rounded-xl text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.agriverse.stores.destroy', $store->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa cửa hàng này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 rounded-xl text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-all">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $stores->links() }}
    </div>
</div>
@endsection
