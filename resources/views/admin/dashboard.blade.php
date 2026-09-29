@extends('layouts.admin')

@section('admin_title', 'Bảng điều khiển Quản trị')

@section('admin_content')
<!-- Dashboard Top Metrics Grid -->
<div class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-[var(--color-text)] uppercase tracking-wider">Thống kê Tổng thể Hệ thống</h2>
        <span class="text-xs text-[var(--color-text-secondary)]">Cập nhật thời gian thực</span>
    </div>

    <!-- 1. Users Metrics (users, authors, viewers) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-[var(--color-surface)] p-5 rounded-2xl border border-[var(--color-border)]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Tổng người dùng</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-[var(--color-text)] mt-2">{{ number_format($metrics['users']) }}</p>
            <div class="flex items-center gap-3 mt-3 pt-3 border-t border-[var(--color-border)] text-xs text-[var(--color-text-secondary)]">
                <span>Tác giả: <strong class="text-indigo-400 font-bold">{{ number_format($metrics['authors']) }}</strong></span>
                <span>&bull;</span>
                <span>Độc giả: <strong class="text-[var(--color-text)] font-bold">{{ number_format($metrics['viewers']) }}</strong></span>
            </div>
        </div>

        <!-- 2. Views Metrics -->
        <div class="bg-[var(--color-surface)] p-5 rounded-2xl border border-[var(--color-border)]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Lượt xem bài viết</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-2">{{ number_format($metrics['views']) }}</p>
            <p class="text-xs text-[var(--color-text-secondary)] mt-3 pt-3 border-t border-[var(--color-border)]">Tổng lưu lượng truy cập toàn trang</p>
        </div>

        <!-- 3. Comments Metrics -->
        <div class="bg-[var(--color-surface)] p-5 rounded-2xl border border-[var(--color-border)]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Bình luận độc giả</span>
                <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                </span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-[var(--color-text)] mt-2">{{ number_format($metrics['comments']) }}</p>
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-[var(--color-border)] text-xs">
                <span class="text-[var(--color-text-secondary)]">Chờ duyệt:</span>
                <span class="px-2 py-0.5 rounded-full font-bold {{ $metrics['pending_comments'] > 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)]' }}">
                    {{ number_format($metrics['pending_comments']) }}
                </span>
                @if($metrics['pending_comments'] > 0)
                    <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="ml-auto text-indigo-400 font-semibold hover:underline">Xử lý ngay &rarr;</a>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. Posts Lifecycle Metrics Grid -->
    <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)] mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-[var(--color-text-secondary)] uppercase tracking-wider">Tiến trình Biên tập & Xuất bản Bài viết</h3>
            <span class="text-xs text-[var(--color-text-secondary)]">Tổng số: <strong class="text-[var(--color-text)] font-extrabold">{{ number_format($metrics['posts']) }}</strong> bài viết</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
            <!-- Total Posts -->
            <a href="{{ route('admin.posts.index') }}" class="p-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-hover)] hover:opacity-90 transition group">
                <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Tổng bài</span>
                <p class="text-xl sm:text-2xl font-black text-[var(--color-text)] mt-1 transition">{{ number_format($metrics['posts']) }}</p>
            </a>

            <!-- Pending Posts -->
            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="p-4 rounded-xl border {{ $metrics['pending'] > 0 ? 'border-amber-500/40 bg-amber-500/10' : 'border-[var(--color-border)] bg-[var(--color-surface-hover)]' }} hover:border-amber-400 transition group">
                <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">Chờ duyệt (Pending)</span>
                <p class="text-xl sm:text-2xl font-black text-amber-400 mt-1">{{ number_format($metrics['pending']) }}</p>
            </a>

            <!-- Published Posts -->
            <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="p-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-hover)] hover:opacity-90 transition group">
                <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">Đã xuất bản</span>
                <p class="text-xl sm:text-2xl font-black text-emerald-400 mt-1">{{ number_format($metrics['published']) }}</p>
            </a>

            <!-- Draft Posts -->
            <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="p-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-hover)] hover:opacity-90 transition group">
                <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Bản nháp (Draft)</span>
                <p class="text-xl sm:text-2xl font-black text-[var(--color-text-secondary)] mt-1">{{ number_format($metrics['draft']) }}</p>
            </a>

            <!-- Rejected Posts -->
            <a href="{{ route('admin.posts.index', ['status' => 'rejected']) }}" class="p-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-hover)] hover:opacity-90 transition group">
                <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider block">Bị từ chối</span>
                <p class="text-xl sm:text-2xl font-black text-rose-400 mt-1">{{ number_format($metrics['rejected']) }}</p>
            </a>
        </div>
    </div>
