@extends('layouts.admin')

@section('page-title', 'Quản lý Sản phẩm')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Sản phẩm</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý tất cả sản phẩm trên nền tảng.</p>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <input type="text" name="search" placeholder="Tìm kiếm..." value="{{ request('search') }}"
                    class="px-4 py-2 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none">
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-tertiary/10 border border-tertiary/20 text-tertiary rounded-2xl font-bold flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Sản phẩm</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Chủ sở hữu</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Giá</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Tồn kho</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black">
                                {{ substr($product->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-base font-black text-on-surface">{{ $product->name }}</span>
                                <span class="text-xs text-on-surface-variant opacity-60">{{ $product->category }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-on-surface font-semibold">{{ $product->user->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6 text-on-surface font-bold">{{ number_format($product->price, 0) }}đ</td>
                    <td class="px-8 py-6 text-on-surface font-bold">{{ $product->stock ?? 0 }}</td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border
                            {{ $product->status === 'published' ? 'bg-tertiary/10 text-tertiary border-tertiary/20' : '' }}
                            {{ $product->status === 'draft' ? 'bg-warning/10 text-warning border-warning/20' : '' }}
                            {{ $product->status === 'archived' ? 'bg-error/10 text-error border-error/20' : '' }}">
                            {{ $product->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-3">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 text-on-surface-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa sản phẩm này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-on-surface-variant hover:text-error transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
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
