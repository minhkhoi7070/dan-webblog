@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme shadow-lg overflow-hidden" style="border-radius: 1.5rem;">
        
        <!-- Top Back Bar -->
        <div class="p-3 border-theme-bottom d-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small fw-semibold text-theme-secondary text-decoration-none d-inline-flex align-items-center gap-2">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="small text-theme-secondary">Trang tác giả</span>
        </div>

        <!-- Social Profile Header -->
        <div class="p-4 p-sm-5 border-theme-bottom bg-theme-surface">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="min-w-0">
                    <h1 class="h3 fw-bold text-theme tracking-tight text-truncate mb-1">
                        {{ $author->name }}
                    </h1>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-theme-secondary">
                            {{ '@' . ($author->username ?? strtolower(str_replace(' ', '', $author->name))) }}
                        </span>
                        <x-badge variant="status-draft" size="xs" class="text-capitalize">
                            {{ $author->role }}
                        </x-badge>
                    </div>
                </div>

                <!-- Avatar -->
                <x-avatar :user="$author" size="2xl" />
            </div>

            <!-- Bio -->
            <p class="mt-3 small text-theme-secondary lh-base mb-0" style="max-width: 520px;">
                {{ $author->bio ?: 'Tác giả chưa cập nhật tiểu sử cá nhân trên nền tảng BlogMNM.' }}
            </p>

            <!-- Metrics / Counters -->
            <div class="mt-4 d-flex align-items-center gap-4 small text-theme-secondary">
                <div>
                    <span class="fw-bold text-theme fs-6">{{ $author->posts_count }}</span>
                    <span class="ms-1 opacity-75">bài viết</span>
                </div>
                <div>
                    <span id="follower-count" class="fw-bold text-theme fs-6">{{ $author->followers_count }}</span>
                    <span class="ms-1 opacity-75">người theo dõi</span>
                </div>
            </div>

            <!-- Follow Action -->
            <div class="mt-4">
                <x-follow-button :author="$author" size="md" />
            </div>
        </div>

        <!-- Author Feed Section Header & Search -->
        <div class="px-4 py-3 border-theme-bottom bg-theme-surface-hover d-flex flex-column flex-sm-row sm-align-items-center justify-content-between gap-2">
            <div class="fw-bold small text-theme">
                Bài viết đã đăng <span class="text-theme-secondary fw-normal">({{ $posts->total() }})</span>
            </div>

            <!-- Search -->
            <form action="{{ route('authors.show', $author) }}" method="GET" class="position-relative m-0">
                <input type="search"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Tìm trong bài của tác giả..."
                       class="form-control form-control-sm rounded-pill ps-4 pe-3 py-1 small"
                       style="max-width: 240px; font-size: 12px;">
                <svg style="width: 14px; height: 14px; position: absolute; left: 10px; top: 8px; pointer-events: none;" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>

        <!-- Posts Feed -->
        <div class="d-flex flex-column">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <x-empty-state
                    title="Chưa có bài viết phù hợp"
                    description="Tác giả chưa đăng bài viết nào hoặc không có bài viết khớp với từ khóa tìm kiếm."
                    :action-url="route('authors.show', $author)"
                    action-label="Xem tất cả bài viết của tác giả"
                />
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
