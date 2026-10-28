@extends('layouts.public')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">
    <!-- Top Publishing Bar (Ghost + Notion style) -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('posts.stats') }}" data-app-nav class="btn btn-sm btn-outline-theme rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0" style="width: 38px; height: 38px;" title="Quay lại bảng điều khiển" aria-label="Quay lại bảng điều khiển">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 small text-theme-muted label-uppercase mb-0.5" style="font-size: 11px;">
                    <a href="{{ route('posts.stats') }}" data-app-nav class="text-decoration-none text-theme-muted hover-accent">Tác giả</a>
                    <span>&rsaquo;</span>
                    <span class="text-accent">Chỉnh sửa bài viết</span>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="fw-bold text-theme fs-5">Chỉnh sửa bài viết</span>
                    
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Xem bài live</span>
                </a>
            @endif

            <a href="{{ route('posts.stats') }}" data-app-nav class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium btn-cancel-action">
                Quay lại
            </a>

            @if($post->status === 'draft')
                <button type="submit" form="edit-post-form" name="action" value="draft" class="btn btn-sm btn-outline-theme rounded-pill px-3.5 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 btn-action-draft">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Lưu bản nháp</span>
                </button>
                <button type="submit" form="edit-post-form" name="action" value="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5 btn-action-submit">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Gửi bài viết để Admin duyệt</span>
                </button>
            @elseif($post->status === 'rejected')
                <button type="submit" form="edit-post-form" name="action" value="draft" class="btn btn-sm btn-outline-theme rounded-pill px-3.5 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 btn-action-draft">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Lưu bản nháp</span>
                </button>
                <button type="submit" form="edit-post-form" name="action" value="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5 btn-action-submit">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Gửi lại để Admin duyệt (Resubmit)</span>
                </button>
            @else
                <button type="submit" form="edit-post-form" name="action" value="draft" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5 btn-action-draft">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Lưu thay đổi bài viết</span>
                </button>
            @endif
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

    <!-- Redesigned Status Banner Component -->
    <x-post-status-banner :post="$post" />

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-xs border border-danger border-opacity-25 rounded-3" role="alert">
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
        <!-- Left Main Writing Canvas (Readable Editor) -->
        <div class="col-12 col-lg-8">
            <form id="edit-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
                    <!-- Title Input (Notion / Ghost seamless title) -->
                    <div class="mb-3">
                        <label for="title" class="visually-hidden">Tiêu đề bài viết</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $post->title) }}"
                            placeholder="Tiêu đề bài viết..."
                            required
                            class="editor-title-input @error('title') is-invalid @enderror"
                        >
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Excerpt Input -->
                    <div class="mb-4">
                        <label for="excerpt" class="visually-hidden">Mô tả tóm tắt (Excerpt)</label>
                        <textarea
                            name="excerpt"
                            id="excerpt"
                            rows="2"
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong danh sách đọc..."
                            class="editor-excerpt-input @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt', $post->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- WYSIWYG Formatting Toolbar -->
                    <div class="editor-toolbar" role="toolbar" aria-label="Định dạng bài viết">
                        <!-- History: Undo / Redo -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="undo" title="Hoàn tác (Ctrl+Z)" aria-label="Hoàn tác (Undo)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="redo" title="Làm lại (Ctrl+Y / Ctrl+Shift+Z)" aria-label="Làm lại (Redo)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4" />
                            </svg>
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Block Format Dropdown (H1 - H6, Paragraph, Quote) -->
                        <div class="dropdown d-inline-block">
                            <button class="editor-toolbar-btn dropdown-toggle d-inline-flex align-items-center gap-1.5 px-2" type="button" id="editor-block-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Chọn kiểu văn bản">
                                <span id="editor-current-block-label">Đoạn văn</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-theme py-1" aria-labelledby="editor-block-dropdown" style="min-width: 210px; font-size: 13px;">
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="p"><span>Đoạn văn (Paragraph)</span><span class="text-theme-muted small">Ctrl+Alt+7</span></button></li>
                                <li><hr class="dropdown-divider my-1 border-theme"></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h1"><span class="fw-bold fs-6">Tiêu đề 1 (H1)</span><span class="text-theme-muted small">Ctrl+Alt+1</span></button></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h2"><span class="fw-bold" style="font-size: 0.95rem;">Tiêu đề 2 (H2)</span><span class="text-theme-muted small">Ctrl+Alt+2</span></button></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h3"><span class="fw-semibold" style="font-size: 0.9rem;">Tiêu đề 3 (H3)</span><span class="text-theme-muted small">Ctrl+Alt+3</span></button></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h4"><span>Tiêu đề 4 (H4)</span><span class="text-theme-muted small">Ctrl+Alt+4</span></button></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h5"><span class="small">Tiêu đề 5 (H5)</span><span class="text-theme-muted small">Ctrl+Alt+5</span></button></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="h6"><span class="small text-uppercase">Tiêu đề 6 (H6)</span><span class="text-theme-muted small">Ctrl+Alt+6</span></button></li>
                                <li><hr class="dropdown-divider my-1 border-theme"></li>
                                <li><button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center justify-content-between" data-editor-block="quote"><span>Trích dẫn (Quote)</span><span class="text-theme-muted small">Ctrl+Alt+Q</span></button></li>
                            </ul>
                        </div>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Character Formatting -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="bold" title="In đậm (Ctrl+B)" aria-label="In đậm (Bold)">
                            <strong>B</strong>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="italic" title="In nghiêng (Ctrl+I)" aria-label="In nghiêng (Italic)">
                            <em>I</em>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="underline" title="Gạch chân (Ctrl+U)" aria-label="Gạch chân (Underline)">
                            <u>U</u>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="strike" title="Gạch ngang chữ (Ctrl+Alt+D)" aria-label="Gạch ngang (Strikethrough)">
                            <s>S</s>
                        </button>

                        <!-- Text Color Picker Dropdown -->
                        <div class="dropdown d-inline-block">
                            <button class="editor-toolbar-btn dropdown-toggle d-inline-flex align-items-center gap-1" type="button" id="editor-color-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Màu chữ">
                                <span class="fw-bold" style="border-bottom: 2.5px solid #3b82f6; line-height: 1;">A</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-dark shadow-lg border-theme p-2.5" aria-labelledby="editor-color-dropdown" style="min-width: 175px;">
                                <div class="small fw-semibold text-theme-secondary mb-1.5">Màu chữ</div>
                                <div class="d-flex flex-wrap gap-1.5 mb-2">
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #f8fafc;" data-editor-color="#f8fafc" title="Trắng sáng"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #94a3b8;" data-editor-color="#94a3b8" title="Xám"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #ef4444;" data-editor-color="#ef4444" title="Đỏ"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #f97316;" data-editor-color="#f97316" title="Cam"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #eab308;" data-editor-color="#eab308" title="Vàng"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #10b981;" data-editor-color="#10b981" title="Xanh lá"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #3b82f6;" data-editor-color="#3b82f6" title="Xanh dương"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #8b5cf6;" data-editor-color="#8b5cf6" title="Tím"></button>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-1 border-top border-theme">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 small text-theme-muted" data-editor-color="reset">Xóa màu chữ</button>
                                    <label class="btn btn-sm btn-outline-secondary p-0 px-1.5 small d-flex align-items-center gap-1 m-0" style="cursor: pointer; font-size: 11px;">
                                        Tùy chọn
                                        <input type="color" id="editor-custom-color-picker" class="visually-hidden" value="#3b82f6">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Dropdown -->
                        <div class="dropdown d-inline-block">
                            <button class="editor-toolbar-btn dropdown-toggle d-inline-flex align-items-center gap-1" type="button" id="editor-highlight-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Tô màu nền chữ (Highlight)">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-dark shadow-lg border-theme p-2.5" aria-labelledby="editor-highlight-dropdown" style="min-width: 175px;">
                                <div class="small fw-semibold text-theme-secondary mb-1.5">Màu nền chữ</div>
                                <div class="d-flex flex-wrap gap-1.5 mb-2">
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #fef08a;" data-editor-highlight="#fef08a" title="Vàng nhạt"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #bbf7d0;" data-editor-highlight="#bbf7d0" title="Xanh lá nhạt"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #bfdbfe;" data-editor-highlight="#bfdbfe" title="Xanh dương nhạt"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #fbcfe8;" data-editor-highlight="#fbcfe8" title="Hồng nhạt"></button>
                                    <button type="button" class="btn btn-sm p-0 rounded-circle border border-secondary" style="width: 22px; height: 22px; background-color: #e9d5ff;" data-editor-highlight="#e9d5ff" title="Tím nhạt"></button>
                                </div>
                                <div class="pt-1 border-top border-theme">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 small text-theme-muted" data-editor-highlight="reset">Xóa màu nền</button>
                                </div>
                            </div>
                        </div>

                        <!-- Clear Formatting -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="clear-format" title="Xóa toàn bộ định dạng ký tự (Clear formatting)" aria-label="Xóa toàn bộ định dạng (Clear formatting)">
                            <span style="font-family: serif; font-size: 13px; text-decoration: line-through;">T</span><small style="font-size: 9px; margin-left: 1px;">x</small>
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Alignment Buttons -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="align-left" title="Căn trái (Ctrl+Alt+L)" aria-label="Căn trái">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="align-center" title="Căn giữa (Ctrl+Alt+E)" aria-label="Căn giữa">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="align-right" title="Căn phải (Ctrl+Alt+R)" aria-label="Căn phải">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="align-justify" title="Căn đều (Ctrl+Alt+J)" aria-label="Căn đều">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Lists & Indent Group -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="bullet-list" title="Danh sách dấu chấm (Bulleted List)" aria-label="Danh sách dấu chấm (Bulleted list)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h.01M4 12h.01M4 18h.01M8 6h12M8 12h12M8 18h12" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="ordered-list" title="Danh sách số thứ tự (Numbered List)" aria-label="Danh sách số (Ordered list)">
                            <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="10" y1="6" x2="21" y2="6"></line>
                                <line x1="10" y1="12" x2="21" y2="12"></line>
                                <line x1="10" y1="18" x2="21" y2="18"></line>
                                <path d="M4 6h1v4"></path>
                                <path d="M4 10h2"></path>
                                <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path>
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="indent" title="Tăng thụt lề (Increase Indent - Tab)" aria-label="Tăng thụt lề (Indent)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16m-4-9l4 3-4 3" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="outdent" title="Giảm thụt lề (Decrease Indent - Shift+Tab)" aria-label="Giảm thụt lề (Outdent)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16m4-9l-4 3 4 3" />
                            </svg>
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Blockquote, Code & HR -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="quote" title="Trích dẫn (Ctrl+Alt+Q)" aria-label="Trích dẫn (Quote)">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="code" title="Khối mã nguồn" aria-label="Khối mã nguồn (Code block)">
                            &lt;/&gt;
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="hr" title="Đường phân tách ngang (---)" aria-label="Đường phân tách ngang (Horizontal rule)">
                            &mdash;
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Insert: Link & Image -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="link" title="Chèn / Sửa liên kết (Ctrl+K)" aria-label="Chèn liên kết (Link)">
                            <svg style="width: 14px; height: 14px; margin-right: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Link
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="image" title="Chèn hình ảnh từ máy tính" aria-label="Chèn ảnh (Image)">
                            <svg style="width: 14px; height: 14px; margin-right: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Ảnh
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="video" title="Chèn video YouTube" aria-label="Chèn video (Video)">
                            <svg style="width: 14px; height: 14px; margin-right: 2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Video
                        </button>

                        <div class="editor-toolbar-divider"></div>

                        <!-- Utilities: Paste Plain Text & Keyboard Shortcuts Modal -->
                        <button type="button" class="editor-toolbar-btn" data-editor-action="paste-text" title="Dán văn bản thuần (Ctrl+Shift+V)" aria-label="Dán văn bản thuần">
                            <span class="small font-monospace fw-bold">Txt</span>
                        </button>
                        <button type="button" class="editor-toolbar-btn" data-editor-action="shortcuts-help" title="Danh sách phím tắt" aria-label="Trợ giúp phím tắt">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Hidden file input for uploading images directly into editor content -->
                    <input type="file" id="editor-image-file-input" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" class="d-none">

                    <!-- WYSIWYG Editor Surface -->
                    <div>
                        <div id="tiptap-editor" class="editor-wysiwyg-surface"></div>

                        <!-- Hidden sync textarea for native form submission & validation -->
                        <textarea
                            name="content"
                            id="content"
                            class="d-none @error('content') is-invalid @enderror @error('body') is-invalid @enderror"
                        >{{ old('content', old('body', $post->editor_content)) }}</textarea>
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
                        <span class="opacity-75">Trình soạn thảo trực quan (WYSIWYG)</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Sidebar: Settings & Publishing Panel -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-3.5">
                <!-- Publishing & Workflow Box -->
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <h2 class="label-uppercase text-theme mb-0 d-flex align-items-center gap-1.5 fw-bold">
                            <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quy trình xuất bản
                        </h2>
                        <span class="badge rounded-pill font-monospace small px-2.5 py-0.5" style="font-size: 11px;
                            @if($post->status === 'published') background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3);
                            @elseif($post->status === 'pending') background: rgba(234, 179, 8, 0.15); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.3);
                            @elseif($post->status === 'rejected') background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);
                            @else background: var(--color-bg-subtle, #27272a); color: var(--color-text-secondary, #a1a1aa); border: 1px solid var(--color-border-subtle, #3f3f46);
                            @endif">
                            {{ $post->status_label }}
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        @if($post->status === 'draft')
                            <div class="p-2.5 rounded-3 bg-surface-2 border border-theme mb-1 small text-theme-secondary">
                                <div class="d-flex align-items-center gap-1.5 mb-1 text-theme fw-semibold" style="font-size: 12px;">
                                    <svg style="width: 13px; height: 13px; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Kiểm duyệt nội dung</span>
                                </div>
                                <p class="mb-0 text-theme-muted" style="font-size: 11.5px; line-height: 1.45;">
                                    Bài viết sẽ được chuyển đến Admin để phê duyệt trước khi hiển thị công khai.
                                </p>
                            </div>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="submit"
                                class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2 btn-action-submit"
                                title="Gửi bài viết để Admin kiểm duyệt và xuất bản"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Gửi bài viết để Admin duyệt</span>
                            </button>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="draft"
                                class="btn btn-outline-theme rounded-pill w-100 py-2 small fw-medium d-flex align-items-center justify-content-center gap-2 btn-action-draft"
                                title="Lưu lại bản nháp để tiếp tục chỉnh sửa sau"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span>Lưu bản nháp</span>
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                data-app-nav
                                class="btn btn-link text-theme-muted text-decoration-none rounded-pill w-100 py-1.5 small text-center btn-cancel-action"
                            >
                                Hủy bỏ & Quay lại
                            </a>
                        @elseif($post->status === 'rejected')
                            <div class="p-2.5 rounded-3 bg-surface-2 border border-danger border-opacity-25 mb-1 small text-theme-secondary">
                                <div class="d-flex align-items-center gap-1.5 mb-1 text-danger fw-semibold" style="font-size: 12px;">
                                    <svg style="width: 13px; height: 13px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Bài viết bị từ chối</span>
                                </div>
                                <p class="mb-0 text-theme-muted" style="font-size: 11.5px; line-height: 1.45;">
                                    Vui lòng chỉnh sửa nội dung theo góp ý và gửi lại để Ban biên tập xét duyệt.
                                </p>
                            </div>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="submit"
                                class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2 btn-action-submit"
                                title="Gửi lại bài viết cho Admin kiểm duyệt"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Gửi lại để Admin duyệt (Resubmit)</span>
                            </button>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="draft"
                                class="btn btn-outline-theme rounded-pill w-100 py-2 small fw-medium d-flex align-items-center justify-content-center gap-2 btn-action-draft"
                                title="Lưu tiến trình sửa đổi bản nháp"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span>Lưu bản nháp</span>
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                data-app-nav
                                class="btn btn-link text-theme-muted text-decoration-none rounded-pill w-100 py-1.5 small text-center btn-cancel-action"
                            >
                                Hủy bỏ & Quay lại
                            </a>
                        @elseif($post->status === 'pending')
                            <div class="p-2.5 rounded-3 bg-surface-2 border border-warning border-opacity-50 text-warning small text-center mb-1">
                                <span style="font-size: 11.5px;">Bài viết đang trong hàng đợi kiểm duyệt của Admin.</span>
                            </div>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="draft"
                                class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2 btn-action-draft"
                                title="Cập nhật nội dung bài viết đang chờ duyệt"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Lưu thay đổi bài viết</span>
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                data-app-nav
                                class="btn btn-link text-theme-muted text-decoration-none rounded-pill w-100 py-1.5 small text-center btn-cancel-action"
                            >
                                Quay lại danh sách
                            </a>
                        @else
                            <div class="p-2.5 rounded-3 bg-surface-2 border border-success border-opacity-30 text-success small text-center mb-1">
                                <span style="font-size: 11.5px;">Bài viết đang hiển thị công khai.</span>
                            </div>

                            <button
                                type="submit"
                                form="edit-post-form"
                                name="action"
                                value="draft"
                                class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2 btn-action-draft"
                                title="Cập nhật trực tiếp bài viết đã xuất bản"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Lưu thay đổi bài viết</span>
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                data-app-nav
                                class="btn btn-link text-theme-muted text-decoration-none rounded-pill w-100 py-1.5 small text-center btn-cancel-action"
                            >
                                Quay lại danh sách
                            </a>
                        @endif

                        <!-- Delete Button Form -->
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
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label for="category_id" class="label-uppercase text-theme mb-0 d-flex align-items-center gap-1.5 fw-bold">
                            <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Chuyên mục <span class="text-danger">*</span>
                        </label>
                        <span class="badge bg-surface-2 text-theme-muted border border-theme font-monospace small" style="font-size: 11px;">
                            Bắt buộc
                        </span>
                    </div>
                    <select
                        name="category_id"
                        id="category_id"
                        form="edit-post-form"
                        required
                        class="form-select form-control-editorial @error('category_id') is-invalid @enderror"
                    >
                        <option value="">-- Chọn chuyên mục bài viết --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thumbnail Upload & Management -->
                <x-thumbnail-uploader :current-thumbnail="$post->thumbnail" form-id="edit-post-form" />

                <!-- Tags Selection (Dynamic Tag Selector) -->
                @php
                    $currentTagIds = $post->tags->pluck('id')->all();
                    $selectedTags = old('tags', $currentTagIds);
                @endphp
                <x-post-tag-selector :tags="$tags" :selected-tags="$selectedTags" form-id="edit-post-form" />
            </div>
        </div>
    </div>
</div>

    <!-- Modal Chèn / Sửa Liên Kết -->
    <div class="modal fade" id="editor-link-modal" tabindex="-1" aria-labelledby="editorLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-theme bg-theme-surface shadow-lg text-theme" style="border-radius: var(--radius-lg, 14px);">
                <div class="modal-header border-theme-bottom py-2.5 px-3">
                    <h6 class="modal-title fs-6 fw-bold text-theme m-0" id="editorLinkModalLabel">Chèn / Sửa liên kết</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label for="editor-link-url" class="form-label small fw-semibold text-theme mb-1">Địa chỉ URL:</label>
                        <input type="url" id="editor-link-url" class="form-control form-control-sm form-control-editorial" placeholder="https://example.com" required>
                        <div class="form-text small text-theme-muted mt-1" style="font-size: 11px;">Hỗ trợ giao thức web: <code>https://</code> hoặc <code>http://</code>.</div>
                    </div>
                </div>
                <div class="modal-footer border-theme-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-danger d-none rounded-pill px-2.5 py-1" id="editor-link-unlink-btn">Xóa link</button>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1 fw-semibold" id="editor-link-save-btn">Áp dụng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chỉnh Sửa Thuộc Tính Hình Ảnh & ALT -->
    <x-editor-image-modal />

    <!-- Modal Chèn Video YouTube -->
    <x-editor-video-modal />

    <!-- Modal Hướng Dẫn Phím Tắt -->
    <div class="modal fade" id="editor-shortcuts-modal" tabindex="-1" aria-labelledby="editorShortcutsModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title m-0 d-flex align-items-center gap-2" id="editorShortcutsModalLabel">
                        <svg style="width: 20px; height: 20px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Phím tắt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Danh sách các phím tắt được hỗ trợ khi soạn thảo nội dung. Nhấn <kbd>Esc</kbd> hoặc click ra ngoài vùng hộp thoại để đóng.
                    </p>

                    <!-- 1. Định dạng văn bản -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Định dạng văn bản</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>In đậm (Bold)</td>
                                        <td><kbd>⌘ + B</kbd></td>
                                        <td><kbd>Ctrl + B</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>In nghiêng (Italic)</td>
                                        <td><kbd>⌘ + I</kbd></td>
                                        <td><kbd>Ctrl + I</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Gạch chân (Underline)</td>
                                        <td><kbd>⌘ + U</kbd></td>
                                        <td><kbd>Ctrl + U</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Gạch ngang chữ (Strike)</td>
                                        <td><kbd>⌘ + Shift + X</kbd> <span class="text-muted small">hoặc</span> <kbd>⌥ + ⌘ + D</kbd></td>
                                        <td><kbd>Ctrl + Shift + X</kbd> <span class="text-muted small">hoặc</span> <kbd>Ctrl + Alt + D</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Tiêu đề và đoạn văn -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Tiêu đề và đoạn văn</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Đoạn văn (Paragraph)</td>
                                        <td><kbd>⌥ + ⌘ + 7</kbd></td>
                                        <td><kbd>Ctrl + Alt + 7</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 1 (H1)</td>
                                        <td><kbd>⌥ + ⌘ + 1</kbd></td>
                                        <td><kbd>Ctrl + Alt + 1</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 2 (H2)</td>
                                        <td><kbd>⌥ + ⌘ + 2</kbd></td>
                                        <td><kbd>Ctrl + Alt + 2</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 3 (H3)</td>
                                        <td><kbd>⌥ + ⌘ + 3</kbd></td>
                                        <td><kbd>Ctrl + Alt + 3</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 4 (H4)</td>
                                        <td><kbd>⌥ + ⌘ + 4</kbd></td>
                                        <td><kbd>Ctrl + Alt + 4</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 5 (H5)</td>
                                        <td><kbd>⌥ + ⌘ + 5</kbd></td>
                                        <td><kbd>Ctrl + Alt + 5</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tiêu đề 6 (H6)</td>
                                        <td><kbd>⌥ + ⌘ + 6</kbd></td>
                                        <td><kbd>Ctrl + Alt + 6</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. Danh sách -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Danh sách & Thụt lề</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Danh sách dấu chấm (Bulleted List)</td>
                                        <td><kbd>⌘ + Shift + 8</kbd></td>
                                        <td><kbd>Ctrl + Shift + 8</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Danh sách số thứ tự (Numbered List)</td>
                                        <td><kbd>⌘ + Shift + 7</kbd></td>
                                        <td><kbd>Ctrl + Shift + 7</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Tăng thụt lề danh sách (Lồng cấp con)</td>
                                        <td><kbd>Tab</kbd></td>
                                        <td><kbd>Tab</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Giảm thụt lề danh sách (Đưa lên cấp cha)</td>
                                        <td><kbd>Shift + Tab</kbd></td>
                                        <td><kbd>Shift + Tab</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Căn lề và trích dẫn -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Căn lề và trích dẫn</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Căn lề trái (Align Left)</td>
                                        <td><kbd>⌥ + ⌘ + L</kbd></td>
                                        <td><kbd>Ctrl + Alt + L</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Căn giữa (Align Center)</td>
                                        <td><kbd>⌥ + ⌘ + E</kbd></td>
                                        <td><kbd>Ctrl + Alt + E</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Căn lề phải (Align Right)</td>
                                        <td><kbd>⌥ + ⌘ + R</kbd></td>
                                        <td><kbd>Ctrl + Alt + R</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Căn đều hai bên (Align Justify)</td>
                                        <td><kbd>⌥ + ⌘ + J</kbd></td>
                                        <td><kbd>Ctrl + Alt + J</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Khối trích dẫn (Blockquote)</td>
                                        <td><kbd>⌥ + ⌘ + Q</kbd></td>
                                        <td><kbd>Ctrl + Alt + Q</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 5. Liên kết -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Liên kết</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Chèn / Sửa liên kết (Link)</td>
                                        <td><kbd>⌘ + K</kbd></td>
                                        <td><kbd>Ctrl + K</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 6. Lịch sử thao tác (Undo/Redo) -->
                    <div class="mb-3">
                        <span class="shortcut-section-badge mb-2">Lịch sử thao tác</span>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 44%;">Thao tác</th>
                                        <th style="width: 28%;">macOS</th>
                                        <th style="width: 28%;">Windows / Linux</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Hoàn tác (Undo)</td>
                                        <td><kbd>⌘ + Z</kbd></td>
                                        <td><kbd>Ctrl + Z</kbd></td>
                                    </tr>
                                    <tr>
                                        <td>Làm lại (Redo)</td>
                                        <td><kbd>⌘ + Shift + Z</kbd></td>
                                        <td><kbd>Ctrl + Y</kbd> <span class="text-muted small">hoặc</span> <kbd>Ctrl + Shift + Z</kbd></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 7. Khác & Markdown nhanh -->
                    <div>
                        <span class="shortcut-section-badge mb-2">Quy tắc gõ tắt tự động (Markdown)</span>
                        <div class="p-2.5 rounded bg-light border border-slate-200">
                            <ul class="mb-0 ps-3 small text-secondary">
                                <li><code># </code> đến <code>###### </code> + <kbd>Space</kbd>: Chuyển thành Tiêu đề H1 đến H6 tương ứng</li>
                                <li><code>&gt; </code> + <kbd>Space</kbd>: Tạo khối trích dẫn (Blockquote)</li>
                                <li><code>- </code> hoặc <code>* </code> + <kbd>Space</kbd>: Tạo danh sách dấu chấm (Bullet List)</li>
                                <li><code>1. </code> + <kbd>Space</kbd>: Tạo danh sách số thứ tự (Numbered List)</li>
                                <li><code>---</code> + <kbd>Enter</kbd>: Tạo đường kẻ phân cách ngang</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Nhấn <kbd>Esc</kbd> hoặc click vùng nền tối bên ngoài để đóng</span>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3.5 py-1" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
