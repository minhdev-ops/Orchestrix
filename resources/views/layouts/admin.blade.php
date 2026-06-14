<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảng quản trị | Orchestrix</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <style>
        :root { --sidebar-width: 260px; }
        body {
            background-color: var(--surface);
            color: var(--on-surface);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid var(--outline-variant);
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--on-surface-variant);
            transition: all 0.15s ease;
        }
        .sidebar-link:hover {
            background: var(--surface-container-high);
            color: var(--on-surface);
        }
        .sidebar-link.active {
            background: var(--primary);
            color: var(--on-primary);
            font-weight: 600;
        }
        .sidebar-link .icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="font-body antialiased overflow-hidden bg-surface text-on-surface">

    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-[var(--sidebar-width)] bg-white border-r border-outline-variant flex flex-col shrink-0">
            {{-- Logo --}}
            <div class="px-5 h-16 flex items-center border-b border-outline-variant">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary text-on-primary rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">layers</span>
                    </div>
                    <span class="font-bold text-on-surface font-display text-base">Orchestrix</span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-hide">
                <div class="px-3 py-2">
                    <span class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider opacity-50">Hệ thống</span>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">dashboard</span>
                    <span>Bảng điều khiển</span>
                </a>
                <a href="{{ route('admin.stores.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.stores.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">store</span>
                    <span>Cửa hàng</span>
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">inventory_2</span>
                    <span>Sản phẩm</span>
                </a>
                <a href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">receipt_long</span>
                    <span>Đơn hàng</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">confirmation_number</span>
                    <span>Mã giảm giá</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">group</span>
                    <span>Nhân sự</span>
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">settings</span>
                    <span>Cài đặt</span>
                </a>
                <a href="{{ route('admin.reports.revenue') }}"
                    class="sidebar-link {{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">bar_chart</span>
                    <span>Báo cáo</span>
                </a>
                <a href="{{ route('admin.modules.index') }}"
                    class="sidebar-link {{ Request::routeIs('admin.modules.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">extension</span>
                    <span>Module</span>
                </a>

                <a href="{{ route('admin.agriverse.dashboard') }}"
                    class="sidebar-link {{ Request::routeIs('admin.agriverse.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined icon">eco</span>
                    <span>AgriVerse</span>
                </a>
            </nav>

            {{-- Logout --}}
            <div class="px-3 py-4 border-t border-outline-variant">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left">
                        <span class="material-symbols-outlined icon">logout</span>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col min-w-0 bg-surface">
            {{-- Top Bar --}}
            <header class="h-16 bg-white border-b border-outline-variant flex items-center justify-between px-6 shrink-0">
                <h1 class="text-sm font-semibold text-on-surface">@yield('page-title', 'Bảng điều khiển')</h1>
                <div class="flex items-center gap-3">
                    <button class="material-symbols-outlined text-xl text-on-surface-variant hover:text-on-surface transition-colors">search</button>
                    <button class="material-symbols-outlined text-xl text-on-surface-variant hover:text-on-surface transition-colors">notifications</button>
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center ml-2">
                        <span class="material-symbols-outlined text-lg text-primary">person</span>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto p-6 scrollbar-hide bg-surface">
                <div class="max-w-[1400px] mx-auto">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    {{-- Toast --}}
    @if(session('success'))
        <div id="toast" class="fixed bottom-6 right-6 z-50">
            <div class="bg-white border border-outline-variant rounded-xl px-5 py-4 shadow-lg flex items-center gap-3 min-w-[300px]">
                <span class="material-symbols-outlined text-primary">check_circle</span>
                <span class="text-sm font-semibold text-on-surface">{{ session('success') }}</span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const t = document.getElementById('toast');
                if (t) { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(() => t.remove(), 300); }
            }, 4000);
        </script>
    @endif

    @stack('scripts')
</body>
</html>
