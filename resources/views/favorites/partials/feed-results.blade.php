<div id="feed-results" tabindex="-1" aria-label="Bài viết đã lưu"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Feed of Saved Posts -->
    <div class="d-flex flex-column">
        @forelse($posts as $post)
            <div data-favorite-entry data-saved="true">
                <span class="visually-hidden" data-favorite-entry-status role="status">Đã lưu</span>
                <x-post-card :post="$post" />
            </div>
        @empty
            <x-empty-state
                title="Chưa có bài viết đã lưu"
                description="Những bài viết bạn lưu sẽ xuất hiện tại đây."
                :action-url="route('posts.index')"
                action-label="Khám phá bài viết"
                class="py-5 px-3"
            >
                <x-slot:icon>
                    <svg style="width: 24px; height: 24px;" class="text-warning" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </x-slot:icon>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div data-feed-pagination class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
