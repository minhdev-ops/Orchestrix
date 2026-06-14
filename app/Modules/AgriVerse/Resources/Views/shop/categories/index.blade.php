@extends('agriverse::shop.layout')

@section('title', 'Danh mục')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Danh mục</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Danh mục sản phẩm</h1>

    <div class="grid grid-cols-12 gap-3">
        @forelse($categories as $category)
        <div class="col-span-12 md:col-span-4">
            <a href="{{ route('agriverse.shop.products.index', ['category' => $category->slug]) }}" class="block bg-white rounded-xl shadow-sm border border-stone-200/80 p-4 hover:border-emerald-200 hover:shadow-md transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0 group-hover:scale-105 group-hover:bg-emerald-100 transition-all">
                        {{ strtoupper(mb_substr($category->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-bold text-stone-800 group-hover:text-emerald-700 transition-colors">{{ $category->name }}</div>
                        <div class="text-xs text-stone-400 mt-0.5">{{ $category->products_count ?? 0 }} sản phẩm</div>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-emerald-500 text-base transition-colors">chevron_right</span>
                </div>
                @if($category->children->count() > 0)
                <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-stone-100">
                    @foreach($category->children as $child)
                    <span class="text-[10px] text-stone-500 bg-stone-50 rounded-md px-2 py-0.5 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">{{ $child->name }}</span>
                    @endforeach
                </div>
                @endif
            </a>
        </div>
        @empty
        <div class="col-span-12 bg-white rounded-xl shadow-sm border border-stone-200/80 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-3xl text-stone-300">category</span>
            </div>
            <div class="text-sm font-semibold text-stone-600 mt-4">Chưa có danh mục</div>
            <div class="text-xs text-stone-400 mt-1">Danh mục sản phẩm sẽ sớm được cập nhật.</div>
        </div>
        @endforelse
    </div>
</section>
@endsection