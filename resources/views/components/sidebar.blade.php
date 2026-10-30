<aside class="d-none d-sm-flex flex-column position-fixed top-0 bottom-0 start-0 border-theme-right bg-theme-surface px-2 px-lg-3 py-3 user-select-none sidebar-container" style="z-index: 1040;">
    <!-- Brand Logo -->
    <a href="{{ route('posts.index') }}" class="brand-link d-flex align-items-center gap-2.5 font-bold tracking-tight text-decoration-none px-2 mb-3 mt-1" aria-label="CloudBlog Trang chủ">
        <div class="sidebar-logo-box d-flex align-items-center justify-content-center flex-shrink-0">
            <x-logo class="w-8 h-8" style="width: 30px; height: 30px;" />
        </div>
        <span class="brand-text sidebar-label text-theme fw-bold" style="font-size: 16px; letter-spacing: -0.02em;">CloudBlog</span>
    </a>

    <!-- Primary Nav Links -->
    <nav class="d-flex flex-column gap-1 flex-grow-1 overflow-y-auto no-scrollbar py-1" role="navigation" aria-label="Menu điều hướng chính">
        <!-- 1. Trang chủ (All Roles) -->
        <a href="{{ route('home') }}"
           class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}"
           title="Trang chủ"
           aria-label="Trang chủ">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="sidebar-label small">Trang chủ</span>
        </a>

        <!-- 2. Tìm kiếm (All Roles) -->
        <a href="{{ route('posts.index') }}#search"
           onclick="document.getElementById('feed-search-input')?.focus();"
           class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request('q') ? 'active' : '' }}"
           title="Tìm kiếm"
           aria-label="Tìm kiếm">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span class="sidebar-label small">Tìm kiếm</span>
        </a>

        @auth
            <!-- 3. Viết bài (Author only) -->
            @if(Auth::user()->isAuthor())
                <a href="{{ route('posts.create') }}"
                   class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('posts.create') ? 'active' : '' }}"
                   title="Viết bài mới"
                   aria-label="Viết bài mới">
                    <svg style="width: 18px; height: 18px; flex-shrink: 0; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="sidebar-label small fw-semibold text-accent">Viết bài</span>
                </a>
            @endif

            <!-- 4. Đang theo dõi (Viewer, Author, Admin) -->
            <a href="{{ route('following.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('following.*') ? 'active' : '' }}"
               title="Đang theo dõi"
               aria-label="Đang theo dõi">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="sidebar-label small">Đang theo dõi</span>
            </a>

            <!-- 5. Đã lưu (Viewer, Author, Admin) -->
            <a href="{{ route('favorites.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('favorites.*') ? 'active' : '' }}"
               title="Bài viết đã lưu"
               aria-label="Bài viết đã lưu">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span class="sidebar-label small">Đã lưu</span>
            </a>

            <!-- 6. Hoạt động (Viewer, Author, Admin) -->
            <a href="{{ route('activity.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('activity.*') ? 'active' : '' }}"
               title="Nhật ký hoạt động"
               aria-label="Nhật ký hoạt động">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="sidebar-label small">Hoạt động</span>
            </a>

            <!-- 7. Role-Specific Section -->

            <!-- Case A: Author Workspace Group -->
            @if(Auth::user()->isAuthor())
                <div class="my-2 border-theme-top pt-2 opacity-75">
                    <div class="sidebar-label px-3 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">
                        Tác giả
                    </div>
                    <a href="{{ route('posts.stats') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('posts.stats') || request()->routeIs('author.posts.*') || request()->routeIs('posts.edit') ? 'active' : '' }}"
                       title="Thống kê bài viết"
                       aria-label="Thống kê bài viết">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="sidebar-label small">Thống kê bài viết</span>
                    </a>
                    <a href="{{ route('author.comments.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('author.comments.*') ? 'active' : '' }}"
                       title="Quản lý bình luận bài viết"
                       aria-label="Bình luận bài viết">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="sidebar-label small">Bình luận bài viết</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>

            <!-- Case B: Admin Console Group -->
            @elseif(Auth::user()->isAdmin())
                <div class="my-2 border-theme-top pt-2 opacity-75">
                    <div class="sidebar-label px-3 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">
                        QUẢN TRỊ
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       title="Dashboard Quản trị"
                       aria-label="Dashboard">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="sidebar-label small">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       title="Quản lý người dùng"
                       aria-label="Quản lý người dùng">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý người dùng</span>
                    </a>
                    <a href="{{ route('admin.posts.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"
                       title="Quản lý bài viết"
                       aria-label="Quản lý bài viết">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý bài viết</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                       title="Quản lý danh mục"
                       aria-label="Quản lý danh mục">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý danh mục</span>
                    </a>
                    <a href="{{ route('admin.tags.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"
                       title="Quản lý Tags"
                       aria-label="Quản lý Tags">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span class="sidebar-label small">Quản lý Tags</span>
                    </a>
                    <a href="{{ route('admin.comments.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                       title="Quản lý bình luận"
                       aria-label="Quản lý bình luận">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý bình luận</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>

            <!-- Case C: Viewer Personal Group -->
            @else
                <div class="my-2 border-theme-top pt-2 opacity-75">
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>
            @endif
        @endauth
    </nav>

    <!-- Bottom User Section / Auth (Clean Linear/Notion Layout) -->
    <div class="sidebar-account mt-auto pt-2 border-theme-top flex-shrink-0" style="border-color: var(--color-border-subtle) !important;">
        @auth
            <!-- Logged-in User Section -->
            <div class="sidebar-account-card sidebar-user-container d-flex flex-column gap-2">
                <!-- User Profile Summary Row -->
                <a href="{{ route('profile.show') }}"
                   class="sidebar-user-row d-flex align-items-center gap-2.5 text-decoration-none p-1.5 rounded-2 transition-all"
                   title="Xem hồ sơ: {{ Auth::user()->name }}">
                    <div class="flex-shrink-0">
                        <x-avatar :user="Auth::user()" size="sm" class="border border-theme shadow-2xs" />
                    </div>

                    <div class="sidebar-label min-w-0 flex-grow-1">
                        <div class="sidebar-account__name sidebar-user-name fw-semibold text-theme text-truncate mb-0.5" style="font-size: 13px;">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="sidebar-account__sub-row d-flex align-items-center justify-content-between gap-1">
                            <span class="sidebar-account__username sidebar-user-handle small text-theme-muted text-truncate d-block" style="font-size: 11px;">
                                {{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}
                            </span>
                            @if(Auth::user()->role === 'admin')
                                <span class="sidebar-account__badge sidebar-account__badge--admin sidebar-role-pill sidebar-role-pill--admin">Admin</span>
                            @elseif(Auth::user()->role === 'author')
                                <span class="sidebar-account__badge sidebar-account__badge--author sidebar-role-pill sidebar-role-pill--author">Tác giả</span>
                            @else
                                <span class="sidebar-account__badge sidebar-account__badge--viewer sidebar-role-pill sidebar-role-pill--viewer">Độc giả</span>
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Theme Switcher: Segmented Control on Desktop (1 SINGLE ROW, 50/50), Compact Icon only on Tablet (<992px) -->
                <div class="sidebar-account__theme sidebar-theme-wrapper w-100">
                    <!-- Expanded View (Desktop >= 992px) -->
                    <div class="sidebar-label w-100">
                        <div class="sidebar-theme-toggle sidebar-theme-segmented p-1 rounded-2 bg-surface-2 border border-theme d-flex align-items-center gap-1 w-100"
                             role="radiogroup"
                             aria-label="Chuyển đổi giao diện">
                            <button type="button"
                                    class="sidebar-theme-seg-btn flex-fill d-flex align-items-center justify-content-center gap-1.5 py-1 px-2 rounded-2 border-0"
                                    id="sidebar-theme-btn-light"
                                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')"
                                    aria-label="Giao diện Sáng">
                                <svg width="13" height="13" class="text-warning flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                                </svg>
                                <span style="font-size: 11.5px; font-weight: 500;">Sáng</span>
                            </button>

                            <button type="button"
                                    class="sidebar-theme-seg-btn flex-fill d-flex align-items-center justify-content-center gap-1.5 py-1 px-2 rounded-2 border-0"
                                    id="sidebar-theme-btn-dark"
                                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')"
                                    aria-label="Giao diện Tối">
                                <svg width="13" height="13" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <span style="font-size: 11.5px; font-weight: 500;">Tối</span>
                            </button>
                        </div>
                    </div>

                    <!-- Compact View (Collapsed Sidebar < 992px ONLY - NEVER ON DESKTOP) -->
                    <div class="sidebar-theme-compact d-none">
                        <button type="button"
                                class="sidebar-theme-compact-btn btn btn-sm btn-outline-theme rounded-2 p-0 d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;"
                                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                                aria-label="Chuyển đổi giao diện sáng/tối"
                                title="Chuyển đổi giao diện">
                            <svg class="sidebar-icon-sun" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                            </svg>
                            <svg class="sidebar-icon-moon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Logout Button: Integrated within Account Container -->
                <form method="POST" action="{{ route('logout') }}" class="w-100 m-0">
                    @csrf
                    <button type="submit"
                            class="sidebar-account__logout sidebar-logout-btn w-100 d-flex align-items-center justify-content-center gap-2 py-1.5 px-2 rounded-2 border-0 bg-transparent text-theme-secondary transition-colors"
                            aria-label="Đăng xuất"
                            title="Đăng xuất khỏi tài khoản">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="sidebar-label small" style="font-size: 12px; font-weight: 500;">Đăng xuất</span>
                    </button>
                </form>
            </div>
        @else
            <!-- Guest Section -->
            <div class="sidebar-account-card sidebar-user-container d-flex flex-column gap-2">
                <!-- Expanded View (Desktop >= 992px) -->
                <div class="sidebar-label">
                    <span class="sidebar-account__name small fw-semibold text-theme d-block mb-1" style="font-size: 12px;">
                        Khám phá CloudBlog
                    </span>
                    <p class="small text-theme-muted mb-2" style="font-size: 11px; line-height: 1.35;">
                        Đăng nhập để tương tác và lưu bài viết.
                    </p>
                    <div class="d-flex gap-1.5 w-100">
                        <a href="{{ route('login') }}"
                           class="btn btn-sm btn-outline-theme rounded-2 flex-fill py-1 fw-medium text-center"
                           style="font-size: 12px;">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn btn-sm btn-editorial-primary rounded-2 flex-fill py-1 fw-medium text-center"
                           style="font-size: 12px;">
                            Đăng ký
                        </a>
                    </div>
                </div>

                <!-- Compact View (Collapsed < 992px ONLY) -->
                <div class="sidebar-guest-compact d-none">
                    <a href="{{ route('login') }}"
                       class="btn btn-sm btn-outline-theme rounded-2 p-0 d-flex align-items-center justify-content-center"
                       style="width: 36px; height: 36px;"
                       title="Đăng nhập"
                       aria-label="Đăng nhập">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>

                <!-- Theme Toggle for Guests -->
                <div class="sidebar-account__theme sidebar-theme-wrapper w-100 pt-1">
                    <div class="sidebar-label w-100">
                        <div class="sidebar-theme-toggle sidebar-theme-segmented p-1 rounded-2 bg-surface-2 border border-theme d-flex align-items-center gap-1 w-100">
                            <button type="button"
                                    class="sidebar-theme-seg-btn flex-fill d-flex align-items-center justify-content-center gap-1.5 py-1 px-2 rounded-2 border-0"
                                    id="sidebar-guest-theme-btn-light"
                                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')"
                                    aria-label="Giao diện Sáng">
                                <svg width="13" height="13" class="text-warning flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                                </svg>
                                <span style="font-size: 11.5px; font-weight: 500;">Sáng</span>
                            </button>

                            <button type="button"
                                    class="sidebar-theme-seg-btn flex-fill d-flex align-items-center justify-content-center gap-1.5 py-1 px-2 rounded-2 border-0"
                                    id="sidebar-guest-theme-btn-dark"
                                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')"
                                    aria-label="Giao diện Tối">
                                <svg width="13" height="13" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <span style="font-size: 11.5px; font-weight: 500;">Tối</span>
                            </button>
                        </div>
                    </div>

                    <div class="sidebar-theme-compact d-none">
                        <button type="button"
                                class="sidebar-theme-compact-btn btn btn-sm btn-outline-theme rounded-2 p-0 d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;"
                                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                                aria-label="Chuyển đổi giao diện"
                                title="Chuyển đổi giao diện">
                            <svg class="sidebar-icon-sun" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                            </svg>
                            <svg class="sidebar-icon-moon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endauth

        <script>
            (function() {
                function syncSidebarThemeState() {
                    if (!window.BlogMNMTheme) return;
                    var theme = window.BlogMNMTheme.getResolvedTheme();

                    var lightBtn = document.getElementById('sidebar-theme-btn-light') || document.getElementById('sidebar-guest-theme-btn-light');
                    var darkBtn = document.getElementById('sidebar-theme-btn-dark') || document.getElementById('sidebar-guest-theme-btn-dark');

                    if (lightBtn && darkBtn) {
                        lightBtn.classList.toggle('active', theme === 'light');
                        darkBtn.classList.toggle('active', theme === 'dark');
                    }

                    var sunIcons = document.querySelectorAll('.sidebar-icon-sun');
                    var moonIcons = document.querySelectorAll('.sidebar-icon-moon');

                    if (theme === 'dark') {
                        sunIcons.forEach(el => el.classList.remove('d-none'));
                        moonIcons.forEach(el => el.classList.add('d-none'));
                    } else {
                        sunIcons.forEach(el => el.classList.add('d-none'));
                        moonIcons.forEach(el => el.classList.remove('d-none'));
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', syncSidebarThemeState);
                } else {
                    syncSidebarThemeState();
                }
                window.addEventListener('blogmnm:theme-changed', syncSidebarThemeState);
            })();
        </script>
    </div>
</aside>
