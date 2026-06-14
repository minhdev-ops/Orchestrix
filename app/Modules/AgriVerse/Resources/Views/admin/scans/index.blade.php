@extends('layouts.admin')

@section('page-title', 'AgriVerse — AI Scanning')

@section('content')
<div class="space-y-10 pb-20">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">AI Scanning</h2>
            <p class="text-on-surface-variant text-base mt-2 font-medium opacity-70">Theo dõi các yêu cầu quét 3D bằng AI.</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </form>
    </div>

    <div class="card-premium !p-0 overflow-hidden bg-white shadow-xl shadow-black/[0.02] rounded-3xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Job ID</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Cửa hàng</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Asset</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Trạng thái</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Ngày tạo</th>
                    <th class="px-8 py-5 text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em] text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-8 py-6">
                        <span class="font-mono text-sm font-bold text-on-surface">#{{ substr($job->uuid, 0, 8) }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-on-surface">{{ $job->store?->name ?? 'N/A' }}</td>
                    <td class="px-8 py-6 text-sm text-on-surface-variant">{{ $job->asset?->original_filename ?? 'N/A' }}</td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full border
                            @if($job->status === 'completed') bg-emerald-50 text-emerald-600 border-emerald-200/60
                            @elseif($job->status === 'failed') bg-red-50 text-red-600 border-red-200/60
                            @elseif($job->status === 'processing') bg-blue-50 text-blue-600 border-blue-200/60
                            @else bg-amber-50 text-amber-600 border-amber-200/60
                            @endif
                        ">{{ $job->status ?? 'pending' }}</span>
                    </td>
                    <td class="px-8 py-6 text-sm text-on-surface-variant">{{ $job->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.agriverse.scans.show', $job->id) }}" class="p-2.5 rounded-xl text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-all">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                            <form action="{{ route('admin.agriverse.scans.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Xóa job scan này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 rounded-xl text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-all">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $jobs->links() }}</div>
</div>
@endsection
