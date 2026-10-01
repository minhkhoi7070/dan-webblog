@extends('layouts.admin')

@section('admin_title', 'Quản lý Chuyên mục (Categories)')

@section('admin_content')
<!-- Header Action Bar & Search -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex-grow-1" style="max-width: 450px;">
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm chuyên mục theo tên hoặc slug..."
                class="form-control border-start-0 ps-0"
            >
        </div>
    </form>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-bold small d-inline-flex align-items-center gap-2 align-self-start align-self-sm-auto shadow-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Thêm chuyên mục mới
    </a>
</div>

<!-- Categories Table -->
<div class="card border shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-bold text-secondary">
                <tr>
                    <th class="px-4 py-3">Tên Chuyên mục</th>
                    <th class="px-4 py-3">Slug URL</th>
                    <th class="px-4 py-3">Số bài viết</th>
                    <th class="px-4 py-3">Ngày tạo</th>
                    <th class="px-4 py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-body">
                            {{ $category->name }}
                        </td>
                        <td class="px-4 py-3 font-monospace small text-secondary">
                            {{ $category->slug }}
                        </td>
                        <td class="px-4 py-3 fw-semibold text-body">
                            {{ number_format($category->posts_count) }}
                        </td>
                        <td class="px-4 py-3 small text-secondary">
                            {{ $category->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    Sửa
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
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
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-5 text-center text-secondary small">
                            Không tìm thấy chuyên mục nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
