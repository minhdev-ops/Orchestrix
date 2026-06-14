@extends('agriverse::shop.layout')

@section('title', 'Gian hàng')

@section('content')
<section class="max-w-[1200px] mx-auto px-3 py-4">
    <div class="text-xs text-stone-400 mb-3 flex items-center gap-1">
        <a href="{{ route('agriverse.shop.home') }}" class="hover:text-emerald-700">Trang chủ</a>
        <span class="text-stone-300">/</span>
        <span class="text-stone-600">Gian hàng</span>
    </div>

    <h1 class="text-base font-bold text-stone-800 mb-4">Gian hàng</h1>

    <div class="grid grid-cols-12 gap-3">
        @forelse($stores as $store)
        <div class="col-span-12 md:col-span-4">
            <a href="{{ route('agriverse.shop.stores.show', $store->id) }}" class="block bg-white rounded-xl shadow-sm border border-stone-200/80 p-4 hover:border-emerald-200 hover:shadow-md transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-600 to-emerald-700 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-sm group-hover:scale-105 transition-transform">{{ strtoupper(substr($store->name, 0, 2)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-bold text-stone-800 truncate group-hover:text-emerald-700 transition-colors">{{ $store->name }}</div>
                        <div class="text-xs text-stone-400">{{ $store->products_count ?? 0 }} sản phẩm</div>
                    </div>
                    <span class="material-symbols-outlined text-stone-300 group-hover:text-emerald-500 text-base transition-colors">chevron_right</span>
                </div>
                @if($store->description)
                <div class="text-xs text-stone-500 mt-3 pt-3 border-t border-stone-100 leading-relaxed line-clamp-2">{{ $store->description }}</div>
                @endif
            </a>
        </div>
        @empty
        <div class="col-span-12 bg-white rounded-xl shadow-sm border border-stone-200/80 p-10 text-center">
            <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-3xl text-stone-300">storefront</span>
            </div>
            <div class="text-sm font-semibold text-stone-600 mt-4">Chưa có gian hàng</div>
            <div class="text-xs text-stone-400 mt-1">Các gian hàng sẽ sớm xuất hiện.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $stores->links() }}</div>
</section>
@endsection