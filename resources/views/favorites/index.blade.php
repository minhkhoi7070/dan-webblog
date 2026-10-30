@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
    <div class="w-100 feed-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 overflow-hidden mb-4"
         data-feed-shell
         data-feed-base-url="{{ route('favorites.index') }}">
        
        <!-- Minimalist Threads-Style Header (Desktop / Tablet only; Mobile uses mobile-header) -->
        <div class="sticky-md-top bg-theme-surface border-theme-bottom px-3 px-sm-4 py-3 d-none d-md-flex align-items-center justify-content-between favorites-header" style="z-index: 1020; min-height: 58px;">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <a href="{{ route('posts.index') }}"
                   class="btn-icon-threads text-theme-muted hover-accent text-decoration-none flex-shrink-0 d-none d-md-inline-flex align-items-center justify-content-center"
                   style="width: 32px; height: 32px; border-radius: 50%;"
                   title="Quay lại bảng tin"
                   aria-label="Quay lại bảng tin"
                   data-app-nav>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <svg style="width: 20px; height: 20px;" class="text-warning flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <h1 class="fw-bold text-theme mb-0 text-truncate favorites-title" style="font-size: 20px; line-height: 1.25; letter-spacing: -0.01em;">
                        Bài viết đã lưu
                    </h1>
                </div>
            </div>

            <div class="text-theme-muted small fw-medium flex-shrink-0 ms-2 favorites-counter-badge" data-favorites-total="{{ $posts->total() }}">
                <span data-favorites-count class="interaction-count fw-bold text-theme">{{ $posts->total() }}</span> bài viết
            </div>
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
