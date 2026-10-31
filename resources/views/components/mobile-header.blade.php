@php
    $isHome = request()->routeIs('home') || (request()->routeIs('posts.index') && !request('q') && !request('category') && !request('tag'));
    $subpageTitle = null;

    if (request()->routeIs('posts.show')) {
        $subpageTitle = 'Chi tiết bài viết';
    } elseif (request()->routeIs('posts.create')) {
        $subpageTitle = 'Viết bài mới';
    } elseif (request()->routeIs('posts.edit')) {
        $subpageTitle = 'Chỉnh sửa bài viết';
    } elseif (request()->routeIs('posts.stats') || request()->routeIs('author.posts.*')) {
        $subpageTitle = 'Quản lý bài viết';
    } elseif (request()->routeIs('author.comments.*')) {
        $subpageTitle = 'Bình luận tác giả';
    } elseif (request()->routeIs('following.*')) {
        $subpageTitle = 'Đang theo dõi';
    } elseif (request()->routeIs('favorites.*')) {
        $subpageTitle = 'Bài viết đã lưu';
    } elseif (request()->routeIs('activity.*')) {
        $subpageTitle = 'Nhật ký hoạt động';
    } elseif (request()->routeIs('profile.*')) {
        $subpageTitle = 'Trang cá nhân';
    } elseif (request()->routeIs('admin.dashboard')) {
        $subpageTitle = 'Bảng điều khiển';
    } elseif (request()->routeIs('admin.posts.*')) {
        $subpageTitle = 'Kiểm duyệt bài viết';
    } elseif (request()->routeIs('admin.users.*')) {
        $subpageTitle = 'Quản lý người dùng';
    } elseif (request()->routeIs('admin.categories.*')) {
        $subpageTitle = 'Chuyên mục bài viết';
    } elseif (request()->routeIs('admin.tags.*')) {
        $subpageTitle = 'Thẻ bài viết';
    } elseif (request()->routeIs('admin.comments.*')) {
        $subpageTitle = 'Quản lý bình luận';
    } elseif (request()->routeIs('login')) {
        $subpageTitle = 'Đăng nhập';
    } elseif (request()->routeIs('register')) {
        $subpageTitle = 'Đăng ký tài khoản';
    }
@endphp

<!-- Mobile Top Navigation Header (320px–767px Mobile App Shell) -->
<header {{ $attributes->merge(['class' => 'd-flex d-md-none align-items-center justify-content-between px-3 sticky-top bg-theme-surface border-theme-bottom z-3 user-select-none mobile-top-header']) }} style="height: var(--mobile-header-height, 56px); max-height: var(--mobile-header-height, 56px);">
    <!-- Left: Brand Logo OR Back Button + Subpage Title -->
    <div class="d-flex align-items-center min-w-0 flex-grow-1 flex-shrink-1 me-2" id="mobile-header-left">
        @if($isHome || !$subpageTitle)
            <a href="{{ route('home') }}" class="brand-link d-flex align-items-center gap-2 font-bold text-lg tracking-tight text-decoration-none flex-shrink-0" aria-label="CloudBlog Trang chủ" data-app-nav id="mobile-header-brand">
                <x-logo class="w-8 h-8 flex-shrink-0" style="width: 32px; height: 32px; max-width: 32px; max-height: 32px;" />
                <span class="brand-text text-theme fw-bold" style="white-space: nowrap;">CloudBlog</span>
            </a>
        @else
            <div class="d-flex align-items-center gap-2 min-w-0 w-100">
                <a
                   @if(request()->routeIs('posts.edit'))
                       href="{{ route('posts.stats') }}" data-app-nav
                   @else
                       href="javascript:history.length > 1 ? history.back() : window.location.href='{{ route('home') }}'"
                   @endif
                   class="btn btn-sm btn-link text-theme p-0 d-flex align-items-center justify-content-center rounded-3 text-decoration-none flex-shrink-0"
                   style="width: 40px; height: 40px;"
                   aria-label="Quay lại"
                   title="Quay lại"
                   id="mobile-header-back-btn">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                @if(request()->routeIs('following.index'))
                    <a href="{{ route('home') }}" class="brand-link d-flex align-items-center gap-2 font-bold text-lg tracking-tight text-decoration-none min-w-0 flex-shrink-1" aria-label="CloudBlog Trang chủ" data-app-nav id="mobile-header-brand">
                        <x-logo class="w-8 h-8 flex-shrink-0" style="width: 32px; height: 32px; max-width: 32px; max-height: 32px;" />
                        <span class="brand-text text-theme fw-bold text-truncate">CloudBlog</span>
                    </a>
                @else
                    <span class="fw-bold text-theme text-truncate" style="font-size: 16px; letter-spacing: -0.01em;" id="mobile-header-subpage-title">
                        {{ $subpageTitle }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Right: Theme Toggle, Extended Menu Drawer, & Avatar/Auth -->
    <div class="d-flex align-items-center gap-1 flex-shrink-0">
        <!-- Quick Theme Toggle (44x44px touch target) -->
        <button type="button"
                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                aria-label="Chuyển đổi giao diện sáng/tối"
                title="Chuyển đổi giao diện sáng/tối"
                class="btn btn-sm btn-link text-theme-secondary text-decoration-none p-0 d-flex align-items-center justify-content-center rounded-3"
                style="width: 44px; height: 44px;">
            <svg class="theme-icon-light" style="width: 19px; height: 19px;" id="theme-icon-light-pub" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg class="theme-icon-dark" style="width: 19px; height: 19px;" id="theme-icon-dark-pub" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Extended Drawer Trigger Button (44x44px touch target) -->
        <button type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#mobileAppDrawer"
                aria-controls="mobileAppDrawer"
                aria-label="Mở menu mở rộng"
                title="Menu mở rộng"
                class="btn btn-sm btn-link text-theme-secondary text-decoration-none p-0 d-flex align-items-center justify-content-center rounded-3"
                style="width: 44px; height: 44px;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Profile Avatar or Login -->
        @auth
            <a href="{{ route('profile.show') }}"
               class="d-flex align-items-center justify-content-center text-decoration-none rounded-circle ms-0.5"
               style="width: 40px; height: 40px;"
               title="Trang cá nhân: {{ Auth::user()->name }}"
               aria-label="Trang cá nhân"
               data-app-nav>
                <x-avatar :user="Auth::user()" size="xs" />
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-editorial-primary rounded-pill px-3 py-1 ms-1 text-decoration-none" title="Đăng nhập">
                Đăng nhập
            </a>
        @endauth
    </div>
</header>
