@extends('layouts.public')

@section('content')
<<<<<<< HEAD
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
=======
<!-- Admin Control Bar (Professional Minimal Header) -->
<div class="bg-theme-surface border-theme-bottom sticky-top py-2.5 user-select-none shadow-xs" style="z-index: 1030;">
    <div class="container-fluid px-3 px-sm-4 wide-container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2.5">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="label-uppercase text-accent" style="font-size: 10px; letter-spacing: 0.08em;">
                        Admin Control Center
                    </span>
                    <span class="text-theme-muted d-none d-sm-inline" style="font-size: 11px;">&bull;</span>
                    <span class="small text-theme-muted d-none d-sm-inline" style="font-size: 12px;">Hệ thống quản trị & kiểm duyệt nội dung</span>
                </div>
                <h1 class="h5 fw-bold text-theme mb-0 mt-0.5 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
>>>>>>> origin/main
                    @yield('admin_title', 'Bảng điều khiển Quản trị')
                </h1>
            </div>

            <!-- Admin Sub-navigation Links -->
<<<<<<< HEAD
            <nav class="d-flex flex-wrap align-items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.dashboard') ? 'btn-primary' : 'btn-outline-theme' }}"
=======
            <nav class="d-flex flex-nowrap align-items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5" role="navigation" aria-label="Điều hướng quản trị">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ request()->routeIs('admin.dashboard') ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                    style="font-size: 12.5px;"
>>>>>>> origin/main
                >
                    Tổng quan
                </a>
                <a
                    href="{{ route('admin.posts.index') }}"
<<<<<<< HEAD
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.posts.*') ? 'btn-primary' : 'btn-outline-theme' }}"
=======
                    class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ request()->routeIs('admin.posts.*') ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                    style="font-size: 12.5px;"
>>>>>>> origin/main
                >
                    Bài viết
                </a>
                <a
                    href="{{ route('admin.users.index') }}"
<<<<<<< HEAD
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.users.*') ? 'btn-primary' : 'btn-outline-theme' }}"
=======
                    class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ request()->routeIs('admin.users.*') ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                    style="font-size: 12.5px;"
>>>>>>> origin/main
                >
                    Người dùng
                </a>
                <a
                    href="{{ route('admin.categories.index') }}"
<<<<<<< HEAD
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.categories.*') ? 'btn-primary' : 'btn-outline-theme' }}"
=======
                    class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ request()->routeIs('admin.categories.*') ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                    style="font-size: 12.5px;"
>>>>>>> origin/main
                >
                    Chuyên mục
                </a>
                <a
                    href="{{ route('admin.comments.index') }}"
<<<<<<< HEAD
                    class="btn btn-sm rounded-pill {{ request()->routeIs('admin.comments.*') ? 'btn-primary' : 'btn-outline-theme' }}"
=======
                    class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ request()->routeIs('admin.comments.*') ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                    style="font-size: 12.5px;"
>>>>>>> origin/main
                >
                    Bình luận
                </a>
            </nav>
        </div>
    </div>
</div>

<<<<<<< HEAD
<div class="container-fluid px-3 px-sm-4 py-4 flex-grow-1">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
<div class="container-fluid px-3 px-sm-4 py-4 flex-grow-1 wide-container">
    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-xs border border-success border-opacity-25 rounded-3 py-2.5 px-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="small fw-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
<<<<<<< HEAD
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-xs border border-danger border-opacity-25 rounded-3 py-2.5 px-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="small fw-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('admin_content')
</div>
@endsection
