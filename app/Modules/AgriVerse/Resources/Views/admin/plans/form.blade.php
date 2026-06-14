@extends('layouts.admin')

@section('page-title', 'AgriVerse — ' . (isset($plan) ? 'Sửa Gói Đăng ký' : 'Thêm Gói Đăng ký'))

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-20">
    <div>
        <a href="{{ route('admin.agriverse.plans.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-4">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Quay lại danh sách
        </a>
        <h2 class="text-4xl font-black text-on-surface tracking-tight font-display">{{ isset($plan) ? 'Sửa gói đăng ký' : 'Thêm gói đăng ký mới' }}</h2>
    </div>

    <form action="{{ isset($plan) ? route('admin.agriverse.plans.update', $plan->id) : route('admin.agriverse.plans.store') }}" method="POST" class="space-y-8">
        @csrf
        @if(isset($plan)) @method('PUT') @endif

        <div class="card-premium space-y-6">
            <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.25em]">Thông tin gói</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Tên gói</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" required
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Trạng thái</label>
                    <select name="status" class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="active" {{ old('status', $plan->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $plan->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giá mỗi tháng</label>
                    <div class="relative">
                        <input type="number" step="0.01" name="price_per_month" value="{{ old('price_per_month', $plan->price_per_month ?? '') }}" required
                            class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-sm font-bold text-on-surface-variant">đ</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-on-surface mb-2">Giới hạn 3D models</label>
                    <input type="number" name="limit_3d_models" value="{{ old('limit_3d_models', $plan->limit_3d_models ?? 0) }}" required
                        class="w-full px-5 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-on-surface mb-2">Tính năng</label>
                <div id="features-container" class="space-y-3">
                    @php
                        $features = old('features', $plan->features ?? []);
                    @endphp
                    @if(count($features) > 0)
                        @foreach($features as $i => $feature)
                        <div class="flex items-center gap-3 feature-row">
                            <input type="text" name="features[]" value="{{ $feature }}"
                                class="flex-1 px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <button type="button" class="remove-feature p-2 rounded-xl text-red-400 hover:text-red-500 hover:bg-red-50 transition-all" onclick="this.parentElement.remove()">
                                <span class="material-symbols-outlined text-lg">remove_circle</span>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-3 feature-row">
                            <input type="text" name="features[]" placeholder="Ví dụ: Hỗ trợ 3D model"
                                class="flex-1 px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        </div>
                    @endif
                </div>
                <button type="button" id="add-feature" class="mt-3 text-sm font-bold text-primary hover:text-primary/80 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">add</span>
                    Thêm tính năng
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.agriverse.plans.index') }}" class="px-8 py-3.5 rounded-2xl border border-outline-variant text-on-surface text-sm font-black hover:bg-surface-container-low transition-all">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-primary text-on-primary text-sm font-black shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                {{ isset($plan) ? 'Cập nhật' : 'Tạo gói' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('add-feature')?.addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 feature-row';
    row.innerHTML = `
        <input type="text" name="features[]" placeholder="Thêm tính năng..."
            class="flex-1 px-4 py-3 rounded-2xl border border-outline-variant bg-surface text-on-surface text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
        <button type="button" class="remove-feature p-2 rounded-xl text-red-400 hover:text-red-500 hover:bg-red-50 transition-all" onclick="this.parentElement.remove()">
            <span class="material-symbols-outlined text-lg">remove_circle</span>
        </button>
    `;
    container.appendChild(row);
});
</script>
@endpush
@endsection
