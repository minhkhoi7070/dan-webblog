<div id="feed-results" tabindex="-1" aria-label="Bảng kiểm duyệt bình luận"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $comments->total() }}"
     data-feed-current-status="{{ $currentStatus ?? '' }}"
     data-feed-counts='@json($statusCounts ?? [])'>

    <!-- Comments List Table & Mobile Card View Container -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4 admin-comments-table-card">
        <!-- Desktop Table View (>= 768px Tablet & Desktop) -->
        <div class="admin-comments-table-scroll-tools" data-comments-table-scroll-tools>
            <p id="admin-comments-table-scroll-hint" class="admin-comments-table-scroll-hint small text-theme-muted mb-0">
                Cuộn ngang để xem các cột còn lại.
            </p>
            <div class="admin-comments-table-scroll-controls" role="group" aria-label="Điều khiển cuộn ngang bảng bình luận">
                <button type="button" class="btn btn-sm btn-outline-theme" data-comments-table-scroll="left" aria-label="Cuộn bảng bình luận sang trái" disabled>
                    <span aria-hidden="true">&lsaquo;</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-theme" data-comments-table-scroll="right" aria-label="Cuộn bảng bình luận sang phải" disabled>
                    <span aria-hidden="true">&rsaquo;</span>
                </button>
            </div>
        </div>
        <div class="table-responsive admin-comments-table-scroll d-none d-md-block" role="region" tabindex="0" aria-label="Bảng bình luận có thể cuộn ngang" aria-describedby="admin-comments-table-scroll-hint">
            <table class="table table-hover align-middle mb-0 admin-comments-table" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <colgroup>
                    <col class="admin-comments-col-user">
                    <col class="admin-comments-col-content">
                    <col class="admin-comments-col-post">
                    <col class="admin-comments-col-status">
                    <col class="admin-comments-col-created">
                    <col class="admin-comments-col-actions">
                </colgroup>
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Người bình luận</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Nội dung</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                        <th class="py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                        <th class="py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Thời gian</th>
                        <th class="pe-3 pe-sm-4 py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($comments as $comment)
                        <tr>
                            <!-- User -->
                            <td class="ps-3 ps-sm-4 py-3">
                                <div class="d-flex align-items-center admin-comments-user-info">
                                    <x-avatar :user="$comment->user" size="sm" class="border border-theme flex-shrink-0" />
                                    <div class="admin-comments-user-text min-w-0">
                                        <a href="{{ route('admin.users.show', $comment->user) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 13px;">
                                            {{ $comment->user->name }}
                                        </a>
                                        <span class="small text-theme-muted text-truncate d-block" style="font-size: 11px;">{{ $comment->user->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Comment Body -->
                            <td class="py-3 admin-comments-content-cell">
                                <div class="p-2 rounded-2 bg-surface-2 border border-theme text-theme small mb-1 line-clamp-3 admin-comments-body">
                                    "{{ $comment->body }}"
                                </div>
                                @if($comment->parent_id)
                                    <span class="d-inline-flex align-items-center gap-1 text-theme-muted" style="font-size: 10.5px;">
                                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a4 4 0 014 4v7M3 10l6 6m-6-6l6-6" /></svg>
                                        Phản hồi cho bình luận #{{ $comment->parent_id }}
                                    </span>
                                @endif
                            </td>

                            <!-- Target Post -->
                            <td class="py-3 admin-comments-post-cell">
                                @if($comment->post)
                                    <a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank" class="text-theme text-decoration-none hover-accent fw-medium small line-clamp-2 d-block admin-comments-post-link" style="font-size: 12.5px;">
                                        {{ $comment->post->title }}
                                    </a>
                                @else
                                    <span class="text-theme-muted fst-italic small">Bài viết đã xóa</span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 text-center admin-comments-status-cell">
                                @if($comment->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                        Đã duyệt
                                    </span>
                                @elseif($comment->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill admin-comments-pending" style="font-size: 11px;">
                                        Chờ duyệt
                                    </span>
                                @elseif($comment->status === 'spam')
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                        Spam
                                    </span>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td class="py-3 text-center text-theme-muted small admin-comments-created-cell" style="font-size: 11.5px;">
                                {{ $comment->created_at->format('d/m/Y H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="pe-3 pe-sm-4 py-3 text-center">
                                <div class="d-inline-flex align-items-center gap-1 admin-comments-table-actions">
                                    @if($comment->status !== 'approved')
                                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success border-0 p-1 rounded-circle" title="Duyệt bình luận" aria-label="Duyệt bình luận">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($comment->status !== 'spam')
                                        <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning border-0 p-1 rounded-circle" title="Đánh dấu spam" aria-label="Đánh dấu spam">
                                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa bình luận"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                          data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                                          data-confirm-target-meta="{{ Str::limit($comment->body ?? $comment->content, 60) }}"
                                          data-confirm-description="Hành động này không thể hoàn tác. Bình luận và các phản hồi liên quan sẽ bị xóa hoàn toàn."
                                          data-confirm-btn-text="Xóa bình luận"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-circle admin-comments-delete" title="Xóa bình luận" aria-label="Xóa bình luận">
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <x-empty-state
                                    title="Không tìm thấy bình luận"
                                    description="Không có bình luận nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                                    :action-url="route('admin.comments.index')"
                                    action-label="Đặt lại bộ lọc"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (< 768px: Mobile-First Responsive Cards) -->
        <div class="d-block d-md-none divide-y divide-theme">
            @forelse($comments as $comment)
                <div class="p-3">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        @if($comment->status === 'approved')
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 10px;">
                                Đã duyệt
                            </span>
                        @elseif($comment->status === 'pending')
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill admin-comments-pending" style="font-size: 10px;">
                                Chờ duyệt
                            </span>
                        @elseif($comment->status === 'spam')
                            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 10px;">
                                Spam
                            </span>
                        @endif
                        <span class="small text-theme-muted" style="font-size: 11px;">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="d-flex align-items-center mb-2 admin-comments-user-info">
                        <x-avatar :user="$comment->user" size="sm" class="border border-theme flex-shrink-0" />
                        <div class="admin-comments-user-text min-w-0 flex-grow-1">
                            <a href="{{ route('admin.users.show', $comment->user) }}" class="fw-bold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 13.5px;">
                                {{ $comment->user->name }}
                            </a>
                            <span class="small text-theme-muted text-truncate d-block" style="font-size: 11.5px;">{{ $comment->user->email }}</span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-2 bg-surface-2 border border-theme text-theme small mb-2 line-clamp-3 admin-comments-body" style="font-size: 13px;">
                        "{{ $comment->body }}"
                    </div>

                    @if($comment->post)
                        <div class="mb-2.5 small text-theme-muted">
                            <span class="opacity-75">Bài viết:</span>
                            <a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank" class="text-theme fw-medium text-decoration-none hover-accent ms-1 line-clamp-2 admin-comments-post-link">
                                {{ $comment->post->title }}
                            </a>
                        </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-theme-top">
                        @if($comment->status !== 'approved')
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Duyệt
                                </button>
                            </form>
                        @endif

                        @if($comment->status !== 'spam')
                            <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                    Spam
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
                              data-confirm="true"
                              data-confirm-title="Xóa bình luận"
                              data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                              data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                              data-confirm-target-meta="{{ Str::limit($comment->body ?? $comment->content, 60) }}"
                              data-confirm-description="Hành động này không thể hoàn tác."
                              data-confirm-btn-text="Xóa bình luận"
                              data-confirm-btn-class="btn-danger"
                              data-confirm-type="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1.5 rounded-circle admin-comments-delete" title="Xóa bình luận" aria-label="Xóa bình luận">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-3">
                    <x-empty-state
                        title="Không tìm thấy bình luận"
                        description="Không có bình luận nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                        :action-url="route('admin.comments.index')"
                        action-label="Đặt lại bộ lọc"
                    />
                </div>
            @endforelse
        </div>
    </div>

        @if($comments->hasPages())
            <div data-feed-pagination class="admin-comments-pagination px-3 px-sm-4 py-3 border-theme-top bg-theme-surface">
                {{ $comments->onEachSide(1)->links('admin.comments.partials.pagination') }}
            </div>
        @endif
    </div>
</div>
