@extends('layouts.admin')

@section('page-title', 'Quản lý Module')

@section('content')
    <div class="space-y-10 pb-20">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-up">
            <div>
                <span class="font-black text-[10px] tracking-[0.3em] text-tertiary uppercase">Management_Core</span>
                <h2 class="text-4xl font-black text-on-surface tracking-tighter uppercase mt-1">Module <span
                        class="text-primary italic">Registry.</span></h2>
                <p class="text-on-surface-variant font-light mt-2 tracking-wide uppercase text-[10px]">Cấp phát và điều phối
                    các đơn vị chức năng trong hệ thống.</p>
            </div>
            <div class="flex items-center gap-4">
                <div
                    class="px-6 py-3 rounded-xl bg-surface-container-low border border-outline-variant/10 text-on-surface text-[10px] font-black uppercase tracking-widest">
                    Active: {{ count($activeModules) }} / {{ count($allModules) }}
                </div>
                <button
                    class="bg-primary hover:bg-primary/90 text-on-primary px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                    Scan Services
                </button>
            </div>
        </div>

        {{-- Module Grid/List --}}
        <div class="grid grid-cols-1 gap-4">
            @foreach($allModules as $module)
                <div class="group bg-surface-container-low border border-outline-variant/10 rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-8 hover:border-primary/30 transition-all duration-500 animate-fade-up"
                    style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-16 h-16 rounded-2xl bg-surface-container-high border border-outline-variant/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-all duration-500 shadow-inner">
                            <span class="material-symbols-outlined text-3xl font-light">{{ $module['icon'] }}</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">{{ $module['name'] }}
                                </h3>
                                <span
                                    class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded {{ $module['enabled'] ? 'bg-tertiary/10 text-tertiary border border-tertiary/20' : 'bg-on-surface-variant/10 text-on-surface-variant border border-on-surface-variant/20' }}">
                                    {{ $module['enabled'] ? 'Operational' : 'Disabled' }}
                                </span>
                            </div>
                            <p class="text-[10px] text-on-surface-variant uppercase tracking-wide font-light opacity-60">
                                {{ $module['description'] }}
                            </p>
                            <div class="flex items-center gap-4 pt-2">
                                <span class="text-[8px] font-mono text-on-surface-variant/40 italic uppercase">ID:
                                    {{ $module['id'] }}_NODE</span>
                                <span class="text-[8px] font-mono text-on-surface-variant/40 italic uppercase">CRC:
                                    0x{{ substr(md5($module['id']), 0, 8) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <div class="hidden lg:flex flex-col items-end px-6 border-r border-outline-variant/10">
                            <span class="text-[8px] text-on-surface-variant/40 uppercase tracking-widest leading-none">Status
                                Check</span>
                            <span
                                class="text-[10px] font-black {{ $module['enabled'] ? 'text-tertiary' : 'text-on-surface-variant/20' }} mt-1 uppercase">100%
                                Secure</span>
                        </div>
                        <div class="flex-1 md:flex-none flex items-center gap-3">
                            <form action="{{ route('admin.modules.toggle', $module['id']) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-32 py-3 rounded-xl border border-outline-variant/20 {{ $module['enabled'] ? 'text-error hover:bg-error/10' : 'text-primary hover:bg-primary/10' }} text-[10px] font-black uppercase tracking-widest transition-all active:scale-95">
                                    {{ $module['enabled'] ? 'Shutdown' : 'Execution' }}
                                </button>
                            </form>
                            <button
                                class="w-12 h-12 flex items-center justify-center rounded-xl bg-surface-container-high border border-outline-variant/10 text-on-surface-variant hover:text-primary transition-all">
                                <span class="material-symbols-outlined text-[20px]">terminal</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Terminal Placeholder --}}
            <div
                class="mt-10 p-8 rounded-3xl bg-[#010409] border border-outline-variant/10 font-mono text-[10px] text-on-surface-variant/40 space-y-2 group hover:border-primary/20 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="text-tertiary">➜</span>
                    <span class="text-secondary">admin@orchestrix</span>
                    <span class="text-on-surface">:</span>
                    <span class="text-primary italic">~/registry</span>
                    <span class="text-on-surface">$</span>
                    <span class="text-on-surface animate-pulse">|</span>
                </div>
                <p>System initialized. Waiting for admin instruction...</p>
            </div>
        </div>
    </div>
@endsection