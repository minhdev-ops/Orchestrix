@extends('agriverse::shop.layout')

@section('title', 'Trang chủ')

@section('content')
{{-- ===== HERO: Botanical Heritage Design System ===== --}}
<section class="relative overflow-hidden" style="background: var(--ag-bg); min-height: 480px; display: flex; align-items: center;">
    {{-- Decorative orbs --}}
    <div class="absolute top-1/2 -translate-y-1/2 right-0 w-[500px] h-[500px] rounded-full pointer-events-none"
        style="background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent); filter: blur(100px);"></div>
    <div class="absolute bottom-0 left-[25%] w-[300px] h-[200px] rounded-full pointer-events-none"
        style="background: color-mix(in srgb, var(--ag-secondary-500) 4%, transparent); filter: blur(80px);"></div>
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none"
        style="background-image: repeating-linear-gradient(90deg, transparent, transparent 32px, color-mix(in srgb, var(--ag-primary-500) 8%, transparent) 32px, color-mix(in srgb, var(--ag-primary-500) 8%, transparent) 33px);"></div>

    <div class="mx-auto w-full relative" style="max-width: 1280px; padding: 64px 24px 80px;">
        <div class="max-w-2xl">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full mb-6"
                style="background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent); color: var(--ag-primary-500); font-family: var(--ag-font-body); font-size: 12px; font-weight: 500; letter-spacing: 0.02em;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: var(--ag-primary-500);"></span>
                Cây cảnh Việt — tinh hoa bonsai
            </div>

            {{-- Title: Roboto --}}
            <h1 style="font-family: var(--ag-font-display); font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight: 500; color: var(--ag-text-primary); line-height: 1.05; letter-spacing: -0.02em;">
                Từ vườn ươm <span style="color: var(--ag-primary-500);">đến không gian xanh</span>
            </h1>

            <p style="font-family: var(--ag-font-body); font-size: clamp(1rem, 1.5vw, 1.125rem); color: var(--ag-text-secondary); margin-top: 16px; line-height: 1.6; max-width: 32rem;">
                Đặt mua cây cảnh bonsai từ các vườn ươm uy tín trên toàn quốc. Giao hàng tận nơi, kiểm soát chất lượng.
            </p>

            <div class="flex gap-4 mt-8">
                <a href="{{ route('agriverse.shop.products.index') }}"
                    class="h-12 px-8 rounded-xl flex items-center gap-2 font-semibold text-sm transition-all duration-300 active:scale-[0.97]"
                    style="background: var(--ag-primary-500); color: white; font-family: var(--ag-font-body); text-decoration: none;"
                    onmouseover="this.style.background='var(--ag-primary-600)'; this.style.boxShadow='0 8px 24px -4px color-mix(in srgb, var(--ag-primary-500) 30%, transparent)'"
                    onmouseout="this.style.background='var(--ag-primary-500)'; this.style.boxShadow=''">
                    Mua ngay
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
                <a href="{{ route('agriverse.shop.stores.index') }}"
                    class="h-12 px-8 rounded-xl flex items-center gap-2 text-sm font-medium transition-all duration-300 active:scale-[0.97]"
                    style="border: 2px solid var(--ag-border); color: var(--ag-text-secondary); font-family: var(--ag-font-body); text-decoration: none;"
                    onmouseover="this.style.borderColor='color-mix(in srgb, var(--ag-primary-500) 30%, transparent)'; this.style.color='var(--ag-primary-500)'; this.style.background='white'"
                    onmouseout="this.style.borderColor='var(--ag-border)'; this.style.color='var(--ag-text-secondary)'; this.style.background=''">
                    Gian hàng
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ===== CATEGORIES: Botanical Heritage ===== --}}
@if(isset($categories) && $categories->count() > 0)
<section style="padding: 64px 0; margin-top: 32px; background: white; border-top: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent); border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);">
    <div class="mx-auto" style="max-width: 1280px; padding: 0 24px;">
        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 style="font-family: var(--ag-font-display); font-size: 22px; font-weight: 500; color: var(--ag-text-primary); letter-spacing: -0.01em;">Danh mục</h2>
                <p style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-muted); margin-top: 2px;">Khám phá theo nhu cầu</p>
            </div>
            <a href="{{ route('agriverse.shop.products.index') }}" class="hidden sm:block transition-colors"
                style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 500; color: var(--ag-text-secondary); text-decoration: none;"
                onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">
                Xem tất cả →
            </a>
        </div>
        <div class="grid grid-cols-4 md:grid-cols-8 gap-3">
            @foreach($categories as $category)
            <a href="{{ route('agriverse.shop.products.index', ['category' => $category->slug]) }}"
                class="flex flex-col items-center gap-2.5 p-4 rounded-xl transition-all duration-300 active:scale-[0.97] group"
                style="background: white; border: 1px solid var(--ag-border); text-decoration: none;"
                onmouseover="this.style.borderColor='color-mix(in srgb, var(--ag-primary-500) 30%, transparent)'; this.style.background='color-mix(in srgb, var(--ag-primary-500) 4%, transparent)'"
                onmouseout="this.style.borderColor='var(--ag-border)'; this.style.background='white'">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-base font-medium transition-all duration-400"
                    style="background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent); color: var(--ag-primary-500); font-family: var(--ag-font-display);"
                    onmouseover="this.style.background='var(--ag-primary-500)'; this.style.color='white'; this.style.transform='scale(1.1)'"
                    onmouseout="this.style.background='color-mix(in srgb, var(--ag-primary-500) 10%, transparent)'; this.style.color='var(--ag-primary-500)'; this.style.transform='scale(1)'">
                    {{ strtoupper(mb_substr($category->name, 0, 1)) }}
                </div>
                <span class="text-center leading-tight font-medium transition-colors duration-300"
                    style="font-family: var(--ag-font-body); font-size: 12px; color: var(--ag-text-secondary);"
                    onmouseover="this.style.color='var(--ag-primary-500)'"
                    onmouseout="this.style.color='var(--ag-text-secondary)'">
                    {{ $category->name }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== FEATURED PRODUCTS: Botanical Heritage ===== --}}
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section style="padding: 64px 0;">
    <div class="mx-auto" style="max-width: 1280px; padding: 0 24px;">
        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 style="font-family: var(--ag-font-display); font-size: 22px; font-weight: 500; color: var(--ag-text-primary); letter-spacing: -0.01em;">Nổi bật</h2>
                <p style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-muted); margin-top: 2px;">Sản phẩm được quan tâm nhất</p>
            </div>
            <a href="{{ route('agriverse.shop.products.index') }}" class="hidden sm:block transition-colors"
                style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 500; color: var(--ag-text-secondary); text-decoration: none;"
                onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">
                Xem tất cả →
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($featuredProducts as $product)
            <div class="group rounded-xl overflow-hidden transition-all duration-400"
                style="background: white; border: 1px solid var(--ag-border);"
                onmouseover="this.style.borderColor='color-mix(in srgb, var(--ag-primary-500) 25%, transparent)'; this.style.boxShadow='var(--ag-shadow-md)'"
                onmouseout="this.style.borderColor='var(--ag-border)'; this.style.boxShadow=''">
                <a href="{{ route('agriverse.shop.products.show', $product->id) }}">
                    <div class="aspect-square flex items-center justify-center relative overflow-hidden" style="background: var(--ag-bg);">
                        <span class="text-5xl transition-transform duration-700 ease-out group-hover:scale-125"
                            style="font-family: var(--ag-font-display); color: var(--ag-neutral-300); font-weight: 500;">
                            {{ strtoupper(mb_substr($product->name, 0, 1)) }}
                        </span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            @php $discount = round((1 - $product->price / $product->compare_price) * 100); @endphp
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[10px] font-bold"
                                style="background: var(--ag-danger); color: white;">-{{ $discount }}%</span>
                        @endif
                        @if($product->model_3d_path)
                        <span class="absolute top-3 right-3 w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background: rgba(255,255,255,0.9); backdrop-filter: blur(8px); border: 1px solid var(--ag-border); color: var(--ag-primary-500);">
                            <span class="material-symbols-outlined" style="font-size: 14px;">view_in_ar</span>
                        </span>
                        @endif
                    </div>
                </a>
                <div style="padding: 16px;">
                    <a href="{{ route('agriverse.shop.products.show', $product->id )}}"
                        class="line-clamp-2 leading-snug min-h-[2.5rem] transition-colors"
                        style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 600; color: var(--ag-text-primary); text-decoration: none;"
                        onmouseover="this.style.color='var(--ag-primary-500)'"
                        onmouseout="this.style.color='var(--ag-text-primary)'">
                        {{ $product->name }}
                    </a>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span style="font-family: var(--ag-font-display); font-size: 18px; font-weight: 600; color: var(--ag-danger);">{{ number_format($product->price, 0) }}<span style="font-size: 12px; text-decoration: underline;">₫</span></span>
                        @if($product->compare_price)
                            <span style="font-family: var(--ag-font-body); font-size: 12px; color: var(--ag-text-muted); text-decoration: line-through;">{{ number_format($product->compare_price, 0) }}₫</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-3" style="border-top: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);">
                        <span style="font-family: var(--ag-font-body); font-size: 12px; font-weight: 500; color: var(--ag-text-muted);">Đã bán {{ $product->sold_count ?? 0 }}</span>
                        <form action="{{ route('agriverse.api.cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 cursor-pointer"
                                style="border: 1px solid var(--ag-border); color: var(--ag-text-muted); background: transparent;"
                                onmouseover="this.style.background='var(--ag-primary-500)'; this.style.color='white'; this.style.borderColor='var(--ag-primary-500)'"
                                onmouseout="this.style.background='transparent'; this.style.color='var(--ag-text-muted)'; this.style.borderColor='var(--ag-border)'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">add_shopping_cart</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Mobile link --}}
        <div class="mt-6 text-center sm:hidden">
            <a href="{{ route('agriverse.shop.products.index') }}"
                class="inline-flex items-center gap-1.5 transition-colors"
                style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 500; color: var(--ag-primary-500); text-decoration: none;"
                onmouseover="this.style.color='var(--ag-primary-600)'" onmouseout="this.style.color='var(--ag-primary-500)'">
                Xem tất cả sản phẩm
                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
            </a>
        </div>
    </div>
