<div id="feed-results" tabindex="-1" aria-label="Bảng kiểm duyệt bài viết"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}"
     data-feed-current-status="{{ $currentStatus ?? '' }}"
     data-feed-counts='@json($statusCounts ?? [])'>

    <!-- Posts Moderation Table (High Information Density) -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ap-table" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <colgroup>
                    <col class="col-post">
                    <col class="col-author">
                    <col class="col-cat">
                    <col class="col-status">
                    <col class="col-interact">
                    <col class="col-actions">
                </colgroup>
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-post text-start" style="font-size: 11px;">Bài viết</th>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-author text-start" style="font-size: 11px;">Tác giả</th>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-cat text-center" style="font-size: 11px;">Chuyên mục</th>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-status text-center" style="font-size: 11px;">Trạng thái</th>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-interact text-center" style="font-size: 11px;">Tương tác</th>
                        <th class="ap-col py-3 label-uppercase text-theme-muted col-actions text-center" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($posts as $post)
                        <tr>
                            <!-- Post Title & Thumbnail -->
                            <td class="ap-col py-3 col-post text-start">
                                <div class="ap-post-wrap">
                                    <div class="ap-thumb {{ $post->thumbnail ? '' : 'is-fallback' }} flex-shrink-0">
                                        @if($post->thumbnail)
                                            <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="rounded-2 border border-theme shadow-xs" loading="lazy" onerror="this.parentElement.classList.add('is-fallback');">
                                        @endif
                                        <span class="ap-thumb__placeholder rounded-2 bg-surface-2 text-theme-muted border border-theme fw-bold" aria-hidden="true">B</span>
                                    </div>
                                    <div class="ap-post-text">
                                        <a href="{{ route('admin.posts.show', $post) }}" class="fw-semibold text-theme text-decoration-none hover-accent ap-post-title d-block" style="font-size: 13px;" title="{{ $post->title }}">
                                            {{ $post->title }}
                                        </a>
                                        <div class="small text-theme-muted mt-0.5 ap-post-date" style="font-size: 11px;">
                                            {{ $post->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Author (Unified Single-Line Avatar + Name) -->
                            <td class="ap-col py-3 col-author text-start">
                                <div class="ap-author-wrap">
                                    <div class="flex-shrink-0">
                                        <x-avatar :user="$post->user" size="xs" />
                                    </div>
                                    <div class="ap-author-text">
                                        <a href="{{ route('admin.users.show', $post->user) }}" class="fw-medium text-theme text-decoration-none hover-accent ap-author-name text-truncate d-block" style="font-size: 12.5px;" title="{{ $post->user->name }}">
                                            {{ $post->user->name }}
                                        </a>
                                        <span class="ap-author-email text-truncate d-block" title="{{ $post->user->email }}">{{ $post->user->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="ap-col py-3 col-cat text-center">
                                <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill fw-medium ap-badge" style="font-size: 11px;" title="{{ $post->category->name }}">
                                    {{ $post->category->name }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="ap-col py-3 col-status text-center">
                                @if($post->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill ap-badge" style="font-size: 11px;">
                                        Đã xuất bản
                                    </span>
                                @elseif($post->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill ap-badge" style="font-size: 11px;">
                                        Chờ duyệt
                                    </span>
                                @elseif($post->status === 'draft')
                                    <span class="badge bg-theme-surface border border-theme text-theme-muted rounded-pill ap-badge" style="font-size: 11px;">
                                        Bản nháp
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill ap-badge" style="font-size: 11px;">
                                        Bị từ chối
                                    </span>
                                @endif
                            </td>

                            <!-- Interactions (Centered Vertical Stack) -->
                            <td class="ap-col py-3 col-interact text-center">
                                <div class="ap-interact text-theme-secondary">
                                    <span>{{ number_format($post->views) }} views</span>
                                    <span>{{ number_format($post->likers_count) }} likes</span>
                                    <span>{{ number_format($post->comments_count) }} cmt</span>
                                </div>
                            </td>

                            <!-- Moderation Actions -->
                            <td class="ap-col py-3 col-actions text-center">
                                <div class="ap-actions-wrap">
                                    @if($post->status === 'pending')
                                        <!-- Approve Form -->
                                        <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="d-inline m-0"
                                              data-confirm="true"
                                              data-confirm-title="Duyệt bài viết"
                                              data-confirm-message="Bạn có chắc chắn muốn duyệt bài viết này?"
                                              data-confirm-target-name="{{ $post->title }}"
                                              data-confirm-target-meta="Tác giả: {{ $post->user->name }} &bull; Chuyên mục: {{ $post->category->name }}"
                                              data-confirm-description="Bài viết sẽ được chuyển sang trạng thái 'Đã xuất bản' và hiển thị công khai trên bảng tin cộng đồng CloudBlog."
                                              data-confirm-btn-text="Duyệt & Xuất bản"
                                              data-confirm-btn-class="btn-success"
                                              data-confirm-type="success">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success admin-post-action--approve rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Duyệt bài viết {{ $post->title }}">
                                                Duyệt
                                            </button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-inline m-0"
                                              data-confirm="true"
                                              data-confirm-title="Từ chối bài viết"
                                              data-confirm-message="Từ chối duyệt bài viết của tác giả {{ $post->user->name }}?"
                                              data-confirm-target-name="{{ $post->title }}"
                                              data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                                              data-confirm-description="Vui lòng nêu rõ lý do để tác giả có thể chỉnh sửa lại bài viết."
                                              data-confirm-prompt="true"
                                              data-confirm-prompt-name="rejection_reason"
                                              data-confirm-prompt-label="Lý do từ chối:"
                                              data-confirm-prompt-placeholder="Nhập lý do từ chối bài viết..."
                                              data-confirm-btn-text="Từ chối bài viết"
                                              data-confirm-btn-class="btn-warning"
                                              data-confirm-type="warning">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" value="">
                                            <button type="submit" class="btn btn-sm btn-outline-warning admin-post-action--reject rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Từ chối bài viết {{ $post->title }}">
                                                Từ chối
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;" aria-label="Xem chi tiết bài viết {{ $post->title }}">
                                        Chi tiết
                                    </a>

                                    <!-- Delete Action -->
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa bài viết"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này?"
                                          data-confirm-target-name="{{ $post->title }}"
                                          data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                                          data-confirm-description="Hành động này không thể hoàn tác. Toàn bộ nội dung và bình luận liên quan sẽ bị xóa vĩnh viễn."
                                          data-confirm-btn-text="Xóa bài viết"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-circle" title="Xóa bài viết" aria-label="Xóa bài viết">
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
                                    title="Không tìm thấy bài viết"
                                    description="Không có bài viết nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm hiện tại."
                                    :action-url="route('admin.posts.index')"
                                    action-label="Đặt lại bộ lọc"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div data-feed-pagination class="px-3 px-sm-4 py-3 border-theme-top bg-theme-surface d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
