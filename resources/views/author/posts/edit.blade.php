@extends('layouts.public')

@section('content')
<<<<<<< HEAD
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
=======
<div class="container-fluid py-4" style="max-width: 1200px;">
    <!-- Top Publishing Bar (Ghost + Notion style) -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0" style="width: 38px; height: 38px;" title="Quay lại bảng điều khiển" aria-label="Quay lại bảng điều khiển">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 small text-theme-muted label-uppercase mb-0.5" style="font-size: 11px;">
                    <a href="{{ route('posts.stats') }}" class="text-decoration-none text-theme-muted hover-accent">Tác giả</a>
                    <span>&rsaquo;</span>
                    <span class="text-accent">Chỉnh sửa bài viết</span>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="fw-bold text-theme fs-5" style="font-family: var(--font-serif, 'Lora', serif);">Chỉnh sửa bài viết</span>
                    
                    <!-- Status Badge -->
                    @if($post->status === 'published')
                        <x-badge variant="status-published" size="xs">
                            Đã xuất bản (Published)
                        </x-badge>
                    @elseif($post->status === 'pending')
                        <x-badge variant="status-pending" size="xs">
                            Chờ duyệt (Pending)
                        </x-badge>
                    @elseif($post->status === 'rejected')
                        <x-badge variant="status-rejected" size="xs">
                            Bị từ chối (Rejected)
                        </x-badge>
                    @else
                        <x-badge variant="status-draft" size="xs">
                            Bản nháp (Draft)
                        </x-badge>
                    @endif

                    <span class="small text-theme-muted d-none d-md-inline" style="font-size: 12px;">
                        &bull; Cập nhật {{ $post->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            @if($post->status === 'published')
                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5" title="Mở trang xem bài viết trên website">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem bài live
                </a>
            @endif

<<<<<<< HEAD
            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-4">
=======
            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium">
>>>>>>> origin/main
                Quay lại
            </a>

            <!-- Save Changes Primary Button -->
            <button type="submit" form="edit-post-form" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
        </div>
    </div>

    <!-- Success Feedback Message -->
    @if(session('success'))
<<<<<<< HEAD
        <div class="alert alert-success mb-4 shadow-sm" role="alert">
=======
        <div class="alert alert-success mb-4 shadow-xs border border-success border-opacity-25 rounded-3" role="alert">
>>>>>>> origin/main
            <div class="d-flex align-items-center gap-2">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="small fw-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Rejection Alert / Resubmit Guidance -->
    @if($post->status === 'rejected')
<<<<<<< HEAD
        <div class="alert alert-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
        <div class="alert alert-danger mb-4 shadow-xs border border-danger border-opacity-25 rounded-3" role="alert">
            <div class="d-flex align-items-start gap-2.5">
                <svg style="width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="h6 fw-bold mb-1">Bài viết đã bị Ban biên tập từ chối phê duyệt</h3>
                    <p class="small mb-0">
<<<<<<< HEAD
                        Bạn có thể rà soát và chỉnh sửa nội dung bên dưới theo tiêu chuẩn biên tập, sau đó bấm <strong>"Gửi lại xét duyệt (Resubmit)"</strong> để gửi lại cho Ban biên tập.
=======
                        Vui lòng rà soát lại tiêu đề, tóm tắt và nội dung bài viết theo tiêu chuẩn cộng đồng, sau đó nhấn <strong>"Gửi lại xét duyệt (Resubmit)"</strong> ở góc phải để gửi lại cho Ban biên tập.
>>>>>>> origin/main
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Summary Alert -->
    @if ($errors->any())
<<<<<<< HEAD
        <div class="alert alert-danger mb-4 shadow-sm" role="alert">
=======
        <div class="alert alert-danger mb-4 shadow-xs border border-danger border-opacity-25 rounded-3" role="alert">
>>>>>>> origin/main
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Có lỗi xảy ra khi lưu bài viết:</span>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
<<<<<<< HEAD
        <!-- Left Main Column (Content Form) -->
=======
        <!-- Left Main Writing Canvas (Readable Editor) -->
>>>>>>> origin/main
        <div class="col-12 col-lg-8">
            <form id="edit-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

<<<<<<< HEAD
                <!-- Title & Excerpt -->
                <div class="card p-4 border-theme shadow-sm mb-4">
                    <div class="mb-3">
                        <label for="title" class="form-label small fw-bold text-theme mb-1">
                            Tiêu đề bài viết <span class="text-danger">*</span>
                        </label>
=======
                <div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
                    <!-- Title Input (Notion / Ghost seamless title) -->
                    <div class="mb-3">
                        <label for="title" class="visually-hidden">Tiêu đề bài viết</label>
>>>>>>> origin/main
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $post->title) }}"
                            placeholder="Tiêu đề bài viết..."
                            required
