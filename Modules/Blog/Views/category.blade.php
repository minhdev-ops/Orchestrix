<!DOCTYPE html>
<html lang="vi" class="scroll-smooth light">

<head>
    <script>(function () { const t = localStorage.getItem('theme'); if (t === 'dark') document.documentElement.classList.replace('light', 'dark'); })()</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} — Blog Orchestrix</title>
    <meta name="description"
        content="{{ $category->description ?? 'Bài viết về ' . $category->name . ' từ Orchestrix.' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @php $catColor = $category->color ?? '#3B82F6'; @endphp
    <style>
        :root {
            --cat:
                {{ $catColor }}
            ;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #F1F5F9;
            color: #1E293B;
            transition: background 0.2s ease, color 0.2s ease;
        }

        html.dark body {
            background: #131315;
            color: #e5e1e4;
        }

        .font-headline {
            font-family: 'Roboto', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, .8);
            backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(226, 232, 240, .7);
            transition: background 0.2s ease;
        }

        html.dark .glass {
            background: rgba(19, 19, 21, 0.85);
            border-bottom: 1px solid rgba(61, 73, 76, 0.5);
        }

        .cat-hero {
            background: linear-gradient(135deg, #0A0F1E 0%, #0F1729 40%,
                    {{ $catColor }}
                    22 100%);
            position: relative;
            overflow: hidden;
        }

        .cat-hero-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 80% at 80% 50%,
                    {{ $catColor }}
                    20 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 20% 80%,
                    {{ $catColor }}
                    10 0%, transparent 60%);
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .post-card {
            background: #fff;
            border: 1px solid rgba(226, 232, 240, .8);
            border-radius: 20px;
            overflow: hidden;
            transition: transform .3s cubic-bezier(.34, 1.56, .64, 1), box-shadow .3s ease, background 0.2s ease;
        }

        html.dark .post-card {
            background: #201f22;
            border-color: rgba(61, 73, 76, 0.6);
        }

        .post-card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 24px 48px rgba(15, 23, 42, .10);
        }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #94A3B8;
        }

        .stat-badge .material-symbols-outlined {
            font-size: 13px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp .4s ease backwards;
        }

        .delay-1 {
            animation-delay: .06s;
        }

        .delay-2 {
            animation-delay: .12s;
        }

        .delay-3 {
            animation-delay: .18s;
        }

        .delay-4 {
            animation-delay: .24s;
        }

        .delay-5 {
            animation-delay: .30s;
        }

        .delay-6 {
            animation-delay: .36s;
        }
    </style>
</head>

<body>

    <!-- NAV -->
    <nav class="sticky top-0 z-50 glass">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('blog.index') }}"
                    class="flex items-center gap-1.5 text-slate-500 hover:text-slate-900 text-sm font-medium group">
                    <span
                        class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                    Blog
                </a>
                <span class="text-slate-200">›</span>
                <span class="text-sm font-bold" style="color: {{ $catColor }}">{{ $category->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}"
                    class="font-headline font-bold text-xl text-slate-900 dark:text-white italic uppercase">
                    Orchestrix<span class="text-blue-600">.</span>
                </a>
                <!-- Dark mode toggle -->
                <button onclick="toggleBlogTheme()"
                    class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-white/10"
                    title="Chuyển chế độ sáng/tối">
                    <span class="material-symbols-outlined text-[18px]" id="blog-theme-icon">dark_mode</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="cat-hero py-16 px-6">
        <div class="cat-hero-bg"></div>
        <div class="hero-grid"></div>
        <!-- Big Icon in background -->
        <div class="absolute right-12 top-1/2 -translate-y-1/2 opacity-5">
            <span class="material-symbols-outlined text-white"
                style="font-size: 200px;">{{ $category->icon ?? 'article' }}</span>
        </div>
        <div class="max-w-7xl mx-auto relative z-10">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-xl"
                style="background: linear-gradient(135deg, {{ $catColor }}, {{ $catColor }}88)">
                <span class="material-symbols-outlined text-white text-[28px]">{{ $category->icon ?? 'article' }}</span>
            </div>
            <h1 class="font-headline font-bold text-4xl md:text-5xl text-white mb-3">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-slate-400 text-lg max-w-xl leading-relaxed mb-4">{{ $category->description }}</p>
            @endif
            <div class="flex items-center gap-4 text-sm text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">article</span>
                    {{ $posts->total() }} bài viết
                </span>
            </div>
        </div>
    </section>

    <!-- CATEGORY TABS -->
    <div style="background: rgba(255,255,255,.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(226,232,240,.7);"
        class="sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-1 py-2.5 overflow-x-auto" style="scrollbar-width:none">
                <a href="{{ route('blog.index') }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-all whitespace-nowrap">
                    Tất cả
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                        class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5
                                                  {{ $cat->id === $category->id ? 'text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}"
                        style="{{ $cat->id === $category->id ? 'background:' . $cat->color . ';' : '' }}">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $cat->color }}"></span>
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- POSTS -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        @if($posts->isEmpty())
            <div class="text-center py-24 text-slate-400">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-5">
                    <span class="material-symbols-outlined text-4xl text-slate-300">article</span>
                </div>
                <h3 class="font-headline font-bold text-xl text-slate-700 mb-2">Chưa có bài viết</h3>
                <p class="text-sm">Chuyên mục này chưa có bài viết nào. Hãy quay lại sau!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($posts as $i => $post)
                    <div class="fade-up delay-{{ min($i % 6 + 1, 6) }}">
                        @include('blog.partials.post-card', ['post' => $post])
                    </div>
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="flex items-center gap-2 bg-white rounded-2xl border border-slate-200 p-1.5 shadow-sm">
                        @if($posts->onFirstPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-300"><span
                                    class="material-symbols-outlined text-[18px]">chevron_left</span></span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 transition-colors"><span
                                    class="material-symbols-outlined text-[18px]">chevron_left</span></a>
                        @endif
                        @foreach($posts->links()->elements[0] as $page => $url)
                            @if($page == $posts->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-xl text-white text-sm font-bold"
                                    style="background: {{ $catColor }}">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-medium transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 transition-colors"><span
                                    class="material-symbols-outlined text-[18px]">chevron_right</span></a>
                        @else
                            <span class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-300"><span
                                    class="material-symbols-outlined text-[18px]">chevron_right</span></span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </main>

    <footer class="bg-slate-900 text-slate-500 py-10 mt-16">
        <div class="max-w-7xl mx-auto px-6 text-center text-sm">
            <span class="font-headline font-bold text-white italic uppercase">Orchestrix<span
                    class="text-blue-500">.</span></span>
            <p class="mt-2">© {{ date('Y') }} Orchestrix. All rights reserved.</p>
        </div>
    </footer>
    <script>
        function toggleBlogTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.replace('dark', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.replace('light', 'dark');
                localStorage.setItem('theme', 'dark');
            }
            const icon = document.getElementById('blog-theme-icon');
            if (icon) icon.textContent = document.documentElement.classList.contains('dark') ? 'light_mode' : 'dark_mode';
        }
        // Init icon on load
        document.addEventListener('DOMContentLoaded', function () {
            const icon = document.getElementById('blog-theme-icon');
            if (icon) icon.textContent = document.documentElement.classList.contains('dark') ? 'light_mode' : 'dark_mode';
        });
    </script>
</body>

</html>