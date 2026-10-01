@extends('layouts.admin')

@section('admin_title', 'Bảng điều khiển Quản trị')

@section('admin_content')
<!-- Dashboard Top Metrics Grid -->
<div class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="small fw-bold text-uppercase tracking-wider text-secondary m-0">Thống kê Tổng thể Hệ thống</h2>
        <span class="small text-secondary">Cập nhật thời gian thực</span>
    </div>

    <!-- 1. Users Metrics (users, authors, viewers) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card h-100 p-4 border shadow-sm rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Tổng người dùng</span>
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-body mt-2 mb-0">{{ number_format($metrics['users']) }}</p>
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top small text-secondary">
                    <span>Tác giả: <strong class="text-primary fw-bold">{{ number_format($metrics['authors']) }}</strong></span>
                    <span>&bull;</span>
                    <span>Độc giả: <strong class="text-body fw-bold">{{ number_format($metrics['viewers']) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- 2. Views Metrics -->
        <div class="col-12 col-md-4">
            <div class="card h-100 p-4 border shadow-sm rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Lượt xem bài viết</span>
                    <span class="badge bg-success-subtle text-success p-2 rounded-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-success mt-2 mb-0">{{ number_format($metrics['views']) }}</p>
                <p class="small text-secondary mt-3 pt-3 border-top mb-0">Tổng lưu lượng truy cập toàn trang</p>
            </div>
        </div>

        <!-- 3. Comments Metrics -->
        <div class="col-12 col-md-4">
            <div class="card h-100 p-4 border shadow-sm rounded-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-secondary text-uppercase tracking-wider">Bình luận độc giả</span>
                    <span class="badge bg-info-subtle text-info p-2 rounded-3">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </span>
                </div>
                <p class="h2 fw-bold text-body mt-2 mb-0">{{ number_format($metrics['comments']) }}</p>
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top small">
                    <span class="text-secondary">Cần xử lý (Spam/Treo):</span>
                    @php
                        $moderationTotal = ($metrics['moderation_comments'] ?? ($metrics['pending_comments'] + ($metrics['spam_comments'] ?? 0)));
                    @endphp
                    <span class="badge {{ $moderationTotal > 0 ? 'bg-warning-subtle text-warning border border-warning' : 'bg-body-secondary text-secondary' }}">
                        {{ number_format($moderationTotal) }}
                    </span>
                    @if($moderationTotal > 0)
                        <a href="{{ route('admin.comments.index') }}" class="ms-auto text-primary fw-semibold text-decoration-none">Xử lý ngay &rarr;</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Posts Lifecycle Metrics Grid -->
    <div class="card p-4 border shadow-sm rounded-3 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="small fw-bold text-secondary text-uppercase tracking-wider m-0">Tiến trình Biên tập & Xuất bản Bài viết</h3>
            <span class="small text-secondary">Tổng số: <strong class="text-body fw-bold">{{ number_format($metrics['posts']) }}</strong> bài viết</span>
        </div>

        <div class="row g-2">
            <!-- Total Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index') }}" class="card text-decoration-none p-3 border rounded-3 bg-body-tertiary h-100">
                    <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.7rem;">Tổng bài</span>
                    <p class="h4 fw-bold text-body mt-1 mb-0">{{ number_format($metrics['posts']) }}</p>
                </a>
            </div>

            <!-- Pending Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="card text-decoration-none p-3 border rounded-3 {{ $metrics['pending'] > 0 ? 'bg-warning-subtle border-warning' : 'bg-body-tertiary' }} h-100">
                    <span class="small fw-semibold text-warning text-uppercase d-block" style="font-size: 0.7rem;">Chờ duyệt</span>
                    <p class="h4 fw-bold text-warning mt-1 mb-0">{{ number_format($metrics['pending']) }}</p>
                </a>
            </div>

            <!-- Published Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="card text-decoration-none p-3 border rounded-3 bg-body-tertiary h-100">
                    <span class="small fw-semibold text-success text-uppercase d-block" style="font-size: 0.7rem;">Đã xuất bản</span>
                    <p class="h4 fw-bold text-success mt-1 mb-0">{{ number_format($metrics['published']) }}</p>
                </a>
            </div>

            <!-- Draft Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="card text-decoration-none p-3 border rounded-3 bg-body-tertiary h-100">
                    <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.7rem;">Bản nháp</span>
                    <p class="h4 fw-bold text-secondary mt-1 mb-0">{{ number_format($metrics['draft']) }}</p>
                </a>
            </div>

            <!-- Rejected Posts -->
            <div class="col-6 col-sm">
                <a href="{{ route('admin.posts.index', ['status' => 'rejected']) }}" class="card text-decoration-none p-3 border rounded-3 bg-body-tertiary h-100">
                    <span class="small fw-semibold text-danger text-uppercase d-block" style="font-size: 0.7rem;">Bị từ chối</span>
                    <p class="h4 fw-bold text-danger mt-1 mb-0">{{ number_format($metrics['rejected']) }}</p>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Moderation Quick Queues (Pending Posts & Comments) -->
<div class="row g-4">
    <!-- Queue 1: Pending Posts Needing Review -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border shadow-sm rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                    <h3 class="small fw-bold text-body text-uppercase tracking-wider m-0">Bài viết chờ thẩm định ({{ $recentPendingPosts->count() }})</h3>
                </div>
                <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="small text-primary fw-semibold text-decoration-none">
                    Xem tất cả &rarr;
                </a>
            </div>

            @if($recentPendingPosts->isEmpty())
                <div class="py-5 text-center text-secondary small">
                    Không có bài viết nào đang chờ duyệt. Mọi thứ đã hoàn tất!
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentPendingPosts as $post)
                        <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between gap-3 bg-transparent">
                            <div class="min-w-0 flex-grow-1">
                                <a href="{{ route('admin.posts.show', $post) }}" class="small fw-bold text-body text-decoration-none text-truncate d-block">
                                    {{ $post->title }}
                                </a>
                                <div class="d-flex align-items-center gap-2 small text-secondary mt-1" style="font-size: 0.75rem;">
                                    <span>Bởi {{ $post->user->name }}</span>
                                    <span>&bull;</span>
                                    <span class="text-primary">{{ $post->category->name }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <!-- Quick Approve Button -->
                                <form action="{{ route('admin.posts.approve', $post) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                                        Duyệt
                                    </button>
                                </form>

                                <!-- Review Link -->
                                <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
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
        <div class="card p-4 border shadow-sm rounded-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                    <h3 class="small fw-bold text-body text-uppercase tracking-wider m-0">Bình luận cần xử lý ({{ $recentPendingComments->count() }})</h3>
                </div>
                <a href="{{ route('admin.comments.index') }}" class="small text-primary fw-semibold text-decoration-none">
                    Xem tất cả &rarr;
                </a>
            </div>

            @if($recentPendingComments->isEmpty())
                <div class="py-5 text-center text-secondary small">
                    Không có bình luận nào cần xử lý. Bình luận mới được hiển thị ngay.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentPendingComments as $comment)
                        <div class="list-group-item px-0 py-3 d-flex align-items-start justify-content-between gap-3 bg-transparent">
                            <div class="min-w-0 flex-grow-1">
                                <p class="small text-body mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    "{{ $comment->body }}"
                                </p>
                                <div class="d-flex align-items-center gap-2 small text-secondary" style="font-size: 0.75rem;">
                                    <span>Bởi <strong>{{ $comment->user->name }}</strong></span>
                                    <span>&bull;</span>
                                    <span class="text-truncate" style="max-width: 130px;">Bài: {{ $comment->post->title }}</span>
                                    @if($comment->status === 'spam')
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Spam</span>
                                    @elseif($comment->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning">Chờ xử lý</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <!-- Approve / Restore Comment -->
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Duyệt / Bỏ đánh dấu vi phạm" class="btn btn-sm btn-outline-success border-0 p-1">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- Mark Spam Comment -->
                                @if($comment->status !== 'spam')
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Đánh dấu Spam" class="btn btn-sm btn-outline-warning border-0 p-1">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete Comment -->
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                      data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                                      data-confirm-target-meta="{{ Str::limit($comment->content, 60) }}"
                                      data-confirm-description="Hành động này không thể hoàn tác. Bình luận sẽ bị xóa hoàn toàn khỏi bài viết."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa" class="btn btn-sm btn-outline-danger border-0 p-1">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
