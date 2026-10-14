@extends('layouts.admin')

@section('admin_title', 'Thẩm định Bài viết: ' . $post->title)

@section('admin_content')
<<<<<<< HEAD
<div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-link btn-sm text-secondary text-decoration-none p-0">
        &larr; Quay lại danh sách bài viết
    </a>

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($post->status === 'published')
            <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 fw-bold">
                Xem bài viết công khai &rarr;
=======
<!-- Top Moderation Action Bar -->
<div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pb-3 border-theme-bottom">
    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary d-inline-flex align-items-center gap-1.5" style="font-size: 12.5px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Quay lại danh sách
    </a>

    <!-- Action Buttons -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($post->status === 'published')
            <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                <span>Xem bài viết công khai</span>
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
>>>>>>> origin/main
            </a>
        @endif

        @if($post->status !== 'published')
            <!-- Approve Button Form -->
<<<<<<< HEAD
            <form action="{{ route('admin.posts.approve', $post) }}" method="POST"
=======
            <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="m-0"
>>>>>>> origin/main
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
<<<<<<< HEAD
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-bold">
                    Duyệt & Xuất bản (Approve)
=======
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 py-1 fw-semibold" style="font-size: 12.5px;">
                    Duyệt & Xuất bản
>>>>>>> origin/main
                </button>
            </form>
        @endif

        @if($post->status !== 'rejected')
            <!-- Reject Button Form with prompt -->
<<<<<<< HEAD
            <form action="{{ route('admin.posts.reject', $post) }}" method="POST"
=======
            <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="m-0"
>>>>>>> origin/main
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
<<<<<<< HEAD
                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 fw-bold">
                    Từ chối phê duyệt (Reject)
=======
                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 fw-semibold" style="font-size: 12.5px;">
                    Từ chối duyệt
>>>>>>> origin/main
                </button>
            </form>
        @endif

        <!-- Delete Post Form -->
<<<<<<< HEAD
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
=======
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="m-0"
>>>>>>> origin/main
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
<<<<<<< HEAD
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-bold">
                Xóa bài viết
=======
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 12.5px;" title="Xóa bài viết">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                <span>Xóa bài viết</span>
>>>>>>> origin/main
            </button>
        </form>
    </div>
</div>

<!-- Rejection Reason Banner if rejected -->
@if($post->status === 'rejected' && $post->rejection_reason)
<<<<<<< HEAD
    <div class="alert alert-danger mb-4 p-3 rounded-3">
        <h4 class="h6 fw-bold mb-1 d-flex align-items-center gap-2 text-danger">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Lý do từ chối biên tập:
        </h4>
        <p class="small mb-0 fst-italic">{{ $post->rejection_reason }}</p>
=======
    <div class="alert alert-danger mb-4 p-3.5 rounded-xl border border-danger border-opacity-25 bg-danger-subtle shadow-xs">
        <h4 class="h6 fw-bold mb-1.5 d-flex align-items-center gap-2 text-danger">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Lý do từ chối biên tập:
        </h4>
        <p class="small mb-0 text-danger-emphasis fst-italic">{{ $post->rejection_reason }}</p>
>>>>>>> origin/main
    </div>
@endif

<div class="row g-4">
<<<<<<< HEAD
    <!-- Main Content Area -->
    <div class="col-12 col-lg-8">
        <div class="card p-4 p-sm-5 border shadow-sm rounded-3">
            <h1 class="h3 fw-bold text-body mb-3">
=======
    <!-- Main Content Area (Editorial Post Preview) -->
    <div class="col-12 col-lg-8">
        <article class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl">
            <!-- Title -->
            <h1 class="h3 fw-bold text-theme mb-3 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif); line-height: 1.35;">
>>>>>>> origin/main
                {{ $post->title }}
            </h1>

            <!-- Excerpt -->
            @if($post->excerpt)
<<<<<<< HEAD
                <div class="p-3 rounded-3 bg-body-tertiary border text-secondary small fst-italic mb-4">
=======
                <div class="p-3.5 rounded-xl bg-surface-2 border border-theme text-theme-secondary small fst-italic mb-4" style="line-height: 1.6;">
>>>>>>> origin/main
                    {{ $post->excerpt }}
                </div>
            @endif

            <!-- Thumbnail -->
            @if($post->thumbnail)
<<<<<<< HEAD
                <div class="mb-4 rounded-3 overflow-hidden border shadow-sm" style="max-height: 400px;">
                    <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-100 h-100 object-fit-cover">
=======
                <div class="mb-4 rounded-xl overflow-hidden border border-theme shadow-xs" style="max-height: 420px;">
                    <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="{{ $post->title }}" class="w-100 h-100 object-fit-cover">
>>>>>>> origin/main
                </div>
            @endif

            <!-- Article Body -->
<<<<<<< HEAD
            <div class="text-body border-top pt-4" style="white-space: pre-line; line-height: 1.8;">
=======
            <div class="text-theme border-theme-top pt-4 fs-base" style="white-space: pre-line; line-height: 1.85; font-size: 15.5px;">
>>>>>>> origin/main
                {{ $post->body }}
            </div>
        </article>
    </div>

