@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px;">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small fw-semibold text-theme-secondary text-uppercase mb-1" style="font-size: 11px;">
                <a href="{{ route('posts.stats') }}" class="text-decoration-none text-theme-secondary">Không gian tác giả</a>
                <span>&rsaquo;</span>
                <span style="color: #818cf8;">Chỉnh sửa bài viết</span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <h1 class="h3 fw-bold text-theme tracking-tight mb-0">
                    Chỉnh sửa bài viết
                </h1>
                <!-- Status Badge -->
                @if($post->status === 'published')
                    <x-badge variant="status-published" size="sm">
                        Đã xuất bản (Published)
                    </x-badge>
                @elseif($post->status === 'pending')
                    <x-badge variant="status-pending" size="sm">
                        Chờ duyệt (Pending)
                    </x-badge>
                @elseif($post->status === 'rejected')
                    <x-badge variant="status-rejected" size="sm">
                        Bị từ chối (Rejected)
                    </x-badge>
                @else
                    <x-badge variant="status-draft" size="sm">
                        Bản nháp (Draft)
                    </x-badge>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($post->status === 'published')
                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 d-inline-flex align-items-center gap-1">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem bài live
                </a>
            @endif

            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-4">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Success Feedback Message -->
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

    <!-- Rejection Alert / Resubmit Guidance -->
    @if($post->status === 'rejected')
        <div class="alert alert-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="h6 fw-bold mb-1">Bài viết đã bị Ban biên tập từ chối phê duyệt</h3>
                    <p class="small mb-0">
                        Bạn có thể rà soát và chỉnh sửa nội dung bên dưới theo tiêu chuẩn biên tập, sau đó bấm <strong>"Gửi lại xét duyệt (Resubmit)"</strong> để gửi lại cho Ban biên tập.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Có lỗi xảy ra khi lưu:</span>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Main Column (Content Form) -->
        <div class="col-12 col-lg-8">
            <form id="edit-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title & Excerpt -->
                <div class="card p-4 border-theme shadow-sm mb-4">
                    <div class="mb-3">
                        <label for="title" class="form-label small fw-bold text-theme mb-1">
                            Tiêu đề bài viết <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $post->title) }}"
                            placeholder="Nhập tiêu đề bài viết..."
                            required
                            class="form-control form-control-lg fs-5 fw-semibold @error('title') is-invalid @enderror"
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="form-label small fw-bold text-theme mb-1">
                            Mô tả tóm tắt (Excerpt)
                        </label>
                        <textarea
                            name="excerpt"
                            id="excerpt"
                            rows="2"
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong feed..."
                            class="form-control @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Body / Content -->
                <div class="card p-4 border-theme shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label for="content" class="form-label small fw-bold text-theme mb-0">
                            Nội dung chi tiết <span class="text-danger">*</span>
                        </label>
                        <span class="small text-theme-secondary">Hỗ trợ định dạng văn bản</span>
                    </div>

                    <textarea
                        name="content"
                        id="content"
                        rows="18"
                        placeholder="Nội dung bài viết..."
                        required
                        class="form-control font-monospace @error('content') is-invalid @enderror @error('body') is-invalid @enderror"
                        style="font-size: 0.9rem;"
                    >{{ old('content', old('body', $post->body)) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>

        <!-- Right Sidebar Column (Meta & Actions) -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Publishing Actions Box -->
                <div class="card p-4 border-theme shadow-sm">
                    <h2 class="h6 fw-bold text-theme mb-3 d-flex align-items-center gap-2">
                        <svg style="width: 16px; height: 16px; color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Hành động & Phê duyệt
                    </h2>

                    <div class="d-flex flex-column gap-2">
                        <!-- Update Button -->
                        <button
                            type="submit"
                            form="edit-post-form"
                            class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold small shadow-sm d-flex align-items-center justify-content-center gap-2"
                        >
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi
                        </button>

                        <!-- Submit / Resubmit for Review Button -->
                        @if(in_array($post->status, ['draft', 'rejected'], true))
                            <form action="{{ route('posts.submit', $post) }}" method="POST" class="m-0">
                                @csrf
                                <button
                                    type="submit"
                                    class="btn btn-warning rounded-pill w-100 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2"
                                >
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $post->status === 'rejected' ? 'Gửi lại xét duyệt (Resubmit)' : 'Gửi xét duyệt (Submit)' }}
                                </button>
                            </form>
                        @elseif($post->status === 'pending')
                            <div class="alert alert-warning py-2 px-3 small text-center mb-0">
                                Bài viết đang trong hàng đợi xét duyệt của Ban biên tập.
                            </div>
                        @endif

                        <!-- Delete Button Form -->
                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="m-0"
                              data-confirm="true"
                              data-confirm-title="Xóa bài viết"
                              data-confirm-message="Bạn có chắc chắn muốn xóa bài viết này không?"
                              data-confirm-target-name="{{ $post->title }}"
                              data-confirm-target-meta="Trạng thái: {{ $post->status_label }}"
                              data-confirm-description="Hành động này không thể hoàn tác. Bản nháp hoặc bài viết này sẽ bị xóa hoàn toàn."
                              data-confirm-btn-text="Xóa bài viết"
                              data-confirm-btn-class="btn-danger"
                              data-confirm-type="danger">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-outline-danger rounded-pill w-100 py-2 small d-flex align-items-center justify-content-center gap-2"
                            >
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Xóa bài viết
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="card p-4 border-theme shadow-sm">
                    <label for="category_id" class="form-label small fw-bold text-theme mb-2">
                        Chuyên mục <span class="text-danger">*</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        form="edit-post-form"
                        required
                        class="form-select @error('category_id') is-invalid @enderror"
                    >
                        <option value="">-- Chọn chuyên mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thumbnail Upload & Current Image Preview -->
                <div class="card p-4 border-theme shadow-sm">
                    <label class="form-label small fw-bold text-theme mb-2">
                        Ảnh đại diện (Thumbnail)
                    </label>

                    @if($post->thumbnail)
                        <div id="current-thumbnail-wrapper" class="mb-3">
                            <p class="small text-theme-secondary mb-1">Ảnh hiện tại:</p>
                            <div class="rounded-3 overflow-hidden border-theme">
                                <img
                                    src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}"
                                    alt="{{ $post->title }}"
                                    class="img-fluid w-100"
                                    style="max-height: 140px; object-fit: cover;"
                                >
                            </div>
                        </div>
                    @endif

                    <div
                        id="drop-zone"
                        class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-theme-surface-hover"
                        style="cursor: pointer;"
                    >
                        <input
                            type="file"
                            name="thumbnail"
                            id="thumbnail"
                            form="edit-post-form"
                            accept="image/jpeg,image/png,image/webp,image/jpg"
                            class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                            onchange="previewThumbnail(event)"
                        >
                        <div id="upload-placeholder" class="py-2">
                            <svg class="mx-auto mb-1 text-theme-secondary" style="width: 28px; height: 28px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="small text-theme fw-medium mb-1">Chọn ảnh mới để thay thế</p>
                            <p class="small text-theme-secondary mb-0" style="font-size: 11px;">JPG, PNG, WEBP tối đa 2MB</p>
                        </div>

                        <div id="preview-container" class="d-none">
                            <img id="image-preview" src="#" alt="Thumbnail preview" class="img-fluid rounded-3 mb-2" style="max-height: 140px; object-fit: cover;">
                            <p class="small mb-0" style="color: #818cf8; font-size: 11px;">Bấm để đổi ảnh khác</p>
                        </div>
                    </div>
                    @error('thumbnail')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tags Selection -->
                <div class="card p-4 border-theme shadow-sm">
                    <label class="form-label small fw-bold text-theme mb-1">
                        Thẻ bài viết (Tags)
                    </label>
                    <p class="small text-theme-secondary mb-3">Chọn các từ khóa chủ đề liên quan:</p>

                    @php
                        $currentTagIds = $post->tags->pluck('id')->all();
                        $selectedTags = old('tags', $currentTagIds);
                    @endphp

                    <div class="d-flex flex-wrap gap-2 overflow-y-auto no-scrollbar" style="max-height: 200px;">
                        @foreach ($tags as $tag)
                            <input
                                type="checkbox"
                                class="btn-check"
                                id="tag-{{ $tag->id }}"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                form="edit-post-form"
                                autocomplete="off"
                                @checked(in_array($tag->id, (array)$selectedTags))
                            >
                            <label class="btn btn-sm btn-outline-theme rounded-pill py-1 px-3 small" for="tag-{{ $tag->id }}">
                                #{{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('tags')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewThumbnail(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('upload-placeholder').classList.add('d-none');
            document.getElementById('preview-container').classList.remove('d-none');
            const currentWrapper = document.getElementById('current-thumbnail-wrapper');
            if (currentWrapper) {
                currentWrapper.style.opacity = '0.4';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
