@extends('layouts.admin')

@section('admin_title', 'Bảng điều khiển Quản trị')

@section('admin_content')
<style>
    .admin-dashboard {
        --dashboard-radius: 18px;
        --dashboard-card-border: var(--color-border-subtle);
    }

    .admin-dashboard .dashboard-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .admin-dashboard .dashboard-section-kicker {
        margin: 0 0 .25rem;
        color: var(--color-text-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .admin-dashboard .dashboard-section-title {
        margin: 0;
        color: var(--color-text-primary);
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .admin-dashboard .dashboard-section-meta {
        color: var(--color-text-muted);
        font-size: 12px;
        white-space: nowrap;
    }

    .admin-dashboard .dashboard-metric-card,
    .admin-dashboard .dashboard-panel {
        border: 1px solid var(--dashboard-card-border);
        border-radius: var(--dashboard-radius);
        background: var(--color-bg-surface);
        box-shadow: var(--shadow-xs, 0 1px 2px rgba(0, 0, 0, .08));
    }

    .admin-dashboard .dashboard-metric-card {
        height: 100%;
        padding: 1.25rem;
        transition: border-color var(--duration-fast) var(--ease-standard),
                    transform var(--duration-fast) var(--ease-standard);
    }

    .admin-dashboard .dashboard-metric-card:hover {
        border-color: var(--color-border-strong, var(--color-border-subtle));
        transform: translateY(-1px);
    }

    .admin-dashboard .metric-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .admin-dashboard .metric-label {
        color: var(--color-text-secondary);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .admin-dashboard .metric-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        border: 1px solid var(--color-border-subtle);
        border-radius: 11px;
        background: var(--color-bg-subtle);
        color: var(--color-text-muted);
    }

    .admin-dashboard .metric-value {
        margin: .75rem 0 0;
        color: var(--color-text-primary);
        font-family: var(--font-serif, 'Lora', Georgia, serif);
        font-size: clamp(1.9rem, 2.6vw, 2.35rem);
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .admin-dashboard .metric-value--success {
        color: var(--color-success, #16a34a);
    }

    .admin-dashboard .metric-footer {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .45rem .7rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--color-border-subtle);
        color: var(--color-text-secondary);
        font-size: 11.5px;
    }

    .admin-dashboard .metric-footer a {
        margin-left: auto;
        color: var(--color-text-muted);
        text-decoration: none;
        font-weight: 600;
    }

    .admin-dashboard .metric-footer a:hover {
        color: var(--color-interactive);
    }

    .admin-dashboard .dashboard-panel {
        padding: 1.2rem;
    }

    .admin-dashboard .status-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: .65rem;
    }

    .admin-dashboard .status-card {
        display: block;
        min-width: 0;
        padding: .9rem .75rem;
        border: 1px solid var(--color-border-subtle);
        border-radius: 14px;
        background: var(--color-bg-subtle);
        text-align: center;
        text-decoration: none;
        transition: border-color var(--duration-fast) var(--ease-standard),
                    background-color var(--duration-fast) var(--ease-standard),
                    transform var(--duration-fast) var(--ease-standard);
    }

    .admin-dashboard .status-card:hover {
        border-color: var(--color-border-strong, var(--color-border-subtle));
        background: var(--color-bg-hover);
        transform: translateY(-1px);
    }

    .admin-dashboard .status-card.active-filter {
        border-color: var(--color-text-primary);
        box-shadow: inset 0 0 0 1px var(--color-text-primary);
    }

    .admin-dashboard .status-label {
        display: block;
        margin-bottom: .25rem;
        color: var(--color-text-muted);
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .admin-dashboard .status-value {
        margin: 0;
        color: var(--color-text-primary);
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .admin-dashboard .moderation-grid > [class*="col-"] {
        display: flex;
    }

    .admin-dashboard .queue-panel {
        width: 100%;
        height: 100%;
        padding: 1.15rem 1.2rem;
        border: 1px solid var(--color-border-subtle);
        border-radius: var(--dashboard-radius);
        background: var(--color-bg-surface);
    }

    .admin-dashboard .queue-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: .9rem;
        border-bottom: 1px solid var(--color-border-subtle);
    }

    .admin-dashboard .queue-head-main {
        display: flex;
        align-items: center;
        min-width: 0;
        gap: .55rem;
    }

    .admin-dashboard .queue-dot {
        width: 8px;
        height: 8px;
        flex: 0 0 8px;
        border-radius: 999px;
        background: var(--bs-warning, #ffc107);
    }

    .admin-dashboard .queue-title {
        margin: 0;
        color: var(--color-text-primary);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .admin-dashboard .queue-all-link {
        flex: 0 0 auto;
        color: var(--color-text-muted);
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
    }

    .admin-dashboard .queue-all-link:hover {
        color: var(--color-interactive);
    }

    .admin-dashboard .queue-list {
        display: flex;
        flex-direction: column;
    }

    .admin-dashboard .queue-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: .85rem;
        padding: .85rem 0;
        border-bottom: 1px solid var(--color-border-subtle);
    }

    .admin-dashboard .queue-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .admin-dashboard .queue-copy {
        min-width: 0;
    }

    .admin-dashboard .queue-item-title {
        display: block;
        min-width: 0;
        overflow: hidden;
        color: var(--color-text-primary);
        font-size: 13px;
        font-weight: 650;
        line-height: 1.35;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .admin-dashboard .queue-item-title:hover {
        color: var(--color-interactive);
    }

    .admin-dashboard .queue-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        min-width: 0;
        gap: .2rem .5rem;
        margin-top: .35rem;
        color: var(--color-text-muted);
        font-size: 11px;
        line-height: 1.4;
    }

    .admin-dashboard .queue-meta > span {
        min-width: 0;
        max-width: 100%;
        overflow-wrap: anywhere;
    }

    .admin-dashboard .queue-meta .queue-category {
        color: var(--color-interactive);
        font-weight: 600;
    }

    .admin-dashboard .queue-actions {
        display: flex;
        align-items: center;
        flex: 0 0 auto;
        gap: .4rem;
    }

    .admin-dashboard .queue-actions .btn {
        min-width: 72px;
        white-space: nowrap;
    }

    .admin-dashboard .queue-empty {
        display: flex;
        min-height: 210px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        color: var(--color-text-muted);
        text-align: center;
        font-size: 12px;
    }

    .admin-dashboard .queue-empty svg {
        margin-bottom: .75rem;
        opacity: .45;
    }

    .admin-dashboard .comment-body {
        margin: 0 0 .35rem;
        color: var(--color-text-primary);
        font-size: 12.5px;
        line-height: 1.45;
    }

    .admin-dashboard .icon-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 999px;
    }

    @media (max-width: 1199.98px) {
        .admin-dashboard .status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .admin-dashboard .dashboard-section-head {
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .admin-dashboard .dashboard-section-meta {
            white-space: normal;
            text-align: right;
        }

        .admin-dashboard .dashboard-metric-card,
        .admin-dashboard .dashboard-panel,
        .admin-dashboard .queue-panel {
            border-radius: 15px;
        }
    }

    @media (max-width: 575.98px) {
        .admin-dashboard .status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-dashboard .queue-head {
            align-items: flex-start;
        }

        .admin-dashboard .queue-head-main {
            align-items: flex-start;
        }

        .admin-dashboard .queue-title {
            line-height: 1.35;
        }

        .admin-dashboard .queue-row {
            grid-template-columns: minmax(0, 1fr);
            align-items: start;
        }

        .admin-dashboard .queue-item-title {
            white-space: normal;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .admin-dashboard .queue-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .admin-dashboard .queue-actions--post > form,
        .admin-dashboard .queue-actions--post > a {
            flex: 1 1 0;
        }

        .admin-dashboard .queue-actions--post > form .btn,
        .admin-dashboard .queue-actions--post > a {
            width: 100%;
            text-align: center;
        }
    }
</style>

@php
    $moderationTotal = ($metrics['moderation_comments'] ?? ($metrics['pending_comments'] + ($metrics['spam_comments'] ?? 0)));
@endphp

<div class="admin-dashboard">
    <section class="mb-4 mb-lg-5" aria-labelledby="overview-heading">
        <div class="dashboard-section-head">
            <div>
                <p class="dashboard-section-kicker">Tổng quan hệ thống</p>
                <h2 id="overview-heading" class="dashboard-section-title">Chỉ số vận hành</h2>
            </div>
            <span class="dashboard-section-meta">Dữ liệu thời gian thực</span>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <article class="dashboard-metric-card">
                    <div class="metric-head">
                        <span class="metric-label">Tổng người dùng</span>
                        <span class="metric-icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="metric-value">{{ number_format($metrics['users']) }}</p>
                    <div class="metric-footer">
                        <span>Tác giả <strong class="text-theme">{{ number_format($metrics['authors']) }}</strong></span>
                        <span>Độc giả <strong class="text-theme">{{ number_format($metrics['viewers']) }}</strong></span>
                        <a href="{{ route('admin.users.index') }}">Quản lý &rarr;</a>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="dashboard-metric-card">
                    <div class="metric-head">
                        <span class="metric-label">Lượt xem bài viết</span>
                        <span class="metric-icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                    <p class="metric-value metric-value--success">{{ number_format($metrics['views']) }}</p>
                    <div class="metric-footer">
                        <span>Tổng lưu lượng đọc toàn trang</span>
                        <span class="ms-auto text-theme-muted">Tất cả bài viết</span>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="dashboard-metric-card">
                    <div class="metric-head">
                        <span class="metric-label">Bình luận độc giả</span>
                        <span class="metric-icon" aria-hidden="true">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </span>
                    </div>
                    <p class="metric-value">{{ number_format($metrics['comments']) }}</p>
                    <div class="metric-footer">
                        <span>Cần xử lý</span>
                        <span class="badge {{ $moderationTotal > 0 ? 'bg-warning-subtle text-warning border border-warning' : 'bg-surface-2 border border-theme text-theme-muted' }}">
                            {{ number_format($moderationTotal) }}
                        </span>
                        @if($moderationTotal > 0)
                            <a href="{{ route('admin.comments.index') }}">Xử lý ngay &rarr;</a>
                        @else
                            <span class="ms-auto text-theme-muted">Đã sạch</span>
                        @endif
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="dashboard-panel mb-4 mb-lg-5" aria-labelledby="lifecycle-heading">
        <div class="dashboard-section-head mb-3">
            <div>
                <p class="dashboard-section-kicker">Nội dung</p>
                <h2 id="lifecycle-heading" class="dashboard-section-title">Vòng đời bài viết</h2>
            </div>
            <span class="dashboard-section-meta">Tổng cộng <strong class="text-theme">{{ number_format($metrics['posts']) }}</strong> bài viết</span>
        </div>

        <div class="status-grid">
            <a href="{{ route('admin.posts.index') }}" class="status-card">
                <span class="status-label">Tổng bài</span>
                <p class="status-value">{{ number_format($metrics['posts']) }}</p>
            </a>

            <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="status-card {{ $metrics['pending'] > 0 ? 'active-filter' : '' }}">
                <span class="status-label text-warning">Chờ duyệt</span>
                <p class="status-value text-warning">{{ number_format($metrics['pending']) }}</p>
            </a>

            <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="status-card">
                <span class="status-label" style="color: var(--color-success, #16a34a);">Xuất bản</span>
                <p class="status-value" style="color: var(--color-success, #16a34a);">{{ number_format($metrics['published']) }}</p>
            </a>

            <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="status-card">
                <span class="status-label">Bản nháp</span>
                <p class="status-value text-theme-secondary">{{ number_format($metrics['draft']) }}</p>
            </a>

            <a href="{{ route('admin.posts.index', ['status' => 'rejected']) }}" class="status-card">
                <span class="status-label text-danger">Bị từ chối</span>
                <p class="status-value text-danger">{{ number_format($metrics['rejected']) }}</p>
            </a>
        </div>
    </section>

    <section aria-label="Hàng đợi kiểm duyệt">
        <div class="moderation-grid row g-4">
            <div class="col-12 col-xl-7">
                <div class="queue-panel">
                    <div class="queue-head">
                        <div class="queue-head-main">
                            <span class="queue-dot" aria-hidden="true"></span>
                            <h2 class="queue-title">Hàng đợi bài viết chờ duyệt ({{ $recentPendingPosts->count() }})</h2>
                        </div>
                        <a href="{{ route('admin.posts.index', ['status' => 'pending']) }}" class="queue-all-link">Xem tất cả &rarr;</a>
                    </div>

                    @if($recentPendingPosts->isEmpty())
                        <div class="queue-empty">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mb-0">Hàng đợi bài viết đang trống. Mọi nội dung đã được xử lý xong.</p>
                        </div>
                    @else
                        <div class="queue-list">
                            @foreach($recentPendingPosts as $post)
                                <div class="queue-row">
                                    <div class="queue-copy">
                                        <a href="{{ route('admin.posts.show', $post) }}" class="queue-item-title">
                                            {{ $post->title }}
                                        </a>
                                        <div class="queue-meta">
                                            <span>Tác giả: {{ $post->user->name }}</span>
                                            <span aria-hidden="true">&bull;</span>
                                            <span class="queue-category">{{ $post->category->name }}</span>
                                            <span aria-hidden="true">&bull;</span>
                                            <span>{{ $post->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <div class="queue-actions queue-actions--post">
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
                                            <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 11px;">
                                                Duyệt
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary" style="font-size: 11px;">
                                            Thẩm định
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="queue-panel">
                    <div class="queue-head">
                        <div class="queue-head-main">
                            <span class="queue-dot" aria-hidden="true"></span>
                            <h2 class="queue-title">Bình luận cần hậu kiểm ({{ $recentPendingComments->count() }})</h2>
                        </div>
                        <a href="{{ route('admin.comments.index') }}" class="queue-all-link">Xem tất cả &rarr;</a>
                    </div>

                    @if($recentPendingComments->isEmpty())
                        <div class="queue-empty">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="mb-0">Không có bình luận nào cần can thiệp. Quy trình xuất bản trực tiếp an toàn.</p>
                        </div>
                    @else
                        <div class="queue-list">
                            @foreach($recentPendingComments as $comment)
                                <div class="queue-row align-items-start">
                                    <div class="queue-copy">
                                        <p class="comment-body line-clamp-2">“{{ $comment->body }}”</p>
                                        <div class="queue-meta">
                                            <span>Bởi <strong class="text-theme">{{ $comment->user->name }}</strong></span>
                                            <span aria-hidden="true">&bull;</span>
                                            <span class="text-truncate" style="max-width: 160px;">Bài: {{ $comment->post->title }}</span>
                                            @if($comment->status === 'spam')
                                                <span class="badge bg-danger-subtle text-danger border border-danger">Spam</span>
                                            @elseif($comment->status === 'pending')
                                                <span class="badge bg-warning-subtle text-warning border border-warning">Chờ duyệt</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="queue-actions">
                                        @if($comment->status !== 'approved')
                                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" title="Duyệt / Bỏ đánh dấu vi phạm" aria-label="Duyệt bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 icon-action text-success">
                                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        @if($comment->status !== 'spam')
                                            <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" title="Đánh dấu Spam" aria-label="Đánh dấu Spam bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 icon-action text-warning">
                                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

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
                                            <button type="submit" title="Xóa" aria-label="Xóa bình luận của {{ $comment->user?->name ?? 'Người dùng' }}" class="btn btn-sm btn-outline-theme border-0 icon-action text-danger">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
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
    </section>
</div>
@endsection
