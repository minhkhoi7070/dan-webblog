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
            <div class="position-relative">
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm bài viết theo tiêu đề..."
                    class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1.5"
                    style="font-size: 13px;"
                >
                <svg style="width: 14px; height: 14px; position: absolute; left: 12px; top: 10px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
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

<!-- Posts Moderation Table (High Information Density) -->
<div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
            <thead class="border-theme-bottom bg-surface-2">
                <tr>
                    <th class="ps-3 ps-sm-4 py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Tác giả</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Chuyên mục</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Tương tác</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Người duyệt</th>
                    <th class="pe-3 pe-sm-4 py-3 text-end label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-theme">
                @forelse($posts as $post)
                    <tr>
                        <!-- Post Info with Thumbnail -->
                        <td class="ps-3 ps-sm-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 overflow-hidden bg-surface-2 flex-shrink-0 border border-theme" style="width: 48px; height: 38px;">
                                    @if($post->thumbnail)
                                        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-theme-muted">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0" style="max-width: 280px;">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 13.5px;" title="{{ $post->title }}">
                                        {{ $post->title }}
                                    </a>
                                    <span class="small text-theme-muted" style="font-size: 11.5px;">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Author -->
                        <td class="py-3">
                            <a href="{{ route('admin.users.show', $post->user) }}" class="small fw-semibold text-theme text-decoration-none hover-accent d-block" style="font-size: 13px;">
                                {{ $post->user->name }}
                            </a>
                        </td>

                        <!-- Category -->
                        <td class="py-3">
                            <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill" style="font-size: 11px;">
                                {{ $post->category->name }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3">
                            @if($post->status === 'published')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Đã xuất bản
                                </span>
                            @elseif($post->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Chờ duyệt
                                </span>
                            @elseif($post->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Bị từ chối
                                </span>
                            @else
                                <span class="badge bg-surface-2 text-theme-muted border border-theme rounded-pill" style="font-size: 11px;">
                                    Bản nháp
                                </span>
                            @endif
                        </td>

                        <!-- Interactions -->
                        <td class="py-3 small text-theme-muted text-nowrap" style="font-size: 11.5px;">
                            <span title="Lượt xem">{{ number_format($post->views) }} views</span> &bull;
                            <span title="Lượt thích">{{ number_format($post->likers_count) }} likes</span> &bull;
                            <span title="Bình luận">{{ number_format($post->comments_count) }} cmt</span>
                        </td>

                        <!-- Reviewer Info -->
                        <td class="py-3 small text-theme-muted" style="font-size: 11.5px;">
                            @if($post->reviewer)
                                <span class="fw-medium text-theme d-block">{{ $post->reviewer->name }}</span>
                                <span class="text-theme-muted" style="font-size: 10.5px;">{{ $post->reviewed_at?->format('d/m H:i') }}</span>
                            @else
                                <span class="text-theme-muted">&mdash;</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="pe-3 pe-sm-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
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
                        <td colspan="7" class="p-0 border-0">
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

