@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[660px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl">
        
        <!-- Feed Top Header (Threads Style Header) -->
        <div class="sticky top-0 z-30 bg-[var(--color-surface)]/90 backdrop-blur-xl border-b border-[var(--color-border)] px-4">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-6">
                    <span class="font-bold text-sm sm:text-base text-[var(--color-text)] border-b-2 border-[var(--color-text)] pb-3 pt-3 select-none">
                        Dành cho bạn
                    </span>
                    @auth
                        <a href="{{ route('activity.index') }}" class="font-medium text-sm text-[var(--color-text-secondary)] hover:text-[var(--color-text)] pb-3 pt-3 transition-colors select-none">
                            Đang theo dõi
                        </a>
                    @endauth
                </div>

                <!-- Search Toggle / Icon -->
                <div class="relative flex items-center">
                    <form action="{{ route('posts.index') }}" method="GET" class="flex items-center">
                        @if($currentCategorySlug)
                            <input type="hidden" name="category" value="{{ $currentCategorySlug }}">
                        @endif
                        <div class="relative flex items-center">
                            <input type="text"
                                   id="feed-search-input"
                                   name="q"
                                   value="{{ $search }}"
                                   placeholder="Tìm kiếm bài viết..."
                                   class="w-36 sm:w-48 pl-8 pr-3 py-1.5 text-xs bg-[var(--color-input)] border border-[var(--color-border)] rounded-full text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:w-48 sm:focus:w-60 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-3.5 h-3.5 text-[var(--color-text-secondary)] absolute left-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success') || session('status'))
            <div class="px-4 py-3 bg-emerald-500/10 border-b border-emerald-500/20 text-emerald-400 text-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            </div>
        @endif

        <!-- Inline Post Composer Prompt (Visual & Quick Action) -->
        @auth
            <div class="flex items-center gap-3.5 p-4 border-b border-[var(--color-border)] bg-[var(--color-surface)]">
                <a href="{{ route('profile.show') }}" class="shrink-0">
                    <x-avatar :user="Auth::user()" size="md" />
                </a>
                <div class="flex-1 min-w-0">
                    @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                        <a href="{{ route('posts.create') }}" class="block text-[var(--color-text-secondary)] text-sm hover:text-[var(--color-text)] truncate">
                            Có gì mới trong thế giới công nghệ hôm nay?
                        </a>
                    @else
                        <span class="block text-[var(--color-text-secondary)] text-sm truncate opacity-70">
                            Khám phá bài viết và nâng cấp tài khoản để bắt đầu sáng tạo nội dung...
                        </span>
                    @endif
                </div>
                @if(Auth::user()->isAuthor() || Auth::user()->isAdmin())
                    <a href="{{ route('posts.create') }}"
                       class="px-4 py-1.5 rounded-full border border-[var(--color-border)] text-[var(--color-text)] text-xs font-semibold hover:bg-[var(--color-surface-hover)] transition shadow-sm shrink-0">
                        Viết bài
                    </a>
                @elseif(Auth::user()->role === 'viewer')
                    <form method="POST" action="{{ route('author.become') }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="px-4 py-1.5 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition shadow-sm cursor-pointer">
                            Trở thành tác giả
                        </button>
                    </form>
                @endif
            </div>
        @endauth

        <!-- Category Filter Horizontal Chips -->
        <div class="px-4 py-3 border-b border-[var(--color-border)] bg-[var(--color-surface)]">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth">
                <!-- All Categories -->
                <a href="{{ route('posts.index', array_filter(['q' => $search])) }}"
                   class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ empty($currentCategorySlug) ? 'bg-[var(--color-text)] text-[var(--color-bg)] shadow-sm' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)] border border-[var(--color-border)]' }}">
                    Tất cả
                </a>

                <!-- Categories -->
                @foreach($categories as $category)
                    <a href="{{ route('posts.index', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                       class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $currentCategorySlug === $category->slug ? 'bg-[var(--color-text)] text-[var(--color-bg)] shadow-sm' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)] border border-[var(--color-border)]' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Active Filter Indicator Banner -->
        @if($search || $currentCategorySlug || $currentTagSlug)
            <div class="px-4 py-3 bg-[var(--color-surface-hover)] border-b border-[var(--color-border)] flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-[var(--color-text)] truncate">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="truncate">
                        Đang lọc:
                        @if($search) từ khóa <strong>"{{ $search }}"</strong> @endif
                        @if($currentCategorySlug) @if($search) &bull; @endif <strong>{{ $activeCategory->name ?? $currentCategorySlug }}</strong> @endif
                        @if($currentTagSlug) @if($search || $currentCategorySlug) &bull; @endif thẻ <strong>#{{ $currentTagSlug }}</strong> @endif
                        <span class="text-[var(--color-text-secondary)] ml-1">({{ $posts->total() }} kết quả)</span>
                    </span>
                </div>
                <a href="{{ route('posts.index') }}" class="font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition shrink-0 underline">
                    Xóa lọc
                </a>
            </div>
        @endif

        <!-- Single Column Posts Feed -->
        <div class="flex flex-col divide-y divide-[var(--color-border)]">
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
            <div class="p-5 border-t border-[var(--color-border)] bg-[var(--color-surface)]">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
