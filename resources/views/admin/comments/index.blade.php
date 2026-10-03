@extends('layouts.admin')

@section('admin_title', 'Quản lý & Kiểm duyệt Bình luận')

@section('admin_content')
<!-- Status Tabs (Professional Pill Segment Filter) -->
<div class="d-flex flex-wrap align-items-center gap-1.5 mb-4 pb-1">
    <a
        href="{{ route('admin.comments.index') }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme text-theme-secondary' }}"
        style="font-size: 12.5px;"
    >
        Tất cả <span class="opacity-75 ms-1">({{ number_format($statusCounts['all']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'approved' ? 'btn-success text-white fw-bold' : 'btn-outline-theme text-success' }}"
        style="font-size: 12.5px;"
    >
        Đã duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['approved']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'spam' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
        style="font-size: 12.5px;"
    >
        Spam <span class="opacity-75 ms-1">({{ number_format($statusCounts['spam']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-theme text-warning' }}"
        style="font-size: 12.5px;"
    >
        Chờ duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['pending']) }})</span>
    </a>
</div>

<!-- Search Bar (High Density Toolbar) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2.5">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="position-relative flex-grow-1">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm nội dung bình luận hoặc tên người dùng..."
                class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1.5"
                style="font-size: 13px;"
            >
            <svg style="width: 14px; height: 14px; position: absolute; left: 12px; top: 10px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 fw-semibold flex-shrink-0" style="font-size: 12.5px;">
            Tìm kiếm
        </button>
    </form>
</div>

<!-- Comments List Table (High Information Density) -->
<div class="card border-theme bg-theme-surface shadow-xs rounded-xl overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="--bs-table-bg: transparent; --bs-table-hover-bg: var(--color-surface-2);">
            <thead class="border-theme-bottom bg-surface-2">
                <tr>
                    <th class="ps-3 ps-sm-4 py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Người bình luận</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Nội dung</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Bài viết</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Trạng thái</th>
                    <th class="py-3 label-uppercase text-theme-muted" style="font-size: 11px;">Thời gian</th>
                    <th class="pe-3 pe-sm-4 py-3 text-end label-uppercase text-theme-muted" style="font-size: 11px;">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-theme">
                @forelse($comments as $comment)
                    <tr>
                        <!-- User -->
                        <td class="ps-3 ps-sm-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <x-avatar :user="$comment->user" size="sm" class="border border-theme flex-shrink-0" />
                                <div class="min-w-0" style="max-width: 180px;">
                                    <a href="{{ route('admin.users.show', $comment->user) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 13px;">
                                        {{ $comment->user->name }}
                                    </a>
                                    <span class="small text-theme-muted text-truncate d-block" style="font-size: 11px;">{{ $comment->user->email }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Comment Body -->
                        <td class="py-3" style="max-width: 340px;">
                            <div class="p-2 rounded-2 bg-surface-2 border border-theme text-theme small mb-1 line-clamp-3">
                                "{{ $comment->body }}"
                            </div>
                            @if($comment->parent_id)
                                <span class="d-inline-flex align-items-center gap-1 text-theme-muted" style="font-size: 10.5px;">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a4 4 0 014 4v7M3 10l6 6m-6-6l6-6" /></svg>
                                    Phản hồi cho bình luận #{{ $comment->parent_id }}
                                </span>
                            @endif
                        </td>

                        <!-- Target Post -->
                        <td class="py-3" style="max-width: 220px;">
                            <a href="{{ route('admin.posts.show', $comment->post) }}" class="small fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 12.5px;" title="{{ $comment->post->title }}">
                                {{ $comment->post->title }}
                            </a>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3">
                            @if($comment->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Đã duyệt
                                </span>
                            @elseif($comment->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Chờ duyệt
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Spam
                                </span>
                            @endif
                        </td>

                        <!-- Time -->
                        <td class="py-3 small text-theme-muted text-nowrap" style="font-size: 11.5px;">
                            {{ $comment->created_at->diffForHumans() }}
                        </td>

                        <!-- Moderation Actions -->
                        <td class="pe-3 pe-sm-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Duyệt bình luận của {{ $comment->user?->name ?? 'Người dùng' }}">
                                            Duyệt
                                        </button>
                                    </form>
                                @endif

                                @if($comment->status !== 'spam')
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Đánh dấu Spam bình luận của {{ $comment->user?->name ?? 'Người dùng' }}">
                                            Spam
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                      data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                                      data-confirm-target-meta="{{ Str::limit($comment->body ?? $comment->content, 60) }}"
                                      data-confirm-description="Hành động này không thể hoàn tác. Bình luận và các phản hồi liên quan sẽ bị xóa hoàn toàn."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-circle" title="Xóa bình luận" aria-label="Xóa bình luận">
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
                                title="Không tìm thấy bình luận"
                                description="Không có bình luận nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                                :action-url="route('admin.comments.index')"
                                action-label="Đặt lại bộ lọc"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
        <div class="px-3 px-sm-4 py-3 border-theme-top bg-theme-surface d-flex justify-content-center">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection

