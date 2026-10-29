@extends('layouts.public')

@section('title', config('app.name', 'CloudBlog') . ' — Bài viết từ tác giả đang theo dõi')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
    <div class="w-100 feed-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 overflow-hidden mb-4 shadow-xs"
         data-feed-shell
         data-feed-base-url="{{ route('following.index') }}"
         data-feed-home-url="{{ route('home') }}"
         data-feed-following-url="{{ route('following.index') }}">
        <!-- Feed Top Header (Editorial / Threads Style Header) -->
        <div class="sticky-top bg-theme-surface border-theme-bottom px-3 px-sm-4 feed-top-header">
            <div class="d-flex align-items-center justify-content-between gap-2 gap-sm-3 feed-top-header-inner">
                <div class="d-flex align-items-center gap-2 gap-sm-4" role="tablist" aria-label="Bộ lọc bài viết" data-feed-tabs>
                    <a href="{{ route('home') }}" class="feed-tab" role="tab" aria-selected="false" data-feed-type="for-you">
                        Dành cho bạn
                    </a>
                    <a href="{{ route('following.index') }}" class="feed-tab active" role="tab" aria-selected="true" tabindex="0" data-feed-type="following">
                        Đang theo dõi
                    </a>
                </div>

                <!-- Search Input -->
                <div class="position-relative d-flex align-items-center flex-grow-1 flex-sm-grow-0 justify-content-end">
                    <form action="{{ route('posts.index') }}" method="GET"
                        class="d-flex align-items-center m-0 w-100 justify-content-end" role="search">
                        <div class="position-relative d-flex align-items-center w-100 feed-search-wrapper">
                            <input type="search" id="feed-search-input" name="q" value=""
                                placeholder="Tìm kiếm bài viết..." autocomplete="off"
                                class="form-control form-control-sm rounded-pill ps-4 pe-3 py-1.5 text-xs"
                                aria-label="Tìm kiếm bài viết">
                            <button type="submit"
                                class="btn btn-link p-0 position-absolute border-0 text-theme-secondary d-flex align-items-center justify-content-center cursor-pointer feed-search-btn"
                                aria-label="Tìm kiếm" title="Tìm kiếm">
                                <svg class="icon-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Switcher Tabs: Bài viết vs Danh sách tác giả -->
        <div class="px-3 px-sm-4 py-2.5 border-theme-bottom bg-surface-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('following.index', ['tab' => 'feed']) }}"
                   data-feed-tab="feed"
                   class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $tab === 'feed' ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                   style="font-size: 13px;">
                    <svg width="14" height="14" class="me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    Bài viết mới
                </a>
                <a href="{{ route('following.index', ['tab' => 'authors']) }}"
                   data-feed-tab="authors"
                   class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $tab === 'authors' ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                   style="font-size: 13px;">
                    <svg width="14" height="14" class="me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Tác giả đang theo dõi ({{ $followingCount }})
                </a>
            </div>

            <span class="small text-theme-muted d-none d-sm-inline" style="font-size: 12px;">
                Cập nhật bài viết mới nhất từ những người bạn quan tâm
            </span>
        </div>

        <!-- Category Filter Horizontal Chips (Hidden on Following feed) -->
        <nav class="px-3 px-sm-4 py-2.5 border-theme-bottom bg-theme-surface d-none" aria-label="Bộ lọc chuyên mục" data-feed-categories-nav>
            <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1" role="navigation">
                <a href="{{ route('posts.index') }}"
                    data-feed-category=""
                    data-feed-category-chip
                    class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small btn-editorial-primary"
                    aria-current="page">
                    Tất cả
                </a>
                @if(isset($categories))
                    @foreach ($categories as $category)
                        <a href="{{ route('posts.index', ['category' => $category->slug]) }}"
                            data-feed-category="{{ $category->slug }}"
                            data-feed-category-chip
                            class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small btn-outline-theme">
                            {{ $category->name }}
                        </a>
                    @endforeach
                @endif
            </div>
        </nav>

        <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
        <div id="feed-error" role="alert" class="alert alert-danger mx-3 mx-sm-4 my-2 small" hidden>
            <span data-feed-error-message></span>
            <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
        </div>

        <!-- Feed Results Container -->
        @include('following.partials.feed-results')
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
