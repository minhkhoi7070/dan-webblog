@extends('layouts.public')

@section('content')
    <h1 class="visually-hidden">BlogMNM — Khám phá tin tức & góc nhìn công nghệ</h1>

    <div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
        <div
            class="w-100 feed-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 overflow-hidden mb-4">

            <!-- Feed Top Header (Editorial / Substack Style Header) -->
            <div class="sticky-top bg-theme-surface border-theme-bottom px-3 px-sm-4 feed-top-header">
                <div class="d-flex align-items-center justify-content-between gap-3 feed-top-header-inner">
                    <div class="d-flex align-items-center gap-3 gap-sm-4" role="tablist" aria-label="Bộ lọc bài viết">
                        <span class="feed-tab active" role="tab" aria-selected="true" tabindex="0">
                            Dành cho bạn
                        </span>
                        @auth
                            <a href="{{ route('activity.index') }}" class="feed-tab" role="tab" aria-selected="false">
                                Đang theo dõi
                            </a>
                        @endauth
                    </div>

                    <!-- Search Input -->
                    <div class="position-relative d-flex align-items-center flex-grow-1 flex-sm-grow-0 justify-content-end">
                        <form action="{{ route('posts.index') }}" method="GET"
                            class="d-flex align-items-center m-0 w-100 justify-content-end" role="search">
                            @if ($currentCategorySlug)
                                <input type="hidden" name="category" value="{{ $currentCategorySlug }}">
                            @endif
                            <div class="position-relative d-flex align-items-center w-100 feed-search-wrapper">
                                <input type="search" id="feed-search-input" name="q" value="{{ $search }}"
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
                <div class="d-flex align-items-center gap-3 p-3 p-sm-4 border-theme-bottom bg-theme-surface">
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
                            class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 flex-shrink-0"
                            aria-label="Tạo bài viết mới">
                            Viết bài
                        </a>
                    @elseif(Auth::user()->role === 'viewer')
                        <form method="POST" action="{{ route('author.become') }}" class="m-0 flex-shrink-0">
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
            <div class="px-3 px-sm-4 py-2.5 border-theme-bottom bg-theme-surface">
                <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1" role="navigation"
                    aria-label="Bộ lọc chuyên mục">
                    <!-- All Categories -->
                    <a href="{{ route('posts.index', array_filter(['q' => $search])) }}"
                        class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ empty($currentCategorySlug) ? 'btn-editorial-primary' : 'btn-outline-theme' }}">
                        Tất cả
                    </a>

                    <!-- Categories -->
                    @foreach ($categories as $category)
                        <a href="{{ route('posts.index', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                            class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ $currentCategorySlug === $category->slug ? 'btn-editorial-primary' : 'btn-outline-theme' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Active Filter Indicator Banner -->
                @if ($search || $currentCategorySlug || $currentTagSlug)
                    <div
                        class="px-3 px-sm-4 py-2 d-flex align-items-center justify-content-between gap-3 border-theme-bottom">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <span
                                class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-accent"
                                style="width: 28px; height: 28px; background: var(--color-accent-subtle, rgba(59, 130, 246, 0.08));"
                                aria-hidden="true">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </span>

                            <div class="d-flex align-items-center gap-1 flex-wrap min-w-0 small">
                                <span class="text-theme-secondary">Đang lọc</span>

                                @if ($search)
                                    <span class="fw-semibold text-theme">
                                        "{{ $search }}"
                                    </span>
                                @endif

                                @if ($currentCategorySlug)
                                    @if ($search)
                                        <span class="text-theme-muted">·</span>
                                    @endif
                                    <span class="fw-semibold text-theme">
                                        {{ $activeCategory->name ?? $currentCategorySlug }}
                                    </span>
                                @endif

                                @if ($currentTagSlug)
                                    @if ($search || $currentCategorySlug)
                                        <span class="text-theme-muted">·</span>
                                    @endif
                                    <span class="fw-semibold text-theme">
                                        #{{ $currentTagSlug }}
                                    </span>
                                @endif

                                <span class="text-theme-muted">
                                    · {{ $posts->total() }} kết quả
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('posts.index') }}"
                            class="flex-shrink-0 small text-theme-muted text-decoration-none hover-accent"
                            aria-label="Xóa bộ lọc">
                            Xóa lọc
                        </a>
                    </div>
                @endif

                <!-- Single Column Posts Feed -->
                <div class="d-flex flex-column">
                    @forelse($posts as $post)
                        <x-post-card :post="$post" />
                    @empty
                        <x-empty-state title="Chưa có bài viết nào phù hợp"
                            description="Thử tìm kiếm với từ khóa khác hoặc khám phá các chuyên mục được quan tâm."
                            :action-url="route('posts.index')" action-label="Quay lại tất cả bài viết" />
                    @endforelse
                </div>

                <!-- Pagination Section -->
                @if ($posts->hasPages())
                    <div class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endsection