<<<<<<< HEAD
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
=======
                            class="editor-title-input @error('title') is-invalid @enderror"
                        >
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Excerpt Input -->
                    <div class="mb-4">
                        <label for="excerpt" class="visually-hidden">Mô tả tóm tắt (Excerpt)</label>
>>>>>>> origin/main
                        <textarea
                            name="excerpt"
                            id="excerpt"
                            rows="2"
<<<<<<< HEAD
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong feed..."
                            class="form-control @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
=======
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong danh sách đọc..."
                            class="editor-excerpt-input @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
>>>>>>> origin/main
                        @enderror
                    </div>

<<<<<<< HEAD
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
=======
                    <!-- Minimal Formatting Toolbar -->
                    <div class="editor-toolbar" role="toolbar" aria-label="Định dạng Markdown">
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('bold')" title="Chữ đậm (Ctrl+B)" aria-label="In đậm (Bold)">
                            <strong>B</strong>
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('italic')" title="Chữ nghiêng (Ctrl+I)" aria-label="In nghiêng (Italic)">
                            <em>I</em>
                        </button>
                        <div class="editor-toolbar-divider"></div>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('h2')" title="Tiêu đề 2" aria-label="Tiêu đề 2 (Heading 2)">
                            H2
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('h3')" title="Tiêu đề 3" aria-label="Tiêu đề 3 (Heading 3)">
                            H3
                        </button>
                        <div class="editor-toolbar-divider"></div>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('quote')" title="Trích dẫn" aria-label="Trích dẫn (Quote)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('code')" title="Khối mã nguồn" aria-label="Khối mã nguồn (Code block)">
                            &lt;/&gt;
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('list')" title="Danh sách" aria-label="Danh sách (List)">
                            &bull; List
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('link')" title="Chèn liên kết" aria-label="Chèn liên kết (Link)">
                            Link
                        </button>
                        <button type="button" class="editor-toolbar-btn" onclick="formatDoc('hr')" title="Đường phân tách" aria-label="Đường phân tách ngang (Horizontal rule)">
                            &mdash;
                        </button>
                    </div>

                    <!-- Body / Content Textarea -->
                    <div>
                        <label for="content" class="visually-hidden">Nội dung chi tiết</label>
                        <textarea
                            name="content"
                            id="content"
                            rows="20"
                            placeholder="Nội dung bài viết..."
                            required
                            class="editor-textarea w-100 @error('content') is-invalid @enderror @error('body') is-invalid @enderror"
                            oninput="updateEditorStats()"
                        >{{ old('content', old('body', $post->body)) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Editor Live Status Bar -->
                    <div class="editor-status-bar mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <span id="stat-words">0 từ</span>
                            <span>&bull;</span>
                            <span id="stat-chars">0 ký tự</span>
                            <span>&bull;</span>
                            <span id="stat-reading">~0 phút đọc</span>
                        </div>
                        <span class="opacity-75">Hỗ trợ Markdown</span>
                    </div>
>>>>>>> origin/main
                </div>
            </form>
        </div>

<<<<<<< HEAD
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
=======
        <!-- Right Sidebar: Settings & Publishing Panel -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-3.5">
                <!-- Publishing & Workflow Box -->
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <h2 class="label-uppercase text-theme mb-3 d-flex align-items-center gap-1.5">
                        <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Thao tác & Xét duyệt
>>>>>>> origin/main
                    </h2>

                    <div class="d-flex flex-column gap-2">
                        <!-- Update Button -->
                        <button
                            type="submit"
                            form="edit-post-form"
<<<<<<< HEAD
                            class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold small shadow-sm d-flex align-items-center justify-content-center gap-2"
                        >
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi
=======
                            class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2"
                        >
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Lưu thay đổi bài viết
>>>>>>> origin/main
                        </button>

                        <!-- Submit / Resubmit for Review Button -->
                        @if(in_array($post->status, ['draft', 'rejected'], true))
                            <form action="{{ route('posts.submit', $post) }}" method="POST" class="m-0">
                                @csrf
                                <button
                                    type="submit"
<<<<<<< HEAD
                                    class="btn btn-warning rounded-pill w-100 py-2 fw-bold small d-flex align-items-center justify-content-center gap-2"
                                >
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
=======
                                    class="btn btn-warning rounded-pill w-100 py-2 fw-bold small text-dark d-flex align-items-center justify-content-center gap-2 shadow-xs"
                                >
                                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
>>>>>>> origin/main
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $post->status === 'rejected' ? 'Gửi lại xét duyệt (Resubmit)' : 'Gửi xét duyệt (Submit)' }}
                                </button>
                            </form>
                        @elseif($post->status === 'pending')
