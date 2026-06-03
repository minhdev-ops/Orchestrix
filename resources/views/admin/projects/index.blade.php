@extends('layouts.admin')

@section('page-title', 'Quản lý Dự án')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Cấu hình Dự án</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý các domain và gán module cho từng dự án con.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.projects.create') }}" class="btn-premium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Thêm Dự án mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-tertiary/10 border border-tertiary/20 text-tertiary rounded-2xl font-bold flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02]">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Domain / Host</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Modules đang chạy</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $domain => $config)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-base font-black text-on-surface">{{ $domain }}</span>
                            <span class="text-xs text-on-surface-variant opacity-60">Status: Operational</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($config['modules'] as $module)
                            <span class="px-3 py-1 bg-primary/5 text-primary text-[10px] font-black uppercase tracking-widest rounded-full border border-primary/10">
                                {{ $module }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-3">
                            <a href="{{ route('admin.projects.edit', $domain) }}" class="p-2 text-on-surface-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <form action="{{ route('admin.projects.destroy', $domain) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-on-surface-variant hover:text-error transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
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