</section>
@endif

{{-- ===== STORES: Botanical Heritage ===== --}}
@if(isset($stores) && $stores->count() > 0)
<section style="padding: 64px 0; background: var(--ag-bg); border-top: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);">
    <div class="mx-auto" style="max-width: 1280px; padding: 0 24px;">
        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 style="font-family: var(--ag-font-display); font-size: 22px; font-weight: 500; color: var(--ag-text-primary); letter-spacing: -0.01em;">Gian hàng</h2>
                <p style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-muted); margin-top: 2px;">Nhà vườn uy tín</p>
            </div>
            <a href="{{ route('agriverse.shop.stores.index') }}" class="hidden sm:block transition-colors"
                style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 500; color: var(--ag-text-secondary); text-decoration: none;"
                onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">
                Xem tất cả →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($stores as $store)
            <a href="{{ route('agriverse.shop.stores.show', $store->id) }}"
                class="flex items-center gap-4 p-5 rounded-xl transition-all duration-400 group"
                style="background: white; border: 1px solid var(--ag-border); text-decoration: none;"
                onmouseover="this.style.borderColor='color-mix(in srgb, var(--ag-primary-500) 25%, transparent)'; this.style.boxShadow='var(--ag-shadow-md)'"
                onmouseout="this.style.borderColor='var(--ag-border)'; this.style.boxShadow=''">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center font-medium shrink-0 text-base"
                    style="background: var(--ag-primary-500); color: white; font-family: var(--ag-font-display);">
                    {{ strtoupper(substr($store->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate font-semibold transition-colors"
                        style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-primary);"
                        onmouseover="this.style.color='var(--ag-primary-500)'"
                        onmouseout="this.style.color='var(--ag-text-primary)'">
                        {{ $store->name }}
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <span style="font-family: var(--ag-font-body); font-size: 12px; color: var(--ag-text-muted);">{{ $store->products_count ?? 0 }} sản phẩm</span>
                        <span style="display: inline-flex; align-items: center; gap: 4px; font-family: var(--ag-font-body); font-size: 11px; font-weight: 500; color: var(--ag-primary-500); background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent); padding: 2px 8px; border-radius: 9999px; white-space: nowrap;">Đang hoạt động</span>
                    </div>
                </div>
                <span class="transition-colors" style="font-size: 18px; color: var(--ag-neutral-300);"
                    onmouseover="this.style.color='var(--ag-primary-500)'"
                    onmouseout="this.style.color='var(--ag-neutral-300)'">chevron_right</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection