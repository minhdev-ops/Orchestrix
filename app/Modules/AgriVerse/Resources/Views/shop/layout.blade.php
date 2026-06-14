<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'AgriVerse — Chợ cây cảnh bonsai trực tuyến, kết nối người mua với vườn ươm uy tín trên toàn quốc.')">
    <title>@yield('title', 'AgriVerse') — Chợ Cây Cảnh</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    {{-- Roboto Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        body { font-family: 'Roboto', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="antialiased" style="background-color: var(--ag-bg); color: var(--ag-text-primary);">

{{-- ========== HEADER: Glassmorphism Sticky Nav ========== --}}
<header id="site-header" class="sticky top-0 z-50 transition-all duration-400" style="background: rgba(252,249,248,0.7); backdrop-filter: blur(12px) saturate(1.4); -webkit-backdrop-filter: blur(12px) saturate(1.4); border-bottom: 1px solid rgba(234,231,231,0.6);">
    <div class="mx-auto flex items-center h-20 gap-12" style="max-width: 1280px; padding: 0 64px;">
        {{-- Brand: Roboto --}}
        <a href="{{ route('agriverse.shop.home') }}" class="flex items-center gap-2 shrink-0 group">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--ag-primary-500);">
                <span class="material-symbols-outlined text-white" style="font-size: 18px;">eco</span>
            </div>
            <span class="italic" style="font-family: var(--ag-font-display); font-size: 24px; font-weight: 500; color: var(--ag-primary-500); letter-spacing: -0.02em;">AgriVerse</span>
        </a>

        {{-- Navigation: Roboto --}}
        <nav class="hidden md:flex items-center gap-8" style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 500;">
            <a href="{{ route('agriverse.shop.products.index') }}" class="px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('agriverse.shop.products.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('agriverse.shop.products.*') ? 'color: var(--ag-primary-500); background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);' : 'color: var(--ag-neutral-700);' }}" onmouseover="if(!this.classList.contains('font-semibold')){this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';}" onmouseout="if(!this.classList.contains('font-semibold')){this.style.background=''; this.style.color='var(--ag-neutral-700)';}">Sản phẩm</a>
            <a href="{{ route('agriverse.shop.categories.index') }}" class="px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('agriverse.shop.categories.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('agriverse.shop.categories.*') ? 'color: var(--ag-primary-500); background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);' : 'color: var(--ag-neutral-700);' }}" onmouseover="if(!this.classList.contains('font-semibold')){this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';}" onmouseout="if(!this.classList.contains('font-semibold')){this.style.background=''; this.style.color='var(--ag-neutral-700)';}">Danh mục</a>
            <a href="{{ route('agriverse.shop.stores.index') }}" class="px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('agriverse.shop.stores.*') ? 'font-semibold' : '' }}" style="{{ request()->routeIs('agriverse.shop.stores.*') ? 'color: var(--ag-primary-500); background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);' : 'color: var(--ag-neutral-700);' }}" onmouseover="if(!this.classList.contains('font-semibold')){this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';}" onmouseout="if(!this.classList.contains('font-semibold')){this.style.background=''; this.style.color='var(--ag-neutral-700)';}">Gian hàng</a>
        </nav>

        {{-- Search --}}
        <form action="{{ route('agriverse.shop.products.index') }}" method="GET" class="flex-1 max-w-[480px] hidden sm:block">
            <div class="relative">
                <input type="text" name="search" placeholder="Tìm cây cảnh, phụ kiện..."
                    value="{{ request('search') }}"
                    style="width: 100%; height: 40px; padding: 0 40px 0 16px; border-radius: 8px; border: 1px solid var(--ag-border); background: var(--ag-neutral-100); font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-primary); outline: none; transition: all 200ms;"
                    onfocus="this.style.borderColor='var(--ag-primary-500)'; this.style.boxShadow='0 0 0 3px color-mix(in srgb, var(--ag-primary-500) 10%, transparent)'; this.style.background='#fff';"
                    onblur="this.style.borderColor='var(--ag-border)'; this.style.boxShadow=''; this.style.background='var(--ag-neutral-100)';">
                <button type="submit" class="absolute right-0 top-0 w-10 h-10 flex items-center justify-center" style="color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">
                    <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                </button>
            </div>
        </form>

        {{-- Icons & Auth --}}
        <div class="flex items-center gap-1">
            <a href="{{ route('agriverse.shop.wishlist.index') }}" class="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-200" style="color: var(--ag-text-secondary);" onmouseover="this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';" onmouseout="this.style.background=''; this.style.color='var(--ag-text-secondary)';" title="Yêu thích">
                <span class="material-symbols-outlined" style="font-size: 22px;">favorite</span>
            </a>
            <a href="{{ route('agriverse.shop.cart.index') }}" class="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-200 relative" style="color: var(--ag-text-secondary);" onmouseover="this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';" onmouseout="this.style.background=''; this.style.color='var(--ag-text-secondary)';" title="Giỏ hàng">
                <span class="material-symbols-outlined" style="font-size: 22px;">shopping_bag</span>
                @auth
                @php $cartCount = \App\Modules\AgriVerse\Models\Cart::active()->where('user_id', auth()->id())->count(); @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-[4px]" style="background: var(--ag-secondary-500); color: #fff; font-family: var(--ag-font-body); font-size: 10px; font-weight: 700;">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
                @endauth
            </a>
        </div>
        <div class="flex items-center gap-1" style="font-family: var(--ag-font-body); font-size: 14px;">
            @auth
            <a href="{{ route('agriverse.shop.orders.index') }}" class="px-3 py-2 rounded-lg transition-all duration-200" style="color: var(--ag-neutral-700);" onmouseover="this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';" onmouseout="this.style.background=''; this.style.color='var(--ag-neutral-700)';">Đơn hàng</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="px-3 py-2 rounded-lg transition-all duration-200" style="color: var(--ag-neutral-700);" onmouseover="this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';" onmouseout="this.style.background=''; this.style.color='var(--ag-neutral-700)';">Đăng xuất</button>
            </form>
            @else
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg transition-all duration-200" style="color: var(--ag-neutral-700);" onmouseover="this.style.background='color-mix(in srgb, var(--ag-primary-500) 6%, transparent)'; this.style.color='var(--ag-primary-500)';" onmouseout="this.style.background=''; this.style.color='var(--ag-neutral-700)';">Đăng nhập</a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg transition-all duration-200" style="background: var(--ag-primary-500); color: #fff; font-weight: 600;" onmouseover="this.style.background='var(--ag-primary-600)';" onmouseout="this.style.background='var(--ag-primary-500)';">Đăng ký</a>
            @endauth
        </div>
    </div>
</header>

{{-- ========== MAIN CONTENT ========== --}}
<main class="min-h-[60vh]">
    @yield('content')
</main>

{{-- ========== FOOTER: Botanical Heritage ========== --}}
<footer style="background: var(--ag-surface-container, #f0eded); border-top: 1px solid var(--ag-outline-10, rgba(116, 121, 108, 0.1)); margin-top: 80px;">
    <div class="mx-auto" style="max-width: 1280px; padding: 80px 64px 40px;">
        <div class="grid grid-cols-12 gap-8">
            {{-- Brand Column --}}
            <div class="col-span-12 md:col-span-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--ag-primary-500);">
                        <span class="material-symbols-outlined text-white" style="font-size: 18px;">eco</span>
                    </div>
                    <span class="italic" style="font-family: var(--ag-font-display); font-size: 24px; font-weight: 500; color: var(--ag-primary-500);">AgriVerse</span>
                </div>
                <p style="font-family: var(--ag-font-body); font-size: 14px; line-height: 22px; color: var(--ag-text-secondary); max-width: 320px;">Chợ cây cảnh bonsai trực tuyến kết nối người mua với vườn ươm uy tín trên toàn quốc. Cây cảnh chất lượng, giao hàng tận nơi.</p>
                <div class="flex items-center gap-3 mt-5">
                    <a href="#" class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200" style="background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent); color: var(--ag-primary-500);" onmouseover="this.style.background='var(--ag-primary-500)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--ag-primary-500) 10%, transparent)'; this.style.color='var(--ag-primary-500)';">
                        <span class="material-symbols-outlined" style="font-size: 18px;">public</span>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200" style="background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent); color: var(--ag-primary-500);" onmouseover="this.style.background='var(--ag-primary-500)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--ag-primary-500) 10%, transparent)'; this.style.color='var(--ag-primary-500)';">
                        <span class="material-symbols-outlined" style="font-size: 18px;">photo_camera</span>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200" style="background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent); color: var(--ag-primary-500);" onmouseover="this.style.background='var(--ag-primary-500)'; this.style.color='#fff';" onmouseout="this.style.background='color-mix(in srgb, var(--ag-primary-500) 10%, transparent)'; this.style.color='var(--ag-primary-500)';">
                        <span class="material-symbols-outlined" style="font-size: 18px;">mail</span>
                    </a>
                </div>
            </div>

            {{-- Collections --}}
            <div class="col-span-6 md:col-span-2">
                <div style="font-family: var(--ag-font-body); font-size: 12px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ag-neutral-700); margin-bottom: 16px;">Khám phá</div>
                <div class="space-y-3">
                    <a href="{{ route('agriverse.shop.products.index') }}" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Sản phẩm</a>
                    <a href="{{ route('agriverse.shop.stores.index') }}" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Gian hàng</a>
                    <a href="{{ route('agriverse.shop.categories.index') }}" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Danh mục</a>
                </div>
            </div>

            {{-- Company --}}
            <div class="col-span-6 md:col-span-2">
                <div style="font-family: var(--ag-font-body); font-size: 12px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ag-neutral-700); margin-bottom: 16px;">Hỗ trợ</div>
                <div class="space-y-3">
                    <a href="#" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Trung tâm trợ giúp</a>
                    <a href="#" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Chính sách đổi trả</a>
                    <a href="#" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Điều khoản sử dụng</a>
                    <a href="#" class="block transition-colors duration-200" style="font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-secondary);" onmouseover="this.style.color='var(--ag-primary-500)'" onmouseout="this.style.color='var(--ag-text-secondary)'">Giao hàng & Đổi trả</a>
                </div>
            </div>

            {{-- Newsletter --}}
            <div class="col-span-12 md:col-span-4">
                <div style="font-family: var(--ag-font-body); font-size: 12px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--ag-neutral-700); margin-bottom: 16px;">Bản tin</div>
                <p style="font-family: var(--ag-font-body); font-size: 14px; line-height: 22px; color: var(--ag-text-secondary); margin-bottom: 16px;">Nhận sớm thông tin khuyến mãi và kiến thức chăm sóc cây trồng từ chuyên gia.</p>
                <div class="flex items-center border-b pb-2" style="border-bottom-color: rgba(116, 121, 108, 0.3);">
                    <input type="email" placeholder="Email của bạn" style="flex: 1; background: transparent; border: none; font-family: var(--ag-font-body); font-size: 14px; color: var(--ag-text-primary); outline: none; box-shadow: none;">
                    <button type="button" class="flex items-center justify-center h-8 transition-opacity duration-200" style="color: var(--ag-primary-500); background: transparent; border: none; cursor: pointer;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <span class="material-symbols-outlined" style="font-size: 24px;">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="text-center" style="margin-top: 48px; padding-top: 32px; border-top: 1px solid var(--ag-accent-300);">
            <p style="font-family: var(--ag-font-body); font-size: 12px; color: var(--ag-accent-500);">&copy; {{ date('Y') }} AgriVerse. Phiên chợ cây cảnh bonsai &mdash; Kết nối vườn ươm Việt.</p>
        </div>
    </div>
</footer>

{{-- Toast Notification --}}
@if(session('success'))
<div id="toast" class="fixed bottom-6 right-6 z-50">
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl" style="background: #fff; border: 1px solid #c9eea9; box-shadow: 0 10px 30px -8px rgba(72,103,48,0.12);">
        <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: #f3f8ef;">
            <span class="material-symbols-outlined" style="color: var(--ag-primary-500); font-size: 18px;">check</span>
        </div>
        <span style="font-family: var(--ag-font-body); font-size: 14px; font-weight: 600; color: var(--ag-text-primary);">{{ session('success') }}</span>
    </div>
</div>
<script>
    setTimeout(() => { const t = document.getElementById('toast'); if(t) { t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; t.style.transition = 'all .3s ease'; setTimeout(() => t.remove(), 300); } }, 3500);
</script>
@endif

@stack('scripts')
</body>
</html>