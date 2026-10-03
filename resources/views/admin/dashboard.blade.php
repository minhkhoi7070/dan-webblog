@extends('layouts.admin')

@section('admin_title', 'Bảng điều khiển Quản trị')

@section('admin_content')
<!-- Dashboard Top Metrics Overview (High Information Density) -->
<div class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="label-uppercase text-theme-muted m-0" style="font-size: 11px;">Thống kê Tổng thể Hệ thống</h2>
        <span class="small text-theme-muted" style="font-size: 11.5px;">Dữ liệu thời gian thực</span>
    </div>

    <!-- 1. Primary Metrics Grid (Users, Traffic Views, Comments) -->
    <div class="row g-3 mb-4">
        <!-- Users Metric -->
        <div class="col-12 col-md-4">
            <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label-uppercase text-theme-secondary" style="font-size: 11px;">Tổng người dùng</span>
                    <span class="d-inline-flex p-1.5 rounded-2 bg-surface-2 border border-theme text-theme-muted">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-theme mt-2 mb-0 metric-number" style="font-family: var(--font-serif, 'Lora', serif);">{{ number_format($metrics['users']) }}</p>
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-theme-top small text-theme-secondary" style="font-size: 12px;">
                    <span>Tác giả: <strong class="text-theme fw-semibold">{{ number_format($metrics['authors']) }}</strong></span>
                    <span class="text-theme-muted">&bull;</span>
                    <span>Độc giả: <strong class="text-theme fw-semibold">{{ number_format($metrics['viewers']) }}</strong></span>
                    <a href="{{ route('admin.users.index') }}" class="ms-auto text-theme-muted hover-accent text-decoration-none">Quản lý &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Views Metric -->
        <div class="col-12 col-md-4">
            <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label-uppercase text-theme-secondary" style="font-size: 11px;">Lượt xem bài viết</span>
                    <span class="d-inline-flex p-1.5 rounded-2 bg-surface-2 border border-theme text-theme-muted">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-theme mt-2 mb-0 metric-number" style="font-family: var(--font-serif, 'Lora', serif); color: var(--color-success, #16a34a) !important;">{{ number_format($metrics['views']) }}</p>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-theme-top small text-theme-secondary" style="font-size: 12px;">
                    <span>Tổng lưu lượng đọc toàn trang</span>
                    <span class="small text-theme-muted" style="font-size: 11px;">Tất cả bài viết</span>
                </div>
            </div>
        </div>

        <!-- Comments Metric -->
        <div class="col-12 col-md-4">
            <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="label-uppercase text-theme-secondary" style="font-size: 11px;">Bình luận độc giả</span>
                    <span class="d-inline-flex p-1.5 rounded-2 bg-surface-2 border border-theme text-theme-muted">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-theme mt-2 mb-0 metric-number" style="font-family: var(--font-serif, 'Lora', serif);">{{ number_format($metrics['comments']) }}</p>
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-theme-top small" style="font-size: 12px;">
                    <span class="text-theme-secondary">Cần xử lý (Spam/Treo):</span>
                    @php
                        $moderationTotal = ($metrics['moderation_comments'] ?? ($metrics['pending_comments'] + ($metrics['spam_comments'] ?? 0)));
                    @endphp
                    <span class="badge {{ $moderationTotal > 0 ? 'bg-warning-subtle text-warning border border-warning' : 'bg-surface-2 border border-theme text-theme-muted' }}">
                        {{ number_format($moderationTotal) }}
                    </span>
                    @if($moderationTotal > 0)
                        <a href="{{ route('admin.comments.index') }}" class="ms-auto text-accent fw-medium text-decoration-none">Xử lý ngay &rarr;</a>
                    @else
                        <span class="ms-auto text-theme-muted" style="font-size: 11px;">Đã sạch</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Posts Lifecycle Metrics Strip -->
    <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="label-uppercase text-theme-muted m-0" style="font-size: 11px;">Vòng đời bài viết & Tiến trình biên tập</h3>
            <span class="small text-theme-muted" style="font-size: 12px;">Tổng cộng: <strong class="text-theme">{{ number_format($metrics['posts']) }}</strong> bài viết</span>
        </div>

        <div class="row g-2">
            <!-- Total Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index') }}" class="dashboard-stat-card text-center p-2.5">
                    <span class="label-uppercase d-block mb-1 text-theme-muted" style="font-size: 10px;">Tổng bài</span>
                    <p class="h4 fw-bold text-theme m-0">{{ number_format($metrics['posts']) }}</p>
                </a>
            </div>

            <!-- Pending Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="dashboard-stat-card text-center p-2.5 {{ $metrics['pending'] > 0 ? 'active-filter' : '' }}">
                    <span class="label-uppercase d-block mb-1 text-warning" style="font-size: 10px;">Chờ duyệt</span>
                    <p class="h4 fw-bold text-warning m-0">{{ number_format($metrics['pending']) }}</p>
                </a>
            </div>

            <!-- Published Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="dashboard-stat-card text-center p-2.5">
                    <span class="label-uppercase d-block mb-1" style="font-size: 10px; color: var(--color-success, #16a34a);">Xuất bản</span>
                    <p class="h4 fw-bold m-0" style="color: var(--color-success, #16a34a);">{{ number_format($metrics['published']) }}</p>
                </a>
            </div>

            <!-- Draft Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="dashboard-stat-card text-center p-2.5">
                    <span class="label-uppercase d-block mb-1 text-theme-muted" style="font-size: 10px;">Bản nháp</span>
                    <p class="h4 fw-bold text-theme-secondary m-0">{{ number_format($metrics['draft']) }}</p>
                </a>
            </div>

            <!-- Rejected Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'rejected']) }}" class="dashboard-stat-card text-center p-2.5">
                    <span class="label-uppercase d-block mb-1 text-danger" style="font-size: 10px;">Bị từ chối</span>
                    <p class="h4 fw-bold text-danger m-0">{{ number_format($metrics['rejected']) }}</p>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Moderation Quick Queues (High Efficiency Admin Workspace) -->
