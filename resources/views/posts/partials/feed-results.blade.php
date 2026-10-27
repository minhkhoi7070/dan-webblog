<div id="feed-results" tabindex="-1" aria-label="Kết quả bài viết"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Active Filter Indicator Banner -->
    @if ($search || $currentCategorySlug || $currentTagSlug)
        <div class="px-3 px-sm-4 py-2 d-flex align-items-center justify-content-between gap-3 border-theme-bottom">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle text-accent"
                      style="width: 28px; height: 28px; background: var(--color-interactive-subtle, rgba(59, 130, 246, 0.08));"
                      aria-hidden="true">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </span>

                <div class="d-flex align-items-center gap-1 flex-wrap min-w-0 small">
                    <span class="text-theme-secondary">Đang lọc</span>

                    @if ($search)
                        <span class="fw-semibold text-theme">
                            "{{ $search }}"
                        </span>
                    @endif

                    @if ($currentCategorySlug)
                        @if ($search)
                            <span class="text-theme-muted">·</span>
                        @endif
                        <span class="fw-semibold text-theme">
                            {{ $activeCategory->name ?? $currentCategorySlug }}
                        </span>
                    @endif

                    @if ($currentTagSlug)
                        @if ($search || $currentCategorySlug)
                            <span class="text-theme-muted">·</span>
                        @endif
                        <span class="fw-semibold text-theme">
                            #{{ $currentTagSlug }}
                        </span>
                    @endif

                    <span class="text-theme-muted">
                        · {{ $posts->total() }} kết quả
                    </span>
                </div>
            </div>

            <a href="{{ route('posts.index') }}"
               data-feed-clear-filter
               class="flex-shrink-0 small text-theme-muted text-decoration-none hover-accent"
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
                title="Chưa có bài viết nào phù hợp"
                description="Thử tìm kiếm với từ khóa khác hoặc khám phá các chuyên mục được quan tâm."
                :action-url="route('posts.index')"
                action-label="Quay lại tất cả bài viết" />
        @endforelse
    </div>

    <!-- Pagination Section -->
    @if ($posts->hasPages())
        <div data-feed-pagination class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
