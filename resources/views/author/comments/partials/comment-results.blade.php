<div id="feed-results" tabindex="-1" aria-label="Bình luận trên bài viết của bạn"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $comments->total() }}">

    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <caption class="visually-hidden">Bình luận trên bài viết của bạn, bao gồm cả bình luận chưa hiển thị và spam.</caption>
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th scope="col" class="ps-3 ps-sm-4 py-3 small text-theme-muted">Người bình luận</th>
                        <th scope="col" class="py-3 small text-theme-muted">Nội dung / Bài viết</th>
                        <th scope="col" class="py-3 small text-theme-muted">Trạng thái</th>
                        <th scope="col" class="pe-3 pe-sm-4 py-3 small text-theme-muted text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            <td class="ps-3 ps-sm-4 py-3 align-top">
                                <div class="d-flex align-items-center gap-2">
                                    <x-avatar :user="$comment->user" size="sm" />
                                    <div>
                                        <span class="fw-semibold text-theme small d-block">{{ $comment->user->name }}</span>
                                        <time class="text-theme-muted small" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3" style="min-width: 240px; max-width: 560px;">
                                <p class="small text-theme text-break mb-2">{{ $comment->body }}</p>
                                <a href="{{ route('posts.show', $comment->post->slug) }}#comments" class="small text-theme-secondary text-decoration-none hover-accent">{{ $comment->post->title }}</a>
                                @if($comment->parent_id)
                                    <span class="small text-theme-muted d-block">Phản hồi trong luồng #{{ $comment->parent_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 align-top">
                                @if($comment->status === 'approved')
                                    <span class="badge rounded-pill bg-success-subtle text-success">Đã duyệt</span>
                                @elseif($comment->status === 'pending')
                                    <span class="badge rounded-pill bg-warning-subtle text-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger">Spam</span>
                                @endif
                            </td>
                            <td class="pe-3 pe-sm-4 py-3 text-end align-top">
                                @can('delete', $comment)
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa bình luận"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa bình luận này?"
                                          data-confirm-target-name="{{ $comment->user->name }}"
                                          data-confirm-target-meta="{{ Str::limit($comment->body, 60) }}"
                                          data-confirm-description="Bình luận và các phản hồi liên quan sẽ bị xóa. Hành động này không thể hoàn tác."
                                          data-confirm-btn-text="Xóa bình luận"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" aria-label="Xóa bình luận của {{ $comment->user->name }}">Xóa</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-0 border-0">
                                <x-empty-state title="Không tìm thấy bình luận" description="Chưa có bình luận trên bài viết của bạn hoặc không có kết quả phù hợp bộ lọc." :action-url="route('author.comments.index')" action-label="Đặt lại bộ lọc" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($comments->hasPages())
            <div data-feed-pagination class="p-3 border-theme-top d-flex justify-content-center">{{ $comments->links() }}</div>
        @endif
    </div>
</div>
