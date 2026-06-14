@extends('layouts.admin')

@section('page-title', 'AgriVerse — Quản lý Sản phẩm')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Sản phẩm</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý tất cả sản phẩm cây cảnh bonsai trên nền tảng.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-3">
                <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}"
                        class="pl-11 pr-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </form>
            <a href="{{ route('admin.agriverse.products.create') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all whitespace-nowrap">
                <span class="material-symbols-outlined text-base align-text-bottom mr-1">add</span>
                Thêm sản phẩm
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
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Cửa hàng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Giá</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Tồn kho</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-sm">
                                {{ strtoupper(substr($product->name, 0, 2)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-base font-black text-on-surface">{{ $product->name }}</span>
                                <span class="text-xs text-on-surface-variant opacity-60">{{ Str::limit($product->category ?? 'Không có danh mục', 30) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-on-surface font-semibold text-sm">{{ $product->store?->name ?? '—' }}</td>
                    <td class="px-8 py-6">
                        <span class="font-bold text-on-surface font-mono">{{ number_format($product->price, 0) }}đ</span>
                        @if($product->compare_price)
                            <span class="text-xs text-on-surface-variant line-through ml-1 font-mono">{{ number_format($product->compare_price, 0) }}đ</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <span class="font-bold font-mono {{ $product->stock > 0 ? 'text-on-surface' : 'text-red-500' }}">{{ $product->stock ?? 0 }}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                            {{ $product->status === 'published' ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : '' }}
                            {{ $product->status === 'draft' ? 'bg-amber-50 text-amber-600 border-amber-200/60' : '' }}
                            {{ $product->status === 'archived' ? 'bg-red-50 text-red-600 border-red-200/60' : '' }}">
                            {{ $product->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.agriverse.products.edit', $product->id) }}" class="p-2.5 rounded-xl text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.agriverse.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa sản phẩm này?');">
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
        {{ $products->links() }}
    </div>
</div>
@endsection
