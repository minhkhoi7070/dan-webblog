@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt & Quản lý Bài viết')

@section('admin_content')
<!-- Status Tabs (Professional Pill Segment Filter) -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-4 pb-1">
    <a
        href="{{ route('admin.posts.index') }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-secondary' }}"
        style="font-size: 12.5px;"
    >
        Tất cả <span class="opacity-75 ms-1">({{ number_format($statusCounts['all']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
        style="font-size: 12.5px;"
    >
        Chờ duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['pending']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'published']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'published' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
        style="font-size: 12.5px;"
    >
        Đã xuất bản <span class="opacity-75 ms-1">({{ number_format($statusCounts['published']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'draft' ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-muted' }}"
        style="font-size: 12.5px;"
    >
        Bản nháp <span class="opacity-75 ms-1">({{ number_format($statusCounts['draft']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'rejected']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
        style="font-size: 12.5px;"
    >
        Bị từ chối <span class="opacity-75 ms-1">({{ number_format($statusCounts['rejected']) }})</span>
    </a>
</div>

<!-- Search & Category Filters (High Density Toolbar) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2.5 align-items-center">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="col-12 col-md-6">
           <div class="position-relative w-100">
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
        name="search"
        value="{{ request('search') }}"
        class="form-control bg-surface-2 border-theme text-theme rounded-3 w-100"
        placeholder="Tìm theo tên hoặc email người dùng..."
        style="
            min-height: 46px;
            padding-left: 46px;
            padding-right: 16px;
        "
    >
</div>
        </div>

        <div class="col-12 col-sm-8 col-md-4">
            <select
                name="category_id"
                class="form-select form-select-sm form-control-editorial rounded-pill"
                style="font-size: 13px;"
            >
                <option value="">-- Tất cả chuyên mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($currentCategory == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-sm-4 col-md-2">
            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill w-100 fw-semibold" style="font-size: 12.5px;">
                Lọc kết quả
            </button>
        </div>
    </form>
</div>

{{-- Scoped styles: Admin Posts Table column layout --}}
<style>
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
.ap-post-title {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.ap-author-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 100%;
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

/* Tablet refinement (576px - 991px) */
@media (min-width: 576px) and (max-width: 991.98px) {
    .ap-table { min-width: 720px; }
    .ap-table .col-post    { width: 30%; }
    .ap-table .col-author  { width: 16%; }
    .ap-table .col-cat     { width: 11%; }
    .ap-table .col-status  { width: 11%; }
    .ap-table .col-interact{ width: 10%; }
    .ap-table .col-actions { width: 22%; }
}

/* Mobile card stacking (< 576px) */
@media (max-width: 575.98px) {
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
        padding: 0.875rem 1rem !important;
        border-bottom: 1px solid var(--color-border) !important;
    }
    .ap-table td {
        padding: 0.25rem 0 !important;
        border: none !important;
    }
    .ap-table td.col-actions {
        margin-top: 0.5rem;
        padding-top: 0.5rem !important;
        border-top: 1px dashed var(--color-border) !important;
        text-align: center;
    }
    .ap-table .ap-actions-wrap {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }
    .ap-table .ap-interact {
        flex-direction: row;
        gap: 0.75rem;
    }
}
</style>

<div data-feed-shell
     data-feed-base-url="{{ route('admin.posts.index') }}"
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

    @include('admin.posts.partials.table-results')
</div>
@endsection

