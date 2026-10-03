@extends('layouts.public')

@section('content')
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
                    <span class="text-accent">Soạn thảo mới</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-theme fs-5" style="font-family: var(--font-serif, 'Lora', serif);">Tạo bài viết mới</span>
                    <x-badge variant="status-draft" size="xs">
                        Bản nháp (Draft)
                    </x-badge>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium">
                Hủy bỏ
            </a>
            <button type="submit" form="create-post-form" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Lưu bản nháp
            </button>
        </div>
    </div>

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-xs border border-danger border-opacity-25 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Vui lòng kiểm tra lại thông tin nhập:</span>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="create-post-form" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Main Writing Canvas (Readable Editor) -->
            <div class="col-12 col-lg-8">
                <div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
                    <!-- Title Input (Notion / Ghost seamless title) -->
                    <div class="mb-3">
                        <label for="title" class="visually-hidden">Tiêu đề bài viết</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            placeholder="Tiêu đề bài viết..."
                            required
                            autofocus
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
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong danh sách đọc (tối đa 500 ký tự)..."
                            class="editor-excerpt-input @error('excerpt') is-invalid @enderror"
                        >{{ old('excerpt') }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

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
                            rows="18"
                            placeholder="Bắt đầu chia sẻ kiến thức, kinh nghiệm và câu chuyện của bạn tại đây..."
                            required
                            class="editor-textarea w-100 @error('content') is-invalid @enderror @error('body') is-invalid @enderror"
                            oninput="updateEditorStats()"
                        >{{ old('content', old('body')) }}</textarea>
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
                </div>
            </div>

            <!-- Right Sidebar: Settings & Publishing Panel -->
            <div class="col-12 col-lg-4">
                <div class="d-flex flex-column gap-3.5">
                    <!-- Publishing Workflow Card -->
                    <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                        <h2 class="label-uppercase text-theme mb-3 d-flex align-items-center gap-1.5">
                            <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quy trình xuất bản
                        </h2>

                        <div class="p-3 rounded-3 bg-surface-2 border border-theme mb-3 small text-theme-secondary">
                            <p class="fw-semibold text-theme mb-1">Luồng kiểm duyệt bài viết:</p>
                            Bài viết mới luôn được lưu ở trạng thái <strong>Bản nháp (Draft)</strong>. Sau khi soạn xong, bạn có thể gửi cho Ban biên tập xét duyệt (Pending) từ trang chỉnh sửa bài viết.
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <button
                                type="submit"
                                class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-2"
                            >
                                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Lưu bản nháp (Draft)
                            </button>

                            <a
                                href="{{ route('posts.stats') }}"
                                class="btn btn-outline-theme rounded-pill w-100 py-2 small fw-medium"
                            >
                                Hủy bỏ & Quay lại
                            </a>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                        <label for="category_id" class="label-uppercase text-theme mb-2 d-block">
                            Chuyên mục <span class="text-danger">*</span>
                        </label>
                        <select
                            name="category_id"
                            id="category_id"
                            required
                            class="form-select form-control-editorial @error('category_id') is-invalid @enderror"
                        >
                            <option value="">-- Chọn chuyên mục bài viết --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thumbnail Upload (Featured Image) -->
                    <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                        <label class="label-uppercase text-theme mb-2 d-block">
                            Ảnh bìa đại diện (Thumbnail)
                        </label>

                        <div
                            id="drop-zone"
                            class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-surface-2 transition-colors"
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
                                <svg class="mx-auto mb-2 text-theme-muted" style="width: 28px; height: 28px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="small text-theme fw-medium mb-1">Tải ảnh bìa lên bài viết</p>
                                <p class="small text-theme-muted mb-0" style="font-size: 11px;">JPG, PNG, WEBP tối đa 2MB</p>
                            </div>

                            <div id="preview-container" class="d-none">
                                <img id="image-preview" src="#" alt="Thumbnail preview" class="img-fluid rounded-3 mb-2" style="max-height: 160px; object-fit: cover;">
                                <p class="small mb-0 text-accent" style="font-size: 11px;">Bấm để đổi ảnh khác</p>
                            </div>
                        </div>
                        @error('thumbnail')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tags Selection -->
                    <div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl">
                        <label class="label-uppercase text-theme mb-1 d-block">
                            Thẻ bài viết (Tags)
                        </label>
                        <p class="small text-theme-muted mb-3" style="font-size: 12px;">Gắn các thẻ chủ đề liên quan:</p>

                        @php
                            $selectedTags = old('tags', []);
                        @endphp

                        <div class="d-flex flex-wrap gap-1.5 overflow-y-auto no-scrollbar" style="max-height: 190px;">
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
                                <label class="btn btn-sm btn-outline-theme rounded-pill py-1 px-2.5 small" for="tag-{{ $tag->id }}" style="font-size: 12px;">
                                    #{{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('tags')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
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
