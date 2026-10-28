<!-- Modal Chèn Video (Tải lên hoặc YouTube - BlogMNM WYSIWYG) -->
<div class="modal fade" id="editor-video-modal" tabindex="-1" aria-labelledby="editorVideoModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-theme bg-theme-surface shadow-lg text-theme" style="border-radius: var(--radius-lg, 14px);">
            <!-- Modal Header with Tab Navigation -->
            <div class="modal-header border-theme-bottom py-2.5 px-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-1.5 d-flex align-items-center justify-content-center bg-danger-subtle text-danger border border-danger-subtle" style="width: 28px; height: 28px;">
                        <svg style="width: 15px; height: 15px;" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                        </svg>
                    </span>
                    <h6 class="modal-title fs-6 fw-bold text-theme m-0" id="editorVideoModalLabel">Chèn video</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <!-- Tab Pills Selector -->
            <div class="px-3.5 pt-3 pb-1 border-theme-bottom bg-surface-2 bg-opacity-25">
                <ul class="nav nav-pills nav-fill gap-2" id="editor-video-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active py-2 px-3 fw-semibold small d-flex align-items-center justify-content-center gap-2 rounded-pill"
                            id="tab-video-upload"
                            data-bs-toggle="pill"
                            data-bs-target="#tab-video-upload-pane"
                            type="button"
                            role="tab"
                            aria-controls="tab-video-upload-pane"
                            aria-selected="true"
                        >
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span>Tải video lên từ máy</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link py-2 px-3 fw-semibold small d-flex align-items-center justify-content-center gap-2 rounded-pill"
                            id="tab-video-youtube"
                            data-bs-toggle="pill"
                            data-bs-target="#tab-video-youtube-pane"
                            type="button"
                            role="tab"
                            aria-controls="tab-video-youtube-pane"
                            aria-selected="false"
                        >
                            <svg style="width: 15px; height: 15px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                            </svg>
                            <span>Chèn video YouTube</span>
                        </button>
                    </li>
                </ul>
            </div>

            <form id="editor-video-form" onsubmit="return false;">
                <div class="modal-body p-3.5">
                    <div class="tab-content" id="editor-video-tab-content">
                        <!-- TAB 1: UPLOAD LOCAL VIDEO -->
                        <div class="tab-pane fade show active" id="tab-video-upload-pane" role="tabpanel" aria-labelledby="tab-video-upload">
                            <!-- Hidden File Input -->
                            <input
                                type="file"
                                id="editor-video-file-input"
                                class="d-none"
                                accept="video/mp4,video/webm,.mp4,.webm"
                            >

                            <!-- Dropzone & Empty State -->
                            <div
                                id="editor-video-dropzone"
                                class="border-2 border-dashed border-theme rounded-3 p-4 text-center cursor-pointer transition-all hover-border-accent bg-surface-2 bg-opacity-25"
                                tabindex="0"
                                role="button"
                                aria-label="Tải video lên từ máy tính"
                            >
                                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                                    <div class="rounded-circle bg-surface-3 p-3 mb-2.5 text-accent d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="fw-semibold text-theme mb-1">
                                        Kéo thả video vào đây hoặc <span class="text-accent text-decoration-underline">chọn từ thiết bị</span>
                                    </div>
                                    <div class="small text-theme-muted" style="font-size: 11.5px;">
                                        Định dạng hỗ trợ: <strong>MP4, WebM</strong> &bull; Dung lượng tối đa: <strong>100MB/video</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Progress State -->
                            <div id="editor-video-upload-progress" class="d-none my-3 p-3.5 border-theme bg-surface-2 rounded-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="spinner-border spinner-border-sm text-accent" role="status" aria-hidden="true" style="width: 14px; height: 14px;"></span>
                                        <span class="small fw-semibold text-theme" id="editor-video-upload-filename">Đang tải video...</span>
                                    </div>
                                    <span class="badge bg-surface-3 text-theme font-monospace small" id="editor-video-upload-percent">0%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div
                                        id="editor-video-progress-bar"
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        role="progressbar"
                                        style="width: 0%;"
                                        aria-valuenow="0"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 small text-theme-muted" style="font-size: 11px;">
                                    <span id="editor-video-upload-size-info">0 MB / 0 MB</span>
                                    <span>Vui lòng không đóng cửa sổ này trong khi tải</span>
                                </div>
                            </div>

                            <!-- Upload Error Alert -->
                            <div id="editor-video-upload-error" class="alert alert-danger d-none my-3 py-2 px-3 small rounded-3 shadow-xs" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span id="editor-video-upload-error-text">Đã xảy ra lỗi khi tải video lên.</span>
                                </div>
                            </div>

                            <!-- Uploaded Preview Player -->
                            <div id="editor-video-upload-preview" class="d-none mt-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-circle p-1 bg-success-subtle text-success d-inline-flex" style="width: 16px; height: 16px;">
                                            <svg style="width: 10px; height: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                        <span class="small fw-semibold text-theme">Xem trước video đã tải lên:</span>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5 small d-flex align-items-center gap-1"
                                        id="editor-video-remove-btn"
                                        style="font-size: 11px;"
                                        title="Xóa video và chọn lại"
                                    >
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Xóa & chọn file khác</span>
                                    </button>
                                </div>

                                <div class="video-embed-wrapper ratio ratio-16x9 rounded-3 overflow-hidden border border-theme shadow-xs bg-black">
                                    <video
                                        id="editor-video-preview-player"
                                        controls
                                        playsinline
                                        preload="metadata"
                                        class="w-100 h-100 rounded-3"
                                        style="object-fit: contain; background: #000;"
                                    >
                                        <source id="editor-video-preview-player-source" src="" type="video/mp4">
                                        Trình duyệt của bạn không hỗ trợ thẻ phát video HTML5.
                                    </video>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-2 small text-theme-muted px-1" style="font-size: 11px;">
                                    <span id="editor-video-uploaded-name" class="font-monospace text-truncate" style="max-width: 60%;">video.mp4</span>
                                    <span id="editor-video-uploaded-size" class="font-monospace">0 MB</span>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: YOUTUBE EMBED URL -->
                        <div class="tab-pane fade" id="tab-video-youtube-pane" role="tabpanel" aria-labelledby="tab-video-youtube">
                            <div class="mb-3">
                                <label for="editor-video-url" class="form-label small fw-semibold text-theme mb-1.5 d-flex align-items-center justify-content-between">
                                    <span>Địa chỉ URL video YouTube</span>
                                    <span class="badge bg-surface-2 text-theme-muted font-monospace small" style="font-size: 10px;">Bắt buộc</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-surface-2 border-theme text-theme-muted">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </span>
                                    <input
                                        type="url"
                                        id="editor-video-url"
                                        class="form-control form-control-sm form-control-editorial"
                                        placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/..."
                                        autocomplete="off"
                                        spellcheck="false"
                                    >
                                </div>
                                <div class="form-text small text-theme-muted mt-1.5" style="font-size: 11.5px;">
                                    Hỗ trợ các định dạng: <code>youtube.com/watch?v=...</code>, <code>youtu.be/...</code>, <code>youtube.com/shorts/...</code>
                                </div>
                                <div class="invalid-feedback" id="editor-video-error" style="font-size: 11.5px;">
                                    Đường dẫn YouTube không hợp lệ. Vui lòng kiểm tra lại liên kết.
                                </div>
                            </div>

                            <!-- Live YouTube Preview -->
                            <div id="editor-video-preview" class="d-none">
                                <label class="form-label small fw-semibold text-theme mb-1.5 d-flex align-items-center gap-1.5">
                                    <span class="badge rounded-circle p-1 bg-success-subtle text-success d-inline-flex" style="width: 16px; height: 16px;">
                                        <svg style="width: 10px; height: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                    <span>Xem trước video YouTube:</span>
                                </label>
                                <div class="video-embed-wrapper ratio ratio-16x9 rounded-3 overflow-hidden border border-theme shadow-xs">
                                    <iframe
                                        id="editor-video-preview-iframe"
                                        src=""
                                        title="YouTube video preview"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy"
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-theme-top py-2.5 px-3.5 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-semibold d-flex align-items-center gap-1.5 shadow-xs" id="editor-video-save-btn">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Chèn video</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
