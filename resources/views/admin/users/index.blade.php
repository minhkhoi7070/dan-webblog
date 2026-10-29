@extends('layouts.admin')

@section('admin_title', 'Quản lý Người dùng')

@section('admin_content')
<div data-feed-shell
     data-feed-base-url="{{ route('admin.users.index') }}"
     data-feed-target="#feed-results"
     class="position-relative">

    <!-- Filter & Search Toolbar (Unified Height Toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2.5 align-items-center" data-feed-filter-form>
            <!-- Search Input -->
            <div class="col-12 col-md-5">
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
                        placeholder="Tìm theo tên hoặc email người dùng..."
                        class="form-control bg-surface-2 border-theme text-theme rounded-pill w-100"
                        style="
                            height: 40px;
                            min-height: 40px;
                            padding-left: 42px;
                            padding-right: 16px;
                            font-size: 13.5px;
                        "
                        autocomplete="off"
                        aria-label="Tìm theo tên hoặc email người dùng"
                    >
                </div>
            </div>

            <!-- Role Filter -->
            <div class="col-12 col-sm-6 col-md-3">
                <select
                    name="role"
                    data-feed-auto-submit
                    class="form-select form-control-editorial rounded-pill w-100"
                    style="height: 40px; font-size: 13px;"
                    aria-label="Lọc theo vai trò"
                >
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="admin" @selected($roleFilter === 'admin')>Admin (Quản trị)</option>
                    <option value="author" @selected($roleFilter === 'author')>Author (Tác giả)</option>
                    <option value="viewer" @selected($roleFilter === 'viewer')>Viewer (Độc giả)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-12 col-sm-6 col-md-3">
                <select
                    name="status"
                    data-feed-auto-submit
                    class="form-select form-control-editorial rounded-pill w-100"
                    style="height: 40px; font-size: 13px;"
                    aria-label="Lọc theo trạng thái"
                >
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" @selected($statusFilter === 'active')>Đang hoạt động</option>
                    <option value="locked" @selected($statusFilter === 'locked')>Đã bị khóa</option>
                </select>
            </div>

            <div class="col-12 col-md-1">
                <button type="submit" class="btn btn-editorial-primary rounded-pill w-100 fw-semibold d-inline-flex align-items-center justify-content-center" style="height: 40px; font-size: 13px;">
                    Lọc
                </button>
            </div>
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

    @include('admin.users.partials.table-results')
</div>
@endsection
