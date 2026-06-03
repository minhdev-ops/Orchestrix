<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Orchestrix | Senior Software Architect')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,FILL@0..1,GRAD@0,opsz@20..48&display=block"
        rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>

<body class="antialiased selection:bg-brand/10 bg-background text-text transition-colors duration-300">
    {{-- Standalone Page 3D Background --}}
    <canvas id="three-canvas" class="fixed inset-0 z-[-1] pointer-events-none opacity-40"></canvas>

    {{-- Standalone Page Shell (Blade) --}}
    <div class="min-h-screen flex flex-col relative z-10">
        {{-- Navbar (Blade Mirror of React version) --}}
        <nav class="fixed top-0 left-0 right-0 z-50 bg-background/80 backdrop-blur-md border-b border-border px-6 py-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-background font-bold shadow-lg shadow-brand/20">
                        OX
                    </div>
                    <span class="text-[20px] font-bold tracking-tight text-text uppercase">
                        Orchestrix
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}"
                        class="text-[14px] font-bold text-text-muted hover:text-brand transition-colors">Trang chủ</a>

                    @if(Route::has('blog.index'))
                    <a href="{{ route('blog.index') }}"
                        class="text-[14px] font-bold {{ request()->routeIs('blog.*') ? 'text-brand' : 'text-text-muted' }} hover:text-brand transition-colors">Bài
                        viết</a>
                    @endif

                    <a href="{{ url('/#signals') }}"
                        class="text-[14px] font-bold text-text-muted hover:text-brand transition-colors">Liên hệ</a>
                </div>

                <div class="flex items-center gap-4">
                    <button
                        class="px-6 py-2 rounded-full bg-brand text-background text-[14px] font-bold hover:bg-brand-hover transition-all flex items-center gap-2 group shadow-lg shadow-brand/10">
                        Kết nối
                        <span
                            class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        {{-- Footer (Blade Mirror of React version) --}}
        <footer class="py-20 px-6 bg-background-dark text-text border-t border-border mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center text-background font-bold shadow-lg shadow-brand/20">
                        OX
                    </div>
                    <span class="text-[20px] font-bold tracking-tight uppercase">
                        Orchestrix
                    </span>
                </div>

                <div class="flex gap-8 text-text-muted text-[14px] font-bold uppercase tracking-widest">
                    <a href="#" class="hover:text-brand transition-colors">Twitter</a>
                    <a href="#" class="hover:text-brand transition-colors">GitHub</a>
                    <a href="#" class="hover:text-brand transition-colors">LinkedIn</a>
                </div>

                <div class="text-text-muted text-[14px] font-medium">
                    © 2026 Orchestrix Orchestration. Bảo lưu mọi bản quyền.
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
