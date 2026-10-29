<aside class="d-none d-md-flex flex-column position-fixed top-0 bottom-0 start-0 border-theme-right bg-theme-surface px-2 px-lg-3 py-4 user-select-none sidebar-container" style="z-index: 1040;">
    <!-- Brand Logo -->
    <a href="{{ route('posts.index') }}" class="brand-link flex items-center gap-2.5 font-bold text-xl tracking-tight text-decoration-none px-2 mb-4" aria-label="CloudBlog Trang chủ">
        <x-logo class="w-11 h-11" />
        <span class="brand-text sidebar-label">CloudBlog</span>
    </a>

    <!-- Primary Nav Links -->
    <nav class="d-flex flex-column gap-1 flex-grow-1 overflow-y-auto no-scrollbar" role="navigation" aria-label="Menu điều hướng chính">
        <!-- 1. Trang chủ (All Roles) -->
        <a href="{{ route('home') }}"
           class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}"
           title="Trang chủ"
           aria-label="Trang chủ">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <svg style="width: 20px; height: 20px; flex-shrink: 0; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="sidebar-label small">Đang theo dõi</span>
            </a>

            <!-- 5. Đã lưu (Viewer, Author, Admin) -->
            <a href="{{ route('favorites.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('favorites.*') ? 'active' : '' }}"
               title="Bài viết đã lưu"
               aria-label="Bài viết đã lưu">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <span class="sidebar-label small">Đã lưu</span>
            </a>

            <!-- 6. Hoạt động (Viewer, Author, Admin) -->
            <a href="{{ route('activity.index') }}"
               class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('activity.*') ? 'active' : '' }}"
               title="Nhật ký hoạt động"
               aria-label="Nhật ký hoạt động">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span class="sidebar-label small">Hoạt động</span>
            </a>

            <!-- 7. Role-Specific Section -->

            <!-- Case A: Author Workspace Group -->
            @if(Auth::user()->isAuthor())
                <div class="my-2 border-theme-top pt-2">
                    <div class="sidebar-label px-3 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">
                        Tác giả
                    </div>
                    <a href="{{ route('posts.stats') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('posts.stats') || request()->routeIs('author.posts.*') || request()->routeIs('posts.edit') ? 'active' : '' }}"
                       title="Thống kê bài viết"
                       aria-label="Thống kê bài viết">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="sidebar-label small">Thống kê bài viết</span>
                    </a>
                    <a href="{{ route('author.comments.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('author.comments.*') ? 'active' : '' }}"
                       title="Quản lý bình luận bài viết"
                       aria-label="Bình luận bài viết">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="sidebar-label small">Bình luận bài viết</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>

            <!-- Case B: Admin Console Group -->
            @elseif(Auth::user()->isAdmin())
                <div class="my-2 border-theme-top pt-2">
                    <div class="sidebar-label px-3 py-1 label-uppercase text-theme-muted" style="font-size: 10px; letter-spacing: 0.08em;">
                        QUẢN TRỊ
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       title="Dashboard Quản trị"
                       aria-label="Dashboard">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="sidebar-label small">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       title="Quản lý người dùng"
                       aria-label="Quản lý người dùng">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý người dùng</span>
                    </a>
                    <a href="{{ route('admin.posts.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"
                       title="Quản lý bài viết"
                       aria-label="Quản lý bài viết">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý bài viết</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                       title="Quản lý danh mục"
                       aria-label="Quản lý danh mục">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý danh mục</span>
                    </a>
                    <a href="{{ route('admin.tags.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"
                       title="Quản lý Tags"
                       aria-label="Quản lý Tags">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span class="sidebar-label small">Quản lý Tags</span>
                    </a>
                    <a href="{{ route('admin.comments.index') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
                       title="Quản lý bình luận"
                       aria-label="Quản lý bình luận">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="sidebar-label small">Quản lý bình luận</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>

            <!-- Case C: Viewer Personal Group -->
            @else
                <div class="my-2 border-theme-top pt-2">
                    <a href="{{ route('profile.edit') }}"
                       class="d-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                       title="Cài đặt tài khoản"
                       aria-label="Cài đặt">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="sidebar-label small">Cài đặt</span>
                    </a>
                </div>
            @endif
        @endauth
    </nav>

    <!-- Bottom User Section / Auth -->
    <div class="sidebar-account mt-auto border-theme-top flex-shrink-0">
        @auth
            <!-- Account Card (Author, Viewer, Admin) -->
            <div class="sidebar-account-card">
                <!-- 1. Avatar, user name, @username, role badge -->
                <a href="{{ route('profile.show') }}"
                   class="sidebar-account__top text-decoration-none"
                   title="Hồ sơ cá nhân: {{ Auth::user()->name }}">
                    <x-avatar :user="Auth::user()" size="sm" />

                    <div class="sidebar-account__meta sidebar-label">
                        <div class="sidebar-account__name" title="{{ Auth::user()->name }}">
                            {{ Auth::user()->name }}
                        </div>

                        <div class="sidebar-account__sub-row">
                            <span class="sidebar-account__username text-truncate" title="{{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}">
                                {{ '@' . (Auth::user()->username ?? strtolower(str_replace(' ', '', Auth::user()->name))) }}
                            </span>

                            @if(Auth::user()->role === 'admin')
                                <span class="sidebar-account__badge sidebar-account__badge--admin">Admin</span>
                            @elseif(Auth::user()->role === 'author')
                                <span class="sidebar-account__badge sidebar-account__badge--author">Tác giả</span>
                            @else
                                <span class="sidebar-account__badge sidebar-account__badge--viewer">Độc giả</span>
                            @endif
                        </div>
                    </div>
                </a>

                <!-- 2. Thin Divider -->
                <div class="sidebar-account__divider"></div>

                <!-- 3. Theme Toggle Row -->
                <div class="sidebar-account__theme">
                    <!-- Expanded View (Desktop >= 992px) -->
                    <div class="sidebar-account__theme-row sidebar-label">
                        <span class="sidebar-theme-title">CHẾ ĐỘ GIAO DIỆN</span>

                        <div class="sidebar-theme-toggle"
                             role="group"
                             aria-label="Chuyển đổi giao diện sáng hoặc tối"
                             onkeydown="window.BlogMNMTheme && window.BlogMNMTheme.handleKeydown && window.BlogMNMTheme.handleKeydown(event)"
                             onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()">
                            <div class="sidebar-theme-toggle__indicator" aria-hidden="true"></div>

                            <button type="button"
                                    class="sidebar-theme-toggle__btn sidebar-theme-toggle__btn--light"
                                    onclick="event.stopPropagation(); window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')"
                                    aria-label="Chuyển sang giao diện Sáng"
                                    aria-pressed="false"
                                    title="Chuyển sang giao diện Sáng">
                                <svg class="sidebar-theme-toggle__icon sidebar-theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                                </svg>
                            </button>

                            <button type="button"
                                    class="sidebar-theme-toggle__btn sidebar-theme-toggle__btn--dark"
                                    onclick="event.stopPropagation(); window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')"
                                    aria-label="Chuyển sang giao diện Tối"
                                    aria-pressed="false"
                                    title="Chuyển sang giao diện Tối">
                                <svg class="sidebar-theme-toggle__icon sidebar-theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Compact View (Collapsed Sidebar < 992px) -->
                    <div class="sidebar-account__theme-compact">
                        <button type="button"
                                class="sidebar-theme-compact__btn"
                                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                                aria-label="Chuyển đổi giao diện sáng/tối"
                                title="Chuyển đổi giao diện sáng/tối">
                            <svg class="sidebar-theme-compact__sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                            </svg>
                            <svg class="sidebar-theme-compact__moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 4. Thin Divider -->
                <div class="sidebar-account__divider"></div>

                <!-- 5. Logout Button -->
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
            <!-- Guest Card: EXACT same width, border, radius, spacing as Author card -->
            <div class="sidebar-account-card">
                <!-- Expanded View (Desktop >= 992px) -->
                <div class="sidebar-label">
                    <div class="sidebar-account__name mb-1">
                        Khám phá CloudBlog
                    </div>
                    <p class="small text-theme-muted mb-2.5" style="font-size: 11.5px; line-height: 1.35;">
                        Đăng nhập để tương tác, lưu bài viết và theo dõi tác giả yêu thích.
                    </p>
                    <div class="d-flex gap-2 w-100">
                        <a href="{{ route('login') }}"
                           class="btn btn-sm btn-outline-theme rounded-pill flex-fill fw-semibold d-flex align-items-center justify-content-center"
                           style="min-height: 36px; font-size: 12.5px;"
                           title="Đăng nhập tài khoản">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn btn-sm btn-editorial-primary rounded-pill flex-fill fw-semibold d-flex align-items-center justify-content-center"
                           style="min-height: 36px; font-size: 12.5px;"
                           title="Đăng ký tài khoản">
                            Đăng ký
                        </a>
                    </div>
                </div>

                <!-- Compact View (Collapsed < 992px) -->
                <div class="sidebar-guest-compact">
                    <a href="{{ route('login') }}"
                       class="sidebar-guest-compact__btn"
                       title="Đăng nhập"
                       aria-label="Đăng nhập">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>

                <!-- Thin Divider -->
                <div class="sidebar-account__divider"></div>

                <!-- Theme switch (Exact same theme switch as Author) -->
                <div class="sidebar-account__theme">
                    <!-- Expanded View (Desktop >= 992px) -->
                    <div class="sidebar-account__theme-row sidebar-label">
                        <span class="sidebar-theme-title">CHẾ ĐỘ GIAO DIỆN</span>

                        <div class="sidebar-theme-toggle"
                             role="group"
                             aria-label="Chuyển đổi giao diện sáng hoặc tối"
                             onkeydown="window.BlogMNMTheme && window.BlogMNMTheme.handleKeydown && window.BlogMNMTheme.handleKeydown(event)"
                             onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()">
                            <div class="sidebar-theme-toggle__indicator" aria-hidden="true"></div>

                            <button type="button"
                                    class="sidebar-theme-toggle__btn sidebar-theme-toggle__btn--light"
                                    onclick="event.stopPropagation(); window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')"
                                    aria-label="Chuyển sang giao diện Sáng"
                                    aria-pressed="false"
                                    title="Chuyển sang giao diện Sáng">
                                <svg class="sidebar-theme-toggle__icon sidebar-theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                                </svg>
                            </button>

                            <button type="button"
                                    class="sidebar-theme-toggle__btn sidebar-theme-toggle__btn--dark"
                                    onclick="event.stopPropagation(); window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')"
                                    aria-label="Chuyển sang giao diện Tối"
                                    aria-pressed="false"
                                    title="Chuyển sang giao diện Tối">
                                <svg class="sidebar-theme-toggle__icon sidebar-theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Compact View (Collapsed Sidebar < 992px) -->
                    <div class="sidebar-account__theme-compact">
                        <button type="button"
                                class="sidebar-theme-compact__btn"
                                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                                aria-label="Chuyển đổi giao diện sáng/tối"
                                title="Chuyển đổi giao diện sáng/tối">
                            <svg class="sidebar-theme-compact__sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                            </svg>
                            <svg class="sidebar-theme-compact__moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endauth

        <script>
            (function() {
                function syncSidebarThemeA11y() {
                    if (window.BlogMNMTheme && window.BlogMNMTheme.updateAria) {
                        window.BlogMNMTheme.updateAria();
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', syncSidebarThemeA11y);
                } else {
                    syncSidebarThemeA11y();
                }
                window.addEventListener('blogmnm:theme-changed', syncSidebarThemeA11y);
            })();
        </script>
    </div>
</aside>
