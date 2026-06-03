<a href="{{ route('admin.portfolio.projects') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.portfolio.projects*') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">account_tree</span>
    <span class="text-sm">Quản lý Dự án</span>
</a>
<a href="{{ route('admin.portfolio.skills') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.portfolio.skills*') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">psychology</span>
    <span class="text-sm">Quản lý Kỹ năng</span>
</a>
<a href="{{ route('admin.portfolio.contacts') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.portfolio.contacts*') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">mail</span>
    <span class="text-sm">Tin nhắn</span>
</a>
<a href="{{ route('admin.files') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.files') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">folder_managed</span>
    <span class="text-sm">Quản lý Tệp</span>
</a>
<div class="pt-4 pb-2 px-4">
    <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant/40">Cấu hình trang</span>
</div>
<a href="{{ route('admin.portfolio.settings.home') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.portfolio.settings.home') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">home</span>
    <span class="text-sm">Trang chủ</span>
</a>
<a href="{{ route('admin.portfolio.settings.about') }}"
    class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all {{ Request::routeIs('admin.portfolio.settings.about') ? 'bg-primary/10 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
    <span class="material-symbols-outlined text-xl">person</span>
    <span class="text-sm">Về tôi</span>
</a>