</div>

<!-- Moderation Quick Queues (Pending Posts & Comments) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Queue 1: Pending Posts Needing Review -->
    <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <h3 class="text-sm font-bold text-[var(--color-text)] uppercase tracking-wider">Bài viết chờ thẩm định ({{ $recentPendingPosts->count() }})</h3>
            </div>
            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="text-xs text-indigo-400 font-semibold hover:underline">
                Xem tất cả &rarr;
            </a>
        </div>

        @if($recentPendingPosts->isEmpty())
            <div class="py-8 text-center text-[var(--color-text-secondary)] text-xs">
                Không có bài viết nào đang chờ duyệt. Mọi thứ đã hoàn tất!
            </div>
        @else
            <div class="divide-y divide-[var(--color-border)]">
                @foreach($recentPendingPosts as $post)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.posts.show', $post) }}" class="text-sm font-semibold text-[var(--color-text)] hover:opacity-80 truncate block">
                                {{ $post->title }}
                            </a>
                            <div class="flex items-center gap-2 text-xs text-[var(--color-text-secondary)] mt-0.5">
                                <span>Bởi {{ $post->user->name }}</span>
                                <span>&bull;</span>
                                <span class="text-indigo-400">{{ $post->category->name }}</span>
                                <span>&bull;</span>
                                <span>{{ $post->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Quick Approve Button -->
                            <form action="{{ route('admin.posts.approve', $post) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-xs font-bold text-emerald-300 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 rounded-full transition">
                                    Duyệt
                                </button>
                            </form>

                            <!-- Review Link -->
                            <a href="{{ route('admin.posts.show', $post) }}" class="px-3 py-1 text-xs font-semibold text-[var(--color-text)] hover:opacity-90 bg-[var(--color-surface-hover)] border border-[var(--color-border)] rounded-full transition">
                                Thẩm định
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Queue 2: Pending Comments Needing Review -->
    <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                <h3 class="text-sm font-bold text-[var(--color-text)] uppercase tracking-wider">Bình luận chờ kiểm duyệt ({{ $recentPendingComments->count() }})</h3>
            </div>
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="text-xs text-indigo-400 font-semibold hover:underline">
                Xem tất cả &rarr;
            </a>
        </div>

        @if($recentPendingComments->isEmpty())
            <div class="py-8 text-center text-[var(--color-text-secondary)] text-xs">
                Không có bình luận nào đang chờ kiểm duyệt.
            </div>
        @else
            <div class="divide-y divide-[var(--color-border)]">
                @foreach($recentPendingComments as $comment)
                    <div class="py-3 flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-[var(--color-text)] line-clamp-2">"{{ $comment->body }}"</p>
                            <div class="flex items-center gap-2 text-[11px] text-[var(--color-text-secondary)] mt-1">
                                <span>Bởi <strong>{{ $comment->user->name }}</strong></span>
                                <span>&bull;</span>
                                <span class="truncate max-w-[150px]">Bài: {{ $comment->post->title }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Approve Comment -->
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                @csrf
                                <button type="submit" title="Duyệt bình luận" class="p-1.5 text-emerald-400 hover:bg-emerald-950/60 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </form>

                            <!-- Spam Comment -->
                            <form action="{{ route('admin.comments.spam', $comment) }}" method="POST">
                                @csrf
                                <button type="submit" title="Đánh dấu Spam" class="p-1.5 text-amber-400 hover:bg-amber-950/60 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </button>
                            </form>

                            <!-- Delete Comment -->
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Xóa bình luận này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Xóa" class="p-1.5 text-rose-400 hover:bg-rose-950/60 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
