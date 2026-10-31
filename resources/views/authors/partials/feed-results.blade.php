<div id="feed-results" tabindex="-1" aria-label="Bài viết của tác giả"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Posts Feed -->
    <div class="d-flex flex-column">
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
        <div data-feed-pagination class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
