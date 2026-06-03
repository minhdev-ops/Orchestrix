@extends('layouts.admin')

@section('page-title', $skill->id ? 'Chỉnh sửa Kỹ năng' : 'Thêm Kỹ năng mới')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="max-w-4xl animate-fade-up">
        <form
            action="{{ $skill->id ? route('admin.portfolio.skills.update', $skill) : route('admin.portfolio.skills.store') }}"
            method="POST"
            class="space-y-10 bg-surface-container-low border border-outline-variant/10 rounded-3xl p-10 shadow-2xl shadow-primary/5">
            @csrf
            @if($skill->id) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Tên Kỹ
                        năng</label>
                    <input type="text" name="name" value="{{ old('name', $skill->name) }}" required
                        class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Lõi Kỹ
                        thuật (Category)</label>
                    <input type="text" name="category" value="{{ old('category', $skill->category) }}"
                        placeholder="e.g. Infrastructure, Database" required
                        class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Độ thành
                        thạo (%)</label>
                    <div class="relative group">
                        <input type="number" name="level" value="{{ old('level', $skill->level ?? 80) }}" min="0" max="100"
                            required
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span
                            class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-primary/40 group-focus-within:text-primary transition-colors">%</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Icon
                        Identifier</label>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="icon" id="icon-input" value="{{ old('icon', $skill->icon ?? 'code') }}"
                                required placeholder="e.g. psychology or logos:redis"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                        <div id="icon-preview-container"
                            class="w-[62px] h-[62px] bg-primary/5 rounded-2xl flex items-center justify-center text-primary border border-primary/20 shadow-inner shrink-0 group hover:scale-110 transition-transform duration-300">
                            <span id="material-preview" class="material-symbols-outlined text-3xl"></span>
                            <iconify-icon id="iconify-preview" width="32"></iconify-icon>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-3">
                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Tóm tắt
                        chuyên môn</label>
                    <textarea name="description" rows="4"
                        class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all resize-none leading-relaxed">{{ old('description', $skill->description) }}</textarea>
                </div>
            </div>

            <div class="pt-10 border-t border-outline-variant/5 flex items-center gap-6">
                <button type="submit"
                    class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                    {{ $skill->id ? 'Lưu thay đổi' : 'Tạo kỹ năng' }}
                </button>
                <a href="{{ route('admin.portfolio.skills') }}"
                    class="font-bold text-xs uppercase tracking-[0.2em] text-on-surface-variant hover:text-on-surface transition-all">
                    Hủy bỏ
                </a>
            </div>
                <script>
                    const iconInput = document.getElementById('icon-input');
                    const materialPreview = document.getElementById('material-preview');
                    const iconifyPreview = document.getElementById('iconify-preview');

                    function updatePreview() {
                        const val = iconInput.value.trim();
                        if (val.startsWith('http')) {
                            materialPreview.style.display = 'none';
                            iconifyPreview.style.display = 'none';
                            let img = document.getElementById('icon-img-preview');
                            if (!img) {
                                img = document.createElement('img');
                                img.id = 'icon-img-preview';
                                img.className = 'w-8 h-8 object-contain';
                                document.getElementById('icon-preview-container').appendChild(img);
                            }
                            img.style.display = 'block';
                            img.src = val;
                        } else {
                            const img = document.getElementById('icon-img-preview');
                            if (img) img.style.display = 'none';

                            if (val.includes(':')) {
                                materialPreview.style.display = 'none';
                                iconifyPreview.style.display = 'block';
                                iconifyPreview.setAttribute('icon', val);
                            } else {
                                materialPreview.style.display = 'block';
                                iconifyPreview.style.display = 'none';
                                materialPreview.textContent = val || 'question_mark';
                            }
                        }
                    }

                    iconInput.addEventListener('input', updatePreview);
                    document.addEventListener('DOMContentLoaded', updatePreview);
                </script>
        </form>
    </div>
@endsection