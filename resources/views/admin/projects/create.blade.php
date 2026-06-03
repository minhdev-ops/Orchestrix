@extends('layouts.admin')

@section('page-title', 'Thêm Dự án mới')

@section('content')
<div class="max-w-4xl space-y-10 pb-20">
    <div>
        <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-2 text-primary font-black uppercase tracking-widest text-[10px] mb-6 hover:translate-x-[-4px] transition-transform">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Thêm Dự án mới</h2>
        <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Định nghĩa domain mới và kích hoạt các module tương ứng.</p>
    </div>

    <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-8">
        @csrf
        <div class="card-premium !p-10 space-y-10">
            {{-- Domain Input --}}
            <div class="space-y-4">
                <label for="domain" class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Domain / Host Name</label>
                <div class="relative">
                    <input type="text" name="domain" id="domain" value="{{ old('domain') }}" placeholder="ví dụ: project-a.test" 
                           class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-bold placeholder:opacity-30 focus:outline-none focus:border-primary/50 transition-all">
                </div>
                @error('domain')
                    <p class="text-xs text-error font-bold italic">{{ $message }}</p>
                @enderror
            </div>

            {{-- Module Selection --}}
            <div class="space-y-6">
                <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Chọn Module Kích hoạt</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($allModules as $module)
                    <label class="group flex items-center justify-between p-5 rounded-2xl bg-surface-container-low/50 border border-transparent hover:border-primary/20 hover:bg-white hover:shadow-lg transition-all cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-xl text-primary">{{ $module['icon'] }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">{{ $module['name'] }}</span>
                                <span class="text-[10px] font-medium text-on-surface-variant opacity-50">{{ $module['description'] }}</span>
                            </div>
                        </div>
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="modules[]" value="{{ $module['id'] }}" class="peer hidden">
                            <div class="w-6 h-6 rounded-lg border-2 border-outline-variant/30 peer-checked:bg-primary peer-checked:border-primary transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-[16px] scale-0 peer-checked:scale-100 transition-transform">check</span>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button type="submit" class="btn-premium px-12">
                Lưu cấu hình Dự án
            </button>
        </div>
    </form>
</div>
@endsection
