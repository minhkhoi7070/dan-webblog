<<<<<<< HEAD
<nav class="d-sm-none position-fixed bottom-0 start-0 end-0 bg-theme-surface border-theme-top d-flex align-items-center justify-content-around px-2 user-select-none" style="height: 60px; z-index: 1040;">
    <!-- Home -->
    <a href="{{ route('home') }}"
       aria-label="Trang chủ"
       class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q')) ? 'text-theme' : 'text-theme-secondary' }}">
=======
<!-- Mobile Bottom Navigation Bar (5 Primary Actions: Home, Search, Create, Favorites, Profile) -->
<nav class="d-sm-none position-fixed bottom-0 start-0 end-0 bg-theme-surface border-theme-top d-flex align-items-center justify-content-around px-2 user-select-none mobile-bottom-nav" role="navigation" aria-label="Điều hướng di động">
    <!-- 1. Home -->
    <a href="{{ route('home') }}"
       aria-label="Trang chủ"
       title="Trang chủ"
       class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}">
>>>>>>> origin/main
        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
    </a>

    <!-- 2. Search -->
    <a href="{{ route('posts.index') }}#feed-search-input"
       onclick="const el = document.getElementById('feed-search-input'); if(el) { el.focus(); el.scrollIntoView({behavior: 'smooth', block: 'center'}); }"
<<<<<<< HEAD
       aria-label="Tìm kiếm"
       class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none {{ request('q') ? 'text-theme' : 'text-theme-secondary' }}">
=======
       aria-label="Tìm kiếm bài viết"
       title="Tìm kiếm"
       class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request('q') ? 'active' : '' }}">
>>>>>>> origin/main
        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </a>

<<<<<<< HEAD
    <!-- Mobile Quick Theme Toggle -->
    <button type="button"
            onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
            aria-label="Chuyển đổi giao diện sáng/tối"
            title="Chuyển đổi giao diện sáng/tối"
            class="btn p-2 border-0 d-flex align-items-center justify-content-center text-theme-secondary">
        <svg id="mobile-theme-icon-light" style="width: 22px; height: 22px; display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="mobile-theme-icon-dark" style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
    <script>
        function updateMobileThemeUI() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const light = document.getElementById('mobile-theme-icon-light');
            const dark = document.getElementById('mobile-theme-icon-dark');
            if (light && dark) {
                light.style.display = isDark ? 'block' : 'none';
                dark.style.display = isDark ? 'none' : 'block';
            }
        }
        window.addEventListener('DOMContentLoaded', updateMobileThemeUI);
        new MutationObserver(updateMobileThemeUI).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    </script>

=======
    <!-- 3. Create / Write / Become Author Action -->
>>>>>>> origin/main
    @auth
        @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
            <a href="{{ route('posts.create') }}"
<<<<<<< HEAD
               aria-label="Viết bài"
               title="Viết bài"
               class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none"
               style="background-color: var(--color-surface-hover); color: #818cf8; border: 1px solid var(--color-border);">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
               aria-label="Viết bài mới"
               title="Viết bài mới"
               class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}"
               style="background-color: var(--color-bg-subtle);">
                <svg style="width: 20px; height: 20px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        @elseif(Auth::user()->role === 'viewer')
            <form method="POST" action="{{ route('author.become') }}" class="m-0 p-0">
                @csrf
                <button type="submit"
                        aria-label="Trở thành tác giả"
                        title="Trở thành tác giả"
                        class="btn p-2 rounded-3 d-flex align-items-center justify-content-center border-0"
<<<<<<< HEAD
                        style="background-color: rgba(99, 102, 241, 0.15); color: #818cf8;">
=======
                        style="background-color: var(--color-bg-subtle); color: var(--color-brand-warm); width: 44px; height: 44px;">
>>>>>>> origin/main
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>
            </form>
        @endif
<<<<<<< HEAD

        <!-- Activity -->
        <a href="{{ route('activity.index') }}"
           aria-label="Hoạt động"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none {{ request()->routeIs('activity.*') ? 'text-theme' : 'text-theme-secondary' }}">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
=======
    @else
        <a href="{{ route('register') }}"
           aria-label="Đăng ký tài khoản"
           title="Đăng ký"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('register') ? 'active' : '' }}"
           style="background-color: var(--color-bg-subtle);">
            <svg style="width: 20px; height: 20px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
>>>>>>> origin/main
            </svg>
        </a>
    @endauth

    <!-- 4. Favorites / Saved -->
    @auth
        <a href="{{ route('favorites.index') }}"
           aria-label="Bài viết đã lưu"
           title="Bài viết đã lưu"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
        </a>
    @else
        <a href="{{ route('login') }}"
           aria-label="Đăng nhập để xem bài viết đã lưu"
           title="Đã lưu"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
        </a>
    @endauth

    <!-- 5. Profile / Account -->
    @auth
        <a href="{{ route('profile.show') }}"
<<<<<<< HEAD
           aria-label="Trang cá nhân"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none {{ request()->routeIs('profile.*') ? 'text-theme' : 'text-theme-secondary' }}">
=======
           aria-label="Trang cá nhân: {{ Auth::user()->name }}"
           title="Trang cá nhân"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
>>>>>>> origin/main
            <x-avatar :user="Auth::user()" size="xs" />
        </a>
    @else
        <a href="{{ route('login') }}"
           aria-label="Đăng nhập"
<<<<<<< HEAD
           class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1">
            Đăng nhập
=======
           title="Đăng nhập"
           class="d-flex align-items-center justify-content-center p-2 rounded-3 text-decoration-none mobile-bottom-nav-link {{ request()->routeIs('login') ? 'active' : '' }}">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
>>>>>>> origin/main
        </a>
    @endauth
</nav>
