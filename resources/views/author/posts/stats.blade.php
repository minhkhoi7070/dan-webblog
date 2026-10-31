@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;"
     data-feed-shell
     data-feed-base-url="{{ route('posts.stats') }}">
    <!-- Top Dashboard Header (Ghost + Notion style) -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 small text-theme-muted label-uppercase mb-0" style="font-size: 11px;">
                <span>Tác giả</span>
                <span>&rsaquo;</span>
                <span class="text-accent">Bảng điều khiển</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0 w-100 w-md-auto justify-content-start justify-content-md-end">
            <a href="{{ route('posts.create') }}" data-app-nav class="btn btn-editorial-primary rounded-pill px-3.5 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-2 dashboard-create-btn">
                <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Soạn bài viết mới</span>
            </a>
        </div>
    </div>

    <!-- Statistics Grid (All 8 Metrics - Notion & Ghost Style) -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Posts -->
        <div class="col-6 col-sm-3 col-xxl">
            <a href="{{ route('posts.stats') }}" class="dashboard-stat-card h-100 {{ empty($currentStatus) ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-3xs">Tổng bài viết</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['total']) }}</p>
                <span class="dashboard-stat-desc">Tất cả bài</span>
            </a>
        </div>

        <!-- 2. Draft -->
        <div class="col-6 col-sm-3 col-xxl">
            <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'draft' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bản nháp</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['draft']) }}</p>
                <span class="dashboard-stat-desc">Đang soạn</span>
            </a>
        </div>

        <!-- 3. Pending -->
        <div class="col-6 col-sm-3 col-xxl">
            <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="dashboard-stat-card dashboard-stat-pending h-100 {{ $currentStatus === 'pending' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-warning text-3xs">Chờ duyệt</span>
                <p class="h4 fw-bold text-warning m-0">{{ number_format($stats['pending']) }}</p>
                <span class="dashboard-stat-desc">Đang xét duyệt</span>
            </a>
        </div>

        <!-- 4. Published -->
        <div class="col-6 col-sm-3 col-xxl">
            <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'published' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-success text-3xs">Xuất bản</span>
                <p class="h4 fw-bold m-0 text-success">{{ number_format($stats['published']) }}</p>
                <span class="dashboard-stat-desc">Công khai</span>
            </a>
        </div>

        <!-- 5. Rejected -->
        <div class="col-6 col-sm-3 col-xxl">
            <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'rejected' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-danger text-3xs">Bị từ chối</span>
                <p class="h4 fw-bold text-danger m-0">{{ number_format($stats['rejected']) }}</p>
                <span class="dashboard-stat-desc">Cần chỉnh sửa</span>
            </a>
        </div>

        <!-- 6. Views -->
        <div class="col-6 col-sm-3 col-xxl">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs">Lượt xem</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-accent-text);">{{ number_format($stats['views']) }}</p>
                <span class="dashboard-stat-desc">Tổng tương tác</span>
            </div>
        </div>

        <!-- 7. Likes -->
        <div class="col-6 col-sm-3 col-xxl">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs" style="color: var(--color-like);">Lượt thích</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-like);">{{ number_format($stats['likes']) }}</p>
                <span class="dashboard-stat-desc">Từ độc giả</span>
            </div>
        </div>

        <!-- 8. Comments -->
        <div class="col-6 col-sm-3 col-xxl">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bình luận</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['comments']) }}</p>
                <span class="dashboard-stat-desc">Thảo luận</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search Controls (Notion style Database toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <div class="author-post-toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Filter Tabs -->
            <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-0.5" data-feed-status-tabs>
                <a href="{{ route('posts.stats', array_filter(['q' => $search])) }}"
                   data-feed-tab=""
                   data-active-class="btn-editorial-primary text-white"
                   data-inactive-class="btn-outline-theme text-theme-secondary"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ empty($currentStatus) ? 'btn-editorial-primary text-white' : 'btn-outline-theme text-theme-secondary' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Tất cả ({{ $stats['total'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'draft', 'q' => $search])) }}"
                   data-feed-tab="draft"
                   data-active-class="btn-editorial-primary text-white"
                   data-inactive-class="btn-outline-theme text-theme-muted"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'draft' ? 'btn-editorial-primary text-white' : 'btn-outline-theme text-theme-muted' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Bản nháp ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'pending', 'q' => $search])) }}"
                   data-feed-tab="pending"
                   data-active-class="btn-warning text-dark fw-bold"
                   data-inactive-class="btn-outline-theme text-warning"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Chờ duyệt ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'published', 'q' => $search])) }}"
                   data-feed-tab="published"
                   data-active-class="btn-editorial-primary text-white"
                   data-inactive-class="btn-outline-theme text-theme-secondary"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'published' ? 'btn-editorial-primary text-white' : 'btn-outline-theme text-theme-secondary' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Đã xuất bản ({{ $stats['published'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'rejected', 'q' => $search])) }}"
                   data-feed-tab="rejected"
                   data-active-class="btn-danger text-white fw-bold"
                   data-inactive-class="btn-outline-theme text-danger"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Bị từ chối ({{ $stats['rejected'] }})
                </a>
            </div>

            <!-- Search Form with vertically centered icon & clear button -->
            <form action="{{ route('posts.stats') }}" method="GET" class="position-relative m-0 w-100" data-feed-filter-form>
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <div class="position-relative d-flex align-items-center w-100">
                    <!-- Magnifying glass icon: centered vertically with generous padding-left -->
                    <span class="dashboard-search-icon position-absolute d-flex align-items-center pointer-events-none" style="left: 13px; top: 50%; transform: translateY(-50%); z-index: 3;" aria-hidden="true">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Tìm theo tiêu đề bài viết..."
                        class="form-control form-control-sm form-control-editorial dashboard-search-input rounded-pill"
                        autocomplete="off"
                        aria-label="Tìm theo tiêu đề bài viết"
                    >

                    @if($search)
                        <!-- Clear Search Button -->
                        <a href="{{ route('posts.stats', $currentStatus ? ['status' => $currentStatus] : []) }}"
                           class="position-absolute end-0 top-50 translate-middle-y pe-2.5 d-flex align-items-center text-theme-muted hover-accent text-decoration-none"
                           style="z-index: 3;"
                           title="Xóa từ khóa tìm kiếm"
                           aria-label="Xóa từ khóa tìm kiếm">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
    <div id="feed-error" role="alert" class="alert alert-danger mb-4 small" hidden>
        <span data-feed-error-message></span>
        <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
    </div>

    <!-- Posts Database Table & Pagination Container -->
    @include('author.posts.partials.stats-results')
</div>
@endsection
