@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
<<<<<<< HEAD
    <div class="w-100 card border-0 border-sm border-theme shadow-lg p-3 p-sm-5" style="max-width: 760px; border-radius: 1.5rem;">
=======
    <div class="w-100 card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs" style="max-width: 880px; border-radius: var(--radius-xl, 16px);">
>>>>>>> origin/main
        
        <!-- Top Context Bar -->
        <div class="p-3 px-sm-4 border-theme-bottom d-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="label-uppercase m-0 text-theme-muted" style="letter-spacing: 0.06em;">
                Nhật ký hoạt động
            </span>
        </div>

        <!-- Header -->
<<<<<<< HEAD
        <div class="mb-4 pb-3 border-theme-bottom">
            <h1 class="h4 fw-bold text-theme tracking-tight mb-1">
                Nhật ký hoạt động của bạn
            </h1>
            <p class="small text-theme-secondary mb-0">
                Tổng hợp lịch sử tương tác, bài viết bạn đã thích, bình luận và tác giả đang theo dõi.
            </p>
        </div>

        <!-- Quick Stats Grid -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-6 col-sm-3">
                <x-stat-card
                    title="Bình luận"
                    :value="number_format($stats['comments_count'])"
                    color="default"
                />
            </div>
            <div class="col-6 col-sm-3">
                <x-stat-card
                    title="Đã thích"
                    :value="number_format($stats['likes_count'])"
                    color="rose"
                />
            </div>
            <div class="col-6 col-sm-3">
                <x-stat-card
                    title="Đã lưu"
                    :value="number_format($stats['favorites_count'])"
                    color="amber"
                />
            </div>
            <div class="col-6 col-sm-3">
                <x-stat-card
                    title="Đang theo dõi"
                    :value="number_format($stats['following_count'])"
                    color="indigo"
                />
            </div>
        </div>

        <div class="row g-4">
            <!-- Section 1: Recent Comments -->
            <div class="col-12 col-md-6">
                <div class="card p-3 p-sm-4 border-theme bg-theme-surface-hover h-100 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between pb-2 border-theme-bottom mb-3">
                        <h2 class="h6 fw-bold text-theme mb-0">Bình luận gần đây</h2>
                        <span class="small text-theme-secondary" style="font-size: 11px;">10 mới nhất</span>
                    </div>

                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        @forelse($comments as $comment)
                            <div class="card p-2.5 border-theme bg-theme-surface small">
                                <div class="d-flex align-items-center justify-content-between text-theme-secondary mb-1" style="font-size: 11px;">
                                    <a href="{{ route('posts.show', $comment->post->slug) }}" class="fw-semibold text-truncate text-decoration-none" style="color: #818cf8; max-width: 180px;">
                                        {{ Str::limit($comment->post->title, 32) }}
                                    </a>
                                    <span class="flex-shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-theme line-clamp-2 mb-0 lh-sm">
                                    {{ $comment->body }}
                                </p>
                            </div>
                        @empty
                            <p class="small text-theme-secondary text-center py-4 mb-0">Bạn chưa gửi bình luận nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Section 2: Liked Posts -->
            <div class="col-12 col-md-6">
                <div class="card p-3 p-sm-4 border-theme bg-theme-surface-hover h-100 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between pb-2 border-theme-bottom mb-3">
                        <h2 class="h6 fw-bold text-theme mb-0">Bài viết đã thích</h2>
                        <span class="small text-theme-secondary" style="font-size: 11px;">10 mới nhất</span>
                    </div>

                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        @forelse($likedPosts as $post)
                            <a href="{{ route('posts.show', $post->slug) }}" class="card p-2.5 border-theme bg-theme-surface text-decoration-none d-block">
                                <div class="d-flex align-items-center gap-1 text-theme-secondary mb-1" style="font-size: 11px;">
                                    <span>{{ $post->category->name }}</span>
                                    <span>&bull;</span>
                                    <span class="text-truncate">{{ $post->user->name }}</span>
                                </div>
                                <h4 class="small fw-bold text-theme line-clamp-1 mb-0">
                                    {{ $post->title }}
                                </h4>
                            </a>
                        @empty
                            <p class="small text-theme-secondary text-center py-4 mb-0">Bạn chưa thích bài viết nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Section 3: Authors Following -->
            <div class="col-12">
                <div class="card p-3 p-sm-4 border-theme bg-theme-surface-hover">
                    <div class="d-flex align-items-center justify-content-between pb-2 border-theme-bottom mb-3">
                        <h2 class="h6 fw-bold text-theme mb-0">Tác giả đang theo dõi</h2>
                        <span class="small text-theme-secondary" style="font-size: 11px;">{{ count($following) }} tác giả</span>
                    </div>

                    <div class="row g-2">
                        @forelse($following as $author)
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="card p-2.5 border-theme bg-theme-surface d-flex flex-row align-items-center justify-content-between gap-2">
                                    <a href="{{ route('authors.show', $author) }}" class="d-flex align-items-center gap-2 text-decoration-none min-w-0">
                                        <x-avatar :user="$author" size="sm" />
                                        <div class="min-w-0 text-truncate">
                                            <div class="small fw-semibold text-theme text-truncate">
                                                {{ $author->name }}
                                            </div>
                                            <div class="text-theme-secondary text-truncate" style="font-size: 10px;">
                                                {{ '@' . ($author->username ?? strtolower(str_replace(' ', '', $author->name))) }}
                                            </div>
                                        </div>
                                    </a>
                                    <div class="flex-shrink-0">
                                        <x-follow-button :author="$author" size="sm" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 small text-theme-secondary">
                                Bạn chưa theo dõi tác giả nào. Khám phá các bài viết trên bảng tin và bấm theo dõi!
                            </div>
                        @endforelse
