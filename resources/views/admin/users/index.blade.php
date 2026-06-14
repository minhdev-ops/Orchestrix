@extends('layouts.admin')

@section('page-title', 'Quản lý Nhân viên')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Nhân sự Hệ thống</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý tài khoản truy cập và phân quyền module cho nhân viên.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.create') }}" class="btn-premium flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Thêm Nhân viên mới
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
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Nhân viên</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Vai trò</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em]">Quyền Module</th>
                    <th class="px-8 py-5 text-[10px] font-black text-primary uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-base font-black text-on-surface">{{ $user->name }}</span>
                                <span class="text-xs text-on-surface-variant opacity-60">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 bg-surface-container-high text-on-surface text-[10px] font-black uppercase tracking-widest rounded-full border border-outline-variant/30">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-wrap gap-2">
                            @if($user->user_permissions)
                                @foreach($user->user_permissions as $perm)
                                <span class="px-2 py-0.5 bg-primary/5 text-primary text-[9px] font-bold uppercase rounded border border-primary/10">
                                    {{ $perm }}
                                </span>
                                @endforeach
                            @else
                                <span class="text-[10px] text-on-surface-variant italic opacity-50">Không có quyền riêng biệt</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-3">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-on-surface-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa nhân viên này?');">
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
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
