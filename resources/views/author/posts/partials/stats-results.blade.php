<div id="feed-results" tabindex="-1" aria-label="Danh sách bài viết của tác giả"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $posts->total() }}">

    <!-- Posts Table -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                        <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Chuyên mục</th>
                        <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                        <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Tương tác</th>
                        <th class="pe-3 pe-sm-4 py-3 text-end label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($posts as $post)
                        <tr>
                            <!-- Title, Thumbnail & Meta -->
                            <td class="ps-3 ps-sm-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($post->thumbnail)
                                        <img src="{{ asset($post->thumbnail) }}" alt="" class="rounded-3 flex-shrink-0 border border-theme shadow-xs" style="width: 46px; height: 46px; object-fit: cover;">
                                    @else
                                        <div class="rounded-3 bg-surface-2 text-theme-muted d-flex align-items-center justify-content-center flex-shrink-0 border border-theme fw-bold" style="width: 46px; height: 46px; font-size: 11px;">
                                            B
                                        </div>
                                    @endif
                                    <div class="min-w-0" style="max-width: 420px;">
                                        <a href="{{ route('posts.edit', $post) }}" data-app-nav class="dashboard-post-title text-truncate d-block" title="{{ $post->title }}">
                                            {{ $post->title }}
                                        </a>
                                        <div class="dashboard-post-timestamp mt-1">
                                            Cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($post->status === 'rejected' && $post->rejection_reason)
                                            <div class="text-danger small mt-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                                <svg style="width: 12px; height: 12px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span class="text-truncate" title="{{ $post->rejection_reason }}"><strong>Lý do từ chối:</strong> {{ Str::limit($post->rejection_reason, 55) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3">
                                <span class="badge bg-surface-2 text-theme-secondary border border-theme rounded-pill fw-medium" style="font-size: 11.5px;">
                                    {{ $post->category->name }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3">
                                @if($post->status === 'published')
                                    <x-badge variant="status-published" size="xs">
                                        Đã xuất bản
                                    </x-badge>
                                @elseif($post->status === 'pending')
                                    <x-badge variant="status-pending" size="xs">
                                        Chờ duyệt
                                    </x-badge>
                                @elseif($post->status === 'rejected')
                                    <x-badge variant="status-rejected" size="xs">
                                        Bị từ chối
                                    </x-badge>
                                @else
                                    <x-badge variant="status-draft" size="xs">
                                        Bản nháp
                                    </x-badge>
                                @endif
                            </td>

                            <!-- Metrics -->
                            <td class="py-3 small text-theme-secondary">
                                <div class="d-flex align-items-center gap-2" style="font-size: 12px;">
                                    <span>{{ number_format($post->views) }} views</span>
                                    <span class="opacity-50">&bull;</span>
                                    <span>{{ $post->likers_count }} likes</span>
                                    <span class="opacity-50">&bull;</span>
                                    <span>{{ $post->comments_count }} cmt</span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="pe-3 pe-sm-4 py-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap flex-sm-nowrap">
                                    <!-- Submit for review button (Available if draft or rejected) -->
                                    @if(in_array($post->status, ['draft', 'rejected'], true))
                                        <form action="{{ route('posts.submit', $post) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-action-table btn-action-submit" title="Gửi bài viết để Admin duyệt" aria-label="Gửi xét duyệt bài viết {{ $post->title }}">
                                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>Gửi duyệt</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- View Live (if published) -->
                                    @if($post->status === 'published')
                                        <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noopener noreferrer" class="btn btn-action-table btn-action-live text-decoration-none" title="Xem bài viết trực tiếp" aria-label="Xem bài viết trực tiếp {{ $post->title }}">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <span>Live</span>
                                        </a>
                                    @endif

                                    <!-- Edit Link -->
                                    <a href="{{ route('posts.edit', $post) }}" data-app-nav class="btn btn-action-table btn-action-edit text-decoration-none" title="Chỉnh sửa bài viết" aria-label="Chỉnh sửa bài viết {{ $post->title }}">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Sửa</span>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa bài viết"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa bài viết này không?"
                                          data-confirm-target-name="{{ $post->title }}"
                                          data-confirm-target-meta="Trạng thái: {{ $post->status_label }}"
                                          data-confirm-description="Hành động này không thể hoàn tác. Bản nháp hoặc bài viết này sẽ bị xóa hoàn toàn khỏi hệ thống."
                                          data-confirm-btn-text="Xóa bài viết"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action-table btn-action-delete" title="Xóa bài viết" aria-label="Xóa bài viết {{ $post->title }}">
                                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-theme-secondary small">
                                <div class="py-4">
                                    @if($search)
                                        <div class="mb-2 text-theme-muted">
                                            <svg style="width: 36px; height: 36px; opacity: 0.6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-theme fw-semibold mb-1" style="font-size: 15px;">Không tìm thấy bài viết phù hợp</p>
                                        <p class="small text-theme-muted mb-3" style="max-width: 420px; margin-left: auto; margin-right: auto;">
                                            Không có bài viết nào khớp với từ khóa "<strong>{{ $search }}</strong>"@if($currentStatus) trong mục <strong>{{ $currentStatus === 'draft' ? 'Bản nháp' : ($currentStatus === 'pending' ? 'Chờ duyệt' : ($currentStatus === 'published' ? 'Đã xuất bản' : 'Bị từ chối')) }}</strong>@endif.
                                        </p>
                                        <a href="{{ route('posts.stats', $currentStatus ? ['status' => $currentStatus] : []) }}" class="btn btn-sm btn-outline-theme rounded-pill px-3.5 py-1.5 fw-medium">
                                            Xóa từ khóa tìm kiếm
                                        </a>
                                    @else
                                        <p class="text-theme fw-medium mb-1">Không tìm thấy bài viết nào</p>
                                        <p class="small text-theme-muted mb-3">Bắt đầu sáng tạo bài viết đầu tiên của bạn trên nền tảng CloudBlog</p>
                                        <a href="{{ route('posts.create') }}" data-app-nav class="btn btn-editorial-primary btn-sm rounded-pill px-4">
                                            Soạn bài viết mới
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div data-feed-pagination class="d-flex justify-content-center pt-2">
            {{ $posts->links() }}
        </div>
    @endif
</div>