=======
        <div class="p-4 p-sm-5 border-theme-bottom">
            <h1 class="h4 fw-bold text-theme tracking-tight mb-1" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                Nhật ký hoạt động của bạn
            </h1>
            <p class="small text-theme-secondary mb-0" style="font-size: 14px;">
                Tổng hợp các tương tác và hoạt động gần đây của bạn trên BlogMNM.
            </p>
        </div>

        <div class="p-4 p-sm-5">
            <!-- Quick Stats Grid: 3 Clean Cards (Comments, Likes, Favorites) -->
            <div class="row g-3 mb-4 pb-1">
                <div class="col-12 col-sm-4">
                    <div class="card p-3 p-sm-4 border-theme bg-surface-2 rounded-xl shadow-2xs h-100">
                        <div class="d-flex align-items-center justify-content-between text-theme-secondary mb-2" style="font-size: 13.5px;">
                            <span class="fw-medium">Bình luận</span>
                            <div class="text-theme-muted">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                        </div>
                        <div class="h3 fw-bold tracking-tight mb-0 text-theme metric-number">
                            {{ number_format($stats['comments_count']) }}
                        </div>
                        <span class="small text-theme-muted mt-1 d-block" style="font-size: 12px;">Đã thảo luận</span>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="card p-3 p-sm-4 border-theme bg-surface-2 rounded-xl shadow-2xs h-100">
                        <div class="d-flex align-items-center justify-content-between text-theme-secondary mb-2" style="font-size: 13.5px;">
                            <span class="fw-medium">Đã thích</span>
                            <div class="text-danger opacity-75">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </div>
                        </div>
                        <div class="h3 fw-bold tracking-tight mb-0 metric-number" style="color: var(--color-like, #e11d48);">
                            {{ number_format($stats['likes_count']) }}
                        </div>
                        <span class="small text-theme-muted mt-1 d-block" style="font-size: 12px;">Bài viết yêu thích</span>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <a href="{{ route('favorites.index') }}" class="card p-3 p-sm-4 border-theme bg-surface-2 rounded-xl shadow-2xs h-100 text-decoration-none transition-colors hover-accent d-block">
                        <div class="d-flex align-items-center justify-content-between text-theme-secondary mb-2" style="font-size: 13.5px;">
                            <span class="fw-medium text-theme">Đã lưu</span>
                            <div class="text-warning">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </div>
                        </div>
                        <div class="h3 fw-bold tracking-tight mb-0 metric-number" style="color: var(--color-save, #d97706);">
                            {{ number_format($stats['favorites_count']) }}
                        </div>
                        <span class="small text-theme-secondary mt-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                            Xem bộ sưu tập &rarr;
                        </span>
                    </a>
                </div>
            </div>

            <!-- Two Columns Section: Recent Comments & Liked Posts -->
            <div class="row g-4">
                <!-- Section 1: Recent Comments -->
                <div class="col-12 col-lg-6">
                    <div class="card p-3.5 p-sm-4 border-theme bg-surface-2 h-100 d-flex flex-column rounded-xl shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between pb-2.5 border-theme-bottom mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="16" height="16" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <h2 class="h6 fw-bold text-theme mb-0" style="font-size: 15px;">Bình luận gần đây</h2>
                            </div>
                            <span class="badge bg-theme-surface border border-theme text-theme-secondary rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                {{ count($comments) }} mới nhất
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-3 flex-grow-1">
                            @forelse($comments as $comment)
                                <div class="card p-3 border-theme bg-theme-surface rounded-xl transition-all shadow-2xs">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-baseline justify-content-between gap-1 mb-2">
                                        <a href="{{ route('posts.show', $comment->post->slug) }}" class="fw-semibold text-theme text-decoration-none hover-accent line-clamp-2" style="font-size: 14.5px; line-height: 1.4;" title="{{ $comment->post->title }}">
                                            {{ $comment->post->title }}
                                        </a>
                                        <span class="small text-theme-muted text-nowrap flex-shrink-0 ms-sm-2" style="font-size: 12px;">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="p-2.5 rounded-2 bg-surface-2 border border-theme text-theme line-clamp-3 mb-0 fst-italic" style="font-size: 13.5px; line-height: 1.55;">
                                        "{{ $comment->body }}"
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 px-3 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-theme-surface border border-theme text-theme-muted mb-2.5" style="width: 44px; height: 44px;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </div>
                                    <p class="fw-semibold text-theme mb-1" style="font-size: 14.5px;">Chưa có bình luận gần đây</p>
                                    <p class="small text-theme-secondary mb-0" style="font-size: 13px;">Bình luận của bạn trên các bài viết sẽ hiển thị tại đây.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Section 2: Liked Posts -->
                <div class="col-12 col-lg-6">
                    <div class="card p-3.5 p-sm-4 border-theme bg-surface-2 h-100 d-flex flex-column rounded-xl shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between pb-2.5 border-theme-bottom mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="16" height="16" class="text-danger flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                <h2 class="h6 fw-bold text-theme mb-0" style="font-size: 15px;">Bài viết đã thích</h2>
                            </div>
                            <span class="badge bg-theme-surface border border-theme text-theme-secondary rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                {{ count($likedPosts) }} mới nhất
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-3 flex-grow-1">
                            @forelse($likedPosts as $post)
                                <a href="{{ route('posts.show', $post->slug) }}" class="card p-3 border-theme bg-theme-surface text-decoration-none d-block rounded-xl transition-all hover-accent shadow-2xs">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-1.5 text-theme-secondary text-truncate" style="font-size: 13px;">
                                            <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill px-2 py-0.5" style="font-size: 11px;">
                                                {{ $post->category->name }}
                                            </span>
                                            <span class="text-theme-muted">&bull;</span>
                                            <span class="fw-medium text-theme text-truncate">{{ $post->user->name }}</span>
                                        </div>
                                        <span class="text-danger flex-shrink-0 d-inline-flex align-items-center" title="Đã thích">
                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        </span>
                                    </div>
                                    <h3 class="fw-semibold text-theme line-clamp-2 mb-0" style="font-size: 15px; line-height: 1.45;">
                                        {{ $post->title }}
                                    </h3>
                                </a>
                            @empty
                                <div class="text-center py-5 px-3 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-theme-surface border border-theme text-danger opacity-75 mb-2.5" style="width: 44px; height: 44px;">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    </div>
                                    <p class="fw-semibold text-theme mb-1" style="font-size: 14.5px;">Chưa có bài viết đã thích</p>
                                    <p class="small text-theme-secondary mb-0" style="font-size: 13px;">Những bài viết bạn thả tim yêu thích sẽ xuất hiện ở đây.</p>
                                </div>
                            @endforelse
                        </div>
>>>>>>> origin/main
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
