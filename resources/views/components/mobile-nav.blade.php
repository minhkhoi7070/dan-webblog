<nav class="sm:hidden fixed bottom-0 left-0 right-0 h-16 bg-[var(--color-surface)]/95 backdrop-blur-xl border-t border-[var(--color-border)] z-50 flex items-center justify-around px-2 select-none safe-area-pb">
    <!-- Home -->
    <a href="{{ route('home') }}"
       aria-label="Trang chủ"
       class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl transition {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q')) ? 'text-[var(--color-text)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
    </a>

    <!-- Search -->
    <a href="{{ route('posts.index') }}#feed-search-input"
       onclick="const el = document.getElementById('feed-search-input'); if(el) { el.focus(); el.scrollIntoView({behavior: 'smooth', block: 'center'}); }"
       aria-label="Tìm kiếm"
       class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl transition {{ request('q') ? 'text-[var(--color-text)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </a>

    <!-- Mobile Quick Theme Toggle -->
    <button type="button"
            onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
            aria-label="Chuyển đổi giao diện sáng/tối"
            title="Chuyển đổi giao diện sáng/tối"
            class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition">
        <svg class="w-6 h-6 hidden [html[data-theme='dark']_&]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg class="w-6 h-6 block [html[data-theme='dark']_&]:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    @auth
        <!-- Write Button (if Author/Admin) or Become Author (if Viewer) -->
        @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
            <a href="{{ route('posts.create') }}"
               aria-label="Viết bài"
               title="Viết bài"
               class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-indigo-400 hover:text-indigo-300 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        @elseif(Auth::user()->role === 'viewer')
            <form method="POST" action="{{ route('author.become') }}">
                @csrf
                <button type="submit"
                        aria-label="Trở thành tác giả"
                        title="Trở thành tác giả"
                        class="p-2 min-w-[40px] min-h-[40px] flex items-center justify-center rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:text-indigo-300 transition shadow-sm cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </form>
        @endif

        <!-- Activity -->
        <a href="{{ route('activity.index') }}"
           aria-label="Hoạt động"
           class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl transition {{ request()->routeIs('activity.*') ? 'text-[var(--color-text)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.show') }}"
           aria-label="Trang cá nhân"
           class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-xl transition {{ request()->routeIs('profile.*') ? 'text-[var(--color-text)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
            <x-avatar :user="Auth::user()" size="xs" />
        </a>
    @else
        <!-- Guest Login Link -->
        <a href="{{ route('login') }}"
           aria-label="Đăng nhập"
           class="p-3 min-w-[44px] min-h-[44px] flex items-center justify-center text-xs font-semibold text-[var(--color-text)] hover:opacity-80 transition">
            Đăng nhập
        </a>
    @endauth
</nav>
