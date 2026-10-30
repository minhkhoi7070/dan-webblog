@extends('layouts.public')

@section('content')
    <h1 class="visually-hidden">CloudBlog — Khám phá tin tức & góc nhìn công nghệ</h1>

    <div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
        <div class="w-100 feed-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 overflow-hidden mb-4"
            data-feed-shell data-feed-base-url="{{ route('posts.index') }}" data-feed-home-url="{{ route('home') }}"
            @auth data-feed-following-url="{{ route('following.index') }}" @endauth>

            <!-- Feed Top Header (Editorial / Threads Style Header) -->
            <div class="sticky-top bg-theme-surface border-theme-bottom px-3 px-sm-4 feed-top-header">
                <div
                    class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-2 gap-sm-3 feed-top-header-inner">
                    <div class="d-flex align-items-center gap-2 gap-sm-4 feed-tabs-container" role="tablist"
                        aria-label="Bộ lọc bài viết" data-feed-tabs>
                        <a href="{{ route('home') }}" class="feed-tab active" role="tab" aria-selected="true"
                            tabindex="0" data-feed-type="for-you">
                            Dành cho bạn
                        </a>
                        @auth
                            <a href="{{ route('following.index') }}" class="feed-tab" role="tab" aria-selected="false"
                                data-feed-type="following">
                                Đang theo dõi
                            </a>
                        @endauth
                    </div>

                    <!-- Search Input -->
                    <div class="feed-search-section d-flex align-items-center justify-content-end">
                        <form action="{{ route('posts.index') }}" method="GET"
                            class="d-flex align-items-center m-0 w-100 justify-content-end" role="search">
                            @if ($currentCategorySlug)
                                <input type="hidden" name="category" value="{{ $currentCategorySlug }}">
                            @endif
                            <div class="position-relative d-flex align-items-center w-100 feed-search-wrapper">
                                <button type="submit"
                                    class="btn btn-link p-0 position-absolute top-0 bottom-0 start-0 h-100 border-0 text-theme-secondary d-flex align-items-center justify-content-center cursor-pointer feed-search-btn"
                                    aria-label="Tìm kiếm" title="Tìm kiếm">
                                    <svg class="feed-search-icon" width="15" height="15" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                <input type="search" id="feed-search-input" name="q" value="{{ $search }}"
                                    placeholder="Tìm kiếm bài viết..." autocomplete="off"
                                    class="form-control rounded-pill feed-search-input"
                                    aria-label="Tìm kiếm bài viết">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if (session('success') || session('status'))
                <div
                    class="alert alert-success m-3 mb-0 d-flex align-items-center gap-2 small py-2 px-3 border border-success-subtle">
                    <svg class="icon-16 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif

            <!-- Inline Post Composer Prompt (Substack Notes / Threads Style) -->
            @auth
                <div class="d-flex align-items-center gap-3 p-3 p-sm-4 border-theme-bottom bg-theme-surface" data-feed-for-you-composer>
                    <a href="{{ route('profile.show') }}" class="flex-shrink-0 text-decoration-none" aria-label="Hồ sơ của bạn">
                        <x-avatar :user="Auth::user()" size="sm" />
                    </a>
                    <div class="flex-grow-1 min-w-0">
                        @if (Auth::user()->isAuthor() || Auth::user()->isAdmin())
                            <a href="{{ route('posts.create') }}"
                                class="text-theme-secondary small text-decoration-none text-truncate d-block hover-accent">
                                Có gì mới trong thế giới công nghệ hôm nay?
                            </a>
                        @else
                            <span class="text-theme-secondary small text-truncate d-block opacity-75">
                                Khám phá bài viết và nâng cấp tài khoản để bắt đầu sáng tạo...
                            </span>
                        @endif
                    </div>
                    @if (Auth::user()->isAuthor() || Auth::user()->isAdmin())
                        <a href="{{ route('posts.create') }}"
                            class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 flex-shrink-0 d-none d-lg-inline-flex align-items-center"
                            aria-label="Tạo bài viết mới">
                            Viết bài
                        </a>
                    @elseif(Auth::user()->role === 'viewer')
                        <form method="POST" action="{{ route('author.become') }}" class="m-0 flex-shrink-0 d-none d-lg-block">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill px-3 py-1"
                                aria-label="Đăng ký trở thành tác giả">
                                Trở thành tác giả
                            </button>
                        </form>
                    @endif
                </div>
            @endauth

            <!-- Category Filter Horizontal Chips -->
            <nav class="px-3 px-sm-4 py-2.5 bg-theme-surface" aria-label="Bộ lọc chuyên mục"
                data-feed-categories-nav>
                <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1" role="navigation">
                    <!-- All Categories -->
                    <a href="{{ route('posts.index', array_filter(['q' => $search])) }}" data-feed-category=""
                        data-feed-category-chip
                        class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ empty($currentCategorySlug) ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                        @if (empty($currentCategorySlug)) aria-current="page" @endif>
                        Tất cả
                    </a>

                    <!-- Categories -->
                    @foreach ($categories as $category)
                        <a href="{{ route('posts.index', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                            data-feed-category="{{ $category->slug }}" data-feed-category-chip
                            class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ $currentCategorySlug === $category->slug ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                            @if ($currentCategorySlug === $category->slug) aria-current="page" @endif>
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </nav>

            <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
            <div id="feed-error" role="alert" class="alert alert-danger mx-3 mx-sm-4 my-2 small" hidden>
                <span data-feed-error-message></span>
                <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
            </div>

            <!-- Feed Results Container -->
            @include('posts.partials.feed-results')
        </div>
    </div>

    <!-- Skeleton Loading Template -->
    <template id="feed-skeleton-template">
        <div class="feed-skeleton-list" aria-hidden="true">
            @for ($i = 0; $i < 4; $i++)
                <div class="feed-skeleton-card">
                    <!-- Author Row -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="skeleton-shimmer rounded-circle" style="width: 32px; height: 32px; flex-shrink: 0;">
                        </div>
                        <div class="d-flex flex-column gap-1 flex-grow-1">
                            <div class="skeleton-shimmer" style="width: 120px; height: 13px;"></div>
                            <div class="skeleton-shimmer" style="width: 60px; height: 10px;"></div>
                        </div>
                    </div>
                    <!-- Title & Excerpt -->
                    <div class="mt-2 mb-3">
                        <div class="skeleton-shimmer mb-2" style="width: 75%; height: 18px;"></div>
                        <div class="skeleton-shimmer mb-2" style="width: 50%; height: 18px;"></div>
                        <div class="skeleton-shimmer mb-1.5" style="width: 90%; height: 13px;"></div>
                        <div class="skeleton-shimmer" style="width: 65%; height: 13px;"></div>
                    </div>
                    <!-- Category Badge -->
                    <div class="mb-3">
                        <div class="skeleton-shimmer rounded-pill" style="width: 80px; height: 22px;"></div>
                    </div>
                    <!-- Footer Actions -->
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
