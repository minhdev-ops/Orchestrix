@extends('layouts.admin')

@section('page-title', 'AgriVerse — Quản lý Danh mục')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Danh mục</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Phân loại cây cảnh bonsai theo danh mục.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-5 bg-emerald-50 border border-emerald-200/60 text-emerald-700 rounded-2xl font-bold flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-5 bg-red-50 border border-red-200/60 text-red-700 rounded-2xl font-bold flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-red-500">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-8">
        {{-- Add Category Form --}}
        <div class="col-span-12 md:col-span-4">
            <div class="card-premium">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] mb-6">Thêm danh mục</h3>
                <form action="{{ route('admin.agriverse.categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-on-surface mb-1.5">Tên danh mục</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface mb-1.5">Mô tả</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-1.5">Icon (Material Symbol)</label>
                            <input type="text" name="icon" placeholder="eco"
                                class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-on-surface mb-1.5">Thứ tự</label>
                            <input type="number" name="sort_order" value="0"
                                class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface mb-1.5">Danh mục cha</label>
                        <select name="parent_id" class="w-full px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <option value="">— Không có (danh mục gốc) —</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                            class="w-4 h-4 rounded border-outline-variant text-emerald-600 focus:ring-emerald-500">
                        <label for="is_active" class="text-sm font-bold text-on-surface">Kích hoạt</label>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                        Thêm danh mục
                    </button>
                </form>
            </div>
        </div>

        {{-- Categories List --}}
        <div class="col-span-12 md:col-span-8">
            <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02] rounded-3xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                            <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Danh mục</th>
                            <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Cấp</th>
                            <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm</th>
                            <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                            <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">{{ $category->icon ?? 'category' }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-black text-on-surface">{{ $category->name }}</span>
                                        @if($category->description)
                                            <span class="text-xs text-on-surface-variant opacity-60">{{ Str::limit($category->description, 40) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold {{ $category->parent_id ? 'text-on-surface-variant' : 'text-primary' }}">
                                    {{ $category->parent_id ? 'Con' : 'Gốc' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 font-bold font-mono text-on-surface">{{ $category->products_count }}</td>
                            <td class="px-8 py-6">
                                <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                                    {{ $category->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : 'bg-red-50 text-red-600 border-red-200/60' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <form action="{{ route('admin.agriverse.categories.update', $category->id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $category->name }}" required
                                            class="w-32 px-3 py-2 rounded-xl border border-outline-variant bg-surface text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                        <input type="hidden" name="is_active" value="0">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}
                                                class="w-3.5 h-3.5 rounded border-outline-variant text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-[9px] font-black uppercase tracking-widest">Active</span>
                                        </label>
                                        <button type="submit" class="p-2 rounded-xl text-primary hover:bg-primary/5 transition-all" title="Cập nhật">
                                            <span class="material-symbols-outlined text-lg">check</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.agriverse.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-all">
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
            <div class="mt-6">{{ $categories->links() }}</div>
        </div>
    </div>
</div>
@endsection
