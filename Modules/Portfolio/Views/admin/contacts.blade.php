@extends('layouts.admin')

@section('page-title', 'Tin nhắn liên hệ')

@section('module-nav')
    @include('portfolio::admin.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-8 animate-fade-up">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-black text-on-surface uppercase tracking-tighter">Hộp thư đến</h2>
        </div>

        <div class="bg-surface-container-low border border-outline-variant/10 rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high/50 border-b border-outline-variant/10">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Người gửi</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Tiêu đề</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Thời gian</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Trạng thái</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-on-surface-variant text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-surface-container-high/30 transition-all {{ $contact->is_read ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-on-surface">{{ $contact->name }}</div>
                                <div class="text-xs text-on-surface-variant">{{ $contact->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant text-sm">{{ $contact->subject }}</td>
                            <td class="px-6 py-4 text-xs font-mono text-on-surface-variant">{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                @if(!$contact->is_read)
                                    <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-full">Mới</span>
                                @else
                                    <span class="px-2 py-0.5 bg-surface-container-high text-on-surface-variant/40 text-[10px] font-black uppercase rounded-full">Đã đọc</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.portfolio.contacts.show', $contact) }}" class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.portfolio.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Xóa tin nhắn này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center hover:bg-error hover:text-white transition-all text-error">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-5xl text-on-surface-variant/20">mail</span>
                                    <p class="text-on-surface-variant font-light uppercase tracking-widest text-xs">Hộp thư đang trống</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    </div>
@endsection
