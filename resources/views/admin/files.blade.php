@extends('layouts.admin')

@section('page-title', 'Quản lý Tệp tin')

@section('content')
    <div x-data="{ 
        showToast: false, 
        toastMsg: '',
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.toastMsg = 'Đã sao chép đường dẫn!';
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            }).catch(err => console.error(err));
        }
    }" class="w-full space-y-10 animate-fade-in pb-20">
        
        {{-- Toast Notification --}}
        <template x-teleport="body">
            <div x-show="showToast" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="fixed bottom-12 right-12 z-[9999]">
                <div class="bg-primary text-on-primary px-8 py-4 rounded-2xl shadow-2xl shadow-primary/40 font-black text-[10px] uppercase tracking-[0.2em] flex items-center gap-4">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <span x-text="toastMsg"></span>
                </div>
            </div>
        </template>

        {{-- Standard Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Thư viện Media</h2>
        </div>

        {{-- Stats & Recent Library Container --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Left: Stats Grid --}}
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em]">Tổng quan lưu trữ</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-6 flex flex-col gap-1">
                        <span class="text-[8px] font-bold text-on-surface-variant/40 uppercase tracking-widest leading-none mb-1">Dung lượng</span>
                        <span class="text-xl font-black text-primary">{{ $stats['total_size_human'] }}</span>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-6 flex flex-col gap-1">
                        <span class="text-[8px] font-bold text-on-surface-variant/40 uppercase tracking-widest leading-none mb-1">Tổng số tệp</span>
                        <span class="text-xl font-black text-on-surface">{{ $stats['total_files'] }}</span>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-6 flex flex-col gap-1">
                        <span class="text-[8px] font-bold text-on-surface-variant/40 uppercase tracking-widest leading-none mb-1">Hình ảnh</span>
                        <span class="text-xl font-black text-on-surface">{{ $stats['types']['images'] }}</span>
                    </div>
                    <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-6 flex flex-col gap-1">
                        <span class="text-[8px] font-bold text-on-surface-variant/40 uppercase tracking-widest leading-none mb-1">Tài liệu</span>
                        <span class="text-xl font-black text-on-surface">{{ $stats['types']['documents'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: Recent Media --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-on-surface-variant uppercase tracking-[0.2em]">Thư viện gần đây</h3>
                    <p class="text-[10px] text-on-surface-variant/40 uppercase tracking-widest">{{ count($recent_images) }} Ảnh mới nhất</p>
                </div>

                @if(count($recent_images) > 0)
                    <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                        @foreach($recent_images as $image)
                            <div class="group relative aspect-square bg-surface-container-high rounded-xl overflow-hidden border border-outline-variant/10 shadow-lg hover:shadow-primary/10 transition-all">
                                <img src="{{ $image['url'] }}" alt="{{ $image['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                
                                {{-- Actions Overlay --}}
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <button @click="copyToClipboard('{{ $image['url'] }}')" 
                                            class="w-8 h-8 bg-white/10 backdrop-blur-md text-white rounded-lg flex items-center justify-center hover:bg-primary transition-colors"
                                            title="Sao chép">
                                        <span class="material-symbols-outlined text-sm">content_copy</span>
                                    </button>
                                    <a href="{{ $image['url'] }}" target="_blank"
                                       class="w-8 h-8 bg-white/10 backdrop-blur-md text-white rounded-lg flex items-center justify-center hover:bg-primary transition-colors"
                                       title="Mở">
                                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-[120px] bg-surface-container-low border border-dashed border-outline-variant/20 rounded-2xl flex flex-col items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-2xl text-on-surface-variant/20">image_not_supported</span>
                        <p class="text-[9px] font-black text-on-surface-variant/30 uppercase tracking-[0.3em]">Trống</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- CKFinder File Manager --}}
        <div class="space-y-6 pt-10 border-t border-outline-variant/10">
            <div class="flex items-center gap-4">
                <h3 class="text-[10px] font-black text-on-surface uppercase tracking-[0.2em]">Trình quản lý tệp tin</h3>
                <div class="h-px flex-1 bg-outline-variant/10"></div>
            </div>
            {{-- Wrapper to isolate CKFinder and prevent layout spill --}}
            <div class="bg-[#f0f0f0] rounded-3xl overflow-hidden border border-outline-variant/20 shadow-2xl relative isolate" style="min-height: 500px;">
                <div id="ckfinder-widget" class="relative z-0" style="height: 600px;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('ckfinder::setup')
    <script>
        CKFinder.start({
            displayHelpButton: false,
            width: '100%',
            height: '100%',
            containerId: 'ckfinder-widget'
        });
    </script>
@endpush