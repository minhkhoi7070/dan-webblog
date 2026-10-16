@extends('layouts.admin')

@section('admin_title', 'Quản lý Thẻ bài viết (Tags)')

@section('admin_content')
<!-- Header Action Bar & Search -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <form action="{{ route('admin.tags.index') }}" method="GET" class="flex-grow-1" style="max-width: 440px;">
        <div class="position-relative w-100">
            <span
                class="position-absolute d-flex align-items-center justify-content-center text-theme-muted"
                style="
                    left: 16px;
                    top: 50%;
                    width: 18px;
                    height: 18px;
                    transform: translateY(-50%);
                    pointer-events: none;
                    z-index: 2;
                "
                aria-hidden="true"
            >
                <svg
                    width="16"
                    height="16"
                    style="display: block;"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </span>

            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm thẻ theo tên hoặc slug..."
                class="form-control bg-surface-2 border-theme text-theme rounded-3 w-100"
                style="
                    min-height: 46px;
                    padding-left: 46px;
                    padding-right: 16px;
                "
            >
        </div>
    </form>

    <a href="{{ route('admin.tags.create') }}" class="btn btn-editorial-primary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 align-self-start align-self-sm-auto shadow-xs">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Thêm thẻ mới
    </a>
</div>

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
        <div class="p-3 border-theme-top bg-surface-2 d-flex justify-content-center">
            {{ $tags->links() }}
        </div>
    @endif
</div>
@endsection

