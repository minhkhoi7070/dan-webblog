@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[660px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl">
        
        <!-- Top Back Bar -->
        <div class="p-4 border-b border-[var(--color-border)] flex items-center justify-between sticky top-0 bg-[var(--color-surface)]/90 backdrop-blur-xl z-30">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="text-xs font-semibold text-[var(--color-text-secondary)]">Trang tác giả</span>
        </div>

        <!-- Social Profile Header -->
        <div class="p-6 sm:p-8 border-b border-[var(--color-border)] bg-[var(--color-surface)]">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[var(--color-text)] tracking-tight truncate">
                        {{ $author->name }}
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-medium text-[var(--color-text-secondary)]">
                            {{ '@' . ($author->username ?? strtolower(str_replace(' ', '', $author->name))) }}
                        </span>
                        <x-badge variant="status-draft" size="xs" class="capitalize">
                            {{ $author->role }}
                        </x-badge>
                    </div>
                </div>

                <!-- Avatar -->
                <x-avatar :user="$author" size="2xl" class="shadow-lg shadow-indigo-600/10" />
            </div>

            <!-- Bio -->
            <p class="mt-4 text-sm text-[var(--color-text-secondary)] leading-relaxed max-w-lg">
                {{ $author->bio ?: 'Tác giả chưa cập nhật tiểu sử cá nhân trên nền tảng BlogMNM.' }}
            </p>

            <!-- Metrics / Counters -->
            <div class="mt-5 flex items-center gap-6 text-xs sm:text-sm text-[var(--color-text-secondary)]">
                <div>
                    <span class="font-bold text-[var(--color-text)] text-sm sm:text-base">{{ $author->posts_count }}</span>
                    <span class="ml-1 opacity-70">bài viết</span>
                </div>
                <div>
                    <span id="follower-count" class="font-bold text-[var(--color-text)] text-sm sm:text-base">{{ $author->followers_count }}</span>
                    <span class="ml-1 opacity-70">người theo dõi</span>
                </div>
            </div>

            <!-- Follow Action -->
            <div class="mt-6">
                <x-follow-button :author="$author" size="md" />
            </div>
        </div>

        <!-- Author Feed Section Header & Search -->
        <div class="px-5 py-4 border-b border-[var(--color-border)] bg-[var(--color-surface-hover)] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="font-bold text-sm text-[var(--color-text)]">
                Bài viết đã đăng <span class="text-[var(--color-text-secondary)] font-normal">({{ $posts->total() }})</span>
            </div>

            <!-- Minimal Search -->
            <form action="{{ route('authors.show', $author) }}" method="GET" class="relative">
                <input type="search"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Tìm trong bài của tác giả..."
                       class="w-full sm:w-56 pl-8 pr-3 py-1.5 text-xs bg-[var(--color-input)] border border-[var(--color-border)] rounded-full text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <svg class="w-3.5 h-3.5 text-[var(--color-text-secondary)] absolute left-2.5 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>

        <!-- Posts Feed -->
        <div class="flex flex-col divide-y divide-[var(--color-border)]">
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
            <div class="p-5 border-t border-[var(--color-border)] bg-[var(--color-surface)]">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
