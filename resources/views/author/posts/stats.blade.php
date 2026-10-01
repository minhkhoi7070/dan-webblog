@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px;">
    <!-- Top Action Bar -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small fw-semibold text-theme-secondary text-uppercase mb-1" style="font-size: 11px;">
                <span>Không gian tác giả</span>
                <span>&rsaquo;</span>
                <span style="color: #818cf8;">Biên tập & Thống kê</span>
            </div>
            <h1 class="h3 fw-bold text-theme tracking-tight mb-0">
                Quản lý bài viết của bạn
            </h1>
        </div>

        <a href="{{ route('posts.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold small shadow-sm d-inline-flex align-items-center gap-2">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Soạn bài viết mới
        </a>
    </div>

    <!-- Feedback messages -->
    @if(session('success'))
        <div class="alert alert-success mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics Grid (All 8 Metrics) -->
    <div class="row g-2 g-sm-3 mb-4">
        <!-- 1. Total Posts -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats') }}" class="card p-3 border-theme h-100 text-decoration-none {{ empty($currentStatus) ? 'bg-theme-surface-hover border-white' : 'bg-theme-surface' }}">
                <span class="small fw-semibold text-theme-secondary text-uppercase d-block" style="font-size: 10px;">Tổng bài</span>
                <p class="h4 fw-bold text-theme mt-1 mb-0">{{ number_format($stats['total']) }}</p>
            </a>
        </div>

        <!-- 2. Draft -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="card p-3 border-theme h-100 text-decoration-none {{ $currentStatus === 'draft' ? 'bg-theme-surface-hover border-white' : 'bg-theme-surface' }}">
                <span class="small fw-semibold text-theme-secondary text-uppercase d-block" style="font-size: 10px;">Bản nháp</span>
                <p class="h4 fw-bold text-theme-secondary mt-1 mb-0">{{ number_format($stats['draft']) }}</p>
            </a>
        </div>

        <!-- 3. Pending -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="card p-3 border-theme h-100 text-decoration-none {{ $currentStatus === 'pending' ? 'bg-warning-subtle border-warning' : 'bg-theme-surface' }}">
                <span class="small fw-semibold text-warning text-uppercase d-block" style="font-size: 10px;">Chờ duyệt</span>
                <p class="h4 fw-bold text-warning mt-1 mb-0">{{ number_format($stats['pending']) }}</p>
            </a>
        </div>

        <!-- 4. Published -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="card p-3 border-theme h-100 text-decoration-none {{ $currentStatus === 'published' ? 'bg-success-subtle border-success' : 'bg-theme-surface' }}">
                <span class="small fw-semibold text-success text-uppercase d-block" style="font-size: 10px;">Xuất bản</span>
                <p class="h4 fw-bold text-success mt-1 mb-0">{{ number_format($stats['published']) }}</p>
            </a>
        </div>

        <!-- 5. Rejected -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="card p-3 border-theme h-100 text-decoration-none {{ $currentStatus === 'rejected' ? 'bg-danger-subtle border-danger' : 'bg-theme-surface' }}">
                <span class="small fw-semibold text-danger text-uppercase d-block" style="font-size: 10px;">Từ chối</span>
                <p class="h4 fw-bold text-danger mt-1 mb-0">{{ number_format($stats['rejected']) }}</p>
            </a>
        </div>

        <!-- 6. Views -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="card p-3 border-theme h-100 bg-theme-surface">
                <span class="small fw-semibold text-theme-secondary text-uppercase d-block" style="font-size: 10px;">Lượt xem</span>
                <p class="h4 fw-bold mt-1 mb-0" style="color: #818cf8;">{{ number_format($stats['views']) }}</p>
            </div>
        </div>

        <!-- 7. Likes -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="card p-3 border-theme h-100 bg-theme-surface">
                <span class="small fw-semibold text-theme-secondary text-uppercase d-block" style="font-size: 10px;">Lượt thích</span>
                <p class="h4 fw-bold text-danger mt-1 mb-0">{{ number_format($stats['likes']) }}</p>
            </div>
        </div>

        <!-- 8. Comments -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="card p-3 border-theme h-100 bg-theme-surface">
                <span class="small fw-semibold text-theme-secondary text-uppercase d-block" style="font-size: 10px;">Bình luận</span>
                <p class="h4 fw-bold text-primary mt-1 mb-0">{{ number_format($stats['comments']) }}</p>
            </div>
        </div>
    </div>

    <!-- Posts Filter & Search Bar -->
    <div class="card p-3 border-theme shadow-sm mb-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <!-- Filter Tabs -->
            <div class="d-flex align-items-center gap-1 overflow-x-auto no-scrollbar py-1">
                <a href="{{ route('posts.stats') }}" class="btn btn-sm rounded-pill px-3 py-1 small {{ empty($currentStatus) ? 'btn-primary' : 'btn-outline-theme' }}">
                    Tất cả ({{ $stats['total'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="btn btn-sm rounded-pill px-3 py-1 small {{ $currentStatus === 'draft' ? 'btn-primary' : 'btn-outline-theme' }}">
                    Bản nháp ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 py-1 small {{ $currentStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-theme' }}">
                    Chờ duyệt ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="btn btn-sm rounded-pill px-3 py-1 small {{ $currentStatus === 'published' ? 'btn-success text-white' : 'btn-outline-theme' }}">
                    Đã xuất bản ({{ $stats['published'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill px-3 py-1 small {{ $currentStatus === 'rejected' ? 'btn-danger text-white' : 'btn-outline-theme' }}">
                    Bị từ chối ({{ $stats['rejected'] }})
                </a>
            </div>

            <!-- Search form -->
            <form action="{{ route('posts.stats') }}" method="GET" class="position-relative m-0" style="max-width: 260px;">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm trong bài viết..."
                    class="form-control form-control-sm rounded-pill ps-4 pe-3 py-1 small"
                >
                <svg style="width: 14px; height: 14px; position: absolute; left: 10px; top: 8px; pointer-events: none;" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="card border-theme shadow-sm overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3 ps-sm-4 py-3">Bài viết</th>
                        <th class="py-3">Chuyên mục</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3">Chỉ số tương tác</th>
                        <th class="pe-3 pe-sm-4 py-3 text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <!-- Title & Excerpt -->
                            <td class="ps-3 ps-sm-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($post->thumbnail)
                                        <img src="{{ asset($post->thumbnail) }}" alt="" class="rounded-3 flex-shrink-0 border-theme" style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <div class="rounded-3 bg-theme-surface-hover text-theme-secondary d-flex align-items-center justify-content-center flex-shrink-0 fw-bold border-theme" style="width: 48px; height: 48px; font-size: 11px;">
                                            DOC
                                        </div>
                                    @endif
                                    <div class="min-w-0" style="max-width: 320px;">
                                        <a href="{{ route('posts.edit', $post) }}" class="fw-bold text-theme text-decoration-none line-clamp-1 small">
                                            {{ $post->title }}
                                        </a>
                                        <div class="text-theme-secondary mt-0" style="font-size: 11px;">
                                            Cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3">
                                <span class="badge bg-theme-surface-hover text-theme border border-theme rounded-pill">
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
                                <div class="d-flex align-items-center gap-2">
                                    <span>{{ number_format($post->views) }} views</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->likers_count }} likes</span>
                                    <span>&bull;</span>
                                    <span>{{ $post->comments_count }} cmt</span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="pe-3 pe-sm-4 py-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Submit for review button (Available if draft or rejected) -->
                                    @if(in_array($post->status, ['draft', 'rejected'], true))
                                        <form action="{{ route('posts.submit', $post) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2" style="font-size: 11px;" title="Gửi xét duyệt">
                                                Gửi duyệt &rarr;
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Link -->
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-link text-theme-secondary p-1" title="Chỉnh sửa">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline m-0"
                                          data-confirm="true"
                                          data-confirm-title="Xóa bài viết"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa bài viết này không?"
                                          data-confirm-target-name="{{ $post->title }}"
                                          data-confirm-target-meta="Trạng thái: {{ $post->status_label }}"
                                          data-confirm-description="Hành động này không thể hoàn tác. Bài viết và dữ liệu tương ứng sẽ bị xóa."
                                          data-confirm-btn-text="Xóa bài viết"
                                          data-confirm-btn-class="btn-danger"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Xóa">
                                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-theme-secondary small">
                                Không tìm thấy bài viết nào. Hãy bấm "Soạn bài viết mới" để tạo bài viết đầu tiên!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
