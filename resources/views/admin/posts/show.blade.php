@extends('layouts.admin')

@section('admin_title', 'Thẩm định Bài viết: ' . $post->title)

@section('admin_content')
<div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-link btn-sm text-secondary text-decoration-none p-0">
        &larr; Quay lại danh sách bài viết
    </a>

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($post->status === 'published')
            <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 fw-bold">
                Xem bài viết công khai &rarr;
            </a>
        @endif

        @if($post->status !== 'published')
            <!-- Approve Button Form -->
            <form action="{{ route('admin.posts.approve', $post) }}" method="POST"
                  data-confirm="true"
                  data-confirm-title="Duyệt bài viết"
                  data-confirm-message="Bạn có chắc chắn muốn duyệt và xuất bản ngay bài viết này?"
                  data-confirm-target-name="{{ $post->title }}"
                  data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                  data-confirm-description="Bài viết sẽ được chuyển sang trạng thái đã xuất bản và hiển thị công khai trên BlogMNM."
                  data-confirm-btn-text="Duyệt bài viết"
                  data-confirm-btn-class="btn-success"
                  data-confirm-type="success">
                @csrf
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold">
                    Duyệt & Xuất bản (Approve)
                </button>
            </form>
        @endif

        @if($post->status !== 'rejected')
            <!-- Reject Button Form with prompt -->
            <form action="{{ route('admin.posts.reject', $post) }}" method="POST"
                  data-confirm="true"
                  data-confirm-title="Từ chối phê duyệt"
                  data-confirm-message="Vui lòng cung cấp lý do từ chối bài viết:"
                  data-confirm-target-name="{{ $post->title }}"
                  data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
                  data-confirm-description="Tác giả sẽ thấy phản hồi này để chỉnh sửa lại bài viết cho phù hợp."
                  data-confirm-prompt="true"
                  data-confirm-prompt-name="rejection_reason"
                  data-confirm-prompt-label="Lý do từ chối:"
                  data-confirm-prompt-placeholder="Nhập lý do từ chối bài viết..."
                  data-confirm-btn-text="Từ chối bài viết"
                  data-confirm-btn-class="btn-warning"
                  data-confirm-type="warning">
                @csrf
                <input type="hidden" name="rejection_reason" value="">
                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 fw-bold">
                    Từ chối phê duyệt (Reject)
                </button>
            </form>
        @endif

        <!-- Delete Post Form -->
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
              data-confirm="true"
              data-confirm-title="Xóa vĩnh viễn bài viết"
              data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này?"
              data-confirm-target-name="{{ $post->title }}"
              data-confirm-target-meta="Tác giả: {{ $post->user->name }}"
              data-confirm-description="Hành động này không thể hoàn tác. Bài viết và toàn bộ dữ liệu liên quan sẽ bị xóa khỏi hệ thống."
              data-confirm-btn-text="Xóa bài viết"
              data-confirm-btn-class="btn-danger"
              data-confirm-type="danger">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-bold">
                Xóa bài viết
            </button>
        </form>
    </div>
</div>

<!-- Rejection Reason Banner if rejected -->
@if($post->status === 'rejected' && $post->rejection_reason)
    <div class="alert alert-danger mb-4 p-3 rounded-3">
        <h4 class="h6 fw-bold mb-1 d-flex align-items-center gap-2 text-danger">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Lý do từ chối biên tập:
        </h4>
        <p class="small mb-0 fst-italic">{{ $post->rejection_reason }}</p>
    </div>
@endif

<div class="row g-4">
    <!-- Main Content Area -->
    <div class="col-12 col-lg-8">
        <div class="card p-4 p-sm-5 border shadow-sm rounded-3">
            <h1 class="h3 fw-bold text-body mb-3">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <div class="p-3 rounded-3 bg-body-tertiary border text-secondary small fst-italic mb-4">
                    {{ $post->excerpt }}
                </div>
            @endif

            @if($post->thumbnail)
                <div class="mb-4 rounded-3 overflow-hidden border shadow-sm" style="max-height: 400px;">
                    <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-100 h-100 object-fit-cover">
                </div>
            @endif

            <!-- Article Body -->
            <div class="text-body border-top pt-4" style="white-space: pre-line; line-height: 1.8;">
                {{ $post->body }}
            </div>
        </div>
    </div>

    <!-- Right Sidebar Metadata -->
    <div class="col-12 col-lg-4">
        <div class="d-flex flex-column gap-4">
            <!-- Status & Reviewer Card -->
            <div class="card p-4 border shadow-sm rounded-3">
                <h3 class="small fw-bold text-secondary text-uppercase tracking-wider mb-3">Thông tin Xuất bản & Kiểm duyệt</h3>

                <div class="mb-3">
                    <span class="small text-secondary d-block mb-1">Trạng thái hiện tại:</span>
                    @if($post->status === 'published')
                        <x-badge variant="status-published" size="sm">Đã xuất bản (Published)</x-badge>
                    @elseif($post->status === 'pending')
                        <x-badge variant="status-pending" size="sm">Chờ thẩm định (Pending)</x-badge>
                    @elseif($post->status === 'rejected')
                        <x-badge variant="status-rejected" size="sm">Bị từ chối (Rejected)</x-badge>
                    @else
                        <x-badge variant="status-draft" size="sm">Bản nháp (Draft)</x-badge>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="small text-secondary d-block mb-1">Tác giả:</span>
                    <a href="{{ route('admin.users.show', $post->user) }}" class="small fw-bold text-body text-decoration-none">
                        {{ $post->user->name }} ({{ $post->user->email }})
                    </a>
                </div>

                <div class="mb-3">
                    <span class="small text-secondary d-block mb-1">Chuyên mục:</span>
                    <span class="small fw-semibold text-body">{{ $post->category->name }}</span>
                </div>

                @if($post->tags->isNotEmpty())
                    <div class="mb-3">
                        <span class="small text-secondary d-block mb-1">Thẻ bài viết:</span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($post->tags as $tag)
                                <span class="badge bg-body-secondary text-secondary rounded-pill">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-3 border-top small text-secondary d-flex flex-column gap-1">
                    <p class="mb-0">Khởi tạo: <strong class="text-body">{{ $post->created_at->format('d/m/Y H:i') }}</strong></p>
                    @if($post->published_at)
                        <p class="mb-0">Xuất bản: <strong class="text-body">{{ $post->published_at->format('d/m/Y H:i') }}</strong></p>
                    @endif
                    @if($post->reviewer)
                        <p class="mb-0">Người kiểm duyệt: <strong class="text-body">{{ $post->reviewer->name }}</strong></p>
                        <p class="mb-0">Thời điểm kiểm duyệt: <strong class="text-body">{{ $post->reviewed_at?->format('d/m/Y H:i') }}</strong></p>
                    @endif
                </div>
            </div>

            <!-- Rejection Box for quick reject -->
            @if($post->status !== 'rejected')
                <div class="card p-4 border shadow-sm rounded-3">
                    <h3 class="small fw-bold text-secondary text-uppercase tracking-wider mb-2">Từ chối bài viết</h3>
                    <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-flex flex-column gap-2">
                        @csrf
                        <textarea
                            name="rejection_reason"
                            rows="3"
                            placeholder="Nhập lý do hoặc góp ý chỉnh sửa cho tác giả..."
                            class="form-control small"
                        ></textarea>
                        <button
                            type="submit"
                            class="btn btn-outline-danger btn-sm rounded-pill fw-bold w-100 mt-1"
                        >
                            Gửi lý do & Từ chối phê duyệt
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
