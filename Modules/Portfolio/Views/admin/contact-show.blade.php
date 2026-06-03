@extends('layouts.admin')

@section('page-title', 'Chi tiết tin nhắn')

    @section('module-nav')
        @include('portfolio::admin.partials.sidebar')
    @endsection

    @section('content')
        <div class="max-w-4xl space-y-6 animate-fade-up">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.portfolio.contacts') }}"
                    class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
                <h2 class="text-2xl font-black text-on-surface uppercase tracking-tight">Chi tiết tin nhắn</h2>
            </div>

            <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl p-10 space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Người
                            gửi</span>
                        <div
                            class="flex items-center gap-4 p-4 bg-surface border-2 border-outline-variant/5 rounded-2xl transition-all">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <div>
                                <div class="font-bold text-on-surface text-sm uppercase">{{ $contact->name }}</div>
                                <div class="text-[10px] text-on-surface-variant uppercase font-mono">{{ $contact->email }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Thời
                            gian</span>
                        <div
                            class="flex items-center gap-4 p-4 bg-surface border-2 border-outline-variant/5 rounded-2xl transition-all">
                            <div
                                class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface-variant flex items-center justify-center">
                                <span class="material-symbols-outlined">schedule</span>
                            </div>
                            <div>
                                <div class="font-bold text-on-surface text-sm uppercase">
                                    {{ $contact->created_at->format('H:i, d/m/Y') }}</div>
                                <div class="text-[10px] text-on-surface-variant uppercase font-mono">
                                    {{ $contact->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Tiêu đề</span>
                    <div
                        class="p-6 bg-surface border-2 border-outline-variant/10 rounded-2xl text-xl font-black text-on-surface uppercase tracking-tight">
                        {{ $contact->subject }}
                    </div>
                </div>

                <div class="space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant pl-1">Nội dung</span>
                    <div
                        class="p-8 bg-surface border-2 border-outline-variant/10 rounded-2xl text-on-surface text-sm leading-relaxed whitespace-pre-wrap font-medium">
                        {{ $contact->message }}
                    </div>
                </div>

                <div class="pt-6 flex gap-4">
                    <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}"
                        class="bg-primary text-on-primary px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/30 hover:opacity-90 active:scale-95 transition-all">
                        Phản hồi Email
                    </a>
                    <form action="{{ route('admin.portfolio.contacts.destroy', $contact) }}" method="POST"
                        onsubmit="return confirm('Xác nhận xóa vĩnh viễn?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-[0.2em] text-error hover:bg-error hover:text-white transition-all text-center">
                            Xóa tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endsection
@endsection