@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">
    <!-- Top Dashboard Header (Ghost + Notion style) -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 small text-theme-muted label-uppercase mb-0.5" style="font-size: 11px;">
                <span>Tác giả</span>
                <span>&rsaquo;</span>
                <span class="text-accent">Bảng điều khiển</span>
            </div>
            <h1 class="h4 fw-bold text-theme tracking-tight mb-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                Quản lý bài viết & Thống kê
            </h1>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('posts.create') }}" class="btn btn-editorial-primary btn-sm rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Soạn bài viết mới
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
                <span class="text-theme-muted text-2xs">Tất cả bài</span>
            </a>
        </div>

        <!-- 2. Draft -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'draft' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bản nháp</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['draft']) }}</p>
                <span class="text-theme-muted text-2xs">Đang soạn</span>
            </a>
        </div>

        <!-- 3. Pending -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'pending' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-warning text-3xs">Chờ duyệt</span>
                <p class="h4 fw-bold text-warning m-0">{{ number_format($stats['pending']) }}</p>
                <span class="text-theme-muted text-2xs">Đang xét duyệt</span>
            </a>
        </div>

        <!-- 4. Published -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'published' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-success text-3xs">Xuất bản</span>
                <p class="h4 fw-bold m-0 text-success">{{ number_format($stats['published']) }}</p>
                <span class="text-theme-muted text-2xs">Công khai</span>
            </a>
        </div>

        <!-- 5. Rejected -->
        <div class="col-6 col-sm-3 col-lg">
            <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="dashboard-stat-card h-100 {{ $currentStatus === 'rejected' ? 'active-filter' : '' }}">
                <span class="label-uppercase d-block mb-1 text-danger text-3xs">Bị từ chối</span>
                <p class="h4 fw-bold text-danger m-0">{{ number_format($stats['rejected']) }}</p>
                <span class="text-theme-muted text-2xs">Cần chỉnh sửa</span>
            </a>
        </div>

        <!-- 6. Views -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs">Lượt xem</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-accent-text);">{{ number_format($stats['views']) }}</p>
                <span class="text-theme-muted text-2xs">Tổng tương tác</span>
            </div>
        </div>

        <!-- 7. Likes -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-3xs" style="color: var(--color-like);">Lượt thích</span>
                <p class="h4 fw-bold m-0" style="color: var(--color-like);">{{ number_format($stats['likes']) }}</p>
                <span class="text-theme-muted text-2xs">Từ độc giả</span>
            </div>
        </div>

        <!-- 8. Comments -->
        <div class="col-6 col-sm-3 col-lg">
            <div class="dashboard-stat-card h-100" style="cursor: default;">
                <span class="label-uppercase d-block mb-1 text-theme-secondary text-3xs">Bình luận</span>
                <p class="h4 fw-bold text-theme m-0">{{ number_format($stats['comments']) }}</p>
                <span class="text-theme-muted text-2xs">Thảo luận</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search Controls (Notion style Database toolbar) -->
    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- Filter Tabs -->
            <div class="d-flex align-items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                <a href="{{ route('posts.stats') }}" class="btn btn-sm rounded-pill px-3 py-1 small fw-medium {{ empty($currentStatus) ? 'btn-editorial-primary' : 'btn-outline-theme' }}" style="font-size: 12.5px;">
                    Tất cả ({{ $stats['total'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="btn btn-sm rounded-pill px-3 py-1 small fw-medium {{ $currentStatus === 'draft' ? 'btn-editorial-primary' : 'btn-outline-theme' }}" style="font-size: 12.5px;">
                    Bản nháp ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 py-1 small fw-medium {{ $currentStatus === 'pending' ? 'btn-warning text-dark' : 'btn-outline-theme' }}" style="font-size: 12.5px;">
                    Chờ duyệt ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="btn btn-sm rounded-pill px-3 py-1 small fw-medium {{ $currentStatus === 'published' ? 'btn-editorial-primary' : 'btn-outline-theme' }}" style="font-size: 12.5px;">
                    Đã xuất bản ({{ $stats['published'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="btn btn-sm rounded-pill px-3 py-1 small fw-medium {{ $currentStatus === 'rejected' ? 'btn-outline-danger' : 'btn-outline-theme' }}" style="font-size: 12.5px;">
                    Bị từ chối ({{ $stats['rejected'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('posts.stats') }}" method="GET" class="position-relative m-0" style="min-width: 240px; max-width: 280px;">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm theo tiêu đề bài..."
                    class="form-control form-control-sm form-control-editorial rounded-pill ps-4 pe-3 py-1 small"
                    style="font-size: 12.5px;"
                >
                <svg style="width: 14px; height: 14px; position: absolute; left: 11px; top: 9px; pointer-events: none;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
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
                                    <div class="min-w-0" style="max-width: 380px;">
                                        <a href="{{ route('posts.edit', $post) }}" class="fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block small" style="font-size: 14px;">
                                            {{ $post->title }}
                                        </a>
                                        <div class="small text-theme-muted mt-0.5" style="font-size: 11.5px;">
                                            Cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}
                                        </div>
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
                                <div class="d-flex align-items-center justify-content-end gap-1.5">
                                    <!-- Submit for review button (Available if draft or rejected) -->
                                    @if(in_array($post->status, ['draft', 'rejected'], true))
                                        <form action="{{ route('posts.submit', $post) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-theme rounded-pill py-1 px-2.5 text-warning" style="font-size: 11px;" title="Gửi xét duyệt" aria-label="Gửi xét duyệt bài viết {{ $post->title }}">
                                                Gửi duyệt &rarr;
                                            </button>
                                        </form>
                                    @endif

                                    <!-- View Live (if published) -->
                                    @if($post->status === 'published')
                                        <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-theme rounded-pill py-1 px-2 text-theme-secondary" style="font-size: 11px;" title="Xem bài viết trực tiếp" aria-label="Xem bài viết trực tiếp {{ $post->title }}">
                                            Live &nearr;
                                        </a>
                                    @endif

                                    <!-- Edit Link -->
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-outline-theme rounded-pill py-1 px-2 text-theme-secondary" style="font-size: 11px;" title="Chỉnh sửa bài viết" aria-label="Chỉnh sửa bài viết {{ $post->title }}">
                                        Sửa
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
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill py-1 px-2" style="font-size: 11px;" title="Xóa" aria-label="Xóa bài viết {{ $post->title }}">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-theme-secondary small">
                                <div class="py-4">
                                    <p class="text-theme fw-medium mb-1">Không tìm thấy bài viết nào</p>
                                    <p class="small text-theme-muted mb-3">Bắt đầu sáng tạo bài viết đầu tiên của bạn trên nền tảng BlogMNM</p>
                                    <a href="{{ route('posts.create') }}" class="btn btn-editorial-primary btn-sm rounded-pill px-4">
                                        Soạn bài viết mới
                                    </a>
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