<<<<<<< HEAD
                            <div class="alert alert-warning py-2 px-3 small text-center mb-0">
                                Bài viết đang trong hàng đợi xét duyệt của Ban biên tập.
=======
                            <div class="p-2.5 rounded-3 bg-surface-2 border border-theme text-warning small text-center">
                                Bài viết đang trong hàng đợi duyệt của Ban biên tập.
>>>>>>> origin/main
                            </div>
                        @endif

                        <!-- Delete Button Form -->
<<<<<<< HEAD
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
=======
                        <div class="pt-2 mt-1 border-theme-top">
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="m-0"
                                  data-confirm="true"
                                  data-confirm-title="Xóa bài viết"
                                  data-confirm-message="Bạn có chắc chắn muốn xóa bài viết này không?"
                                  data-confirm-target-name="{{ $post->title }}"
                                  data-confirm-target-meta="Trạng thái: {{ $post->status_label }}"
                                  data-confirm-description="Hành động này không thể hoàn tác. Bản nháp hoặc bài viết này sẽ bị xóa vĩnh viễn."
                                  data-confirm-btn-text="Xóa bài viết"
                                  data-confirm-btn-class="btn-danger"
                                  data-confirm-type="danger">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-outline-danger rounded-pill w-100 py-1.5 small d-flex align-items-center justify-content-center gap-1.5"
                                >
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Xóa bài viết này
                                </button>
                            </form>
                        </div>
>>>>>>> origin/main
                    </div>
                </div>

                <!-- Category Selection -->
<<<<<<< HEAD
                <div class="card p-4 border-theme shadow-sm">
                    <label for="category_id" class="form-label small fw-bold text-theme mb-2">
=======
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <label for="category_id" class="label-uppercase text-theme mb-2 d-block">
>>>>>>> origin/main
                        Chuyên mục <span class="text-danger">*</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        form="edit-post-form"
                        required
<<<<<<< HEAD
                        class="form-select @error('category_id') is-invalid @enderror"
                    >
                        <option value="">-- Chọn chuyên mục --</option>
=======
                        class="form-select form-control-editorial @error('category_id') is-invalid @enderror"
                    >
                        <option value="">-- Chọn chuyên mục bài viết --</option>
>>>>>>> origin/main
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
<<<<<<< HEAD
                        <div class="invalid-feedback">{{ $message }}</div>
=======
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
>>>>>>> origin/main
                    @enderror
                </div>

                <!-- Thumbnail Upload & Current Image Preview -->
<<<<<<< HEAD
                <div class="card p-4 border-theme shadow-sm">
                    <label class="form-label small fw-bold text-theme mb-2">
                        Ảnh đại diện (Thumbnail)
=======
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <label class="label-uppercase text-theme mb-2 d-block">
                        Ảnh bìa bài viết (Thumbnail)
>>>>>>> origin/main
                    </label>

                    @if($post->thumbnail)
                        <div id="current-thumbnail-wrapper" class="mb-3">
<<<<<<< HEAD
                            <p class="small text-theme-secondary mb-1">Ảnh hiện tại:</p>
                            <div class="rounded-3 overflow-hidden border-theme">
=======
                            <p class="small text-theme-muted mb-1" style="font-size: 11px;">Ảnh hiện tại:</p>
                            <div class="rounded-3 overflow-hidden border border-theme">
>>>>>>> origin/main
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
<<<<<<< HEAD
                        class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-theme-surface-hover"
=======
                        class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-surface-2 transition-colors"
>>>>>>> origin/main
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
<<<<<<< HEAD
                            <svg class="mx-auto mb-1 text-theme-secondary" style="width: 28px; height: 28px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="small text-theme fw-medium mb-1">Chọn ảnh mới để thay thế</p>
                            <p class="small text-theme-secondary mb-0" style="font-size: 11px;">JPG, PNG, WEBP tối đa 2MB</p>
=======
                            <svg class="mx-auto mb-1 text-theme-muted" style="width: 26px; height: 26px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="small text-theme fw-medium mb-1">Tải ảnh mới để thay thế</p>
                            <p class="small text-theme-muted mb-0" style="font-size: 11px;">JPG, PNG, WEBP tối đa 2MB</p>
