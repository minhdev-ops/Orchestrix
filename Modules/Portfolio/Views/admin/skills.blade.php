@extends('layouts.admin')

@section('page-title', 'Quản lý Kỹ năng')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-8 animate-fade-up">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Ma trận Kỹ năng</h2>
                <p class="text-xs text-on-surface-variant uppercase tracking-widest mt-1">Quản lý năng lực kỹ thuật</p>
            </div>
            <a href="{{ route('admin.portfolio.skills.create') }}"
                class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm font-bold">add</span>
                Thêm kỹ năng mới
            </a>
        </div>

        {{-- Skills Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($skills as $skill)
                <div
                    class="bg-surface-container-low border border-outline-variant/10 rounded-3xl p-8 group hover:border-primary/30 hover:shadow-2xl hover:shadow-primary/5 transition-all relative overflow-hidden">
                    {{-- Decorative background element --}}
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-primary/5 blur-3xl rounded-full group-hover:bg-primary/10 transition-colors">
                    </div>

                    <div class="flex items-start justify-between mb-8 relative z-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-surface flex items-center justify-center text-primary border border-outline-variant/5 shadow-inner group-hover:scale-110 transition-transform duration-500">
                            <x-portfolio.icon :name="$skill->icon" width="28" />
                        </div>
                        <div
                            class="flex items-center gap-2 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <a href="{{ route('admin.portfolio.skills.edit', $skill) }}"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-high text-on-surface-variant hover:text-primary transition-all active:scale-90"
                                title="Chỉnh sửa">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form action="{{ route('admin.portfolio.skills.destroy', $skill) }}" method="POST"
                                onsubmit="return confirm('Xác nhận xóa kỹ năng này?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-high text-on-surface-variant hover:text-red-500 transition-all active:scale-90"
                                    title="Xóa">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <div>
                            <div class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-1">
                                {{ $skill->category }}</div>
                            <div class="text-lg font-black text-on-surface uppercase tracking-tight">{{ $skill->name }}</div>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex justify-between items-end">
                                <span
                                    class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest opacity-60">Mức
                                    độ chuyên gia</span>
                                <span class="text-sm font-black text-primary">{{ $skill->level }}%</span>
                            </div>
                            <div
                                class="h-2 w-full bg-surface rounded-full overflow-hidden p-0.5 border border-outline-variant/5">
                                <div class="h-full bg-primary rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(76,215,246,0.5)]"
                                    style="width: {{ $skill->level }}%">
                                </div>
                            </div>
                        </div>

                        @if($skill->description)
                            <p class="text-[11px] text-on-surface-variant/70 leading-relaxed line-clamp-2">
                                {{ $skill->description }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection