<div id="feed-results" tabindex="-1" aria-label="Bảng thẻ bài viết"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $tags->total() }}">

    <!-- Tags Table -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Tên Thẻ (Tag)</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Đường dẫn tĩnh (Slug)</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Số bài viết</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Ngày tạo</th>
                        <th class="pe-3 pe-sm-4 py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($tags as $tag)
                        <tr>
                            <td class="ps-3 ps-sm-4 py-3 text-start fw-semibold text-theme" style="font-size: 13.5px;">
                                <span class="badge rounded-pill bg-theme-surface-hover text-theme border border-theme px-2.5 py-1 font-monospace" style="font-size: 12px;">
                                    #{{ $tag->name }}
                                </span>
                            </td>
                            <td class="py-3 text-start font-monospace small text-theme-muted" style="font-size: 12px;">
                                {{ $tag->slug }}
                            </td>
                            <td class="py-3 text-start fw-semibold text-theme small" style="font-size: 13px;">
                                {{ number_format($tag->posts_count) }} bài
                            </td>
                            <td class="py-3 text-start small text-theme-muted" style="font-size: 12px;">
                                {{ $tag->created_at->format('d/m/Y') }}
                            </td>
                            <td class="pe-3 pe-sm-4 py-3 text-center">
                                <div class="d-inline-flex gap-1.5">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;" aria-label="Chỉnh sửa thẻ {{ $tag->name }}">
                                        Sửa
                                    </a>

                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa thẻ bài viết"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn thẻ này không?"
                                          data-confirm-target-name="#{{ $tag->name }}"
                                          data-confirm-target-meta="Slug: {{ $tag->slug }} &bull; {{ $tag->posts_count }} bài viết đang gắn thẻ này"
                                          data-confirm-description="Hành động này sẽ gỡ liên kết thẻ khỏi tất cả bài viết liên quan. Bài viết sẽ không bị xóa."
                                          data-confirm-btn-text="Xóa thẻ"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 11px;" aria-label="Xóa thẻ {{ $tag->name }}">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-theme-muted">
                                <svg style="width: 36px; height: 36px;" class="mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
                                <p class="mb-1 fw-medium text-theme">Không tìm thấy thẻ bài viết nào</p>
                                <span class="small">{{ $search ? 'Thử tìm kiếm với từ khóa khác.' : 'Chưa có thẻ bài viết nào trong hệ thống.' }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tags->hasPages())
            <div data-feed-pagination class="p-3 border-theme-top bg-surface-2 d-flex justify-content-center">
                {{ $tags->links() }}
            </div>
        @endif
    </div>
</div>
