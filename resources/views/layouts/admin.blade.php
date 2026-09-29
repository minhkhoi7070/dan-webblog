@extends('layouts.public')

@section('content')
<div class="bg-[var(--color-surface)]/90 border-b border-[var(--color-border)] sticky top-0 sm:top-auto z-20 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-md bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                        Admin Control Center
                    </span>
                    <span class="text-xs text-[var(--color-text-secondary)]">Hệ thống quản trị & kiểm duyệt nội dung BlogMNM</span>
                </div>
                <h1 class="text-lg sm:text-xl font-black tracking-tight text-[var(--color-text)] mt-1">
                    @yield('admin_title', 'Bảng điều khiển Quản trị')
                </h1>
            </div>

            <!-- Admin Sub-navigation Links -->
            <nav class="flex flex-wrap items-center gap-1.5 overflow-x-auto no-scrollbar">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold shadow-sm' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}"
                >
                    Tổng quan
                </a>
                <a
                    href="{{ route('admin.posts.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('admin.posts.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold shadow-sm' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}"
                >
                    Bài viết
                </a>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold shadow-sm' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}"
                >
                    Người dùng
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold shadow-sm' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}"
                >
                    Chuyên mục
                </a>
                <a
                    href="{{ route('admin.comments.index') }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ request()->routeIs('admin.comments.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold shadow-sm' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}"
                >
                    Bình luận
                </a>
            </nav>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 w-full flex-1">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/60 text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-800/60 text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('admin_content')
</div>
@endsection
