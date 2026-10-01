@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt & Quản lý Bài viết')

@section('admin_content')
<!-- Status Tabs -->
<div class="d-flex flex-wrap align-items-center gap-2 mb-4 pb-2 border-bottom">
    <a
        href="{{ route('admin.posts.index') }}"
        class="btn btn-sm rounded-pill fw-bold {{ empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' }}"
    >
        Tất cả ({{ number_format($statusCounts['all']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'pending']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}"
    >
        Chờ duyệt ({{ number_format($statusCounts['pending']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'published']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'published' ? 'btn-success' : 'btn-outline-success' }}"
    >
        Đã xuất bản ({{ number_format($statusCounts['published']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}"
    >
        Bản nháp ({{ number_format($statusCounts['draft']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'rejected']) }}"
        class="btn btn-sm rounded-pill fw-bold {{ $currentStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}"
    >
        Bị từ chối ({{ number_format($statusCounts['rejected']) }})
    </a>
</div>

<!-- Search & Category Filters -->
<div class="card p-3 border shadow-sm rounded-3 mb-4">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2 align-items-center">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="col-12 col-sm-6 col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm bài viết theo tiêu đề..."
                    class="form-control border-start-0 ps-0"
                >
            </div>
        </div>

        <div class="col-12 col-sm-4 col-md-3">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="form-select"
            >
                <option value="">-- Tất cả chuyên mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($currentCategory == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-sm-2 col-md-2">
            <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold small">
                Lọc
            </button>
        </div>
    </form>
</div>

<!-- Posts Moderation Table -->
<div class="card border shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-bold text-secondary">
                <tr>
                    <th class="px-4 py-3">Bài viết</th>
                    <th class="px-4 py-3">Tác giả</th>
                    <th class="px-4 py-3">Chuyên mục</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Tương tác</th>
                    <th class="px-4 py-3">Kiểm duyệt bởi</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <!-- Post Info with Thumbnail -->
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 overflow-hidden bg-body-secondary flex-shrink-0 border" style="width: 50px; height: 40px;">
                                    @if($post->thumbnail)
                                        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0" style="max-width: 250px;">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="fw-bold text-body text-decoration-none text-truncate d-block small">
                                        {{ $post->title }}
                                    </a>
                                    <span class="small text-secondary" style="font-size: 0.75rem;">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Author -->
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $post->user) }}" class="small fw-semibold text-body text-decoration-none d-block">
                                {{ $post->user->name }}
                            </a>
                        </td>

                        <!-- Category -->
                        <td class="px-4 py-3 small fw-medium text-body">
                            {{ $post->category->name }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-4 py-3">
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

                        <!-- Interactions -->
                        <td class="px-4 py-3 small text-secondary" style="font-size: 0.75rem;">
                            <span>{{ number_format($post->views) }} views</span> &bull;
                            <span>{{ number_format($post->likers_count) }} likes</span> &bull;
                            <span>{{ number_format($post->comments_count) }} cmt</span>
                        </td>

                        <!-- Reviewer Info -->
                        <td class="px-4 py-3 small text-secondary" style="font-size: 0.75rem;">
                            @if($post->reviewer)
                                <span class="fw-medium text-body d-block">{{ $post->reviewer->name }}</span>
                                <span class="text-secondary">{{ $post->reviewed_at?->format('d/m H:i') }}</span>
                            @else
                                <span>&mdash;</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($post->status === 'pending')
                                    <!-- Approve Action -->
                                    <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="d-inline"
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
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                            Duyệt
                                        </button>
                                    </form>

                                    <!-- Reject Action -->
                                    <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-inline"
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
                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 fw-bold" style="font-size: 0.75rem;">
                                            Từ chối
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.75rem;">
                                    Chi tiết
                                </a>

                                <!-- Delete Action -->
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline"
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
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Xóa bài viết">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center text-secondary small">
                            Không có bài viết nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
