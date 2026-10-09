@extends('layouts.public')

@section('content')
<style>
    .admin-control-bar {
        position: sticky;
        top: 0;
        z-index: 1030;
        border-bottom: 1px solid var(--color-border-subtle);
        background: var(--color-bg-surface);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.02);
    }

    .admin-control-bar__inner {
        min-height: 64px;
    }

    .admin-control-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .15rem;
        color: var(--color-text-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .admin-control-kicker::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: var(--color-interactive);
    }

    .admin-control-bar .min-w-0 {
        min-width: 0;
    }

    .admin-control-title {
        margin: 0;
        color: var(--color-text-primary);
        font-family: var(--font-serif, 'Lora', Georgia, serif);
        font-size: clamp(1.1rem, 1.45vw, 1.35rem);
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .admin-control-subtitle {
        margin-top: .15rem;
        color: var(--color-text-muted);
        font-size: 11.5px;
    }

    .admin-nav {
        display: flex;
        align-items: center;
        gap: .2rem;
        max-width: 100%;
        padding: .18rem;
        border: 1px solid var(--color-border-subtle);
        border-radius: 999px;
        background: var(--color-bg-subtle);
        overflow-x: auto;
        scrollbar-width: none;
    }

    .admin-nav::-webkit-scrollbar {
        display: none;
    }

    .admin-nav .btn {
        flex: 0 0 auto;
        min-height: 30px;
        border-radius: 999px !important;
        padding-inline: .8rem;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 600;
    }

    .admin-content-shell {
        min-width: 0;
    }

    @media (max-width: 767.98px) {
        .admin-control-bar__inner {
            min-height: auto;
            padding-top: .65rem;
            padding-bottom: .65rem;
        }

        .admin-control-subtitle {
            display: none;
        }

        .admin-nav {
            width: 100%;
            border-radius: 14px;
        }
    }
</style>

<!-- Admin Control Bar -->
<div class="admin-control-bar user-select-none">
    <div class="container-fluid px-3 px-sm-4 wide-container">
        <div class="admin-control-bar__inner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="min-w-0">
                <div class="admin-control-kicker">Admin Control Center</div>
                <h1 class="admin-control-title">@yield('admin_title', 'Bảng điều khiển Quản trị')</h1>
                <div class="admin-control-subtitle">Hệ thống quản trị và kiểm duyệt nội dung BlogMNM</div>
            </div>

            <nav class="admin-nav" role="navigation" aria-label="Điều hướng quản trị">
                <a href="{{ route('admin.dashboard') }}"
                   class="btn btn-sm {{ request()->routeIs('admin.dashboard') ? 'btn-editorial-primary' : 'btn-outline-theme border-0' }}">
                    Tổng quan
                </a>
                <a href="{{ route('admin.posts.index') }}"
                   class="btn btn-sm {{ request()->routeIs('admin.posts.*') ? 'btn-editorial-primary' : 'btn-outline-theme border-0' }}">
                    Bài viết
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-sm {{ request()->routeIs('admin.users.*') ? 'btn-editorial-primary' : 'btn-outline-theme border-0' }}">
                    Người dùng
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="btn btn-sm {{ request()->routeIs('admin.categories.*') ? 'btn-editorial-primary' : 'btn-outline-theme border-0' }}">
                    Chuyên mục
                </a>
                <a href="{{ route('admin.comments.index') }}"
                   class="btn btn-sm {{ request()->routeIs('admin.comments.*') ? 'btn-editorial-primary' : 'btn-outline-theme border-0' }}">
                    Bình luận
                </a>
            </nav>
        </div>
    </div>
</div>

<div class="admin-content-shell container-fluid px-3 px-sm-4 py-4 py-lg-5 flex-grow-1 wide-container">
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-xs border border-success border-opacity-25 rounded-3 py-2.5 px-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="small fw-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-xs border border-danger border-opacity-25 rounded-3 py-2.5 px-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="small fw-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @yield('admin_content')
</div>
@endsection
