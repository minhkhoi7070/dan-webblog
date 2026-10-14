@extends('layouts.admin')

<<<<<<< HEAD
@section('admin_title', 'Quản lý Bình luận')

@section('admin_content')
<!-- Status Tabs -->
<div class="d-flex flex-wrap align-items-center gap-2 mb-4 pb-2 border-bottom">
    <a
        href="{{ route('admin.comments.index') }}"
        class="btn btn-sm rounded-pill fw-bold {{ empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' }}"
    >
        Tất cả ({{ number_format($statusCounts['all']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' }}"
=======
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
>>>>>>> origin/main
    >
        Đã duyệt <span class="opacity-75 ms-1">({{ number_format($statusCounts['approved']) }})</span>
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
<<<<<<< HEAD
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'spam' ? 'btn-danger' : 'btn-outline-danger' }}"
=======
        class="btn btn-sm rounded-pill px-3 py-1 fw-medium {{ $currentStatus === 'spam' ? 'btn-danger text-white fw-bold' : 'btn-outline-theme text-danger' }}"
        style="font-size: 12.5px;"
>>>>>>> origin/main
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
    <a
        href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}"
    >
        Chờ xử lý ({{ number_format($statusCounts['pending']) }})
    </a>
</div>

<<<<<<< HEAD
<!-- Search Bar -->
<div class="card p-3 border shadow-sm rounded-3 mb-4">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex gap-2">
=======
<!-- Search Bar (High Density Toolbar) -->
<div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2.5">
>>>>>>> origin/main
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

<<<<<<< HEAD
        <div class="input-group flex-grow-1">
            <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
=======
        <div class="position-relative flex-grow-1">
>>>>>>> origin/main
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm nội dung bình luận hoặc tên người dùng..."
<<<<<<< HEAD
                class="form-control border-start-0 ps-0"
=======
                class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1.5"
                style="font-size: 13px;"
>>>>>>> origin/main
            >
            <svg style="width: 14px; height: 14px; position: absolute; left: 12px; top: 10px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

<<<<<<< HEAD
        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold small">
=======
        <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 fw-semibold flex-shrink-0" style="font-size: 12.5px;">
>>>>>>> origin/main
            Tìm kiếm
        </button>
    </form>
</div>

<<<<<<< HEAD
<!-- Comments List Table -->
<div class="card border shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-bold text-secondary">
                <tr>
                    <th class="px-4 py-3">Người bình luận</th>
                    <th class="px-4 py-3">Nội dung</th>
                    <th class="px-4 py-3">Bài viết</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                    <tr>
                        <!-- User -->
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $comment->user) }}" class="fw-bold text-body text-decoration-none d-block">
                                {{ $comment->user->name }}
                            </a>
                            <span class="small text-secondary" style="font-size: 0.75rem;">{{ $comment->user->email }}</span>
                        </td>

                        <!-- Comment Body -->
                        <td class="px-4 py-3" style="max-width: 320px;">
                            <p class="text-body small mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
=======
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
>>>>>>> origin/main
                                "{{ $comment->body }}"
                            </div>
                            @if($comment->parent_id)
<<<<<<< HEAD
                                <span class="d-inline-block mt-1 text-secondary fst-italic" style="font-size: 0.7rem;">
                                    &lrcorner; Trả lời cho bình luận #{{ $comment->parent_id }}
=======
                                <span class="d-inline-flex align-items-center gap-1 text-theme-muted" style="font-size: 10.5px;">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a4 4 0 014 4v7M3 10l6 6m-6-6l6-6" /></svg>
                                    Phản hồi cho bình luận #{{ $comment->parent_id }}
>>>>>>> origin/main
                                </span>
                            @endif
                        </td>

                        <!-- Target Post -->
<<<<<<< HEAD
                        <td class="px-4 py-3" style="max-width: 200px;">
                            <a href="{{ route('admin.posts.show', $comment->post) }}" class="small fw-semibold text-body text-decoration-none text-truncate d-block">
=======
                        <td class="py-3" style="max-width: 220px;">
                            <a href="{{ route('admin.posts.show', $comment->post) }}" class="small fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 12.5px;" title="{{ $comment->post->title }}">
>>>>>>> origin/main
                                {{ $comment->post->title }}
                            </a>
                        </td>

                        <!-- Status Badge -->
<<<<<<< HEAD
                        <td class="px-4 py-3">
                            @if($comment->status === 'approved')
                                <x-badge variant="status-published" size="xs">
                                    Đã duyệt (Công khai)
                                </x-badge>
                            @elseif($comment->status === 'pending')
                                <x-badge variant="status-pending" size="xs">
                                    Chờ xử lý (Thủ công)
                                </x-badge>
=======
                        <td class="py-3">
                            @if($comment->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Đã duyệt
                                </span>
                            @elseif($comment->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Chờ duyệt
                                </span>
>>>>>>> origin/main
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11px;">
                                    Spam
                                </span>
                            @endif
                        </td>

                        <!-- Time -->
<<<<<<< HEAD
                        <td class="px-4 py-3 small text-secondary text-nowrap">
=======
                        <td class="py-3 small text-theme-muted text-nowrap" style="font-size: 11.5px;">
>>>>>>> origin/main
                            {{ $comment->created_at->diffForHumans() }}
                        </td>

                        <!-- Moderation Actions -->
<<<<<<< HEAD
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
=======
                        <td class="pe-3 pe-sm-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Duyệt bình luận của {{ $comment->user?->name ?? 'Người dùng' }}">
>>>>>>> origin/main
                                            Duyệt
                                        </button>
                                    </form>
                                @endif

                                @if($comment->status !== 'spam')
<<<<<<< HEAD
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
=======
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;" aria-label="Đánh dấu Spam bình luận của {{ $comment->user?->name ?? 'Người dùng' }}">
>>>>>>> origin/main
                                            Spam
                                        </button>
                                    </form>
                                @endif

<<<<<<< HEAD
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline"
=======
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
>>>>>>> origin/main
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                      data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
<<<<<<< HEAD
                                      data-confirm-target-meta="{{ Str::limit($comment->content, 60) }}"
=======
                                      data-confirm-target-meta="{{ Str::limit($comment->body ?? $comment->content, 60) }}"
>>>>>>> origin/main
                                      data-confirm-description="Hành động này không thể hoàn tác. Bình luận và các phản hồi liên quan sẽ bị xóa hoàn toàn."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
<<<<<<< HEAD
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Xóa">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
=======
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-circle" title="Xóa bình luận" aria-label="Xóa bình luận">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
>>>>>>> origin/main
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
<<<<<<< HEAD
                        <td colspan="6" class="px-4 py-5 text-center text-secondary small">
                            Không có bình luận nào phù hợp.
=======
                        <td colspan="6" class="p-0 border-0">
                            <x-empty-state
                                title="Không tìm thấy bình luận"
                                description="Không có bình luận nào phù hợp với bộ lọc hoặc từ khóa tìm kiếm."
                                :action-url="route('admin.comments.index')"
                                action-label="Đặt lại bộ lọc"
                            />
>>>>>>> origin/main
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
<<<<<<< HEAD
        <div class="px-4 py-3 border-top">
=======
        <div class="px-3 px-sm-4 py-3 border-theme-top bg-theme-surface d-flex justify-content-center">
>>>>>>> origin/main
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection

