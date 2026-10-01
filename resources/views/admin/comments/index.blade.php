@extends('layouts.admin')

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
    >
        Đã duyệt ({{ number_format($statusCounts['approved']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'spam' ? 'btn-danger' : 'btn-outline-danger' }}"
    >
        Spam ({{ number_format($statusCounts['spam']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}"
    >
        Chờ xử lý ({{ number_format($statusCounts['pending']) }})
    </a>
</div>

<!-- Search Bar -->
<div class="card p-3 border shadow-sm rounded-3 mb-4">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="d-flex gap-2">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="input-group flex-grow-1">
            <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm nội dung bình luận hoặc tên người dùng..."
                class="form-control border-start-0 ps-0"
            >
        </div>

        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold small">
            Tìm kiếm
        </button>
    </form>
</div>

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
                                "{{ $comment->body }}"
                            </p>
                            @if($comment->parent_id)
                                <span class="d-inline-block mt-1 text-secondary fst-italic" style="font-size: 0.7rem;">
                                    &lrcorner; Trả lời cho bình luận #{{ $comment->parent_id }}
                                </span>
                            @endif
                        </td>

                        <!-- Target Post -->
                        <td class="px-4 py-3" style="max-width: 200px;">
                            <a href="{{ route('admin.posts.show', $comment->post) }}" class="small fw-semibold text-body text-decoration-none text-truncate d-block">
                                {{ $comment->post->title }}
                            </a>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-4 py-3">
                            @if($comment->status === 'approved')
                                <x-badge variant="status-published" size="xs">
                                    Đã duyệt (Công khai)
                                </x-badge>
                            @elseif($comment->status === 'pending')
                                <x-badge variant="status-pending" size="xs">
                                    Chờ xử lý (Thủ công)
                                </x-badge>
                            @else
                                <x-badge variant="status-rejected" size="xs">
                                    Spam
                                </x-badge>
                            @endif
                        </td>

                        <!-- Time -->
                        <td class="px-4 py-3 small text-secondary text-nowrap">
                            {{ $comment->created_at->diffForHumans() }}
                        </td>

                        <!-- Moderation Actions -->
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                            Duyệt
                                        </button>
                                    </form>
                                @endif

                                @if($comment->status !== 'spam')
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            Spam
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline"
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bình luận này?"
                                      data-confirm-target-name="{{ $comment->user?->name ?? 'Người dùng' }}"
                                      data-confirm-target-meta="{{ Str::limit($comment->content, 60) }}"
                                      data-confirm-description="Hành động này không thể hoàn tác. Bình luận và các phản hồi liên quan sẽ bị xóa hoàn toàn."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Xóa">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-5 text-center text-secondary small">
                            Không có bình luận nào phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection
