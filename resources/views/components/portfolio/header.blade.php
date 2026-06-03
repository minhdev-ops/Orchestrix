<nav
    class="fixed top-0 w-full z-50 bg-white/80 dark:bg-[#0a0b0d]/80 backdrop-blur-xl border-b border-gray-100 dark:border-white/5 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        {{-- BRAND --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('portfolio.index') }}" class="flex items-center gap-2 group">
                <div
                    class="w-8 h-8 bg-[#0052ff] flex items-center justify-center rounded-lg shadow-lg shadow-[#0052ff]/20">
                    <span class="text-white text-[12px] font-bold">OX</span>
                </div>
                <span class="text-[20px] font-display text-[#0a0b0d] dark:text-white tracking-tight">ORCHESTRIX</span>
            </a>
        </div>

        {{-- LINKS --}}
        <div class="hidden md:flex items-center gap-8">
            @php
                $nav = [
                    ['label' => 'Base', 'url' => route('portfolio.index')],
                    ['label' => 'Dossier', 'url' => route('portfolio.about')],
                    ['label' => 'Stack', 'url' => route('portfolio.skills.index')],
                    ['label' => 'Registry', 'url' => route('portfolio.projects.index')],
                ];
            @endphp

            @foreach($nav as $item)
                <a href="{{ $item['url'] }}"
                    class="text-[14px] font-bold transition-all
                              {{ Request::url() == $item['url'] ? 'text-[#0052ff]' : 'text-[#5b616e] dark:text-gray-400 hover:text-[#0052ff]' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center gap-4">
            <div
                class="hidden sm:flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 rounded-full border border-emerald-100 dark:border-emerald-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span
                    class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-widest">SECURE_NODE</span>
            </div>

            <a href="{{ route('portfolio.contact') }}" class="btn-pill btn-primary px-6 py-2.5 text-xs">
                Uplink
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>

            <button class="md:hidden text-[#0a0b0d] dark:text-white">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
        </div>
    </div>
</nav>