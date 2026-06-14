@extends('layouts.admin')

@section('page-title', 'AgriVerse — Chi tiết Scan')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.scans.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">AI Scan <span class="text-primary font-mono">#{{ substr($job->uuid, 0, 8) }}</span></h2>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-7 space-y-6">
            <div class="card-premium space-y-5">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin job</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Cửa hàng</p>
                        <p class="text-sm font-black text-on-surface">{{ $job->store?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Tên file</p>
                        <p class="text-sm font-black text-on-surface font-mono">{{ $job->asset?->original_filename ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Trạng thái</p>
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border inline-block mt-1
                            @if($job->status === 'completed') bg-emerald-50 text-emerald-600 border-emerald-200/60
                            @elseif($job->status === 'failed') bg-red-50 text-red-600 border-red-200/60
                            @elseif($job->status === 'processing') bg-blue-50 text-blue-600 border-blue-200/60
                            @else bg-amber-50 text-amber-600 border-amber-200/60
                            @endif
                        ">{{ $job->status ?? 'pending' }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant opacity-60 font-bold uppercase tracking-widest">Ngày tạo</p>
                        <p class="text-sm font-black text-on-surface">{{ $job->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($job->result)
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Kết quả</h3>
                <pre class="text-sm text-on-surface bg-surface-container-low rounded-2xl p-5 overflow-x-auto font-mono">{{ json_encode($job->result, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>

        <div class="col-span-12 md:col-span-5">
            @if($job->asset && $job->asset->product)
            <div class="card-premium space-y-4">
                <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Sản phẩm liên quan</h3>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black text-lg">
                        {{ strtoupper(substr($job->asset->product->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-black text-on-surface">{{ $job->asset->product->name }}</p>
                        <p class="text-xs text-on-surface-variant">{{ number_format($job->asset->product->price, 0) }}đ</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
