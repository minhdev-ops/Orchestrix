@extends('layouts.admin')

@section('page-title', 'AgriVerse — ' . (isset($product) ? 'Sửa Sản phẩm' : 'Thêm Sản phẩm'))

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.products.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">{{ isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' }}</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">{{ isset($product) ? 'Cập nhật thông tin cây cảnh bonsai.' : 'Điền thông tin cây cảnh bonsai để đăng bán.' }}</p>
    </div>

    <form action="{{ isset($product) ? route('admin.agriverse.products.update', $product->id) : route('admin.agriverse.products.store') }}" method="POST" class="space-y-8">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="card-premium space-y-6">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin cơ bản</h3>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tên sản phẩm</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                @error('name') <p class="mt-1.5 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Mô tả</label>
                <textarea name="description" rows="5" class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Danh mục</label>
                    <select name="category" class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ old('category', $product->category ?? '') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Trạng thái</label>
                    <select name="status" required class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="draft" {{ old('status', $product->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $product->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $product->status ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tags</label>
                <input type="text" name="tags" value="{{ old('tags', isset($product) ? implode(', ', $product->tags ?? []) : '') }}"
                    placeholder="bonsai, cây cảnh, phụ kiện"
                    class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <p class="mt-1.5 text-xs text-on-surface-variant opacity-60">Phân cách bằng dấu phẩy</p>
            </div>
        </div>

        <div class="card-premium space-y-6">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Giá & Tồn kho</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giá bán</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required
                            class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">đ</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giá so sánh</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}"
                            class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">đ</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Số lượng tồn kho</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required
                    class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.agriverse.products.index') }}" class="px-8 py-3.5 rounded-2xl border border-outline-variant text-on-surface text-sm font-black hover:bg-surface-container-low transition-all">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary text-sm font-black shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                {{ isset($product) ? 'Cập nhật' : 'Tạo sản phẩm' }}
            </button>
        </div>
    </form>
</div>
@endsection
