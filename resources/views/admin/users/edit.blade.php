@extends('layouts.admin')

@section('page-title', 'Sửa thông tin Nhân viên')

@section('content')
<div class="max-w-5xl space-y-10 pb-20">
    <div>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-primary font-black uppercase tracking-widest text-[10px] mb-6 hover:translate-x-[-4px] transition-transform">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Chỉnh sửa Nhân sự</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Cập nhật thông tin và quyền truy cập cho {{ $user->name }}.</p>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-7 space-y-8">
                <div class="card-premium !p-10 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Họ và Tên</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Email Công việc</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Mật khẩu mới <span class="opacity-50">(để trống nếu không đổi)</span></label>
                            <input type="password" name="password"
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Vai trò (Role)</label>
                            <select name="role" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold appearance-none focus:border-primary/50 outline-none transition-all">
                                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin System</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Kích hoạt</label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary">
                            <span class="text-sm font-bold text-on-surface">Tài khoản đang hoạt động</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-5">
                <div class="card-premium !p-8 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">security</span>
                        </div>
                        <h3 class="text-xl font-black text-on-surface font-display">Vai trò</h3>
                    </div>
                    <p class="text-sm text-on-surface-variant opacity-70">Quyền truy cập được quản lý qua vai trò (role).</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.users.index') }}" class="btn-premium-outline px-12 py-4">Hủy</a>
            <button type="submit" class="btn-premium px-12 py-4">
                Cập nhật Nhân viên
            </button>
        </div>
    </form>
</div>
@endsection
