@extends('agriverse::shop.layout')

@section('title', $store->name)

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <a href="{{ route('agriverse.shop.stores.index') }}" class="hover:text-emerald-700">Gian hàng</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">{{ $store->name }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-5 mb-4">
        <div class="flex items-start gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-700 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-sm">{{ strtoupper(substr($store->name, 0, 2)) }}</div>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-bold text-stone-800">{{ $store->name }}</h1>
                <div class="text-sm text-stone-500 mt-1.5 leading-relaxed">{{ $store->description ?? 'Chưa có mô tả.' }}</div>
                <div class="flex items-center gap-4 mt-3 text-xs text-stone-400">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">inventory_2</span>
                        <strong class="text-stone-700">{{ $store->products_count ?? 0 }}</strong> sản phẩm
                    </span>
                    @if($store->owner)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">person</span>
                        <strong class="text-stone-700">{{ $store->owner->name }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 mb-3">
        <span class="material-symbols-outlined text-stone-500 text-lg">inventory_2</span>
        <span class="text-sm font-bold text-stone-800">Sản phẩm</span>
    </div>

    @if($products->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-4">
        <div class="grid grid-cols-6 md:grid-cols-12 gap-3">
            @foreach($products as $product)
            <div class="col-span-6 md:col-span-3">
                <div class="border border-stone-200 rounded-xl hover:border-emerald-200 hover:shadow-md transition-all bg-white group">
                    <a href="{{ route('agriverse.shop.products.show', $product->id) }}">
                        <div class="aspect-square bg-stone-50 flex items-center justify-center rounded-t-xl overflow-hidden">
                            <span class="text-4xl text-stone-200 font-bold group-hover:scale-110 transition-transform duration-300">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                        </div>
                    </a>
                    <div class="p-2.5">
                        <a href="{{ route('agriverse.shop.products.show', $product->id) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 hover:text-emerald-700 leading-snug transition-colors">{{ $product->name }}</a>
                        <div class="text-sm font-bold text-red-500 mt-1.5">{{ number_format($product->price, 0) }}<span class="text-xs underline">₫</span></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-stone-200/80 p-10 text-center">
        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl text-stone-300">inventory_2</span>
        </div>
        <div class="text-sm font-semibold text-stone-600 mt-4">Gian hàng chưa có sản phẩm nào.</div>
    </div>
    @endif
</section>
@endsection