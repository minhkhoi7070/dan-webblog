@php
    $isDrawer = $drawer ?? false;
    $groupClass = $isDrawer ? 'border-theme-top pt-2' : 'my-2 border-theme-top pt-2';
    $headingClass = $isDrawer
        ? 'px-2 py-1 label-uppercase text-theme-muted'
        : 'sidebar-label px-3 py-1 label-uppercase text-theme-muted';
    $listClass = $isDrawer ? 'd-flex flex-column gap-1 mt-1' : 'd-flex flex-column';
    $linkClass = 'd-flex align-items-center sidebar-link gap-3 px-3 py-2 text-decoration-none';

    if ($isDrawer) {
        $linkClass .= ' rounded-3';
    }
@endphp

<div class="{{ $groupClass }}">
    <div class="{{ $headingClass }}" style="font-size: 10px; letter-spacing: 0.08em;">
        QUẢN TRỊ
    </div>
    <div class="{{ $listClass }}">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           title="Dashboard Quản trị"
           aria-label="Dashboard"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Dashboard</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
           title="Quản lý người dùng"
           aria-label="Quản lý người dùng"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Quản lý người dùng</span>
        </a>

        <a href="{{ route('admin.posts.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"
           title="Quản lý bài viết"
           aria-label="Quản lý bài viết"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Quản lý bài viết</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
           title="Quản lý danh mục"
           aria-label="Quản lý danh mục"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Quản lý danh mục</span>
        </a>

        <a href="{{ route('admin.tags.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"
           title="Quản lý Tags"
           aria-label="Quản lý Tags"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Quản lý Tags</span>
        </a>

        <a href="{{ route('admin.comments.index') }}"
           class="{{ $linkClass }} {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}"
           title="Quản lý bình luận"
           aria-label="Quản lý bình luận"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Quản lý bình luận</span>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="{{ $linkClass }} {{ request()->routeIs('profile.*') ? 'active' : '' }}"
           title="Cài đặt tài khoản"
           aria-label="Cài đặt"
           @if($isDrawer) data-bs-dismiss="offcanvas" data-app-nav @endif>
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="{{ $isDrawer ? 'small' : 'sidebar-label small' }}">Cài đặt</span>
        </a>
    </div>
</div>
