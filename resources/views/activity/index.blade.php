@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[760px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl p-5 sm:p-8">
        
        <!-- Header -->
        <div class="mb-6 pb-5 border-b border-[var(--color-border)]">
            <h1 class="text-xl sm:text-2xl font-black text-[var(--color-text)] tracking-tight">
                Nhật ký hoạt động của bạn
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-[var(--color-text-secondary)]">
                Tổng hợp lịch sử tương tác, bài viết bạn đã thích, bình luận và tác giả đang theo dõi.
            </p>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            <x-stat-card
                title="Bình luận"
                :value="number_format($stats['comments_count'])"
                color="default"
            />
            <x-stat-card
                title="Đã thích"
                :value="number_format($stats['likes_count'])"
                color="rose"
            />
            <x-stat-card
                title="Đã lưu"
                :value="number_format($stats['favorites_count'])"
                color="amber"
            />
            <x-stat-card
                title="Đang theo dõi"
                :value="number_format($stats['following_count'])"
                color="indigo"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Section 1: Recent Comments -->
            <div class="p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-[var(--color-border)] mb-4">
                    <h2 class="text-sm font-bold text-[var(--color-text)]">Bình luận gần đây</h2>
                    <span class="text-[11px] text-[var(--color-text-secondary)]">10 mới nhất</span>
                </div>

                <div class="space-y-3 flex-1">
                    @forelse($comments as $comment)
                        <div class="p-3 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] text-xs">
                            <div class="flex items-center justify-between text-[11px] text-[var(--color-text-secondary)] mb-1">
                                <a href="{{ route('posts.show', $comment->post->slug) }}" class="font-semibold text-indigo-400 hover:text-indigo-300 truncate max-w-[200px]">
                                    {{ Str::limit($comment->post->title, 32) }}
                                </a>
                                <span class="shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[var(--color-text)] leading-relaxed line-clamp-2">
                                {{ $comment->body }}
                            </p>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--color-text-secondary)] text-center py-6">Bạn chưa gửi bình luận nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Liked Posts -->
            <div class="p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-[var(--color-border)] mb-4">
                    <h2 class="text-sm font-bold text-[var(--color-text)]">Bài viết đã thích</h2>
                    <span class="text-[11px] text-[var(--color-text-secondary)]">10 mới nhất</span>
                </div>

                <div class="space-y-2.5 flex-1">
                    @forelse($likedPosts as $post)
                        <a href="{{ route('posts.show', $post->slug) }}" class="p-3 rounded-xl hover:opacity-90 bg-[var(--color-surface)] border border-[var(--color-border)] transition block group">
                            <div class="flex items-center gap-1.5 text-[11px] text-[var(--color-text-secondary)] mb-0.5">
                                <span class="font-medium text-[var(--color-text-secondary)]">{{ $post->category->name }}</span>
                                <span>&bull;</span>
                                <span class="truncate">{{ $post->user->name }}</span>
                            </div>
                            <h4 class="font-bold text-xs text-[var(--color-text)] group-hover:text-indigo-400 line-clamp-1">
                                {{ $post->title }}
                            </h4>
                        </a>
                    @empty
                        <p class="text-xs text-[var(--color-text-secondary)] text-center py-6">Bạn chưa thích bài viết nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Section 3: Authors Following -->
            <div class="p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] md:col-span-2">
                <div class="flex items-center justify-between pb-3 border-b border-[var(--color-border)] mb-4">
                    <h2 class="text-sm font-bold text-[var(--color-text)]">Tác giả đang theo dõi</h2>
                    <span class="text-[11px] text-[var(--color-text-secondary)]">{{ count($following) }} tác giả</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse($following as $author)
                        <div class="p-3.5 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] flex items-center justify-between gap-3">
                            <a href="{{ route('authors.show', $author) }}" class="flex items-center gap-2.5 min-w-0 group">
                                <x-avatar :user="$author" size="sm" />
                                <div class="min-w-0">
                                    <div class="font-semibold text-xs text-[var(--color-text)] group-hover:text-indigo-400 truncate">
                                        {{ $author->name }}
                                    </div>
                                    <div class="text-[10px] text-[var(--color-text-secondary)] truncate">
                                        {{ '@' . ($author->username ?? strtolower(str_replace(' ', '', $author->name))) }}
                                    </div>
                                </div>
                            </a>
                            <div class="shrink-0">
                                <x-follow-button :author="$author" size="sm" />
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-6 text-xs text-[var(--color-text-secondary)]">
                            Bạn chưa theo dõi tác giả nào. Khám phá các bài viết trên bảng tin và bấm theo dõi!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
