@extends('layouts.public')

@section('content')
<div class="bg-theme-surface border-theme-bottom sticky-top py-3 user-select-none" style="z-index: 1020;">
    <div class="container-fluid px-3 px-sm-4">
        <div class="d-flex flex-column flex-md-row md-align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background-color: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 10px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase;">
                        Admin Control Center
                    </span>
                    <span class="small text-theme-secondary">Hệ thống quản trị & kiểm duyệt nội dung BlogMNM</span>
                </div>
                <h1 class="h5 fw-bold text-theme mb-0 mt-1">
                    @yield('admin_title', 'Bảng điều khiển Quản trị')
                </h1>
            </div>

            <!-- Admin Sub-navigation Links -->
            <nav class="d-flex flex-wrap align-items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.dashboard') ? 'btn-primary' : 'btn-outline-theme' }}"
                >
                    Tổng quan
                </a>
                <a
                    href="{{ route('admin.posts.index') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.posts.*') ? 'btn-primary' : 'btn-outline-theme' }}"
                >
                    Bài viết
                </a>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.users.*') ? 'btn-primary' : 'btn-outline-theme' }}"
                >
                    Người dùng
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.categories.*') ? 'btn-primary' : 'btn-outline-theme' }}"
                >
                    Chuyên mục
                </a>
                <a
                    href="{{ route('admin.comments.index') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.comments.*') ? 'btn-primary' : 'btn-outline-theme' }}"
                >
                    Bình luận
                </a>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-sm-4 py-4 flex-grow-1">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('admin_content')
</div>
@endsection
