@extends('layouts.admin')

@section('admin_title', 'Quản lý Thẻ bài viết (Tags)')

@section('admin_content')
<div data-feed-shell
     data-feed-base-url="{{ route('admin.tags.index') }}"
     data-feed-target="#feed-results"
     class="position-relative">

    <!-- Header Action Bar & Search -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <form action="{{ route('admin.tags.index') }}" method="GET" class="flex-grow-1" style="max-width: 440px;" data-feed-filter-form>
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
                    placeholder="Tìm thẻ theo tên hoặc slug..."
                    class="form-control bg-surface-2 border-theme text-theme rounded-pill w-100"
                    style="
                        height: 40px;
                        min-height: 40px;
                        padding-left: 42px;
                        padding-right: 16px;
                        font-size: 13.5px;
                    "
                    autocomplete="off"
                    aria-label="Tìm thẻ theo tên hoặc slug"
                >
            </div>
        </form>

        <a href="{{ route('admin.tags.create') }}" class="btn btn-editorial-primary rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 align-self-start align-self-sm-auto shadow-xs" style="height: 40px; font-size: 13px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Thêm thẻ mới
        </a>
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

    @include('admin.tags.partials.table-results')
</div>
@endsection