<<<<<<< HEAD
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
=======
    <!-- Right Sidebar Metadata & Quick Actions -->
    <div class="col-12 col-lg-4">
        <div class="d-flex flex-column gap-3.5">
            <!-- Metadata Card -->
            <div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                <h3 class="label-uppercase text-theme-muted mb-3.5" style="font-size: 11px;">Thông tin Thẩm định</h3>

                <div class="mb-3">
                    <span class="small text-theme-muted d-block mb-1" style="font-size: 12px;">Trạng thái hiện tại:</span>
                    @if($post->status === 'published')
                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 11.5px;">
                            Đã xuất bản (Published)
                        </span>
                    @elseif($post->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill" style="font-size: 11.5px;">
                            Chờ thẩm định (Pending)
                        </span>
                    @elseif($post->status === 'rejected')
                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 11.5px;">
                            Bị từ chối (Rejected)
                        </span>
                    @else
                        <span class="badge bg-surface-2 text-theme-muted border border-theme rounded-pill" style="font-size: 11.5px;">
                            Bản nháp (Draft)
                        </span>
>>>>>>> origin/main
                    @endif
                </div>

                <div class="mb-3">
<<<<<<< HEAD
                    <span class="small text-secondary d-block mb-1">Tác giả:</span>
                    <a href="{{ route('admin.users.show', $post->user) }}" class="small fw-bold text-body text-decoration-none">
                        {{ $post->user->name }} ({{ $post->user->email }})
=======
                    <span class="small text-theme-muted d-block mb-1" style="font-size: 12px;">Tác giả:</span>
                    <a href="{{ route('admin.users.show', $post->user) }}" class="small fw-semibold text-theme text-decoration-none hover-accent d-block" style="font-size: 13px;">
                        {{ $post->user->name }} <span class="text-theme-muted fw-normal">({{ $post->user->email }})</span>
>>>>>>> origin/main
                    </a>
                </div>

                <div class="mb-3">
<<<<<<< HEAD
                    <span class="small text-secondary d-block mb-1">Chuyên mục:</span>
                    <span class="small fw-semibold text-body">{{ $post->category->name }}</span>
=======
                    <span class="small text-theme-muted d-block mb-1" style="font-size: 12px;">Chuyên mục:</span>
                    <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill" style="font-size: 11.5px;">
                        {{ $post->category->name }}
                    </span>
>>>>>>> origin/main
                </div>

                @if($post->tags->isNotEmpty())
                    <div class="mb-3">
<<<<<<< HEAD
                        <span class="small text-secondary d-block mb-1">Thẻ bài viết:</span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($post->tags as $tag)
                                <span class="badge bg-body-secondary text-secondary rounded-pill">
=======
                        <span class="small text-theme-muted d-block mb-1" style="font-size: 12px;">Thẻ bài viết:</span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($post->tags as $tag)
                                <span class="badge bg-surface-2 border border-theme text-theme-muted rounded-pill" style="font-size: 11px;">
>>>>>>> origin/main
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

<<<<<<< HEAD
                <div class="pt-3 border-top small text-secondary d-flex flex-column gap-1">
                    <p class="mb-0">Khởi tạo: <strong class="text-body">{{ $post->created_at->format('d/m/Y H:i') }}</strong></p>
                    @if($post->published_at)
                        <p class="mb-0">Xuất bản: <strong class="text-body">{{ $post->published_at->format('d/m/Y H:i') }}</strong></p>
                    @endif
                    @if($post->reviewer)
                        <p class="mb-0">Người kiểm duyệt: <strong class="text-body">{{ $post->reviewer->name }}</strong></p>
                        <p class="mb-0">Thời điểm kiểm duyệt: <strong class="text-body">{{ $post->reviewed_at?->format('d/m/Y H:i') }}</strong></p>
=======
                <div class="pt-3 border-theme-top small text-theme-muted d-flex flex-column gap-1.5" style="font-size: 12px;">
                    <div class="d-flex justify-content-between">
                        <span>Khởi tạo:</span>
                        <strong class="text-theme">{{ $post->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if($post->published_at)
                        <div class="d-flex justify-content-between">
                            <span>Xuất bản:</span>
                            <strong class="text-theme">{{ $post->published_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                    @if($post->reviewer)
                        <div class="d-flex justify-content-between">
                            <span>Kiểm duyệt bởi:</span>
                            <strong class="text-theme">{{ $post->reviewer->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Thời điểm duyệt:</span>
                            <strong class="text-theme">{{ $post->reviewed_at?->format('d/m/Y H:i') }}</strong>
                        </div>
>>>>>>> origin/main
                    @endif
                </div>
            </div>

            <!-- Rejection Box for quick reject -->
            @if($post->status !== 'rejected')
<<<<<<< HEAD
                <div class="card p-4 border shadow-sm rounded-3">
                    <h3 class="small fw-bold text-secondary text-uppercase tracking-wider mb-2">Từ chối bài viết</h3>
                    <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-flex flex-column gap-2">
=======
                <div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <h3 class="label-uppercase text-theme-muted mb-2" style="font-size: 11px;">Từ chối bài viết</h3>
                    <p class="small text-theme-muted mb-3" style="font-size: 12px;">Gửi nhận xét phản hồi để tác giả chỉnh sửa bổ sung.</p>
                    <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="d-flex flex-column gap-2.5">
>>>>>>> origin/main
                        @csrf
                        <textarea
                            name="rejection_reason"
                            rows="3"
                            placeholder="Nhập lý do hoặc góp ý chỉnh sửa cho tác giả..."
<<<<<<< HEAD
                            class="form-control small"
                        ></textarea>
                        <button
                            type="submit"
                            class="btn btn-outline-danger btn-sm rounded-pill fw-bold w-100 mt-1"
                        >
                            Gửi lý do & Từ chối phê duyệt
=======
                            class="form-control form-control-sm form-control-editorial rounded-3"
                            style="font-size: 13px;"
                        ></textarea>
                        <button
                            type="submit"
                            class="btn btn-outline-warning btn-sm rounded-pill fw-semibold w-100"
                            style="font-size: 12px;"
                        >
                            Gửi lý do & Từ chối duyệt
>>>>>>> origin/main
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

