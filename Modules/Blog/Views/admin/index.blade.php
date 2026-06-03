@extends('layouts.admin')

@section('page-title', 'Quản lý Blog')

@section('module-nav')
    <div class="px-4 py-3">
        <a href="{{ route('admin.blog.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.blog.index') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
            <span class="material-symbols-outlined text-xl">article</span>
            <span class="text-sm">Bài viết</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-up">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Thư viện bài viết</h2>
                <p class="text-xs text-on-surface-variant uppercase tracking-widest mt-1">Truyền thông kiến thức kỹ thuật
                </p>
            </div>
            <a href="{{ route('admin.blog.create') }}"
                class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm font-bold">add</span>
                Viết bài mới
            </a>
        </div>

        <div class="bg-surface-container-low border border-outline-variant/10 rounded-3xl overflow-hidden shadow-2xl shadow-primary/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50 border-b border-outline-variant/10">
                        <th class="px-8 py-6 text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em]">Bài viết</th>
                        <th class="px-8 py-6 text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em]">Chuyên mục</th>
                        <th class="px-8 py-6 text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em]">Trạng thái</th>
                        <th class="px-8 py-6 text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em] text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($posts as $post)
                        <tr class="hover:bg-primary/[0.02] transition-colors group">
                            <td class="px-8 py-8">
                                <div class="flex flex-col gap-1">
                                    <div class="font-black text-on-surface uppercase tracking-tight group-hover:text-primary transition-colors">{{ $post->title }}</div>
                                    <div class="text-[10px] text-on-surface-variant/40 font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[10px]">schedule</span>
                                        {{ $post->published_at_formatted }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <span class="px-3 py-1 bg-surface-container-high rounded-full text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                                    {{ $post->category->name }}
                                </span>
                            </td>
                            <td class="px-8 py-8">
                                @if($post->is_published)
                                    <div class="flex items-center gap-2 text-green-500 font-black text-[10px] uppercase tracking-[0.15em] bg-green-500/10 px-3 py-1 rounded-full w-fit border border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                                        Published
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-on-surface-variant/40 font-black text-[10px] uppercase tracking-[0.15em] bg-surface-container-high px-3 py-1 rounded-full w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-on-surface-variant/20"></span>
                                        Draft
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-8 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.blog.edit', $post) }}"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-high text-on-surface-variant hover:text-primary transition-all active:scale-90"
                                        title="Chỉnh sửa">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </a>
                                    <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                        onsubmit="return confirm('Xác nhận xóa bài viết này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-high text-on-surface-variant hover:text-red-500 transition-all active:scale-90"
                                            title="Xóa">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4 text-on-surface-variant/30">
                                    <span class="material-symbols-outlined text-6xl">article_off</span>
                                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">Chưa có bài viết nào</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $posts->links() }}
        </div>
    </div>
@endsection