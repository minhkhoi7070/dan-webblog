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

    .admin-control-bar .min-w-0 {
        min-width: 0;
    }

    .admin-control-title {
        margin: 0;
        color: var(--color-text-primary);
        font-family: var(--font-sans);
        font-size: clamp(1.1rem, 1.45vw, 1.35rem);
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .admin-control-subtitle {
        margin-top: .15rem;
        color: var(--color-text-muted);
        font-size: 11.5px;
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


    }
</style>

<!-- Admin Control Bar (Desktop / Tablet only; Mobile uses mobile-header with dynamic title) -->
<div class="admin-control-bar user-select-none d-none d-md-block">
    <div class="container-fluid px-3 px-sm-4 wide-container">
        <div class="admin-control-bar__inner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="min-w-0">
                <h1 class="admin-control-title">@yield('admin_title', 'Bảng điều khiển Quản trị')</h1>
            </div>
        </div>
    </div>
</div>

<div class="admin-content-shell container-fluid px-3 px-sm-4 py-4 py-lg-5 flex-grow-1 wide-container">
    @yield('admin_content')
</div>
@endsection
