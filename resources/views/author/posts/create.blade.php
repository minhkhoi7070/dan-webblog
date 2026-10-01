@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px;">
    <!-- Header & Navigation -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small fw-semibold text-theme-secondary text-uppercase mb-1" style="font-size: 11px;">
                <a href="{{ route('posts.stats') }}" class="text-decoration-none text-theme-secondary">Không gian tác giả</a>
                <span>&rsaquo;</span>
                <span style="color: #818cf8;">Soạn bài mới</span>
            </div>
            <h1 class="h3 fw-bold text-theme tracking-tight mb-0">
                Tạo bài viết mới
            </h1>
        </div>

        <div>
            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-4">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Có lỗi xảy ra trong dữ liệu nhập:</span>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Main Column (Content) -->
            <div class="col-12 col-lg-8">
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
                            value="{{ old('title') }}"
                            placeholder="Nhập tiêu đề ấn tượng và súc tích..."
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
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong feed (tối đa 500 ký tự)..."
                            class="form-control @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt') }}</textarea>
                        <div class="small text-theme-secondary mt-1" style="font-size: 11px;">Nếu bỏ trống, hệ thống sẽ tự động trích đoạn từ nội dung.</div>
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
                        rows="16"
                        placeholder="Bắt đầu chia sẻ kiến thức của bạn ở đây..."
                        required
                        class="form-control font-monospace @error('content') is-invalid @enderror @error('body') is-invalid @enderror"
                        style="font-size: 0.9rem;"
                    >{{ old('content', old('body')) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Right Sidebar Column (Meta & Actions) -->
            <div class="col-12 col-lg-4">
                <div class="d-flex flex-column gap-4">
                    <!-- Publishing Workflow Status Box -->
                    <div class="card p-4 border-theme shadow-sm">
                        <h2 class="h6 fw-bold text-theme mb-3 d-flex align-items-center gap-2">
                            <svg style="width: 16px; height: 16px; color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Trạng thái xuất bản
                        </h2>

                        <div class="p-3 rounded-3 bg-theme-surface-hover border-theme mb-3 small text-theme-secondary">
                            <p class="fw-semibold text-theme mb-1">Quy trình kiểm duyệt:</p>
                            Bài viết mới sẽ được lưu dưới dạng <strong>Bản nháp (Draft)</strong>. Sau khi hoàn thiện, bạn có thể gửi xét duyệt để Quản trị viên phê duyệt.
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold small shadow-sm d-flex align-items-center justify-content-center gap-2"
                            >
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Lưu bản nháp (Draft)
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                class="btn btn-outline-theme rounded-pill w-100 py-2 small"
                            >
                                Hủy bỏ
                            </a>
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
                            required
                            class="form-select @error('category_id') is-invalid @enderror"
                        >
                            <option value="">-- Chọn chuyên mục --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="card p-4 border-theme shadow-sm">
                        <label class="form-label small fw-bold text-theme mb-2">
                            Ảnh đại diện (Thumbnail)
                        </label>

                        <div
                            id="drop-zone"
                            class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-theme-surface-hover"
                            style="cursor: pointer;"
                        >
                            <input
                                type="file"
                                name="thumbnail"
                                id="thumbnail"
                                accept="image/jpeg,image/png,image/webp,image/jpg"
                                class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                                onchange="previewThumbnail(event)"
                            >
                            <div id="upload-placeholder" class="py-3">
                                <svg class="mx-auto mb-2 text-theme-secondary" style="width: 32px; height: 32px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="small text-theme fw-medium mb-1">Bấm để tải ảnh hoặc kéo thả vào đây</p>
                                <p class="small text-theme-secondary mb-0" style="font-size: 11px;">JPG, PNG, WEBP tối đa 2MB</p>
                            </div>

                            <div id="preview-container" class="d-none">
                                <img id="image-preview" src="#" alt="Thumbnail preview" class="img-fluid rounded-3 mb-2" style="max-height: 160px; object-fit: cover;">
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
                            $selectedTags = old('tags', []);
                        @endphp

                        <div class="d-flex flex-wrap gap-2 overflow-y-auto no-scrollbar" style="max-height: 200px;">
                            @foreach ($tags as $tag)
                                <input
                                    type="checkbox"
                                    class="btn-check"
                                    id="tag-{{ $tag->id }}"
                                    name="tags[]"
                                    value="{{ $tag->id }}"
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
    </form>
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
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
