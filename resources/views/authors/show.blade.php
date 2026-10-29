@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs"
         style="border-radius: var(--radius-xl, 16px);"
         data-feed-shell
         data-feed-base-url="{{ route('authors.show', $author) }}">
        
        <!-- Top Back Bar (Desktop only; Mobile has top app header) -->
        <div class="p-3 px-sm-4 border-theme-bottom d-none d-md-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="label-uppercase m-0 text-theme-muted">Trang tác giả</span>
        </div>

        <!-- Editorial Author Header -->
        <div class="p-4 p-sm-5 border-theme-bottom">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="flex-grow-1 min-w-0">
                    <h1 class="h3 fw-bold text-theme tracking-tight text-truncate mb-1">
                        {{ $author->name }}
                    </h1>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-theme-secondary fw-medium">
                            {{ '@' . ($author->username ?? strtolower(str_replace(' ', '', $author->name))) }}
                        </span>
                        <x-badge variant="status-draft" size="xs" class="text-capitalize">
                            {{ $author->role }}
                        </x-badge>
                    </div>
                </div>

                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <x-avatar :user="$author" size="2xl" class="border border-theme shadow-xs" />
                </div>
            </div>

            <!-- Bio -->
            <div class="mt-3.5">
                <p class="text-theme small lh-relaxed mb-0" style="max-width: 540px; font-size: 14.5px;">
                    {{ $author->bio ?: 'Tác giả chưa cập nhật tiểu sử cá nhân trên nền tảng CloudBlog.' }}
                </p>
            </div>

            <!-- Metrics / Counters -->
            <div class="mt-4 d-flex align-items-center gap-4 small text-theme-secondary">
                <div class="d-flex align-items-baseline gap-1.5">
                    <span class="fw-bold text-theme fs-6">{{ $author->posts_count }}</span>
                    <span class="opacity-75" style="font-size: 13px;">bài viết</span>
                </div>
                <div class="d-flex align-items-baseline gap-1.5">
                    <span id="follower-count" data-follower-count data-author-id="{{ $author->id }}" class="fw-bold text-theme fs-6 interaction-count">{{ $author->followers_count }}</span>
                    <span class="opacity-75" style="font-size: 13px;">người theo dõi</span>
                </div>
            </div>

            <!-- Follow Action -->
            <div class="mt-4">
                <x-follow-button :author="$author" size="md" />
            </div>
        </div>

        <!-- Author Feed Section Header & Search -->
        <div class="px-4 py-3 border-theme-bottom bg-surface-2 d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-3">
            <div class="label-uppercase m-0">
                Bài viết đã đăng <span class="text-theme-secondary fw-normal">({{ $posts->total() }})</span>
            </div>

            <!-- Search -->
            <form action="{{ route('authors.show', $author) }}" method="GET" class="position-relative m-0">
                <input type="search"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Tìm trong bài tác giả..."
                       class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1 small"
                       style="max-width: 220px; font-size: 12px;">
                <svg style="width: 13px; height: 13px; position: absolute; left: 10px; top: 9px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>

        <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
        <div id="feed-error" role="alert" class="alert alert-danger mx-3 mx-sm-4 my-2 small" hidden>
            <span data-feed-error-message></span>
            <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
        </div>

        <!-- Feed Results Container -->
        @include('authors.partials.feed-results')
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
