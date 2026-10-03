@extends('layouts.admin')

@section('admin_title', 'Quản lý Chuyên mục')

@section('admin_content')
<!-- Header Action Bar & Search -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex-grow-1" style="max-width: 440px;">
        <div class="position-relative">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm chuyên mục theo tên hoặc slug..."
                class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1.5"
                style="font-size: 13px;"
            >
            <svg style="width: 14px; height: 14px; position: absolute; left: 12px; top: 10px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </form>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-editorial-primary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 align-self-start align-self-sm-auto shadow-xs">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Thêm chuyên mục mới
    </a>
</div>

<!-- Categories Table (High Information Density) -->
<div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
            <thead class="border-theme-bottom bg-surface-2">
                <tr>
                    <th class="ps-3 ps-sm-4 py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Tên Chuyên mục</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Đường dẫn tĩnh (Slug)</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Số bài viết</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Ngày tạo</th>
                    <th class="pe-3 pe-sm-4 py-3 text-end label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-theme">
                @forelse($categories as $category)
                    <tr>
                        <td class="ps-3 ps-sm-4 py-3 fw-semibold text-theme" style="font-size: 13.5px;">
                            {{ $category->name }}
                        </td>
                        <td class="py-3 font-monospace small text-theme-muted" style="font-size: 12px;">
                            {{ $category->slug }}
                        </td>
                        <td class="py-3 fw-semibold text-theme small" style="font-size: 13px;">
                            {{ number_format($category->posts_count) }} bài
                        </td>
                        <td class="py-3 small text-theme-muted" style="font-size: 12px;">
                            {{ $category->created_at->format('d/m/Y') }}
                        </td>
                        <td class="pe-3 pe-sm-4 py-3 text-end">
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

    @if($categories->hasPages())
        <div class="px-4 py-3 border-theme-top d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
