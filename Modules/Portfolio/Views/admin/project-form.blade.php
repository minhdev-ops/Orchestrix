@extends('layouts.admin')

@section('page-title', $project->id ? 'Chỉnh sửa Dự án' : 'Thêm Dự án mới')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6 animate-fade-up">
    {{-- Standard Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">
            {{ $project->id ? 'Chỉnh sửa Dự án' : 'Thêm Dự án mới' }}
        </h2>
        <a href="{{ route('admin.portfolio.projects') }}" class="text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Quay lại
        </a>
    </div>

    <div class="max-w-6xl animate-fade-up">
        <form
            action="{{ $project->id ? route('admin.portfolio.projects.update', $project) : route('admin.portfolio.projects.store') }}"
            method="POST" enctype="multipart/form-data" 
            class="bg-surface-container-low border border-outline-variant/10 rounded-3xl p-10 space-y-12 shadow-2xl shadow-primary/5">
            @csrf
            @if($project->id) @method('PUT') @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                {{-- Left Side: Main Metadata --}}
                <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Tiêu đề dự án</label>
                        <input type="text" name="title" value="{{ old('title', $project->title) }}" required
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Lõi Kỹ thuật (Category)</label>
                        <input type="text" name="category" value="{{ old('category', $project->category) }}"
                            placeholder="e.g. Web Development, Mobile App" required
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                    </div>
                </div>

                {{-- Left Column: Detailed Info --}}
                <div class="lg:col-span-8 space-y-10">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Tóm tắt ngắn (Description)</label>
                        <textarea name="description" rows="3"
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all resize-none leading-relaxed">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Nội dung chi tiết (Markdown)</label>
                        <textarea name="content_md" rows="12"
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all leading-relaxed">{{ old('content_md', $project->content_md) }}</textarea>
                        <p class="text-[10px] text-on-surface-variant/40 mt-1 pl-1">Hỗ trợ định dạng Markdown cho nội dung chi tiết.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Đường dẫn dự án (URL)</label>
                            <input type="url" name="link" value="{{ old('link', $project->link) }}"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Tech Stack (phẩy để ngăn cách)</label>
                            <input type="text" name="tech_stack_input" 
                                value="{{ old('tech_stack_input', is_array($project->tech_stack) ? implode(', ', $project->tech_stack) : $project->tech_stack) }}"
                                placeholder="e.g. Laravel, Vue, Tailwind"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- Right Column: Media & Settings --}}
                <div class="lg:col-span-4 space-y-10">
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] pl-1">Ảnh dự án (Thumbnail)</label>
                        
                        <div class="relative group">
                            <input type="file" name="image" id="imageInput" class="hidden" accept="image/*" onchange="previewImage(this)">
                            <div onclick="document.getElementById('imageInput').click()" 
                                 class="aspect-video w-full rounded-2xl bg-surface border-2 border-dashed border-outline-variant/20 hover:border-primary/50 transition-all cursor-pointer overflow-hidden flex items-center justify-center relative">
                                <div id="imagePreview" class="absolute inset-0 z-0">
                                    @if($project->image)
                                        <img src="{{ str_starts_with($project->image, 'http') ? $project->image : asset('storage/' . $project->image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="relative z-10 flex flex-col items-center gap-2 bg-surface/80 px-4 py-2 rounded-xl backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-primary">cloud_upload</span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Thay đổi ảnh</span>
                                </div>
                                @if(!$project->image)
                                    <div id="noImageMessage" class="relative z-0 flex flex-col items-center gap-3 text-on-surface-variant/30">
                                        <span class="material-symbols-outlined text-5xl">image</span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">Chọn ảnh tải lên</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface/50 p-8 rounded-2xl border border-outline-variant/10 space-y-6">
                        <h3 class="text-[10px] font-black text-on-surface uppercase tracking-[0.2em] mb-4">Cài đặt hiển thị</h3>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-on-surface uppercase tracking-tight">Dự án nổi bật</span>
                                <span class="text-[10px] text-on-surface-variant/60 uppercase tracking-widest">Hiển thị đầu trang Portfolio</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" class="sr-only peer" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-on-surface-variant after:border-surface after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary peer-checked:after:bg-on-primary"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-outline-variant/5">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-on-surface uppercase tracking-tight">Trạng thái</span>
                                <span class="text-[10px] text-on-surface-variant/60 uppercase tracking-widest">Cho phép người dùng xem</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_visible" value="0">
                                <input type="checkbox" name="is_visible" value="1" class="sr-only peer" {{ old('is_visible', $project->is_visible) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-on-surface-variant after:border-surface after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary peer-checked:after:bg-on-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t border-outline-variant/5 flex items-center gap-6">
                <button type="submit"
                    class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                    {{ $project->id ? 'Lưu thay đổi' : 'Tạo dự án mới' }}
                </button>
                <a href="{{ route('admin.portfolio.projects') }}"
                    class="font-bold text-xs uppercase tracking-[0.2em] text-on-surface-variant hover:text-on-surface transition-all">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById('imagePreview');
                const noMsg = document.getElementById('noImageMessage');
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    if(noMsg) noMsg.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
