@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs"
         style="border-radius: var(--radius-xl, 16px);"
         data-feed-shell
         data-feed-base-url="{{ route('favorites.index') }}">
        
        <!-- Top Context Bar -->
        <div class="p-3 px-sm-4 border-theme-bottom d-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="label-uppercase m-0 text-theme-muted" data-favorites-total="{{ $posts->total() }}">
                <span data-favorites-count class="interaction-count">{{ $posts->total() }}</span> bài viết
            </span>
        </div>

        <!-- Reading-Oriented Masthead -->
        <div class="p-4 p-sm-5 border-theme-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-surface-2 border border-theme text-warning flex-shrink-0" style="width: 44px; height: 44px;">
                    <svg style="width: 22px; height: 22px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold text-theme mb-1 tracking-tight">
                        Bài viết đã lưu
                    </h1>
                    <p class="small text-theme-secondary mb-0">
                        Danh sách lưu trữ cá nhân cho trải nghiệm đọc tập trung của bạn
                    </p>
                </div>
            </div>
        </div>

        <!-- Clear Saved State Indicator -->
        <div class="px-4 py-2.5 border-theme-bottom bg-surface-2 d-flex align-items-center justify-content-between">
            <span class="small text-theme-secondary d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                <svg style="width: 15px; height: 15px;" class="text-warning flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                Nhấn dấu trang để lưu hoặc bỏ lưu bài viết
            </span>
            <span class="small text-theme-muted d-none d-sm-inline" style="font-size: 11.5px;">
                Nhấn dấu trang để gỡ bỏ
            </span>
        </div>

        <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
        <div id="feed-error" role="alert" class="alert alert-danger mx-3 mx-sm-4 my-2 small" hidden>
            <span data-feed-error-message></span>
            <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
        </div>

        <!-- Feed Results Container -->
        @include('favorites.partials.feed-results')
    </div>
</div>

<!-- Skeleton Loading Template -->
<template id="feed-skeleton-template">
    <div class="feed-skeleton-list" aria-hidden="true">
        @for ($i = 0; $i < 4; $i++)
            <div class="feed-skeleton-card">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="skeleton-shimmer rounded-circle" style="width: 32px; height: 32px; flex-shrink: 0;"></div>
                    <div class="d-flex flex-column gap-1 flex-grow-1">
                        <div class="skeleton-shimmer" style="width: 120px; height: 13px;"></div>
                        <div class="skeleton-shimmer" style="width: 60px; height: 10px;"></div>
                    </div>
                </div>
                <div class="mt-2 mb-3">
                    <div class="skeleton-shimmer mb-2" style="width: 75%; height: 18px;"></div>
                    <div class="skeleton-shimmer mb-2" style="width: 50%; height: 18px;"></div>
                    <div class="skeleton-shimmer mb-1.5" style="width: 90%; height: 13px;"></div>
                    <div class="skeleton-shimmer" style="width: 65%; height: 13px;"></div>
                </div>
                <div class="mb-3">
                    <div class="skeleton-shimmer rounded-pill" style="width: 80px; height: 22px;"></div>
                </div>
                <div class="d-flex align-items-center gap-4 pt-2 border-theme-top">
                    <div class="skeleton-shimmer rounded" style="width: 45px; height: 16px;"></div>
                    <div class="skeleton-shimmer rounded" style="width: 45px; height: 16px;"></div>
                    <div class="skeleton-shimmer rounded ms-auto" style="width: 30px; height: 16px;"></div>
                </div>
            </div>
        @endfor
    </div>
</template>
@endsection
