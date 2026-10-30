<div id="feed-results" tabindex="-1" aria-label="Kết quả bài viết"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Active Filter Indicator Banner -->
    @if ($search || $currentCategorySlug || $currentTagSlug)
        <div class="feed-filter-indicator px-3 px-sm-4 d-flex align-items-center justify-content-between gap-3 border-theme-bottom">
            <div class="d-flex align-items-center gap-1.5 min-w-0 small text-truncate">
                @if ($search)
                    <span class="fw-semibold text-theme text-truncate">
                        "{{ $search }}"
                    </span>
                @endif

                @if ($currentCategorySlug)
                    @if ($search)
                        <span class="text-theme-muted flex-shrink-0" aria-hidden="true">·</span>
                    @endif
                    <span class="fw-semibold text-theme text-truncate">
                        {{ $activeCategory->name ?? $currentCategorySlug }}
                    </span>
                @endif

                @if ($currentTagSlug)
                    @if ($search || $currentCategorySlug)
                        <span class="text-theme-muted flex-shrink-0" aria-hidden="true">·</span>
                    @endif
                    <span class="fw-semibold text-theme text-truncate">
                        #{{ $currentTagSlug }}
                    </span>
                @endif

                <span class="text-theme-secondary flex-shrink-0">
                    · {{ number_format($posts->total()) }} {{ $search ? 'kết quả' : 'bài viết' }}
                </span>
            </div>

            <a href="{{ route('posts.index') }}"
               data-feed-clear-filter
               class="feed-clear-filter-btn flex-shrink-0 small text-theme-secondary text-decoration-none hover-accent fw-medium"
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
            <x-empty-state
                title="Không tìm thấy bài viết"
                description="Không có kết quả phù hợp."
                :action-url="route('posts.index')"
                action-label="Xem tất cả bài viết"
                class="py-5 px-3 border-0 bg-transparent" />
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if ($posts->hasPages())
        <div data-feed-pagination class="py-3 px-3 px-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
