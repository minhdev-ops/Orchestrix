@extends('layouts.admin')

@section('page-title', 'Hệ thống Điều khiển')

@section('content')
    <div class="space-y-8 pb-20 relative">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 relative z-10">
            <div>
                <h2 class="text-5xl font-black text-white tracking-tighter font-display leading-none">Quantum Dashboard</h2>
                <p class="text-white/40 text-lg mt-3 font-medium max-w-xl">Real-time neural monitoring of your Orchestrix ecosystem modules and core resources.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-6 py-3 rounded-2xl bg-white/5 text-white text-xs font-black uppercase tracking-widest border border-white/10 hover:bg-white/10 transition-all">
                    Sync Data
                </button>
                <button class="px-6 py-3 rounded-2xl bg-indigo-600 text-white text-xs font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:scale-105 transition-all">
                    System Report
                </button>
            </div>
        </div>

        {{-- Bento Grid --}}
        <div class="grid grid-cols-12 gap-6 auto-rows-[120px]">
            {{-- Small Stat - System Version --}}
            <div class="col-span-12 md:col-span-3 row-span-1 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-white/5 text-white/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">security</span>
                </div>
                <div>
                    <h3 class="text-white/30 text-[9px] font-black uppercase tracking-widest">Core Version</h3>
                    <p class="text-xl font-black text-white tracking-tight">v1.2.0</p>
                </div>
            </div>

            {{-- System Health Vertical --}}
            <div class="col-span-12 md:col-span-3 row-span-2 glass-panel rounded-3xl p-8 flex flex-col justify-between">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.25em] mb-6">Neural Health</h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-white/40">DB Load</span>
                            <span class="text-white">98%</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: 98%"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-white/40">API Latency</span>
                            <span class="text-white">12ms</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full">
                            <div class="h-full bg-white/20 rounded-full" style="width: 40%"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-white/40">Uptime</span>
                            <span class="text-white">99.9%</span>
                        </div>
                        <div class="h-1 w-full bg-white/5 rounded-full">
                            <div class="h-full bg-indigo-400 rounded-full" style="width: 99.9%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Bottom Activity Section --}}
            <div class="col-span-12 md:col-span-9 row-span-2 glass-panel rounded-3xl p-8 overflow-hidden relative">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-white/5 blur-3xl pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.25em] mb-8">Pulse Activity</h3>
                <div class="space-y-6">
                    <div class="flex gap-4 items-start">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div>
                        <div>
                            <p class="text-sm font-bold text-white">System Core Update</p>
                            <p class="text-xs text-white/30">Successfully deployed v1.2.0 across all edge nodes.</p>
                            <p class="text-[9px] font-black text-indigo-400 uppercase mt-2 tracking-widest">2 min ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection