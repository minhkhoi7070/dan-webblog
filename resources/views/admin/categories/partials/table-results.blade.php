<div id="feed-results" tabindex="-1" aria-label="Bảng chuyên mục"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $categories->total() }}">

    <!-- Categories Table & Mobile Card View Container -->
    <div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
        <!-- Desktop Table View (>= 768px Tablet & Desktop) -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
                <thead class="border-theme-bottom bg-surface-2">
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Tên Chuyên mục</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Đường dẫn tĩnh</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Số bài viết</th>
                        <th class="py-3 text-start label-uppercase text-theme-muted" style="font-size: 11px;">Ngày tạo</th>
                        <th class="pe-3 pe-sm-4 py-3 text-center label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme">
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-3 ps-sm-4 py-3 text-start fw-semibold text-theme" style="font-size: 13.5px;">
                                {{ $category->name }}
                            </td>
                            <td class="py-3 text-start font-monospace small text-theme-muted" style="font-size: 12px;">
                                {{ $category->slug }}
                            </td>
                            <td class="py-3 text-start fw-semibold text-theme small" style="font-size: 13px;">
                                {{ number_format($category->posts_count) }} bài
                            </td>
                            <td class="py-3 text-start small text-theme-muted" style="font-size: 12px;">
                                {{ $category->created_at->format('d/m/Y') }}
                            </td>
                            <td class="pe-3 pe-sm-4 py-3 text-center">
                                <div class="d-inline-flex gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;" aria-label="Chỉnh sửa chuyên mục {{ $category->name }}">
                                        Sửa
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa chuyên mục"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn chuyên mục này?"
                                          data-confirm-target-name="{{ $category->name }}"
                                          data-confirm-target-meta="Slug: {{ $category->slug }} &bull; {{ $category->posts_count }} bài viết"
                                          data-confirm-description="Hành động này không thể hoàn tác. Các bài viết thuộc chuyên mục này có thể bị mất liên kết phân loại."
                                          data-confirm-btn-text="Xóa chuyên mục"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 11px;" aria-label="Xóa chuyên mục {{ $category->name }}">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-theme-muted small">
                                Không tìm thấy chuyên mục nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (< 768px: Mobile-First Responsive Cards) -->
        <div class="d-block d-md-none divide-y divide-theme">
            @forelse($categories as $category)
                <div class="p-3">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                        <span class="fw-bold text-theme" style="font-size: 14.5px;">{{ $category->name }}</span>
                        <span class="badge bg-surface-2 text-theme-secondary border border-theme rounded-pill" style="font-size: 11px;">
                            {{ number_format($category->posts_count) }} bài viết
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 text-theme-muted small" style="font-size: 12px;">
                        <span class="font-monospace text-truncate">/{{ $category->slug }}</span>
                        <span class="flex-shrink-0">{{ $category->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-theme-top">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1" style="font-size: 11.5px;">
                            Chỉnh sửa
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline m-0"
                              data-confirm="true"
                              data-confirm-title="Xóa chuyên mục"
                              data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn chuyên mục này?"
                              data-confirm-target-name="{{ $category->name }}"
                              data-confirm-target-meta="Slug: {{ $category->slug }} &bull; {{ $category->posts_count }} bài viết"
                              data-confirm-description="Hành động này không thể hoàn tác."
                              data-confirm-btn-text="Xóa chuyên mục"
                              data-confirm-btn-class="btn-danger"
                              data-confirm-type="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size: 11.5px;">
                                Xóa
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-5 text-center text-theme-muted small">
                    Không tìm thấy chuyên mục nào phù hợp.
                </div>
            @endforelse
        </div>
    </div>

        @if($categories->hasPages())
            <div data-feed-pagination class="px-4 py-3 border-theme-top d-flex justify-content-center">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
