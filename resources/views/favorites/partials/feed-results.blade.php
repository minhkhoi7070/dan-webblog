<div id="feed-results" tabindex="-1" aria-label="Bài viết đã lưu"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Feed of Saved Posts -->
    <div class="d-flex flex-column">
        @forelse($posts as $post)
            <div data-favorite-entry data-saved="true">
                <div class="favorite-entry-status small text-theme-muted px-3 px-sm-4 pt-2" data-favorite-entry-status role="status">Đã lưu</div>
                <x-post-card :post="$post" />
            </div>
        @empty
            <x-empty-state
                title="Bạn chưa lưu bài viết nào"
                description="Khi bạn tìm thấy bài viết hữu ích hoặc muốn đọc lại sau, hãy nhấn vào biểu tượng dấu trang để lưu vào đây."
                :action-url="route('posts.index')"
                action-label="Khám phá bài viết ngay"
            >
                <x-slot:icon>
                    <svg style="width: 28px; height: 28px;" class="text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
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
