<aside class="d-none d-sm-flex flex-column position-fixed top-0 bottom-0 start-0 border-theme-right bg-theme-surface px-2 px-lg-3 py-4 user-select-none sidebar-container" style="z-index: 1040;">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="d-flex align-items-center gap-3 px-2 mb-4 text-decoration-none" aria-label="BlogMNM Trang chủ">
        <div class="brand-gradient rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" style="width: 38px; height: 38px; font-size: 16px;" data-theme-preserve>
            B
        </div>
        <span class="sidebar-label fs-5 fw-bold text-theme mb-0 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
            Blog<span class="text-accent">MNM</span>
        </span>
    </a>

    <!-- Primary Nav Links -->
    <nav class="d-flex flex-column gap-1 flex-grow-1 overflow-y-auto no-scrollbar" role="navigation" aria-label="Menu điều hướng chính">
        <!-- Home -->
        <a href="{{ route('home') }}"
           class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}"
           title="Trang chủ"
           aria-label="Trang chủ">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-label small">Trang chủ</span>
        </a>

        <!-- Search -->
        <a href="{{ route('posts.index') }}#search"
           onclick="document.getElementById('feed-search-input')?.focus();"
           class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request('q') ? 'active' : '' }}"
           title="Tìm kiếm"
           aria-label="Tìm kiếm">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="sidebar-label small">Tìm kiếm</span>
        </a>

        <!-- Write / Author Actions -->
        @auth
            @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                <a href="{{ route('posts.create') }}"
                   class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('posts.create') ? 'active' : '' }}"
                   title="Viết bài mới"
                   aria-label="Viết bài mới">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="sidebar-label small fw-semibold text-accent">Viết bài</span>
                </a>
            @elseif(Auth::user()->role === 'viewer')
                <form method="POST" action="{{ route('author.become') }}" class="w-100 m-0 p-0">
                    @csrf
                    <button type="submit"
                            title="Đăng ký trở thành tác giả"
                            aria-label="Trở thành tác giả"
                            class="btn p-0 w-100 d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none border-0 text-start">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0; color: var(--color-brand-warm);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span class="sidebar-label small fw-semibold" style="color: var(--color-brand-warm);">Trở thành tác giả</span>
                    </button>
                </form>
            @endif

            <!-- Favorites / Saved -->
            <a href="{{ route('favorites.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('favorites.*') ? 'active' : '' }}"
               title="Bài viết đã lưu"
               aria-label="Bài viết đã lưu">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span class="sidebar-label small">Đã lưu</span>
            </a>

            <!-- Activity -->
            <a href="{{ route('activity.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('activity.*') ? 'active' : '' }}"
               title="Nhật ký hoạt động"
               aria-label="Nhật ký hoạt động">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="sidebar-label small">Hoạt động</span>
            </a>

            <!-- Author Workspace (if Author) -->
            @if(Auth::user()->isAuthor())
                <div class="my-2 border-theme-top pt-2">
                    <div class="sidebar-label px-3 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">
                        Tác giả
                    </div>
                    <a href="{{ route('posts.stats') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('posts.stats') || request()->routeIs('author.posts.*') ? 'active' : '' }}"
                       title="Thống kê bài viết"
                       aria-label="Thống kê bài viết">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="sidebar-label small">Thống kê bài viết</span>
                    </a>
                </div>
            @endif

            <!-- Admin Console (if Admin) -->
            @if(Auth::user()->isAdmin())
                <div class="mt-2 mb-0 border-theme-top pt-2">
                    <div class="sidebar-labenpm.cmd run devl px-3 py-1 label-uppercase text-accent" style="font-size: 10px; letter-spacing: 0.08em;">
                        Quản trị
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       title="Bảng điều khiển Quản trị"
                       aria-label="Bảng điều khiển Quản trị">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="sidebar-label small">Tổng quan</span>
                    </a>
                    <a href="{{ route('admin.posts.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"
                       title="Quản lý bài viết"
                       aria-label="Quản lý bài viết">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span class="sidebar-label small">Bài viết</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       title="Quản lý người dùng"
                       aria-label="Quản lý người dùng">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="sidebar-label small">Người dùng</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                       title="Quản lý chuyên mục"
                       aria-label="Quản lý chuyên mục">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="sidebar-label small">Chuyên mục</span>
                    </a>
                    <a href="{{ route('admin.comments.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                       title="Quản lý bình luận"
                       aria-label="Quản lý bình luận">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="sidebar-label small">Bình luận</span>
                    </a>
                </div>
            @endif

            <!-- Settings / Cài đặt & Giao diện -->
            <a href="{{ route('profile.edit') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
style="margin-top: -4px;"
               title="Cài đặt & Giao diện"
               aria-label="Cài đặt">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="sidebar-label small">Cài đặt</span>
            </a>
        @endauth

        <!-- Quick Theme Toggle -->
        <button type="button"
                id="sidebar-theme-toggle"
                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                aria-label="Chuyển đổi giao diện sáng/tối"
                title="Chuyển đổi giao diện sáng/tối"
                class="btn p-0 w-100 d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none border-0 text-start">
            <div style="width: 20px; height: 20px;" class="d-flex align-items-center justify-content-center flex-shrink-0">
                <svg id="sidebar-icon-light" class="theme-icon-light" style="width: 19px; height: 19px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg id="sidebar-icon-dark" class="theme-icon-dark" style="width: 19px; height: 19px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </div>
            <span class="sidebar-label small font-medium" id="sidebar-theme-text">
                <span class="theme-text-light">Giao diện sáng</span>
                <span class="theme-text-dark">Giao diện tối</span>
            </span>
        </button>
    </nav>

    <!-- Bottom User Section / Auth -->
<div class="sidebar-account mt-auto border-theme-top">
    @auth
        <div class="sidebar-account-card">
            <a href="{{ route('profile.show') }}"
               class="sidebar-account__top text-decoration-none"
               title="Hồ sơ cá nhân: {{ Auth::user()->name }}">

                <x-avatar :user="Auth::user()" size="sm" />

                <div class="sidebar-account__meta sidebar-label">
                    <div class="sidebar-account__name-row">
                        <span class="sidebar-account__name text-truncate">
                            {{ Auth::user()->name }}
                        </span>

                        @if(Auth::user()->role === 'admin')
                            <span class="sidebar-account__badge">Admin</span>
                        @elseif(Auth::user()->role === 'author')
                            <span class="sidebar-account__badge">Tác giả</span>
                        @endif
                    </div>

                    <div class="sidebar-account__username text-truncate">
                        {{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}
                    </div>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-100 m-0">
                @csrf

                <button type="submit"
                        aria-label="Đăng xuất khỏi tài khoản"
                        title="Đăng xuất"
                        class="sidebar-account__logout">
                    <svg style="width: 18px; height: 18px; flex-shrink: 0;"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>

                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>

    @else
        <a href="{{ route('login') }}"
           class="d-flex align-items-center sidebar-link gap-2.5 px-3 py-2 rounded-3 text-decoration-none text-theme"
           title="Đăng nhập">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.8"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            <span class="sidebar-label small">Đăng nhập</span>
        </a>

        <a href="{{ route('register') }}"
           class="sidebar-label btn btn-sm btn-editorial-primary rounded-pill w-100 mt-1"
           title="Đăng ký tài khoản">
            Đăng ký
        </a>
    @endauth
</div>
</aside>
