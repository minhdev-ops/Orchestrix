@extends('layouts.admin')

@section('page-title', 'Cài đặt Hệ thống')

@section('content')
<div class="max-w-6xl space-y-10 pb-20">
    <div>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Cấu hình Tổng thể</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Thiết lập SMTP Mail, Lưu trữ và các thông tin định danh hệ thống.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-tertiary/10 border border-tertiary/20 text-tertiary rounded-2xl font-bold flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-12">
        @csrf
        {{-- General Info Section --}}
        <div class="card-premium !p-10 space-y-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined">settings_suggest</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface font-display">Thông tin Chung</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Tên Hệ thống / Website</label>
                    <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', 'Orchestrix') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Email Liên hệ chính</label>
                    <input type="email" name="contact_email" value="{{ \App\Models\Setting::get('contact_email', 'admin@orchestrix.test') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-primary/50 outline-none transition-all">
                </div>
            </div>
        </div>

        {{-- SMTP Config Section --}}
        <div class="card-premium !p-10 space-y-10 border-l-4 border-l-secondary/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined">alternate_email</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface font-display">Cấu hình SMTP Mail</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.25em]">Mail Host</label>
                    <input type="text" name="mail_host" value="{{ \App\Models\Setting::get('mail_host', 'smtp.mailtrap.io') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-secondary/50 outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.25em]">Mail Port</label>
                    <input type="text" name="mail_port" value="{{ \App\Models\Setting::get('mail_port', '2525') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-secondary/50 outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.25em]">Encryption</label>
                    <select name="mail_encryption" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold appearance-none focus:border-secondary/50 outline-none transition-all">
                        <option value="tls" {{ \App\Models\Setting::get('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ \App\Models\Setting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.25em]">Username</label>
                    <input type="text" name="mail_username" value="{{ \App\Models\Setting::get('mail_username') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-secondary/50 outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-secondary uppercase tracking-[0.25em]">Password</label>
                    <input type="password" name="mail_password" value="{{ \App\Models\Setting::get('mail_password') }}" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold focus:border-secondary/50 outline-none transition-all">
                </div>
            </div>
        </div>

        {{-- Storage Section --}}
        <div class="card-premium !p-10 space-y-10 border-l-4 border-l-tertiary/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-tertiary/10 text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined">cloud_sync</span>
                </div>
                <h3 class="text-2xl font-black text-on-surface font-display">Lưu trữ (Storage)</h3>
            </div>
            
            <div class="space-y-6">
                <label class="text-[10px] font-black text-tertiary uppercase tracking-[0.25em]">Driver Lưu trữ File</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="group flex items-center justify-between p-6 rounded-2xl bg-surface-container-low/50 border border-transparent hover:border-tertiary/20 hover:bg-white transition-all cursor-pointer">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-tertiary">hard_drive</span>
                            <span class="font-bold text-on-surface">Local Disk (Mặc định)</span>
                        </div>
                        <input type="radio" name="storage_driver" value="local" {{ \App\Models\Setting::get('storage_driver', 'local') == 'local' ? 'checked' : '' }} class="accent-tertiary w-5 h-5">
                    </label>
                    <label class="group flex items-center justify-between p-6 rounded-2xl bg-surface-container-low/50 border border-transparent hover:border-tertiary/20 hover:bg-white transition-all cursor-pointer">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-tertiary">cloud_queue</span>
                            <span class="font-bold text-on-surface">Amazon S3 / DigitalOcean</span>
                        </div>
                        <input type="radio" name="storage_driver" value="s3" {{ \App\Models\Setting::get('storage_driver') == 's3' ? 'checked' : '' }} class="accent-tertiary w-5 h-5">
                    </label>
                </div>
            </div>
        </div>


        <div class="flex justify-end gap-4">
            <button type="submit" class="btn-premium px-16 py-5 text-base">
                Lưu toàn bộ Cài đặt
            </button>
        </div>
    </form>
</div>
@endsection