>>>>>>> origin/main
                        </div>

                        <div id="preview-container" class="d-none">
                            <img id="image-preview" src="#" alt="Thumbnail preview" class="img-fluid rounded-3 mb-2" style="max-height: 140px; object-fit: cover;">
<<<<<<< HEAD
                            <p class="small mb-0" style="color: #818cf8; font-size: 11px;">Bấm để đổi ảnh khác</p>
                        </div>
                    </div>
                    @error('thumbnail')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
=======
                            <p class="small mb-0 text-accent" style="font-size: 11px;">Bấm để đổi ảnh khác</p>
                        </div>
                    </div>
                    @error('thumbnail')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
>>>>>>> origin/main
                    @enderror
                </div>

                <!-- Tags Selection -->
<<<<<<< HEAD
                <div class="card p-4 border-theme shadow-sm">
                    <label class="form-label small fw-bold text-theme mb-1">
                        Thẻ bài viết (Tags)
                    </label>
                    <p class="small text-theme-secondary mb-3">Chọn các từ khóa chủ đề liên quan:</p>
=======
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <label class="label-uppercase text-theme mb-1 d-block">
                        Thẻ bài viết (Tags)
                    </label>
                    <p class="small text-theme-muted mb-3" style="font-size: 12px;">Gắn các thẻ chủ đề liên quan:</p>
>>>>>>> origin/main

                    @php
                        $currentTagIds = $post->tags->pluck('id')->all();
                        $selectedTags = old('tags', $currentTagIds);
                    @endphp

<<<<<<< HEAD
                    <div class="d-flex flex-wrap gap-2 overflow-y-auto no-scrollbar" style="max-height: 200px;">
=======
                    <div class="d-flex flex-wrap gap-1.5 overflow-y-auto no-scrollbar" style="max-height: 190px;">
>>>>>>> origin/main
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
<<<<<<< HEAD
                            <label class="btn btn-sm btn-outline-theme rounded-pill py-1 px-3 small" for="tag-{{ $tag->id }}">
=======
                            <label class="btn btn-sm btn-outline-theme rounded-pill py-1 px-2.5 small" for="tag-{{ $tag->id }}" style="font-size: 12px;">
>>>>>>> origin/main
                                #{{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('tags')
<<<<<<< HEAD
                        <div class="invalid-feedback d-block">{{ $message }}</div>
=======
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
>>>>>>> origin/main
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
<<<<<<< HEAD
                currentWrapper.style.opacity = '0.4';
=======
                currentWrapper.style.opacity = '0.35';
>>>>>>> origin/main
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateEditorStats() {
    const textarea = document.getElementById('content');
    if (!textarea) return;
    const text = textarea.value.trim();
    const words = text ? text.split(/\s+/).length : 0;
    const chars = text.length;
    const minutes = Math.max(1, Math.ceil(words / 200));

    const wordsEl = document.getElementById('stat-words');
    const charsEl = document.getElementById('stat-chars');
    const readingEl = document.getElementById('stat-reading');

    if (wordsEl) wordsEl.textContent = words.toLocaleString() + ' từ';
    if (charsEl) charsEl.textContent = chars.toLocaleString() + ' ký tự';
    if (readingEl) readingEl.textContent = '~' + minutes + ' phút đọc';
}

function formatDoc(command) {
    const textarea = document.getElementById('content');
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const sel = textarea.value.substring(start, end);
    let before = textarea.value.substring(0, start);
    let after = textarea.value.substring(end);
    let replace = '';

    switch(command) {
        case 'bold':
            replace = `**${sel || 'văn bản đậm'}**`;
            break;
        case 'italic':
            replace = `*${sel || 'văn bản nghiêng'}*`;
            break;
        case 'h2':
            replace = `\n## ${sel || 'Tiêu đề mục'}\n`;
            break;
        case 'h3':
            replace = `\n### ${sel || 'Tiêu đề phụ'}\n`;
            break;
        case 'quote':
            replace = `\n> ${sel || 'Trích dẫn hay'}\n`;
            break;
        case 'code':
            replace = `\`\`\`\n${sel || '// Viết code tại đây'}\n\`\`\``;
            break;
        case 'list':
            replace = `\n- ${sel || 'Mục danh sách'}\n`;
            break;
        case 'link':
            replace = `[${sel || 'Tiêu đề liên kết'}](https://)`;
            break;
        case 'hr':
            replace = `\n\n---\n\n`;
            break;
    }

    textarea.value = before + replace + after;
    textarea.focus();
    updateEditorStats();
}

document.addEventListener('DOMContentLoaded', updateEditorStats);
</script>
@endsection
