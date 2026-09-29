<aside class="hidden sm:flex flex-col w-20 lg:w-64 fixed inset-y-0 left-0 border-r border-[var(--color-border)] bg-[var(--color-surface)]/95 backdrop-blur-xl z-50 px-3 py-6 transition-all duration-300 select-none">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 mb-8 group w-fit" aria-label="BlogMNM Trang chủ">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-600/20 group-hover:scale-105 transition-transform shrink-0" data-theme-preserve>
            B
        </div>
        <span class="hidden lg:block text-xl font-black tracking-tight text-[var(--color-text)]">
            Blog<span class="text-indigo-400">MNM</span>
        </span>
    </a>

    <!-- Primary Nav Links -->
    <nav class="flex flex-col gap-1.5 flex-grow overflow-y-auto no-scrollbar">
        <!-- Home -->
        <a href="{{ route('home') }}"
           class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q')) ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
            <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="hidden lg:block text-sm">Trang chủ</span>
        </a>

        <!-- Write / Author Actions -->
        @auth
            @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                <a href="{{ route('posts.create') }}"
                   class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request()->routeIs('posts.create') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                    <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden lg:block text-sm">Viết bài</span>
                </a>
            @elseif(Auth::user()->role === 'viewer')
                <form method="POST" action="{{ route('author.become') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            title="Trở thành tác giả"
                            class="w-full flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/10 cursor-pointer">
                        <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span class="hidden lg:block text-sm font-semibold">Trở thành tác giả</span>
                    </button>
                </form>
            @endif
        @endauth

        <!-- Search -->
        <a href="{{ route('posts.index') }}#search"
           onclick="document.getElementById('feed-search-input')?.focus();"
           class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request('q') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
            <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="hidden lg:block text-sm">Tìm kiếm</span>
        </a>

        @auth
            <!-- Activity -->
            <a href="{{ route('activity.index') }}"
               class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request()->routeIs('activity.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="hidden lg:block text-sm">Hoạt động</span>
            </a>

            <!-- Favorites / Saved -->
            <a href="{{ route('favorites.index') }}"
               class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request()->routeIs('favorites.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span class="hidden lg:block text-sm">Đã lưu</span>
            </a>

            <!-- Author Workspace (if Author) -->
            @if(Auth::user()->isAuthor())
                <div class="my-2 border-t border-[var(--color-border)] pt-2">
                    <div class="hidden lg:block px-3.5 py-1 text-[11px] font-bold text-[var(--color-text-secondary)] uppercase tracking-wider">
                        Tác giả
                    </div>
                    <a href="{{ route('posts.stats') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2.5 rounded-2xl transition group {{ request()->routeIs('posts.stats') || request()->routeIs('author.posts.*') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="hidden lg:block text-sm">Thống kê bài viết</span>
                    </a>
                </div>
            @endif

            <!-- Admin Console (if Admin) -->
            @if(Auth::user()->isAdmin())
                <div class="my-2 border-t border-[var(--color-border)] pt-2">
                    <div class="hidden lg:block px-3.5 py-1 text-[11px] font-bold text-indigo-400 uppercase tracking-wider">
                        Quản trị
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2 rounded-2xl transition group {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="hidden lg:block text-xs">Tổng quan</span>
                    </a>
                    <a href="{{ route('admin.posts.index') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2 rounded-2xl transition group {{ request()->routeIs('admin.posts.*') ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span class="hidden lg:block text-xs">Bài viết</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2 rounded-2xl transition group {{ request()->routeIs('admin.users.*') ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="hidden lg:block text-xs">Người dùng</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2 rounded-2xl transition group {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="hidden lg:block text-xs">Chuyên mục</span>
                    </a>
                    <a href="{{ route('admin.comments.index') }}"
                       class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-2 rounded-2xl transition group {{ request()->routeIs('admin.comments.*') ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="hidden lg:block text-xs">Bình luận</span>
                    </a>
                </div>
            @endif
        @endauth

        <!-- Quick Theme Toggle -->
        <button type="button"
                id="sidebar-theme-toggle"
                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                aria-label="Chuyển đổi giao diện sáng/tối"
                title="Chuyển đổi giao diện sáng/tối"
                class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]">
            <div class="w-6 h-6 flex items-center justify-center shrink-0">
                <!-- Sun icon (shown in dark mode) -->
                <svg class="w-5 h-5 hidden [html[data-theme='dark']_&]:block transition-transform group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <!-- Moon icon (shown in light mode) -->
                <svg class="w-5 h-5 block [html[data-theme='dark']_&]:hidden transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </div>
            <span class="hidden lg:block text-sm font-medium">
                <span class="hidden [html[data-theme='dark']_&]:inline">Giao diện sáng</span>
                <span class="inline [html[data-theme='dark']_&]:hidden">Giao diện tối</span>
            </span>
        </button>

        @auth
            <!-- Settings / Cài đặt & Giao diện -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center justify-center lg:justify-start gap-4 px-3.5 py-3 rounded-2xl transition group {{ request()->routeIs('profile.edit') ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)]' }}">
                <svg class="w-6 h-6 shrink-0 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="hidden lg:block text-sm">Cài đặt</span>
            </a>
        @endauth
    </nav>

    <!-- Bottom User Section / Auth -->
    <div class="mt-auto pt-4 border-t border-[var(--color-border)] flex flex-col gap-2">
        @auth
            <a href="{{ route('profile.show') }}"
               class="flex items-center justify-center lg:justify-start gap-3 p-2 rounded-2xl hover:bg-[var(--color-surface-hover)] transition group">
                <x-avatar :user="Auth::user()" size="sm" />
                <div class="hidden lg:block min-w-0 flex-1">
                    <div class="text-sm font-semibold text-[var(--color-text)] truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-[var(--color-text-secondary)] truncate">{{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}</div>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                        aria-label="Đăng xuất"
                        class="w-full flex items-center justify-center lg:justify-start gap-3 px-3 py-2 rounded-2xl hover:bg-[var(--color-surface-hover)] transition text-[var(--color-text-secondary)] hover:text-rose-500 text-xs font-medium group cursor-pointer">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden lg:block">Đăng xuất</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
               class="flex items-center justify-center lg:justify-start gap-3 px-3.5 py-2.5 rounded-2xl hover:bg-[var(--color-surface-hover)] transition text-[var(--color-text)] font-medium">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span class="hidden lg:block text-sm">Đăng nhập</span>
            </a>
            <a href="{{ route('register') }}"
               class="hidden lg:flex items-center justify-center px-4 py-2 mt-1 text-xs font-semibold rounded-full bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 transition shadow-sm">
                Đăng ký tài khoản
            </a>
        @endauth
    </div>
</aside>
