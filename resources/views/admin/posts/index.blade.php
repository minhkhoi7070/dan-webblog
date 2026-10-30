@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt & Quản lý Bài viết')

@section('admin_content')
<div data-feed-shell
     data-feed-base-url="{{ route('admin.posts.index') }}"
     data-feed-target="#feed-results"
     class="position-relative">

    <!-- Status Tabs (Horizontal Scrollable Chips on Mobile, Pill Segment Filter) -->
    <div class="d-flex flex-nowrap overflow-x-auto align-items-center gap-1.5 mb-3 py-1 admin-posts-status-tabs" data-feed-status-tabs tabindex="0" role="region" aria-label="Các trạng thái bài viết, có thể cuộn ngang">
        <a
            href="{{ route('admin.posts.index', array_filter(['q' => $search, 'category_id' => $currentCategory])) }}"
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
            href="{{ route('admin.posts.index', array_filter(['status' => 'pending', 'q' => $search, 'category_id' => $currentCategory])) }}"
            data-feed-tab="pending"
            data-active-class="btn-warning text-dark fw-bold"
            data-inactive-class="btn-outline-theme text-warning"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'pending') aria-current="page" @endif
        >
            Chờ duyệt <span class="opacity-75 ms-1" data-feed-count="pending">({{ number_format($statusCounts['pending']) }})</span>
        </a>
        <a
            href="{{ route('admin.posts.index', array_filter(['status' => 'published', 'q' => $search, 'category_id' => $currentCategory])) }}"
            data-feed-tab="published"
            data-active-class="btn-success text-white fw-bold"
            data-inactive-class="btn-outline-theme text-success"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'published' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'published') aria-current="page" @endif
        >
            Đã xuất bản <span class="opacity-75 ms-1" data-feed-count="published">({{ number_format($statusCounts['published']) }})</span>
        </a>
        <a
            href="{{ route('admin.posts.index', array_filter(['status' => 'draft', 'q' => $search, 'category_id' => $currentCategory])) }}"
            data-feed-tab="draft"
            data-active-class="btn-editorial-primary text-white"
            data-inactive-class="btn-outline-theme text-theme-muted"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'draft' ? 'btn-editorial-primary text-white' : 'btn-outline-theme text-theme-muted' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'draft') aria-current="page" @endif
        >
            Bản nháp <span class="opacity-75 ms-1" data-feed-count="draft">({{ number_format($statusCounts['draft']) }})</span>
        </a>
        <a
            href="{{ route('admin.posts.index', array_filter(['status' => 'rejected', 'q' => $search, 'category_id' => $currentCategory])) }}"
            data-feed-tab="rejected"
            data-active-class="btn-danger text-white fw-bold"
            data-inactive-class="btn-outline-theme text-danger"
            class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
            style="font-size: 12.5px;"
            @if($currentStatus === 'rejected') aria-current="page" @endif
        >
            Bị từ chối <span class="opacity-75 ms-1" data-feed-count="rejected">({{ number_format($statusCounts['rejected']) }})</span>
        </a>
    </div>

    <!-- Search & Category Filters (Unified Height Toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4 admin-posts-filter-card">
        <form action="{{ route('admin.posts.index') }}" method="GET" class="admin-posts-filter-form" data-feed-filter-form>
            <input type="hidden" name="status" value="{{ $currentStatus }}">

            <div class="admin-posts-filter-search">
                <div class="position-relative w-100">
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
                        placeholder="Tìm theo tiêu đề bài viết..."
                        style="
                            height: 40px;
                            min-height: 40px;
                            padding-left: 42px;
                            padding-right: 16px;
                            font-size: 13.5px;
                        "
                        autocomplete="off"
                        aria-label="Tìm kiếm bài viết"
                    >
                </div>
            </div>

            <div class="admin-posts-filter-category">
                <select
                    name="category_id"
                    class="form-select form-control-editorial rounded-pill w-100"
                    style="height: 40px; font-size: 13px; padding-right: 2.75rem; background-position: right 0.9rem center;"
                    aria-label="Chọn chuyên mục"
                >
                    <option value="">-- Tất cả chuyên mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($currentCategory == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-posts-filter-submit">
                <button type="submit" class="btn btn-editorial-primary rounded-pill w-100 fw-semibold d-inline-flex align-items-center justify-content-center" style="height: 40px; font-size: 13px; white-space: nowrap;">
                    Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    {{-- Scoped styles: Admin Posts Table column layout --}}
    <style>
    .admin-posts-status-tabs {
        min-width: 0;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        overscroll-behavior-x: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: var(--color-border-medium) transparent;
    }
    .admin-posts-status-tabs > a {
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .admin-posts-status-tabs::-webkit-scrollbar {
        height: 5px;
    }
    .admin-posts-status-tabs::-webkit-scrollbar-track {
        background: transparent;
    }
    .admin-posts-status-tabs::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: var(--color-border-medium);
    }
    .admin-posts-status-tabs:focus-visible {
        outline: 2px solid var(--color-interactive);
        outline-offset: 2px;
    }

    .admin-posts-filter-card {
        min-width: 0;
        max-width: 100%;
        container-name: admin-posts-filter;
        container-type: inline-size;
    }
    .admin-posts-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 6fr) minmax(190px, 4fr) minmax(128px, 2fr);
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .admin-posts-filter-form > div {
        width: 100%;
        min-width: 0;
    }
    .admin-posts-filter-search .position-relative,
    .admin-posts-filter-search input,
    .admin-posts-filter-category select,
    .admin-posts-filter-submit button {
        min-width: 0;
        max-width: 100%;
    }
    .admin-posts-filter-category select {
        padding-right: 2.75rem;
        background-position: right 0.9rem center;
    }
    .admin-posts-filter-submit button {
        white-space: nowrap;
    }
    @container admin-posts-filter (max-width: 760px) {
        .admin-posts-filter-form {
            grid-template-columns: minmax(0, 1fr) minmax(128px, 0.55fr);
        }
        .admin-posts-filter-search {
            grid-column: 1 / -1;
        }
        .admin-posts-filter-category {
            grid-column: 1;
        }
        .admin-posts-filter-submit {
            grid-column: 2;
        }
    }
    @container admin-posts-filter (max-width: 440px) {
        .admin-posts-filter-form {
            grid-template-columns: minmax(0, 1fr);
        }
        .admin-posts-filter-category,
        .admin-posts-filter-submit {
            grid-column: 1;
        }
    }

    .admin-posts-table-card {
        min-width: 0;
        max-width: 100%;
        container-name: admin-posts-table;
        container-type: inline-size;
    }
    .admin-posts-table-scroll-tools {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-width: 0;
        padding: 0.75rem 1rem 0.5rem;
    }
    .admin-posts-table-card.has-horizontal-overflow .admin-posts-table-scroll-tools {
        display: flex;
    }
    .admin-posts-table-scroll-hint {
        min-width: 0;
    }
    .admin-posts-table-scroll-controls {
        display: inline-flex;
        flex: 0 0 auto;
        gap: 6px;
    }
    .admin-posts-table-scroll-controls button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        font-size: 1.25rem;
        line-height: 1;
    }
    .admin-posts-table-scroll-controls button:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
    .admin-posts-table-scroll {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overscroll-behavior-x: contain;
    }
    .admin-posts-table-scroll:focus-visible {
        outline: 2px solid var(--color-interactive);
        outline-offset: -2px;
    }

    .ap-table {
        width: 100%;
        table-layout: fixed;
    }
    .ap-table th,
    .ap-table td {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        vertical-align: middle;
    }
    .ap-table th {
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ap-table th.col-post {
        text-align: left;
    }

    /* Desktop column proportions (>= 992px) */
    .ap-table .col-post    { width: 31%; }
    .ap-table .col-author  { width: 16%; }
    .ap-table .col-cat     { width: 11%; }
    .ap-table .col-status  { width: 11%; }
    .ap-table .col-interact{ width: 10%; }
    .ap-table .col-actions { width: 21%; }

    /* Column text alignments */
    .ap-table td.col-post     { text-align: left; }
    .ap-table th.col-author,
    .ap-table td.col-author   { text-align: left; }
    .ap-table td.col-cat      { text-align: center; }
    .ap-table td.col-status   { text-align: center; }
    .ap-table td.col-interact { text-align: center; }
    .ap-table td.col-actions  { text-align: center; }

    /* Truncation & text safety */
    .ap-post-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        width: 100%;
        text-align: left;
    }
    .ap-thumb {
        position: relative;
        display: flex;
        flex: 0 0 52px;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        overflow: hidden;
        border-radius: 0.5rem;
    }
    .ap-thumb img,
    .ap-thumb__placeholder {
        width: 52px;
        height: 52px;
        border-radius: 0.5rem;
    }
    .ap-thumb img {
        display: block;
        flex: 0 0 52px;
        object-fit: cover;
    }
    .ap-thumb__placeholder {
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .ap-thumb.is-fallback img {
        display: none;
    }
    .ap-thumb.is-fallback .ap-thumb__placeholder {
        display: flex;
    }
    .ap-post-text {
        flex: 1 1 auto;
        min-width: 0;
    }
    .ap-post-title {
        display: -webkit-box;
        max-width: 100%;
        overflow: hidden;
        white-space: normal;
        overflow-wrap: anywhere;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-height: 1.4;
    }
    .ap-post-date {
        line-height: 1.35;
    }
    .ap-author-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
    }
    .ap-author-text {
        min-width: 0;
    }
    .ap-badge {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
        padding: 0.25rem 0.5rem;
    }

    /* Interactions – centered vertical stack */
    .ap-interact {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        line-height: 1.45;
    }
    .ap-interact span {
        font-size: 11px;
        white-space: nowrap;
    }

    /* Actions */
    .ap-actions-wrap {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .ap-actions-wrap .btn {
        padding: 0.2rem 0.5rem;
        font-size: 11px;
    }

    .admin-posts-pagination__nav {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px 24px;
        width: 100%;
        min-width: 0;
    }
    .admin-posts-pagination__summary {
        flex: 1 1 220px;
        min-width: 0;
    }
    .admin-posts-pagination__links {
        flex: 0 1 auto;
        max-width: 100%;
        margin-left: auto;
    }
    .admin-posts-pagination__links .pagination {
        flex-wrap: wrap;
        justify-content: flex-end;
        max-width: 100%;
        gap: 4px;
    }
    .admin-posts-pagination__links .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        text-align: center;
    }
    /* Tablet refinement (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .ap-table { min-width: 960px; }
        .ap-table .col-post    { width: 30%; }
        .ap-table .col-author  { width: 16%; }
        .ap-table .col-cat     { width: 11%; }
        .ap-table .col-status  { width: 11%; }
        .ap-table .col-interact{ width: 10%; }
        .ap-table .col-actions { width: 22%; }
    }

    /* Mobile card stacking (< 768px Mobile App Shell) */
    @media (max-width: 767.98px) {
        .ap-table,
        .ap-table tbody,
        .ap-table tr,
        .ap-table td {
            display: block !important;
            width: 100% !important;
        }
        .ap-table thead,
        .ap-table colgroup {
            display: none !important;
        }
        .ap-table tr {
            box-sizing: border-box;
            margin: 0;
            padding: 0.85rem 1rem !important;
            border: 1px solid var(--color-border) !important;
            border-radius: 0.875rem;
            background: var(--color-surface);
            box-shadow: var(--shadow-xs, 0 1px 2px rgb(0 0 0 / 0.04));
        }
        .ap-table td {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr);
            justify-items: start;
            gap: 0.35rem;
            padding: 0.65rem 0 !important;
            border: none !important;
        }
        .ap-table td[data-label]::before {
            content: attr(data-label);
            color: var(--color-text-muted);
            font-size: 0.675rem;
            font-weight: 600;
            letter-spacing: 0.045em;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .ap-table td.col-post {
            padding-top: 0 !important;
            padding-bottom: 0.85rem !important;
            border-bottom: 1px solid var(--color-border-subtle) !important;
        }
        .ap-table .ap-author-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 0;
        }
        .ap-table .ap-author-text {
            min-width: 0;
        }
        .ap-table td.col-cat,
        .ap-table td.col-status {
            justify-items: start;
        }
        .ap-table td.col-interact {
            justify-items: start;
        }
        .ap-table td.col-actions {
            margin-top: 0.3rem;
            padding-top: 0.85rem !important;
            border-top: 1px solid var(--color-border-subtle) !important;
            text-align: left;
        }
        .ap-table .ap-actions-wrap {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 6px;
        }
        .ap-table .ap-interact {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .admin-posts-pagination__nav {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .admin-posts-pagination__summary {
            flex: 0 1 auto;
            text-align: center;
        }
        .admin-posts-pagination__links {
            width: 100%;
            margin-left: 0;
        }
        .admin-posts-pagination__links .pagination {
            justify-content: center;
        }
        .admin-posts-pagination__links .page-link {
            min-width: 40px;
            height: 40px;
            border-radius: 999px;
        }
    }
    @media (min-width: 768px) {
        @container admin-posts-table (max-width: 1099.98px) {
            .ap-table {
                min-width: 1100px;
            }
        }
    }
    </style>

    <!-- Live Status Announcement -->
    <div data-feed-status class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></div>

    <!-- Error Box with Retry -->
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

    @include('admin.posts.partials.table-results')
</div>
@endsection

