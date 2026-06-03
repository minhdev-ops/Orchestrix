<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bảng quản trị | Orchestrix</title>

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@900,700,500,300,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.min.js"></script>

    <script>
        (function () {
            const t = localStorage.getItem('theme') || 'dark'; // Default to dark for Quantum Void
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(t);
        })();
    </script>

    <style>
        :root {
            --font-display: 'Satoshi', sans-serif;
            --font-body: 'Satoshi', sans-serif;
        }
        body {
            font-family: var(--font-body), sans-serif;
            background-color: #000; /* Deepest void */
            color: var(--on-surface);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .dark .glass-panel {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        #quantum-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>

<body class="font-body antialiased overflow-hidden bg-black text-on-surface">
    <canvas id="quantum-canvas"></canvas>

    <div class="flex h-screen relative z-10">
        {{-- Sidebar --}}
        <aside
            class="w-72 glass-panel border-r border-white/5 flex flex-col shrink-0 relative z-20">
            <div class="p-8 border-b border-white/5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 transition-all group">
                    <div
                        class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">layers</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-white tracking-tight text-lg font-display">Orchestrix</span>
                        <span
                            class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-black">Command Center</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-6 space-y-1.5 scrollbar-hide">
                {{-- ... existing nav ... --}}
                <div class="px-3 pb-2 pt-4">
                    <span class="text-[10px] font-black text-white/30 uppercase tracking-[0.25em]">Hệ thống</span>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all {{ Request::routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-500/25' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[22px]">dashboard</span>
                    <span class="text-sm">Bảng điều khiển</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all {{ Request::routeIs('admin.users.*') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-500/25' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[22px]">group</span>
                    <span class="text-sm">Nhân sự Hệ thống</span>
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all {{ Request::routeIs('admin.settings.*') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-500/25' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[22px]">settings</span>
                    <span class="text-sm">Cài đặt chung</span>
                </a>

            </nav>

            <div class="p-8 border-t border-white/5">
                <button onclick="window.toggleTheme()"
                    class="w-full h-12 flex items-center justify-center gap-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-all text-white/60 font-bold text-xs border border-white/5">
                    <span class="material-symbols-outlined text-[20px]" data-theme-icon>light_mode</span>
                    <span>Chế độ hiển thị</span>
                </button>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col min-w-0 bg-transparent relative">
            {{-- Top Navbar --}}
            <header
                class="h-16 glass-panel border-b border-white/5 flex items-center justify-between px-8 shrink-0 sticky top-0 z-10">
                <div>
                    <h1 class="text-sm font-bold text-white uppercase tracking-wider font-display">
                        @yield('page-title', 'Bảng điều khiển')</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-4 pr-4 border-r border-white/5 text-white/60">
                        <button
                            class="material-symbols-outlined hover:text-indigo-400 transition-colors cursor-pointer text-xl">search</button>
                        <button
                            class="material-symbols-outlined hover:text-indigo-400 transition-colors cursor-pointer text-xl">notifications</button>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-end">
                            <span class="text-xs font-bold text-white">Quản trị viên</span>
                            <span class="text-[9px] text-indigo-400 uppercase font-black">Trực tuyến</span>
                        </div>
                        <div
                            class="w-9 h-9 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center shadow-lg shadow-indigo-500/5">
                            <span class="material-symbols-outlined text-[20px] text-indigo-400">person</span>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto p-10 scrollbar-hide relative bg-transparent">
                <div class="max-w-[1600px] mx-auto w-full relative z-10">
                    {{-- Standard Breadcrumbs --}}
                    <nav
                        class="flex items-center gap-2 mb-8 text-[11px] font-bold uppercase tracking-widest text-white/40">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-400 transition-colors">Admin</a>
                        <span class="material-symbols-outlined text-[14px] opacity-30">chevron_right</span>
                        <span class="text-white/80">@yield('page-title')</span>
                    </nav>

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        // Three.js Quantum Void Background
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('quantum-canvas'), alpha: true, antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        const particlesGeometry = new THREE.BufferGeometry();
        const count = 3000;
        const positions = new Float32Array(count * 3);
        for(let i = 0; i < count * 3; i++) {
            positions[i] = (Math.random() - 0.5) * 15;
        }
        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

        const particlesMaterial = new THREE.PointsMaterial({
            size: 0.015,
            color: '#6366f1',
            transparent: true,
            opacity: 0.8,
            blending: THREE.AdditiveBlending
        });

        const particles = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particles);

        camera.position.z = 3;

        function animate() {
            requestAnimationFrame(animate);
            particles.rotation.y += 0.0005;
            particles.rotation.x += 0.0002;
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>

    {{-- Notification System --}}
    @if(session('success'))
        <div id="toast" class="fixed bottom-10 right-10 z-[100] animate-fade-up">
            <div
                class="bg-surface-container-high border border-primary/20 rounded-2xl p-6 shadow-2xl flex items-center gap-4 min-w-[320px]">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-primary uppercase tracking-widest">Thông báo hệ thống</div>
                    <div class="text-sm font-black text-on-surface uppercase tracking-tight">{{ session('success') }}</div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                toast.classList.add('opacity-0', 'translate-y-10');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        </script>
    @endif

    <script>
        window.toggleTheme = function () {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            html.classList.remove('light', 'dark');
            if (isDark) {
                html.classList.add('light');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcons();
        };

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('[data-theme-icon]').forEach(el => {
                el.textContent = isDark ? 'light_mode' : 'dark_mode';
            });
        }
        document.addEventListener('DOMContentLoaded', updateThemeIcons);
    </script>
    @stack('scripts')
</body>

</html>