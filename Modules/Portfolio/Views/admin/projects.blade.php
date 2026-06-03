@extends('layouts.admin')

@section('page-title', 'Quản lý Dự án')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-8 animate-fade-up">
        {{-- Header Actions --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Danh sách Dự án</h2>
                <p class="text-xs text-on-surface-variant uppercase tracking-widest mt-1">Tổng cộng:
                    {{ $projects->count() }} dự án
                </p>
            </div>
            <a href="{{ route('admin.portfolio.projects.create') }}"
                class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm font-bold">add</span>
                Thêm dự án mới
            </a>
        </div>

        {{-- Projects Table/List --}}
        <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50 border-b border-outline-variant/10">
                        <th class="px-8 py-5 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Ảnh
                        </th>
                        <th class="px-8 py-5 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Dự án
                        </th>
                        <th class="px-8 py-5 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Phân
                            loại</th>
                        <th
                            class="px-8 py-5 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest text-right">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($projects as $project)
                        <tr class="hover:bg-surface-container-high/30 transition-colors">
                            <td class="px-8 py-6">
                                <div
                                    class="w-12 h-12 rounded-lg bg-surface-container-high overflow-hidden border border-outline-variant/10">
                                    @if($project->image)
                                        <img src="{{ str_starts_with($project->image, 'http') ? $project->image : asset('storage/' . $project->image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
                                            <x-portfolio.icon name="image" />
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-on-surface uppercase tracking-tight">{{ $project->title }}</div>
                                <div class="text-[10px] text-on-surface-variant/60 uppercase tracking-widest mt-1">Slug:
                                    {{ $project->slug }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1.5">
                                    <span
                                        class="px-3 py-1 bg-surface-container-high rounded-full text-[10px] font-bold text-on-surface-variant uppercase tracking-widest w-fit">
                                        {{ $project->category }}
                                    </span>
                                    @if($project->is_featured)
                                        <span
                                            class="px-3 py-1 bg-primary/10 rounded-full text-[10px] font-black text-primary uppercase tracking-[0.2em] w-fit border border-primary/20">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.portfolio.projects.show', $project) }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant hover:text-primary transition-all active:scale-95"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                    <a href="{{ route('admin.portfolio.projects.edit', $project) }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant hover:text-primary transition-all active:scale-95"
                                        title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <form action="{{ route('admin.portfolio.projects.destroy', $project) }}" method="POST"
                                        onsubmit="return confirm('Xác nhận xóa dự án này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant hover:text-red-500 transition-all active:scale-95"
                                            title="Xóa">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection