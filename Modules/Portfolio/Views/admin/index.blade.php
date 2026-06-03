@extends('layouts.admin')

@section('page-title', 'Quản lý Portfolio')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-10 animate-fade-up">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Projects Stats --}}
            <a href="{{ route('admin.portfolio.projects') }}"
                class="group bg-surface-container-low border border-outline-variant/10 rounded-2xl p-8 space-y-6 hover:border-primary/40 transition-all active:scale-95 block">
                <div
                    class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-portfolio.icon name="terminal" class="text-2xl" />
                </div>
                <div>
                    <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">Dự án kỹ thuật</h3>
                    <p class="text-on-surface-variant font-light mt-1">Hiện có {{ $projectCount }} dự án.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest group-hover:gap-4 transition-all">
                    Quản lý dự án
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </div>
            </a>

            {{-- Skills Stats --}}
            <a href="{{ route('admin.portfolio.skills') }}"
                class="group bg-surface-container-low border border-outline-variant/10 rounded-2xl p-8 space-y-6 hover:border-primary/40 transition-all active:scale-95 block">
                <div
                    class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-portfolio.icon name="vitals" class="text-2xl" />
                </div>
                <div>
                    <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">Kỹ năng</h3>
                    <p class="text-on-surface-variant font-light mt-1">Sơ đồ {{ $skillCount }} năng lực.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest group-hover:gap-4 transition-all">
                    Ma trận kỹ năng
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </div>
            </a>

            {{-- Contacts Stats --}}
            <a href="{{ route('admin.portfolio.contacts') }}"
                class="group bg-surface-container-low border border-outline-variant/10 rounded-2xl p-8 space-y-6 hover:border-primary/40 transition-all active:scale-95 block">
                <div
                    class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <x-portfolio.icon name="mail" class="text-2xl" />
                </div>
                <div>
                    <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">Tin nhắn mới</h3>
                    <p class="text-on-surface-variant font-light mt-1">{{ $contactCount }} tin nhắn chưa đọc.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-widest group-hover:gap-4 transition-all">
                    Xem tin nhắn
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </div>
            </a>
        </div>

        {{-- Configuration Section --}}
        <div class="bg-surface-container-high/20 rounded-3xl p-10 border border-outline-variant/5">
            <h2 class="text-3xl font-black text-on-surface uppercase tracking-tighter mb-8">Cấu hình nội dung tĩnh</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    class="group relative bg-surface-container-low rounded-2xl p-8 border border-outline-variant/10 hover:border-primary/40 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <x-portfolio.icon name="home" class="text-primary text-3xl" />
                        <a href="{{ route('admin.portfolio.settings.home') }}"
                            class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </a>
                    </div>
                    <h4 class="text-lg font-black text-on-surface uppercase">Trang chủ (Hero)</h4>
                    <p class="text-on-surface-variant font-light text-sm mt-2">Quản lý tiêu đề, mô tả và khẩu hiệu hiển thị
                        ở đầu trang chủ.</p>
                </div>

                <div
                    class="group relative bg-surface-container-low rounded-2xl p-8 border border-outline-variant/10 hover:border-primary/40 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <x-portfolio.icon name="person" class="text-primary text-3xl" />
                        <a href="{{ route('admin.portfolio.settings.about') }}"
                            class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </a>
                    </div>
                    <h4 class="text-lg font-black text-on-surface uppercase">Giới thiệu (About)</h4>
                    <p class="text-on-surface-variant font-light text-sm mt-2">Cập nhật tiểu sử, triết lý làm việc và thông
                        tin giới thiệu bản thân.</p>
                </div>
            </div>
        </div>
    </div>
@endsection