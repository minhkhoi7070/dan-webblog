@extends('layouts.admin')

@section('admin_title', 'Quản lý & Kiểm duyệt Bình luận')

@section('admin_content')
<!-- Status Tabs (Professional Pill Segment Filter) -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-4 pb-1">
    <a
        href="{{ route('admin.comments.index') }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-secondary' }}"
        style="font-size: 12.5px;"
    >
        Tất cả <span class="opacity-75 ms-1">({{ number_format($statusCounts['all']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'approved' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
        style="font-size: 12.5px;"
    >
        Đã duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['approved']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'spam' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
        style="font-size: 12.5px;"
    >
        Spam <span class="opacity-75 ms-1">({{ number_format($statusCounts['spam']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
        style="font-size: 12.5px;"
    >
        Chờ duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['pending']) }})</span>
    </a>
</div>

<!-- Search Bar (High Density Toolbar) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="position-relative flex-grow-1" style="max-width: 440px; width: 100%;">
            <span
                class="position-absolute d-flex align-items-center justify-content-center text-theme-muted"
                style="
                    left: 16px;
                    top: 50%;
                    width: 18px;
                    height: 18px;
                    transform: translateY(-50%);
                    pointer-events: none;
                    z-index: 2;
                "
                aria-hidden="true"
            >
                <svg
                    width="16"
                    height="16"
                    style="display: block;"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </span>

            <input
                type="text"
                name="q"
                value="{{ $search }}"
                class="form-control bg-surface-2 border-theme text-theme rounded-3 w-100"
                placeholder="Tìm bình luận hoặc tên người dùng..."
                style="
                    min-height: 46px;
                    padding-left: 46px;
                    padding-right: 16px;
                "
            >
        </div>

        <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 fw-semibold flex-shrink-0" style="font-size: 12.5px;">
            Tìm kiếm
        </button>
    </form>
</div>

<div data-feed-shell
     data-feed-base-url="{{ route('admin.comments.index') }}"
     class="position-relative">
    <div data-feed-status class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></div>

    <template data-feed-skeleton>
        <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4 p-4">
            <div class="placeholder-glow">
                <div class="placeholder col-12 mb-3 py-2 rounded"></div>
                <div class="placeholder col-12 mb-3 py-2 rounded"></div>
                <div class="placeholder col-12 mb-3 py-2 rounded"></div>
                <div class="placeholder col-12 mb-3 py-2 rounded"></div>
            </div>
        </div>
    </template>

    @include('admin.comments.partials.table-results')
</div>
@endsection
