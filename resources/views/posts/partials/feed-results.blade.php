<div id="feed-results" tabindex="-1" aria-label="Kết quả bài viết"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Active Filter Bar (Minimalist Threads Style) -->
    @if ($search || $currentCategorySlug || $currentTagSlug)
        <div class="feed-filter-indicator px-3 px-sm-4 py-2 d-flex align-items-center justify-content-between gap-2.5 border-theme-bottom">
            <div class="d-flex align-items-center flex-wrap gap-1 min-w-0" style="font-size: 13px; line-height: 1.4;">
                @if ($search)
                    <span class="fw-semibold text-theme text-truncate" style="max-width: 180px;" title="{{ $search }}">
                        "{{ $search }}"
                    </span>
                @endif

                @if ($currentCategorySlug)
                    @if ($search)
                        <span class="text-theme-muted mx-0.5" aria-hidden="true">&middot;</span>
                    @endif
                    <span class="fw-semibold text-theme text-truncate" style="max-width: 160px;">
                        {{ $activeCategory->name ?? $currentCategorySlug }}
                    </span>
                @endif

                @if ($currentTagSlug)
                    @if ($search || $currentCategorySlug)
                        <span class="text-theme-muted mx-0.5" aria-hidden="true">&middot;</span>
                    @endif
                    <span class="fw-semibold text-theme text-truncate" style="max-width: 160px;">
                        #{{ $currentTagSlug }}
                    </span>
                @endif

                <span class="text-theme-muted mx-0.5" aria-hidden="true">&middot;</span>
                <span class="text-theme-secondary flex-shrink-0">
                    {{ number_format($posts->total()) }} {{ $search ? 'kết quả' : 'bài viết' }}
                </span>
            </div>

            <a href="{{ route('posts.index') }}"
               data-feed-clear-filter
               class="feed-clear-filter-btn flex-shrink-0 text-decoration-none"
               aria-label="Xóa bộ lọc">
                Xóa lọc
            </a>
        </div>
    @endif

    <!-- Single Column Posts Feed -->
    <div class="d-flex flex-column feed-posts-stream">
        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="py-5 px-3 px-sm-4 text-center my-2">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle border border-theme bg-theme-surface mb-3"
                     style="width: 44px; height: 44px;">
                    <svg width="20" height="20" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="fw-semibold text-theme mb-1" style="font-size: 15px;">
                    Không tìm thấy bài viết
                </h3>
                <p class="text-theme-secondary mb-3 mx-auto" style="font-size: 13px; max-width: 320px;">
                    Không có kết quả phù hợp.
                </p>
                <div>
                    <a href="{{ route('posts.index') }}"
                       data-feed-clear-filter
                       class="btn btn-sm btn-editorial-primary rounded-pill px-3.5 py-1.5 fw-semibold"
                       style="font-size: 13px;">
                        Xem tất cả bài viết
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if ($posts->hasPages())
        <div data-feed-pagination class="py-3 px-3 px-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
