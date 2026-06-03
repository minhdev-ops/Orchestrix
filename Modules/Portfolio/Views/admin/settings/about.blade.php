@extends('layouts.admin')

@section('page-title', 'Manage About Page')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6 animate-fade-up">
        {{-- Standard Header --}}
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Cấu hình Giới thiệu</h2>
        </div>

        <div x-data="{ 
                activeTab: 'general',
                statView: 'list',
                expView: 'list',
                statData: {},
                expData: {},
                isEditStat: false,
                isEditExp: false,
                
                openStatForm(data = null) {
                    this.isEditStat = !!data;
                    this.statData = data ? { ...data } : { label: '', value: '', icon: '', id: null };
                    this.statView = 'form';
                },
                openExpForm(data = null) {
                    this.isEditExp = !!data;
                    this.expData = data ? { ...data } : { type: 'work', title: '', organization: '', start_date: '', end_date: '', description: '', is_current: false, id: null };
                    this.expView = 'form';
                },
                getStatAction() {
                    if (!this.isEditStat) return '{{ route('admin.portfolio.settings.about-stats.store') }}';
                    return '{{ route('admin.portfolio.settings.about-stats.update', ['about_stat' => ':ID']) }}'.replace(':ID', this.statData.id);
                },
                getExpAction() {
                    if (!this.isEditExp) return '{{ route('admin.portfolio.settings.about-experiences.store') }}';
                    return '{{ route('admin.portfolio.settings.about-experiences.update', ['about_experience' => ':ID']) }}'.replace(':ID', this.expData.id);
                }
            }" class="space-y-6">
            {{-- Standard Sub-tabs --}}
            <div class="flex gap-4 border-b border-outline-variant/10">
                <button @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'"
                    class="pb-4 px-2 font-black text-[10px] uppercase tracking-widest transition-all">
                    Tiểu sử
                </button>
                <button @click="activeTab = 'stats'; statView = 'list'"
                    :class="activeTab === 'stats' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'"
                    class="pb-4 px-2 font-black text-[10px] uppercase tracking-widest transition-all">
                    Chỉ số
                </button>
                <button @click="activeTab = 'timeline'; expView = 'list'"
                    :class="activeTab === 'timeline' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-on-surface'"
                    class="pb-4 px-2 font-black text-[10px] uppercase tracking-widest transition-all">
                    Lịch sử
                </button>
            </div>

            @if (session('success'))
                <div
                    class="p-4 bg-primary/10 border border-primary/20 text-primary rounded-xl flex items-center gap-3 animate-fade-in w-full">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <span class="font-medium text-xs">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tab: General --}}
            <div x-show="activeTab === 'general'" x-cloak class="w-full">
                <form action="{{ route('admin.portfolio.settings.about.update') }}" method="POST"
                    class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-10 space-y-10">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Tiêu đề (Main Title)</label>
                            <input type="text" name="title" value="{{ $about->title }}"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Tiêu đề phụ (Subtitle)</label>
                            <input type="text" name="subtitle" value="{{ $about->subtitle }}"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4 md:col-span-2">
                             <div class="space-y-3">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Email</label>
                                <input type="email" name="email" value="{{ $about->email }}"
                                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Điện thoại</label>
                                <input type="text" name="phone" value="{{ $about->phone }}"
                                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Địa chỉ</label>
                            <input type="text" name="location" value="{{ $about->location }}"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Resume Link</label>
                            <input type="url" name="resume_url" value="{{ $about->resume_url }}"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Mô tả chi tiết (Bio)</label>
                        <textarea name="description" rows="10"
                            class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all leading-relaxed resize-none">{{ $about->description }}</textarea>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                            Lưu cấu hình
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab: Stats --}}
            <div x-show="activeTab === 'stats'" x-cloak class="space-y-6">
                {{-- List View --}}
                <div x-show="statView === 'list'" class="space-y-6">
                    <div class="flex justify-between items-center w-full">
                        <h3 class="text-sm font-black text-on-surface-variant uppercase tracking-widest pl-1">Chỉ số năng lực</h3>
                        <button @click="openStatForm()"
                            class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Thêm chỉ số
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
                        @foreach($stats as $stat)
                            <div
                                class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/10 shadow-sm relative group overflow-hidden hover:border-primary/30 transition-all">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="text-3xl font-black text-primary">{{ $stat->value }}</div>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openStatForm({{ json_encode($stat) }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant hover:text-primary transition-all">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <form action="{{ route('admin.portfolio.settings.about-stats.destroy', $stat) }}"
                                            method="POST" onsubmit="return confirm('Xóa chỉ số này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant hover:text-red-500 transition-all">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/70">
                                    {{ $stat->label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Form View --}}
                <div x-show="statView === 'form'" class="max-w-2xl animate-fade-up">
                    <div class="flex items-center gap-4 mb-6">
                         <button @click="statView = 'list'" class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        </button>
                        <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">
                            <span x-show="!isEditStat">Thêm chỉ số mới</span>
                            <span x-show="isEditStat">Chỉnh sửa chỉ số</span>
                        </h3>
                    </div>

                    <form :action="getStatAction()" method="POST" class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-10 space-y-8">
                        @csrf
                        <template x-if="isEditStat">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Giá trị (e.g. 150+, 100%)</label>
                            <input type="text" name="value" x-model="statData.value" required
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-black text-3xl uppercase tracking-tighter focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Nhãn mô tả</label>
                            <input type="text" name="label" x-model="statData.label" required
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                                Xác nhận lưu trữ
                            </button>
                            <button type="button" @click="statView = 'list'" class="px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] text-on-surface-variant hover:bg-surface-container-high transition-all text-center">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tab: Timeline --}}
            <div x-show="activeTab === 'timeline'" x-cloak class="space-y-6">
                {{-- List View --}}
                <div x-show="expView === 'list'" class="space-y-6">
                    <div class="flex justify-between items-center w-full">
                        <h3 class="text-sm font-black text-on-surface-variant uppercase tracking-widest pl-1">Lịch sử sự kiện</h3>
                        <button @click="openExpForm()"
                            class="bg-primary text-on-primary px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Thêm sự kiện
                        </button>
                    </div>

                    <div class="space-y-4 w-full">
                        @foreach($experiences as $exp)
                            <div
                                class="bg-surface-container-low p-6 rounded-2xl border border-outline-variant/10 shadow-sm flex items-center justify-between group hover:border-primary/30 transition-all">
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-14 h-14 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary border border-outline-variant/10 group-hover:scale-110 transition-transform">
                                        <span
                                            class="material-symbols-outlined text-2xl">{{ $exp->type === 'work' ? 'work' : 'school' }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="font-bold text-on-surface uppercase tracking-tight">{{ $exp->title }}</h3>
                                            @if($exp->is_current)
                                                <span
                                                    class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[8px] font-black uppercase tracking-widest">Present</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-on-surface-variant mt-1">
                                            <span class="font-bold text-primary">{{ $exp->organization }}</span>
                                            <span class="mx-2 opacity-30">•</span>
                                            {{ $exp->start_date }} — {{ $exp->is_current ? 'Present' : $exp->end_date }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openExpForm({{ json_encode($exp) }})"
                                        class="w-10 h-10 flex items-center justify-center bg-surface-container-high rounded-xl text-on-surface-variant hover:text-primary transition-all">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <form action="{{ route('admin.portfolio.settings.about-experiences.destroy', $exp) }}"
                                        method="POST" onsubmit="return confirm('Xác nhận xóa?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-10 h-10 flex items-center justify-center bg-surface-container-high rounded-xl text-on-surface-variant hover:text-red-500 transition-all">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Form View --}}
                <div x-show="expView === 'form'" class="w-full animate-fade-up">
                    <div class="flex items-center gap-4 mb-6">
                         <button @click="expView = 'list'" class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        </button>
                        <h3 class="text-xl font-black text-on-surface uppercase tracking-tight">
                            <span x-show="!isEditExp">Thêm sự kiện mới</span>
                            <span x-show="isEditExp">Chỉnh sửa sự kiện</span>
                        </h3>
                    </div>

                    <form :action="getExpAction()" method="POST" class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-10 space-y-10">
                        @csrf
                        <template x-if="isEditExp">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                             <div class="space-y-3">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Phân loại</label>
                                <select name="type" x-model="expData.type" required
                                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-4 py-4 text-on-surface font-bold text-sm focus:border-primary outline-none transition-all">
                                    <option value="work">Kinh nghiệm làm việc</option>
                                    <option value="education">Học vấn & Bằng cấp</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Vị trí / Chuyên ngành</label>
                                <input type="text" name="title" x-model="expData.title" required
                                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary outline-none transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Tổ chức / Trường học</label>
                                <input type="text" name="organization" x-model="expData.organization" required
                                    class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-bold focus:border-primary outline-none transition-all">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Năm bắt đầu</label>
                                    <input type="text" name="start_date" x-model="expData.start_date" placeholder="2020"
                                        class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-4 py-4 text-on-surface font-bold text-sm focus:border-primary outline-none transition-all">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Năm kết thúc</label>
                                    <input type="text" name="end_date" x-model="expData.end_date" :disabled="expData.is_current" placeholder="Present"
                                        class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-4 py-4 text-on-surface font-bold text-sm focus:border-primary outline-none transition-all disabled:opacity-30">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 bg-primary/5 p-6 rounded-2xl border border-primary/10 max-w-md">
                            <input type="hidden" name="is_current" value="0">
                            <input type="checkbox" name="is_current" value="1" x-model="expData.is_current" id="is_current_inline"
                                   class="w-6 h-6 rounded accent-primary cursor-pointer">
                            <label for="is_current_inline" class="text-xs font-bold text-on-surface-variant uppercase tracking-widest cursor-pointer select-none">Đang làm việc / học tập tại đây</label>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest pl-1">Mô tả công việc / Thành tựu</label>
                            <textarea name="description" x-model="expData.description" rows="5"
                                class="w-full bg-surface border-2 border-outline-variant/10 rounded-xl px-6 py-4 text-on-surface font-medium text-sm focus:border-primary outline-none transition-all leading-relaxed resize-none"></textarea>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                                Xác nhận lưu trữ
                            </button>
                            <button type="button" @click="expView = 'list'" class="px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] text-on-surface-variant hover:bg-surface-container-high transition-all text-center">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection