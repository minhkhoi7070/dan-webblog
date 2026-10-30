<!-- Mobile App Shell: Extended Offcanvas Drawer (BlogMNM Threads Style) -->
<div class="offcanvas offcanvas-start border-0 bg-theme-surface mobile-drawer" tabindex="-1" id="mobileAppDrawer" aria-labelledby="mobileAppDrawerLabel" style="width: 320px; max-width: 86vw; z-index: 1055;">
    <!-- Drawer Header -->
    <div class="offcanvas-header border-theme-bottom px-3 py-3 d-flex align-items-center justify-content-between">
        <a href="{{ route('home') }}" class="brand-link d-flex align-items-center gap-2 font-bold text-lg tracking-tight text-decoration-none" aria-label="CloudBlog Trang chủ" data-bs-dismiss="offcanvas" data-app-nav>
            <x-logo class="w-8 h-8 flex-shrink-0" style="width: 32px; height: 32px; max-width: 32px; max-height: 32px;" />
            <span class="brand-text fs-6 fw-bold text-theme" id="mobileAppDrawerLabel">CloudBlog</span>
        </a>
        <button type="button" class="btn-close btn-close-white text-reset opacity-75" data-bs-dismiss="offcanvas" aria-label="Đóng menu"></button>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body d-flex flex-column p-3 overflow-y-auto no-scrollbar">
        @auth
            <!-- User Profile Summary Card -->
            <a href="{{ route('profile.show') }}"
               class="mobile-drawer-profile-card d-flex align-items-center rounded-3 border border-theme bg-surface-2 text-decoration-none"
               style="padding: 13px 14px; gap: 12px; margin-top: 0; margin-bottom: 20px; transition: background-color var(--duration-fast, 150ms) ease, border-color var(--duration-fast, 150ms) ease;"
               data-bs-dismiss="offcanvas"
               data-app-nav>
                <x-avatar :user="Auth::user()" size="md" class="flex-shrink-0" />
                <div class="d-flex flex-column justify-content-center min-w-0 flex-grow-1" style="gap: 4px;">
                    <div class="fw-bold text-theme text-truncate" style="font-size: 14.5px; line-height: 1.25;">{{ Auth::user()->name }}</div>
                    <div class="small text-theme-muted text-truncate" style="font-size: 12px; line-height: 1.25;">{{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}</div>
                </div>
                <span class="sidebar-account__badge flex-shrink-0 ms-auto {{ Auth::user()->isAdmin() ? 'sidebar-account__badge--admin' : (Auth::user()->isAuthor() ? 'sidebar-account__badge--author' : 'sidebar-account__badge--viewer') }}"
                      style="margin-left: 10px;">
                    {{ Auth::user()->isAdmin() ? 'Admin' : (Auth::user()->isAuthor() ? 'Tác giả' : 'Độc giả') }}
                </span>
            </a>
        @endauth

        <!-- Navigation Sections -->
        <div class="d-flex flex-column gap-3 flex-grow-1">
            <!-- 1. Khám phá -->
            <div>
                <div class="px-2 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">Khám phá</div>
                <div class="d-flex flex-column gap-1 mt-1">
                    <a href="{{ route('home') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="small">Trang chủ</span>
                    </a>
                    <a href="{{ route('posts.index') }}#search" onclick="document.getElementById('feed-search-input')?.focus();" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request('q') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="small">Tìm kiếm</span>
                    </a>
                    @auth
                        <a href="{{ route('following.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('following.*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="small">Đang theo dõi</span>
                        </a>
                    @endauth
                </div>
            </div>

            @auth
                <!-- 2. Cá nhân -->
                <div class="border-theme-top pt-2">
                    <div class="px-2 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">Cá nhân</div>
                    <div class="d-flex flex-column gap-1 mt-1">
                        <a href="{{ route('favorites.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span class="small">Bài viết đã lưu</span>
                        </a>
                        <a href="{{ route('activity.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('activity.*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="small">Hoạt động</span>
                        </a>
                        <a href="{{ route('profile.show') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="small">Trang cá nhân</span>
                        </a>
                        @if(!Auth::user()->isAdmin())
                        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="small">Cài đặt</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- 3. Khu vực Tác giả (Author Only) -->
                @if(Auth::user()->isAuthor())
                    <div class="border-theme-top pt-2">
                        <div class="px-2 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">Tác giả</div>
                        <div class="d-flex flex-column gap-1 mt-1">
                            <a href="{{ route('posts.create') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('posts.create') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                                <svg style="width: 18px; height: 18px; flex-shrink: 0; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="small fw-semibold text-accent">Soạn bài viết mới</span>
                            </a>
                            <a href="{{ route('posts.stats') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('posts.stats') || request()->routeIs('author.posts.*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="small">Thống kê bài viết</span>
                            </a>
                            <a href="{{ route('author.comments.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none sidebar-link {{ request()->routeIs('author.comments.*') ? 'active' : '' }}" data-bs-dismiss="offcanvas" data-app-nav>
                                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <span class="small">Bình luận bài viết</span>
                            </a>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->isAdmin())
                    @include('components.admin-navigation-links', ['drawer' => true])
                @endif

                @if(Auth::user()->role === 'viewer')
                    <div class="border-theme-top pt-2">
                        <form method="POST" action="{{ route('author.become') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Trở thành tác giả</span>
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <!-- Guest Authentication Options -->
                <div class="border-theme-top pt-3">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('login') }}" class="btn btn-editorial-primary rounded-pill py-2 text-center" data-bs-dismiss="offcanvas">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-theme rounded-pill py-2 text-center" data-bs-dismiss="offcanvas">
                            Đăng ký tài khoản
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Drawer Footer: Theme & Logout -->
        <div class="border-theme-top pt-3 mt-auto">
            <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <span class="small text-theme-muted">Chế độ giao diện</span>
                <button type="button" onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 d-flex align-items-center gap-2" aria-label="Chuyển đổi giao diện sáng/tối">
                    <svg class="theme-icon-light" style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="theme-icon-dark" style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="small">Đổi màu</span>
                </button>
            </div>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100 py-1.5 d-flex align-items-center justify-content-center gap-2">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>

