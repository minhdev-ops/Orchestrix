@extends('layouts.blog')

@section('title', $post->title . ' | Orchestrix Intelligence')

@section('content')
    <div class="bg-background min-h-screen relative pt-48 pb-32">
        {{-- Progress Bar --}}
        <div id="readProgress"
            class="fixed top-0 left-0 w-0 h-[2px] bg-brand z-[60] transition-all duration-300 shadow-[0_0_10px_rgba(88,166,255,0.5)]">
        </div>

        <article class="max-w-4xl mx-auto px-6">
            {{-- Category & Date --}}
            <div class="flex items-center gap-6 mb-12">
                <div class="flex items-center gap-3">
                    <div class="h-[1px] w-8 bg-brand"></div>
                    <span class="text-[10px] font-mono font-bold text-brand uppercase tracking-[0.5em]">
                        {{ $post->category?->name ?? 'Kỹ thuật' }}
                    </span>
                </div>
                <div class="h-4 w-[1px] bg-border"></div>
                <span class="text-[10px] font-mono text-text-muted uppercase tracking-[0.4em]">
                    Thời gian đọc: {{ $post->reading_time }} phút
                </span>
            </div>

            {{-- Header --}}
            <header class="space-y-10 mb-20">
                <h1 class="text-[40px] md:text-[64px] font-display font-medium text-text tracking-tight leading-[1.1]">
                    {{ $post->title }}
                </h1>
                <div class="relative pl-8 border-l-2 border-brand/20">
                    <p class="text-[20px] md:text-[24px] text-text-muted font-light leading-relaxed italic opacity-80">
                        {{ $post->excerpt }}
                    </p>
                </div>
            </header>

            {{-- Article Metadata Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 py-10 mb-20 border-y border-border">
                <div class="space-y-2">
                    <div class="text-[10px] font-mono font-bold text-text-muted/40 uppercase tracking-widest">Tác giả</div>
                    <div class="text-[14px] font-bold text-text">{{ $post->author ?? 'Quản trị viên' }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-[10px] font-mono font-bold text-text-muted/40 uppercase tracking-widest">Ngày đăng
                    </div>
                    <div class="text-[14px] font-bold text-text">{{ $post->published_at_formatted }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-[10px] font-mono font-bold text-text-muted/40 uppercase tracking-widest">Lượt xem</div>
                    <div class="text-[14px] font-bold text-brand">{{ $post->views_count }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-[10px] font-mono font-bold text-text-muted/40 uppercase tracking-widest">Trạng thái
                    </div>
                    <div class="text-[14px] font-bold text-green-500 uppercase">Đã xác minh</div>
                </div>
            </div>

            {{-- Content --}}
            <div class="prose prose-invert max-w-none 
                            prose-headings:font-display prose-headings:font-medium prose-headings:tracking-tight prose-headings:text-text
                            prose-p:text-text-muted prose-p:text-[18px] prose-p:leading-relaxed
                            prose-strong:text-brand prose-strong:font-bold
                            prose-code:text-brand prose-code:bg-surface-muted prose-code:px-2 prose-code:py-0.5 prose-code:rounded
                            prose-pre:bg-surface-secondary prose-pre:border prose-pre:border-border prose-pre:rounded-[12px]
                            prose-blockquote:border-l-brand prose-blockquote:bg-brand/5 prose-blockquote:py-4 prose-blockquote:px-8
                            prose-img:rounded-[16px] prose-img:border prose-img:border-border">
                {!! Str::markdown($post->content_md ?? '') !!}
            </div>

            {{-- Footer / Navigation --}}
            <div class="mt-32 pt-16 border-t border-border flex flex-wrap justify-between items-center gap-10">
                <div class="flex items-center gap-4">
                    <button onclick="likePost()" id="likeBtn"
                        class="flex items-center gap-3 px-8 py-3 rounded-full bg-surface-secondary border border-border hover:border-brand/40 transition-all active:scale-95 group">
                        <span
                            class="material-symbols-outlined text-text-muted group-hover:text-brand transition-all">favorite</span>
                        <span class="text-[14px] font-bold text-text" id="likesCount">{{ $post->likes_count }}</span>
                    </button>
                    <div
                        class="flex items-center gap-3 px-8 py-3 rounded-full bg-surface-muted text-text-muted border border-border opacity-60">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                        <span class="text-[14px] font-bold">{{ $post->views_count }}</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button
                        class="w-12 h-12 rounded-full border border-border flex items-center justify-center text-text-muted hover:text-brand hover:border-brand/40 transition-all">
                        <span class="material-symbols-outlined text-[20px]">share</span>
                    </button>
                    <a href="{{ route('blog.index') }}"
                        class="px-8 py-3 rounded-full bg-surface-secondary border border-border text-[14px] font-bold text-text hover:border-brand/40 transition-all">
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </article>
    </div>

    <script>
        // Reading Progress
        window.onscroll = function () {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            document.getElementById("readProgress").style.width = scrolled + "%";
        };

        // Like Functionality
        function likePost() {
            const btn = document.getElementById('likeBtn');
            fetch('{{ route('blog.like', ['blog_post' => $post->slug]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('likesCount').textContent = data.likes;
                });
        }
    </script>
@endsection