@extends('layouts.admin')

@section('page-title', 'Thêm Nhân viên mới')

@section('content')
<div class="max-w-5xl space-y-10 pb-20">
    <div>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-primary font-black uppercase tracking-widest text-[10px] mb-6 hover:translate-x-[-4px] transition-transform">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Khởi tạo Nhân sự</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Thiết lập tài khoản quản trị và cấp quyền truy cập các module chức năng.</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
        @csrf
        <div class="grid grid-cols-12 gap-8">
            {{-- Basic Info --}}
            <div class="col-span-12 lg:col-span-7 space-y-8">
                <div class="card-premium !p-10 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Họ và Tên</label>
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Email Công việc</label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Mật khẩu tạm thời</label>
                            <input type="password" name="password" required 
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Vai trò (Role)</label>
                            <select name="role" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold appearance-none focus:border-primary/50 outline-none transition-all">
                                <option value="staff">Staff</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Admin System</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="col-span-12 lg:col-span-5">
                <div class="card-premium !p-8 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">security</span>
                        </div>
                        <h3 class="text-xl font-black text-on-surface font-display">Phân quyền Module</h3>
                    </div>

                    <div class="space-y-4">
                        @foreach($allModules as $module)
                        <label class="group flex items-center justify-between p-4 rounded-xl bg-surface-container-low/50 border border-transparent hover:border-primary/20 hover:bg-white transition-all cursor-pointer">
                            <div class="flex items-center gap-4">
                                <span class="material-symbols-outlined text-lg text-primary opacity-50">{{ $module['icon'] }}</span>
                                <span class="text-sm font-bold text-on-surface">{{ $module['name'] }}</span>
                            </div>
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="permissions[]" value="{{ $module['id'] }}.manage" class="peer hidden">
                                <div class="w-5 h-5 rounded-lg border-2 border-outline-variant/30 peer-checked:bg-primary peer-checked:border-primary transition-all flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-[14px] scale-0 peer-checked:scale-100 transition-transform">check</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button type="submit" class="btn-premium px-12 py-4">
                Xác nhận tạo Nhân viên
            </button>
        </div>
    </form>
</div>
@endsection
