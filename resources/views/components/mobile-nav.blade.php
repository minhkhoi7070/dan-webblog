<!-- Mobile Bottom Navigation Bar (320px–767px Mobile App Shell: Role-Based Navigation) -->
<nav class="d-flex d-md-none position-fixed bottom-0 start-0 end-0 bg-theme-surface border-theme-top align-items-stretch justify-content-around user-select-none mobile-bottom-nav" role="navigation" aria-label="Điều hướng di động">
    @guest
        <!-- ================= GUEST (3 TABS) ================= -->
        <!-- 1. Trang chủ -->
        <a href="{{ route('home') }}"
           aria-label="Trang chủ"
           title="Trang chủ"
           class="mobile-bottom-nav-link {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}"
           @if(request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag'))) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </span>
            <span class="mobile-bottom-nav-label">Trang chủ</span>
        </a>

        <!-- 2. Tìm kiếm -->
        <a href="{{ route('posts.index') }}#search"
           onclick="const el = document.getElementById('feed-search-input'); if(el) { el.focus(); el.scrollIntoView({behavior: 'smooth', block: 'center'}); }"
           aria-label="Tìm kiếm bài viết"
           title="Tìm kiếm"
           class="mobile-bottom-nav-link {{ request('q') ? 'active' : '' }}"
           @if(request('q')) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <span class="mobile-bottom-nav-label">Tìm kiếm</span>
        </a>

        <!-- 3. Đăng nhập -->
        <a href="{{ route('login') }}"
           aria-label="Đăng nhập"
           title="Đăng nhập"
           class="mobile-bottom-nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
           @if(request()->routeIs('login')) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </span>
            <span class="mobile-bottom-nav-label">Đăng nhập</span>
        </a>
    @else
        @php
            $user = Auth::user();
            $isAdmin = $user->isAdmin();
            $isAuthor = $user->isAuthor();
        @endphp

        <!-- 1. Trang chủ (All Roles) -->
        <a href="{{ route('home') }}"
           aria-label="Trang chủ"
           title="Trang chủ"
           class="mobile-bottom-nav-link {{ request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag')) ? 'active' : '' }}"
           @if(request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag'))) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </span>
            <span class="mobile-bottom-nav-label">Trang chủ</span>
        </a>

        <!-- 2. Tìm kiếm (All Roles) -->
        <a href="{{ route('posts.index') }}#search"
           onclick="const el = document.getElementById('feed-search-input'); if(el) { el.focus(); el.scrollIntoView({behavior: 'smooth', block: 'center'}); }"
           aria-label="Tìm kiếm bài viết"
           title="Tìm kiếm"
           class="mobile-bottom-nav-link {{ request('q') ? 'active' : '' }}"
           @if(request('q')) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <span class="mobile-bottom-nav-label">Tìm kiếm</span>
        </a>

        <!-- 3. Slot 3 (Center Action - Role Dependent) -->
        @if($isAdmin)
            <!-- Admin: Quản trị Hub / Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               aria-label="Bảng điều khiển Quản trị"
               title="Quản trị"
               class="mobile-bottom-nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
               @if(request()->routeIs('admin.*')) aria-current="page" @endif>
                <span class="mobile-bottom-nav-icon">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 24px; height: 24px; background-color: var(--color-interactive-subtle);">
                        <svg style="width: 15px; height: 15px; color: var(--color-interactive);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                </span>
                <span class="mobile-bottom-nav-label">Quản trị</span>
            </a>
        @elseif($isAuthor)
            <!-- Author: Viết bài (+) -->
            <a href="{{ route('posts.create') }}"
               aria-label="Viết bài mới"
               title="Viết bài mới"
               class="mobile-bottom-nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}"
               @if(request()->routeIs('posts.create')) aria-current="page" @endif>
                <span class="mobile-bottom-nav-icon">
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 24px; height: 24px; background-color: var(--color-interactive-subtle);">
                        <svg style="width: 15px; height: 15px; color: var(--color-interactive);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </span>
                <span class="mobile-bottom-nav-label">Viết bài</span>
            </a>
        @else
            <!-- Viewer: Hoạt động -->
            <a href="{{ route('activity.index') }}"
               aria-label="Nhật ký hoạt động"
               title="Hoạt động"
               class="mobile-bottom-nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}"
               @if(request()->routeIs('activity.*')) aria-current="page" @endif>
                <span class="mobile-bottom-nav-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
                <span class="mobile-bottom-nav-label">Hoạt động</span>
            </a>
        @endif

        <!-- 4. Slot 4 -->
        @if($isAdmin)
            <!-- Admin: Hoạt động -->
            <a href="{{ route('activity.index') }}"
               aria-label="Nhật ký hoạt động"
               title="Hoạt động"
               class="mobile-bottom-nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}"
               @if(request()->routeIs('activity.*')) aria-current="page" @endif>
                <span class="mobile-bottom-nav-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
                <span class="mobile-bottom-nav-label">Hoạt động</span>
            </a>
        @else
            <!-- Viewer / Author: Đã lưu -->
            <a href="{{ route('favorites.index') }}"
               aria-label="Bài viết đã lưu"
               title="Bài viết đã lưu"
               class="mobile-bottom-nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}"
               @if(request()->routeIs('favorites.*')) aria-current="page" @endif>
                <span class="mobile-bottom-nav-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </span>
                <span class="mobile-bottom-nav-label">Đã lưu</span>
            </a>
        @endif

        <!-- 5. Tài khoản (All Roles) -->
        <a href="{{ route('profile.show') }}"
           aria-label="Trang cá nhân: {{ $user->name }}"
           title="Tài khoản"
           class="mobile-bottom-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
           @if(request()->routeIs('profile.*')) aria-current="page" @endif>
            <span class="mobile-bottom-nav-icon">
                <x-avatar :user="$user" size="xs" />
            </span>
            <span class="mobile-bottom-nav-label">Tài khoản</span>
        </a>
    @endguest
</nav>
