@extends('layouts.admin')

@section('page-title', $post->id ? 'Chỉnh sửa Bài viết' : 'Viết bài mới')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-on-surface tracking-tight font-display">
                {{ $post->id ? 'Chỉnh sửa bài viết' : 'Khởi tạo bài viết' }}
            </h2>
            <p class="text-on-surface-variant text-sm mt-1 font-medium opacity-70">Soạn thảo nội dung chất lượng cao và tối ưu SEO cho độc giả.</p>
        </div>
        <a href="{{ route('admin.blog.index') }}"
            class="bg-surface-container-high text-on-surface px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-all border border-outline-variant/30 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Quay lại
        </a>
    </div>

    <form action="{{ $post->id ? route('admin.blog.update', $post) : route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($post->id) @method('PUT') @endif

        <div class="grid grid-cols-12 gap-8">
            {{-- Main Content Column --}}
            <div class="col-span-12 lg:col-span-8 space-y-8">
                <div class="card-premium !p-10 space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em] pl-1">Tiêu đề bài viết</label>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                            placeholder="Nhập tiêu đề ấn tượng..."
                            class="w-full bg-surface-container-low border border-outline-variant/30 rounded-2xl px-6 py-4 text-on-surface font-black text-xl focus:border-primary/50 outline-none transition-all">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em] pl-1">Nội dung chi tiết</label>
                        <div class="prose-editor">
                            <textarea name="content_html" id="editor">{{ old('content_html', $post->content_html) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SEO Configuration --}}
                <div class="card-premium !p-10 space-y-8 border-t-4 border-t-tertiary/20">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center">
                            <span class="material-symbols-outlined">travel_explore</span>
                        </div>
                        <h3 class="text-xl font-black text-on-surface font-display">Tối ưu hóa Tìm kiếm (SEO)</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.25em] pl-1">Meta Title</label>
                            <input type="text" name="seo_title" value="{{ old('seo_title', $post->seo_title) }}"
                                placeholder="Tiêu đề hiển thị trên Google..."
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-6 py-3 text-sm font-bold focus:border-tertiary/50 outline-none transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.25em] pl-1">Meta Description</label>
                            <textarea name="seo_description" rows="3"
                                placeholder="Mô tả ngắn gọn để tăng tỷ lệ click từ kết quả tìm kiếm..."
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-6 py-4 text-sm font-bold focus:border-tertiary/50 outline-none transition-all resize-none">{{ old('seo_description', $post->seo_description) }}</textarea>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.25em] pl-1">Keywords</label>
                            <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $post->seo_keywords) }}"
                                placeholder="Từ khóa ngăn cách bằng dấu phẩy..."
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-6 py-3 text-sm font-bold focus:border-tertiary/50 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="col-span-12 lg:col-span-4 space-y-8">
                <div class="card-premium !p-8 space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em] pl-1">Chuyên mục</label>
                        <select name="blog_category_id" required
                            class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-6 py-4 text-sm font-black appearance-none focus:border-primary/50 outline-none transition-all">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('blog_category_id', $post->blog_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em] pl-1">Ảnh đại diện (Thumbnail)</label>
                        <div class="relative group aspect-video rounded-2xl bg-surface-container-low border-2 border-dashed border-outline-variant/30 overflow-hidden flex items-center justify-center hover:border-primary/50 transition-all cursor-pointer">
                            @if($post->thumbnail)
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center gap-2 opacity-30 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-4xl">add_photo_alternate</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Click để tải ảnh</span>
                                </div>
                            @endif
                            <input type="file" name="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-primary uppercase tracking-[0.25em] pl-1">Tóm tắt ngắn</label>
                        <textarea name="excerpt" rows="4"
                            placeholder="Mô tả ngắn gọn để hiển thị ở trang danh sách..."
                            class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-6 py-4 text-xs font-bold leading-relaxed focus:border-primary/50 outline-none transition-all resize-none">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/10 space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-on-surface uppercase tracking-tight">Xuất bản ngay</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_published" value="0">
                                <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-outline-variant/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-on-surface uppercase tracking-tight">Bài viết nổi bật</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" class="sr-only peer" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-outline-variant/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" class="btn-premium w-full py-5 text-sm">
                        {{ $post->id ? 'Cập nhật bài viết' : 'Đăng bài viết ngay' }}
                    </button>
                    <a href="{{ route('admin.blog.index') }}" class="text-center text-[10px] font-black uppercase tracking-[0.2em] text-on-surface-variant hover:text-primary transition-all">
                        Hủy bỏ thay đổi
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- CKEditor 5 Integration --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            min-height: 400px;
            background: #ffffff !important;
            border-bottom-left-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
        }
        .ck-toolbar {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
            background: #f8f9fa !important;
            border-color: rgba(0,0,0,0.05) !important;
        }
    </style>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
