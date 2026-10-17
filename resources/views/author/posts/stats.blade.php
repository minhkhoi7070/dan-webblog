@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">
    <!-- Top Dashboard Header (Ghost + Notion style) -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 small text-theme-muted label-uppercase mb-0.5" style="font-size: 11px;">
                <span>Tác giả</span>
                <span>&rsaquo;</span>
                <span class="text-accent">Bảng điều khiển</span>
            </div>
            <h1 class="h4 fw-bold text-theme tracking-tight mb-0 dashboard-title">
                Quản lý bài viết & Thống kê
            </h1>
        </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0 w-100 w-md-auto justify-content-start justify-content-md-end">
            <a href="{{ route('posts.create') }}" class="btn btn-editorial-primary rounded-pill px-3.5 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-2 dashboard-create-btn">
                <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Soạn bài viết mới</span>
            </a>
        </div>
    </div>

    <!-- Success Feedback Message -->
    @if(session('success'))
        <div class="alert alert-success mb-4 shadow-xs border border-success border-opacity-25 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="small fw-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics Grid (All 8 Metrics - Notion & Ghost Style) -->
    <div class="row g-2 g-sm-3 mb-4">
        <!-- 1. Total Posts -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats') }}" class="dashboard-stat-card h-100 {{ empty($currentStatus) ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-3xs">Tổng bài viết</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['total']) }}</p>
                <span class="dashboard-stat-desc">Tất cả bài</span>
            </a>
        </div>

        <!-- 2. Draft -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'draft' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bản nháp</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['draft']) }}</p>
                <span class="dashboard-stat-desc">Đang soạn</span>
            </a>
        </div>

        <!-- 3. Pending -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'pending' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-warning text-3xs">Chờ duyệt</span>
                <p class="h4 fw-bold text-warning m-0">{{ number_format($stats['pending']) }}</p>
                <span class="dashboard-stat-desc">Đang xét duyệt</span>
            </a>
        </div>

        <!-- 4. Published -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'published' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-success text-3xs">Xuất bản</span>
                <p class="h4 fw-bold m-0 text-success">{{ number_format($stats['published']) }}</p>
                <span class="dashboard-stat-desc">Công khai</span>
            </a>
        </div>

        <!-- 5. Rejected -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'rejected' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-danger text-3xs">Bị từ chối</span>
                <p class="h4 fw-bold text-danger m-0">{{ number_format($stats['rejected']) }}</p>
                <span class="dashboard-stat-desc">Cần chỉnh sửa</span>
            </a>
        </div>

        <!-- 6. Views -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs">Lượt xem</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-accent-text);">{{ number_format($stats['views']) }}</p>
                <span class="dashboard-stat-desc">Tổng tương tác</span>
            </div>
        </div>

        <!-- 7. Likes -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs" style="color: var(--color-like);">Lượt thích</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-like);">{{ number_format($stats['likes']) }}</p>
                <span class="dashboard-stat-desc">Từ độc giả</span>
            </div>
        </div>

        <!-- 8. Comments -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bình luận</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['comments']) }}</p>
                <span class="dashboard-stat-desc">Thảo luận</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search Controls (Notion style Database toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- Filter Tabs -->
            <div class="d-flex align-items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                <a href="{{ route('posts.stats', array_filter(['q' => $search])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Tất cả ({{ $stats['total'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'draft', 'q' => $search])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'draft' ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Bản nháp ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'pending', 'q' => $search])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-theme' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Chờ duyệt ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'published', 'q' => $search])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'published' ? 'btn-editorial-primary' : 'btn-outline-theme' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Đã xuất bản ({{ $stats['published'] }})
                </a>
                <a href="{{ route('posts.stats', array_filter(['status' => 'rejected', 'q' => $search])) }}"
                   class="btn btn-sm rounded-pill px-3 fw-medium d-inline-flex align-items-center text-nowrap {{ $currentStatus === 'rejected' ? 'btn-outline-danger' : 'btn-outline-theme' }}"
                   style="height: 34px; font-size: 12.5px;">
                    Bị từ chối ({{ $stats['rejected'] }})
                </a>
            </div>

            <!-- Search Form with vertically centered icon & clear button -->
            <form action="{{ route('posts.stats') }}" method="GET" class="position-relative m-0 w-100" style="max-width: 320px;">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <div class="position-relative d-flex align-items-center w-100">
                    <!-- Magnifying glass icon: centered vertically with generous padding-left -->
                    <span class="dashboard-search-icon position-absolute d-flex align-items-center pointer-events-none" style="left: 13px; top: 50%; transform: translateY(-50%); z-index: 3;" aria-hidden="true">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>

                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Tìm theo tiêu đề bài viết..."
                        class="form-control form-control-sm form-control-editorial dashboard-search-input rounded-pill"
                        autocomplete="off"
                        aria-label="Tìm theo tiêu đề bài viết"
                    >

                    @if($search)
                        <!-- Clear Search Button -->
                        <a href="{{ route('posts.stats', $currentStatus ? ['status' => $currentStatus] : []) }}"
                           class="position-absolute end-0 top-50 translate-middle-y pe-2.5 d-flex align-items-center text-theme-muted hover-accent text-decoration-none"
                           style="z-index: 3;"
                           title="Xóa từ khóa tìm kiếm"
                           aria-label="Xóa từ khóa tìm kiếm">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Posts Database Table (Notion / Ghost style) -->
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
                                        <div class="rounded-3 bg-surface-2 text-theme-muted d-flex align-items-center justify-content-center flex-shrink-0 border border-theme fw-bold" style="width: 46px; height: 46px; font-size: 11px; font-family: var(--font-serif, 'Lora', serif);">
                                            B
                                        </div>
                                    @endif
                                    <div class="min-w-0" style="max-width: 420px;">
                                        <a href="{{ route('posts.edit', $post) }}" class="dashboard-post-title text-truncate d-block" title="{{ $post->title }}">
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
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-action-table btn-action-edit text-decoration-none" title="Chỉnh sửa bài viết" aria-label="Chỉnh sửa bài viết {{ $post->title }}">
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
                                        <p class="small text-theme-muted mb-3">Bắt đầu sáng tạo bài viết đầu tiên của bạn trên nền tảng BlogMNM</p>
                                        <a href="{{ route('posts.create') }}" class="btn btn-editorial-primary btn-sm rounded-pill px-4">
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
        <div class="d-flex justify-content-center pt-2">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
