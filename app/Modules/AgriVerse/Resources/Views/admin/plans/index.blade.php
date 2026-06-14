@extends('layouts.admin')

@section('page-title', 'AgriVerse — Gói Đăng ký')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">Gói đăng ký</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Quản lý các gói đăng ký cho cửa hàng.</p>
        </div>
        <a href="{{ route('admin.agriverse.plans.create') }}" class="px-6 py-3 rounded-2xl bg-primary text-on-primary text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-all">
            <span class="material-symbols-outlined text-base align-text-bottom mr-1">add</span>
            Thêm gói
        </a>
    </div>

    @if(session('success'))
        <div class="p-5 bg-emerald-50 border border-emerald-200/60 text-emerald-700 rounded-2xl font-bold flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-6">
        @foreach($plans as $plan)
        <div class="col-span-12 md:col-span-4">
            <div class="card-premium relative overflow-hidden group">
                @if($plan->status === 'inactive')
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full bg-amber-50 text-amber-600 border border-amber-200/60">Inactive</span>
                    </div>
                @endif
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface font-display">{{ $plan->name }}</h3>
                        <div class="mt-3 flex items-baseline gap-1">
                            <span class="text-4xl font-black text-on-surface font-display">{{ number_format($plan->price_per_month, 0) }}</span>
                            <span class="text-sm font-bold text-on-surface-variant">đ / tháng</span>
                        </div>
                    </div>
                    <div class="h-px bg-outline-variant/30"></div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-on-surface-variant">Giới hạn 3D models</span>
                            <span class="text-sm font-black font-mono text-on-surface">{{ $plan->limit_3d_models }}</span>
                        </div>
                        @if($plan->features)
                            @foreach($plan->features as $feature)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-emerald-500 text-base">check</span>
                                <span class="text-sm text-on-surface">{{ $feature }}</span>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <a href="{{ route('admin.agriverse.plans.edit', $plan->id) }}" class="flex-1 px-4 py-3 rounded-2xl border border-outline-variant text-on-surface text-xs font-black uppercase tracking-widest hover:bg-surface-container-low transition-all text-center">
                            Sửa
                        </a>
                        <form action="{{ route('admin.agriverse.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Xóa gói đăng ký này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-3 rounded-2xl border border-outline-variant text-on-surface-variant hover:text-red-500 hover:border-red-200 transition-all">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $plans->links() }}</div>
</div>
@endsection
