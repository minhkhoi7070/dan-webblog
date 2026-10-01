@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 card border-0 border-sm border-theme shadow-lg p-3 p-sm-5" style="max-width: 760px; border-radius: 1.5rem;">
        
        <!-- Header -->
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
