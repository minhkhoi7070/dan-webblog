@extends('layouts.admin')

@section('admin_title', 'Quản lý & Kiểm duyệt Bình luận')

@section('admin_content')
<div data-feed-shell
     data-feed-base-url="{{ route('admin.comments.index') }}"
     data-feed-target="#feed-results"
     class="position-relative">

    <!-- Status Tabs (Professional Pill Segment Filter) -->
    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-4 pb-1" data-feed-status-tabs>
        <a
            href="{{ route('admin.comments.index', array_filter(['q' => $search])) }}"
            data-feed-tab=""
            data-active-class="btn-editorial-primary text-white"
            data-inactive-class="btn-outline-theme text-theme-secondary"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary text-white' : 'btn-outline-theme text-theme-secondary' }}"
            style="font-size: 12.5px;"
            @if(empty($currentStatus)) aria-current="page" @endif
        >
            Tất cả <span class="opacity-75 ms-1" data-feed-count="all">({{ number_format($statusCounts['all']) }})</span>
        </a>
        <a
            href="{{ route('admin.comments.index', array_filter(['status' => 'approved', 'q' => $search])) }}"
            data-feed-tab="approved"
            data-active-class="btn-success text-white fw-bold"
            data-inactive-class="btn-outline-theme text-success"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'approved' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'approved') aria-current="page" @endif
        >
            Đã duyệt <span class="opacity-75 ms-1" data-feed-count="approved">({{ number_format($statusCounts['approved']) }})</span>
        </a>
        <a
            href="{{ route('admin.comments.index', array_filter(['status' => 'spam', 'q' => $search])) }}"
            data-feed-tab="spam"
            data-active-class="btn-danger text-white fw-bold"
            data-inactive-class="btn-outline-theme text-danger"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'spam' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'spam') aria-current="page" @endif
        >
            Spam <span class="opacity-75 ms-1" data-feed-count="spam">({{ number_format($statusCounts['spam']) }})</span>
        </a>
        <a
            href="{{ route('admin.comments.index', array_filter(['status' => 'pending', 'q' => $search])) }}"
            data-feed-tab="pending"
            data-active-class="btn-warning text-dark fw-bold"
            data-inactive-class="btn-outline-theme text-warning"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'pending') aria-current="page" @endif
        >
            Chờ duyệt <span class="opacity-75 ms-1" data-feed-count="pending">({{ number_format($statusCounts['pending']) }})</span>
        </a>
    </div>

    <!-- Search Bar (High Density Toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex flex-column flex-sm-row align-items-sm-center gap-3" data-feed-filter-form>
            @if($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif

            <div class="position-relative flex-grow-1" style="max-width: 440px; width: 100%;">
                <span
                    class="position-absolute d-flex align-items-center justify-content-center text-theme-muted"
                    style="
                        left: 14px;
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
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    class="form-control bg-surface-2 border-theme text-theme rounded-pill w-100"
                    placeholder="Tìm bình luận hoặc tên người dùng..."
                    style="
                        height: 40px;
                        min-height: 40px;
                        padding-left: 42px;
                        padding-right: 16px;
                        font-size: 13.5px;
                    "
                    autocomplete="off"
                    aria-label="Tìm bình luận hoặc tên người dùng"
                >
            </div>

            <button type="submit" class="btn btn-editorial-primary rounded-pill px-4 fw-semibold flex-shrink-0 d-inline-flex align-items-center justify-content-center" style="height: 40px; font-size: 13px;">
                Tìm kiếm
            </button>
        </form>
    </div>

    <div data-feed-status class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></div>

    <div data-feed-error role="alert" class="alert alert-danger mb-4 small" hidden>
        <span data-feed-error-message></span>
        <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
    </div>

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