<div class="row g-4">
    <!-- Queue 1: Pending Posts Needing Review -->
    <div class="col-12 col-lg-6">
        <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-theme-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle bg-warning" style="width: 8px; height: 8px;"></span>
                    <h3 class="label-uppercase text-theme m-0" style="font-size: 11px;">Hàng đợi bài viết chờ duyệt ({{ $recentPendingPosts->count() }})</h3>
                </div>
                <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="small text-accent hover-accent text-decoration-none" style="font-size: 12px;">
                    Xem tất cả &rarr;
                </a>
            </div>

            @if($recentPendingPosts->isEmpty())
                <div class="py-5 text-center text-theme-muted small">
                    <svg style="width: 28px; height: 28px;" class="mx-auto mb-2 text-theme-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mb-0">Hàng đợi bài viết đang trống. Mọi nội dung đã được xử lý xong!</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($recentPendingPosts as $post)
                        <div class="p-2.5 rounded-3 bg-surface-2 border border-theme d-flex align-items-center justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                <a href="{{ route('admin.posts.show', $post) }}" class="small fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 13.5px;">
                                    {{ $post->title }}
                                </a>
                                <div class="d-flex align-items-center gap-2 small text-theme-muted mt-0.5" style="font-size: 11.5px;">
                                    <span>Tác giả: {{ $post->user->name }}</span>
                                    <span>&bull;</span>
                                    <span class="text-accent">{{ $post->category->name }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                <!-- Quick Approve Button -->
                                <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="m-0"
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
                                    <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                        Duyệt
                                    </button>
                                </form>

                                <!-- Review Link -->
                                <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary" style="font-size: 11px;">
                                    Thẩm định
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Queue 2: Actionable Comments Needing Review (Spam or Pending) -->
    <div class="col-12 col-lg-6">
        <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-theme-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle bg-warning" style="width: 8px; height: 8px;"></span>
                    <h3 class="label-uppercase text-theme m-0" style="font-size: 11px;">Bình luận cần hậu kiểm ({{ $recentPendingComments->count() }})</h3>
                </div>
                <a href="{{ route('admin.comments.index') }}" class="small text-accent hover-accent text-decoration-none" style="font-size: 12px;">
                    Xem tất cả &rarr;
                </a>
            </div>

            @if($recentPendingComments->isEmpty())
                <div class="py-5 text-center text-theme-muted small">
                    <svg style="width: 28px; height: 28px;" class="mx-auto mb-2 text-theme-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="mb-0">Không có bình luận nào cần can thiệp. Quy trình xuất bản trực tiếp an toàn.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($recentPendingComments as $comment)
                        <div class="p-2.5 rounded-3 bg-surface-2 border border-theme d-flex align-items-start justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                <p class="small text-theme mb-1 line-clamp-2" style="font-size: 13px;">
                                    "{{ $comment->body }}"
                                </p>
                                <div class="d-flex align-items-center flex-wrap gap-2 small text-theme-muted" style="font-size: 11px;">
                                    <span>Bởi <strong class="text-theme">{{ $comment->user->name }}</strong></span>
                                    <span>&bull;</span>
                                    <span class="text-truncate" style="max-width: 140px;">Bài: {{ $comment->post->title }}</span>
                                    @if($comment->status === 'spam')
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Spam</span>
                                    @elseif($comment->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning">Chờ duyệt</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <!-- Approve / Restore Comment -->
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" title="Duyệt / Bỏ đánh dấu vi phạm" aria-label="Duyệt bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 d-inline-flex align-items-center justify-content-center rounded-circle p-0 text-success" style="width: 32px; height: 32px;">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- Mark Spam Comment -->
                                @if($comment->status !== 'spam')
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" title="Đánh dấu Spam" aria-label="Đánh dấu Spam bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 d-inline-flex align-items-center justify-content-center rounded-circle p-0 text-warning" style="width: 32px; height: 32px;">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete Comment -->
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="m-0"
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                      data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                                      data-confirm-target-meta="{{ Str::limit($comment->body, 60) }}"
                                      data-confirm-description="Hành động này không thể hoàn tác. Bình luận sẽ bị xóa hoàn toàn khỏi bài viết."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa" aria-label="Xóa bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 d-inline-flex align-items-center justify-content-center rounded-circle p-0 text-danger" style="width: 32px; height: 32px;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
