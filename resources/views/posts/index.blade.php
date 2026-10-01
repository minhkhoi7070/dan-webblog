@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme shadow-lg overflow-hidden" style="border-radius: 1.5rem;">
        
        <!-- Feed Top Header (Threads Style Header) -->
        <div class="sticky-top bg-theme-surface border-theme-bottom px-3 px-sm-4" style="z-index: 1020;">
            <div class="d-flex align-items-center justify-content-between" style="height: 56px;">
                <div class="d-flex align-items-center gap-4">
                    <span class="fw-bold small text-theme border-bottom border-2 border-white pb-3 pt-3 user-select-none">
                        Dành cho bạn
                    </span>
                    @auth
                        <a href="{{ route('activity.index') }}" class="small text-theme-secondary text-decoration-none pb-3 pt-3 user-select-none">
                            Đang theo dõi
                        </a>
                    @endauth
                </div>

                <!-- Search Input -->
                <div class="position-relative d-flex align-items-center">
                    <form action="{{ route('posts.index') }}" method="GET" class="d-flex align-items-center m-0">
                        @if($currentCategorySlug)
                            <input type="hidden" name="category" value="{{ $currentCategorySlug }}">
                        @endif
                        <div class="position-relative d-flex align-items-center">
                            <input type="text"
                                   id="feed-search-input"
                                   name="q"
                                   value="{{ $search }}"
                                   placeholder="Tìm kiếm bài viết..."
                                   class="form-control form-control-sm rounded-pill ps-4 pe-3 py-1 small"
                                   style="width: 170px; font-size: 12px;">
                            <svg style="width: 14px; height: 14px; position: absolute; left: 10px; pointer-events: none;" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success') || session('status'))
            <div class="alert alert-success m-3 mb-0 d-flex align-items-center gap-2 small py-2 px-3">
                <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') ?? session('status') }}</span>
            </div>
        @endif

        <!-- Inline Post Composer Prompt -->
        @auth
            <div class="d-flex align-items-center gap-3 p-3 p-sm-4 border-theme-bottom bg-theme-surface">
                <a href="{{ route('profile.show') }}" class="flex-shrink-0 text-decoration-none">
                    <x-avatar :user="Auth::user()" size="md" />
                </a>
                <div class="flex-grow-1 min-w-0">
                    @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                        <a href="{{ route('posts.create') }}" class="text-theme-secondary small text-decoration-none text-truncate d-block">
                            Có gì mới trong thế giới công nghệ hôm nay?
                        </a>
                    @else
                        <span class="text-theme-secondary small text-truncate d-block opacity-75">
                            Khám phá bài viết và nâng cấp tài khoản để bắt đầu sáng tạo...
                        </span>
                    @endif
                </div>
                @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                    <a href="{{ route('posts.create') }}"
                       class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 flex-shrink-0">
                        Viết bài
                    </a>
                @elseif(Auth::user()->role === 'viewer')
                    <form method="POST" action="{{ route('author.become') }}" class="m-0 flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-primary rounded-pill px-3 py-1">
                            Trở thành tác giả
                        </button>
                    </form>
                @endif
            </div>
        @endauth

        <!-- Category Filter Horizontal Chips -->
        <div class="px-3 px-sm-4 py-2 border-theme-bottom bg-theme-surface">
            <div class="d-flex align-items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <!-- All Categories -->
                <a href="{{ route('posts.index', array_filter(['q' => $search])) }}"
                   class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ empty($currentCategorySlug) ? 'btn-primary' : 'btn-outline-theme' }}">
                    Tất cả
                </a>

                <!-- Categories -->
                @foreach($categories as $category)
                    <a href="{{ route('posts.index', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                       class="btn btn-sm rounded-pill flex-shrink-0 py-1 px-3 small {{ $currentCategorySlug === $category->slug ? 'btn-primary' : 'btn-outline-theme' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Active Filter Indicator Banner -->
        @if($search || $currentCategorySlug || $currentTagSlug)
            <div class="px-3 px-sm-4 py-2 bg-theme-surface-hover border-theme-bottom d-flex align-items-center justify-content-between gap-3 small">
                <div class="d-flex align-items-center gap-2 text-theme text-truncate">
                    <svg style="width: 16px; height: 16px; color: #818cf8; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="text-truncate">
                        Đang lọc:
                        @if($search) từ khóa <strong>"{{ $search }}"</strong> @endif
                        @if($currentCategorySlug) @if($search) &bull; @endif <strong>{{ $activeCategory->name ?? $currentCategorySlug }}</strong> @endif
                        @if($currentTagSlug) @if($search || $currentCategorySlug) &bull; @endif thẻ <strong>#{{ $currentTagSlug }}</strong> @endif
                        <span class="text-theme-secondary ms-1">({{ $posts->total() }} kết quả)</span>
                    </span>
                </div>
                <a href="{{ route('posts.index') }}" class="text-theme-secondary text-decoration-underline flex-shrink-0 small">
                    Xóa lọc
                </a>
            </div>
        @endif

        <!-- Single Column Posts Feed -->
        <div class="d-flex flex-column">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <x-empty-state
                    title="Chưa có bài viết nào phù hợp"
                    description="Thử tìm kiếm với từ khóa khác hoặc khám phá các chuyên mục được quan tâm."
                    :action-url="route('posts.index')"
                    action-label="Quay lại tất cả bài viết"
                />
            @endforelse
        </div>

        <!-- Pagination Section -->
        @if($posts->hasPages())
            <div class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
