@extends('layouts.admin')

@section('page-title', 'Chỉnh sửa Sản phẩm')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Chỉnh sửa Sản phẩm</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">{{ $product->name }}</p>
        </div>
    </div>

    <div class="card-premium bg-white p-8 rounded-3xl shadow-xl shadow-black/[0.02]">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="max-w-xl space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tên sản phẩm</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Giá</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tồn kho</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0"
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Trạng thái</label>
                <select name="status" required
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ $product->status === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
