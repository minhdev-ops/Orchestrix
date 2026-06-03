@extends('layouts.admin')

@section('page-title', 'Hệ thống Điều khiển')

@section('content')
    <div class="space-y-8 pb-20">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Dashboard</h2>
                <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Tổng quan hệ thống Orchestrix AgriVerse.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-6 py-3 rounded-2xl bg-surface-container-low text-on-surface text-xs font-black uppercase tracking-widest border border-outline-variant hover:bg-surface-container-high transition-all">
                    Đồng bộ dữ liệu
                </button>
                <button class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
                    Báo cáo hệ thống
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-12 gap-6">
            {{-- Store --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">store</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Cửa hàng</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $storesCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Products --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">inventory_2</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Sản phẩm</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $productsCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Users --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">group</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Người dùng</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $usersCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Orders --}}
            <div class="col-span-12 md:col-span-3 glass-panel rounded-3xl p-6 flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-on-surface-variant text-[9px] font-black uppercase tracking-widest opacity-60">Đơn hàng</h3>
                    <p class="text-2xl font-black text-on-surface tracking-tight">{{ $ordersCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Activity --}}
            <div class="col-span-12 glass-panel rounded-3xl p-8 overflow-hidden relative">
                <div class="absolute right-0 bottom-0 w-32 h-32 bg-primary/5 blur-3xl pointer-events-none"></div>
                <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.25em] mb-6">Hoạt động gần đây</h3>
                <div class="space-y-4">
                    <div class="flex gap-4 items-start">
                        <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shadow-[0_0_10px_rgba(0,82,255,0.4)]"></div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">Hệ thống sẵn sàng</p>
                            <p class="text-xs text-on-surface-variant opacity-60">Orchestrix AgriVerse đang hoạt động.</p>
                            <p class="text-[9px] font-black text-primary uppercase mt-2 tracking-widest">Vừa xong</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
