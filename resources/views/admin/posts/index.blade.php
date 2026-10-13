@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt & Quản lý Bài viết')

@section('admin_content')
<!-- Status Tabs (Professional Pill Segment Filter) -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-4 pb-1">
    <a
        href="{{ route('admin.posts.index') }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-secondary' }}"
        style="font-size: 12.5px;"
    >
        Tất cả <span class="opacity-75 ms-1">({{ number_format($statusCounts['all']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
        style="font-size: 12.5px;"
    >
        Chờ duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['pending']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'published']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'published' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
        style="font-size: 12.5px;"
    >
        Đã xuất bản <span class="opacity-75 ms-1">({{ number_format($statusCounts['published']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'draft' ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-muted' }}"
        style="font-size: 12.5px;"
    >
        Bản nháp <span class="opacity-75 ms-1">({{ number_format($statusCounts['draft']) }})</span>
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'rejected']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'rejected' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
        style="font-size: 12.5px;"
    >
        Bị từ chối <span class="opacity-75 ms-1">({{ number_format($statusCounts['rejected']) }})</span>
    </a>
</div>

<!-- Search & Category Filters (High Density Toolbar) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2.5 align-items-center">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="col-12 col-md-6">
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
        type="text"
        name="search"
        value="{{ request('search') }}"
        class="form-control bg-surface-2 border-theme text-theme rounded-3 w-100"
        placeholder="Tìm theo tên hoặc email người dùng..."
        style="
            min-height: 46px;
            padding-left: 46px;
            padding-right: 16px;
        "
    >
</div>
        </div>

        <div class="col-12 col-sm-8 col-md-4">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="form-select form-select-sm form-control-editorial rounded-pill"
                style="font-size: 13px;"
            >
                <option value="">-- Tất cả chuyên mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($currentCategory == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-sm-4 col-md-2">
            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill w-100 fw-semibold" style="font-size: 12.5px;">
                Lọc kết quả
            </button>
        </div>
    </form>
</div>

{{-- Scoped styles: Admin Posts Table column layout --}}
<style>
.ap-table {
    width: 100%;
    table-layout: fixed;
}
.ap-table th,
.ap-table td {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
    vertical-align: middle;
}
.ap-table th {
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Desktop column proportions (>= 992px) */
.ap-table .col-post    { width: 31%; }
.ap-table .col-author  { width: 16%; }
.ap-table .col-cat     { width: 11%; }
.ap-table .col-status  { width: 11%; }
.ap-table .col-interact{ width: 10%; }
.ap-table .col-actions { width: 21%; }

/* Column text alignments */
.ap-table td.col-post     { text-align: left; }
.ap-table th.col-author,
.ap-table td.col-author   { text-align: left; }
.ap-table td.col-cat      { text-align: center; }
.ap-table td.col-status   { text-align: center; }
.ap-table td.col-interact { text-align: center; }
.ap-table td.col-actions  { text-align: center; }

/* Truncation & text safety */
.ap-post-title {
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.ap-author-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 100%;
}
.ap-badge {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    vertical-align: middle;
    padding: 0.25rem 0.5rem;
}

/* Interactions – centered vertical stack */
.ap-interact {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    line-height: 1.45;
}
.ap-interact span {
    font-size: 11px;
    white-space: nowrap;
}

/* Actions */
.ap-actions-wrap {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.ap-actions-wrap .btn {
    padding: 0.2rem 0.5rem;
    font-size: 11px;
}

/* Tablet refinement (576px - 991px) */
@media (min-width: 576px) and (max-width: 991.98px) {
    .ap-table { min-width: 720px; }
    .ap-table .col-post    { width: 30%; }
    .ap-table .col-author  { width: 16%; }
    .ap-table .col-cat     { width: 11%; }
    .ap-table .col-status  { width: 11%; }
    .ap-table .col-interact{ width: 10%; }
    .ap-table .col-actions { width: 22%; }
}

/* Mobile card stacking (< 576px) */
@media (max-width: 575.98px) {
    .ap-table,
    .ap-table tbody,
    .ap-table tr,
    .ap-table td {
        display: block !important;
        width: 100% !important;
    }
    .ap-table thead,
    .ap-table colgroup {
        display: none !important;
    }
    .ap-table tr {
        padding: 0.875rem 1rem !important;
        border-bottom: 1px solid var(--color-border) !important;
    }
    .ap-table td {
        padding: 0.25rem 0 !important;
        border: none !important;
    }
    .ap-table td.col-actions {
        margin-top: 0.5rem;
        padding-top: 0.5rem !important;
        border-top: 1px dashed var(--color-border) !important;
        text-align: center;
    }
    .ap-table .ap-actions-wrap {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }
    .ap-table .ap-interact {
        flex-direction: row;
        gap: 0.75rem;
    }
}
</style>

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
                    <th class="ap-col py-3 label-uppercase text-theme-muted col-post text-center" style="font-size: 11px;">Bài viết</th>
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
                        <!-- Post Info with Thumbnail -->
                        <td class="py-3 col-post" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-3" style="min-width: 0;">
                                <div class="rounded-2 overflow-hidden bg-surface-2 flex-shrink-0 border border-theme" style="width: 48px; height: 38px;">
                                    @if($post->thumbnail)
                                        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-theme-muted">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div style="min-width: 0; flex: 1 1 0;">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="ap-post-title fw-semibold text-theme text-decoration-none hover-accent small" style="font-size: 13.5px;" title="{{ $post->title }}">
                                        {{ $post->title }}
                                    </a>
                                    <span class="small text-theme-muted d-block" style="font-size: 11.5px;">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Author -->
                        <td class="py-3 col-author" style="min-width: 0;">
                            <a href="{{ route('admin.users.show', $post->user) }}" class="ap-author-name small fw-semibold text-theme text-decoration-none hover-accent" style="font-size: 13px;" title="{{ $post->user->name }}">
                                {{ $post->user->name }}
                            </a>
                        </td>

                        <!-- Category -->
                        <td class="py-3 col-cat text-center">
                            <span class="badge ap-badge bg-surface-2 border border-theme text-theme-secondary rounded-pill" style="font-size: 11px;">
                                {{ $post->category->name }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3 col-status text-center">
                            @if($post->status === 'published')
                                <span class="badge ap-badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Đã xuất bản
                                </span>
                            @elseif($post->status === 'pending')
                                <span class="badge ap-badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Chờ duyệt
                                </span>
                            @elseif($post->status === 'rejected')
                                <span class="badge ap-badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Bị từ chối
                                </span>
                            @else
                                <span class="badge ap-badge bg-surface-2 text-theme-muted border border-theme rounded-pill" style="font-size: 11px;">
                                    Bản nháp
                                </span>
                            @endif
                        </td>

                        <!-- Interactions -->
                        <td class="py-3 col-interact text-center text-theme-muted">
                            <div class="ap-interact">
                                <span title="Lượt xem">{{ number_format($post->views) }} <span class="opacity-60">views</span></span>
                                <span title="Lượt thích">{{ number_format($post->likers_count) }} <span class="opacity-60">likes</span></span>
                                <span title="Bình luận">{{ number_format($post->comments_count) }} <span class="opacity-60">cmt</span></span>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="py-3 col-actions text-center">
                            <div class="ap-actions-wrap">
                                @if($post->status === 'pending')
                                    <!-- Approve Action -->
                                    <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Duyệt bài viết"
                                          data-confirm-message="Bạn có chắc chắn muốn duyệt và xuất bản bài viết này?"
                                          data-confirm-target-name="{{ $post->title }}"
                                          data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                                          data-confirm-description="Bài viết sẽ được chuyển sang trạng thái đã xuất bản và hiển thị công khai tới độc giả."
                                          data-confirm-btn-text="Duyệt bài viết"
                                          data-confirm-btn-class="btn-success"
                                          data-confirm-type="success">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Duyệt bài viết {{ $post->title }}">
                                            Duyệt
                                        </button>
                                    </form>

                                    <!-- Reject Action -->
                                    <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Từ chối bài viết"
                                          data-confirm-message="Vui lòng cung cấp lý do từ chối bài viết này."
                                          data-confirm-target-name="{{ $post->title }}"
                                          data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                                          data-confirm-description="Tác giả sẽ nhận được thông báo về lý do bài viết chưa được duyệt để chỉnh sửa lại."
                                          data-confirm-prompt="true"
                                          data-confirm-prompt-name="rejection_reason"
                                          data-confirm-prompt-label="Lý do từ chối:"
                                          data-confirm-prompt-placeholder="Nhập lý do từ chối bài viết..."
                                          data-confirm-btn-text="Từ chối bài viết"
                                          data-confirm-btn-class="btn-warning"
                                          data-confirm-type="warning">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" value="">
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Từ chối bài viết {{ $post->title }}">
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
        <div class="px-3 px-sm-4 py-3 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection

