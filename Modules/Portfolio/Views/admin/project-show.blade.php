@extends('layouts.admin')

@section('page-title', 'Chi tiết Dự án')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-8 animate-fade-up">
        {{-- Top Header with Actions --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.portfolio.projects') }}"
                    class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">{{ $project->title }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span
                            class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[8px] font-black uppercase tracking-widest">{{ $project->category }}</span>
                        @if($project->is_featured)
                            <span
                                class="px-2 py-0.5 rounded-full bg-secondary/10 text-secondary text-[8px] font-black uppercase tracking-widest">Featured
                                Project</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.portfolio.projects.edit', $project) }}"
                    class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Chỉnh sửa
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Content & Details --}}
            <div class="lg:col-span-8 space-y-8">
                {{-- Main Info Card --}}
                <div class="bg-surface-container-low border border-outline-variant/10 rounded-3xl p-10 space-y-10">
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Mô tả chi tiết
                        </h3>
                        <div class="prose prose-invert max-w-none text-on-surface leading-relaxed">
                            {!! str($project->content_md)->markdown() !!}
                        </div>
                    </div>

                    @if($project->tech_stack)
                        <div class="pt-10 border-t border-outline-variant/10 space-y-4">
                            <h3 class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Công nghệ sử
                                dụng</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->tech_stack as $tech)
                                    <span
                                        class="px-4 py-2 bg-surface rounded-xl border border-outline-variant/10 text-[10px] font-bold text-on-surface uppercase tracking-widest">{{ trim($tech) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Preview & Metadata --}}
            <div class="lg:col-span-4 space-y-8">
                {{-- Image Preview Card --}}
                <div class="bg-surface-container-low border border-outline-variant/10 rounded-3xl overflow-hidden p-2">
                    <div class="aspect-video rounded-2xl overflow-hidden bg-surface relative group">
                        @if($project->image)
                            <img src="{{ str_starts_with($project->image, 'http') ? $project->image : asset('storage/' . $project->image) }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
                                <span class="material-symbols-outlined text-6xl">image</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Links & Visibility Card --}}
                <div class="bg-surface-container-low border border-outline-variant/10 rounded-3xl p-8 space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Đường dẫn dự án
                        </h3>
                        @if($project->link)
                            <a href="{{ $project->link }}" target="_blank"
                                class="flex items-center justify-between p-4 bg-surface rounded-2xl border border-outline-variant/5 text-primary hover:border-primary/30 transition-all group">
                                <span class="text-xs font-bold truncate pr-4">{{ $project->link }}</span>
                                <span
                                    class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">open_in_new</span>
                            </a>
                        @else
                            <div
                                class="p-4 bg-surface/50 rounded-2xl border border-dashed border-outline-variant/20 text-on-surface-variant/40 text-[10px] font-bold uppercase tracking-widest text-center">
                                Chưa có đường dẫn
                            </div>
                        @endif
                    </div>

                    <div class="pt-6 border-t border-outline-variant/10 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Trạng
                                thái</span>
                            <span class="flex items-center gap-2">
                                <span
                                    class="w-2 h-2 rounded-full {{ $project->is_visible ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest">{{ $project->is_visible ? 'Hiển thị' : 'Đang ẩn' }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Ngày
                                tạo</span>
                            <span
                                class="text-[10px] font-bold text-on-surface uppercase tracking-widest">{{ $project->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection