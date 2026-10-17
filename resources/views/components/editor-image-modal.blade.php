<!-- Modal Chỉnh Sửa Thuộc Tính Hình Ảnh, Căn Chỉnh, Chú Thích & ALT (BlogMNM WYSIWYG) -->
<div class="modal fade" id="editor-image-modal" tabindex="-1" aria-labelledby="editorImageModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-theme bg-theme-surface shadow-lg text-theme" style="border-radius: var(--radius-lg, 14px);">
            <div class="modal-header border-theme-bottom py-2.5 px-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-1.5 d-flex align-items-center justify-content-center bg-theme-surface-hover border border-theme text-theme" style="width: 28px; height: 28px;">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <h6 class="modal-title fs-6 fw-bold text-theme m-0" id="editorImageModalLabel">Chỉnh sửa hình ảnh & chú thích</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form id="editor-image-form" onsubmit="return false;">
                <div class="modal-body p-3.5">
                    <!-- Image Preview Container -->
                    <div class="p-2.5 mb-3 rounded-3 bg-surface-2 border border-theme d-flex align-items-center gap-3">
                        <div class="rounded-2 overflow-hidden border border-theme flex-shrink-0 bg-dark bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                            <img id="editor-image-preview" src="" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="small fw-semibold text-theme text-truncate" id="editor-image-filename">Hình ảnh trong bài viết</div>
                            <div class="text-theme-muted small text-truncate" id="editor-image-src" style="font-size: 11px;"></div>
                        </div>
                    </div>

                    <!-- Alignment Control -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-theme mb-1.5 d-block">
                            Căn chỉnh vị trí hình ảnh
                        </label>
                        <div class="btn-group w-100" role="group" id="editor-image-align-group" aria-label="Căn chỉnh ảnh">
                            <button type="button" class="btn btn-sm btn-outline-theme d-flex align-items-center justify-content-center gap-1.5 py-1.5" data-editor-image-align="left">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14" />
                                </svg>
                                <span>Căn trái</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-theme d-flex align-items-center justify-content-center gap-1.5 py-1.5 active" data-editor-image-align="center">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14" />
                                </svg>
                                <span>Căn giữa (Chuẩn)</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-theme d-flex align-items-center justify-content-center gap-1.5 py-1.5" data-editor-image-align="right">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14" />
                                </svg>
                                <span>Căn phải</span>
                            </button>
                        </div>
                        <input type="hidden" id="editor-image-align-input" value="center">
                    </div>

                    <!-- Caption Input -->
                    <div class="mb-3">
                        <label for="editor-image-caption" class="form-label small fw-semibold text-theme mb-1 d-flex align-items-center justify-content-between">
                            <span>Chú thích hình ảnh (Caption)</span>
                            <span class="text-theme-muted fw-normal" style="font-size: 11px;">Hiển thị dưới ảnh</span>
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-sm form-control-editorial rounded-3"
                            id="editor-image-caption"
                            placeholder="Nhập chú thích hiển thị bên dưới bức ảnh..."
                            autocomplete="off"
                        >
                        <div class="form-text small text-theme-muted mt-1" style="font-size: 11.5px; line-height: 1.4;">
                            Chú thích sẽ hiển thị công khai ngay bên dưới bức ảnh cho độc giả xem. Để trống nếu không muốn hiển thị.
                        </div>
                    </div>

                    <!-- ALT Input -->
                    <div class="mb-3">
                        <label for="editor-image-alt" class="form-label small fw-semibold text-theme mb-1 d-flex align-items-center justify-content-between">
                            <span>Văn bản thay thế (ALT text)</span>
                            <span class="text-theme-muted fw-normal" style="font-size: 11px;">Tiếp cận & SEO</span>
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-sm form-control-editorial rounded-3"
                            id="editor-image-alt"
                            placeholder="Mô tả nội dung bức ảnh..."
                            autocomplete="off"
                        >
                        <div class="form-text small text-theme-muted mt-1" style="font-size: 11.5px; line-height: 1.4;">
                            Dùng cho công cụ tìm kiếm và người dùng khiếm thị (trình đọc màn hình), không hiển thị trực tiếp trên trang.
                        </div>
                    </div>

                    <!-- Title / Tooltip Input -->
                    <div class="mb-1">
                        <label for="editor-image-title" class="form-label small fw-semibold text-theme mb-1">
                            Tiêu đề gợi ý <span class="text-theme-muted fw-normal" style="font-size: 11px;">(Tooltip khi rê chuột)</span>
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-sm form-control-editorial rounded-3"
                            id="editor-image-title"
                            placeholder="Tiêu đề hiển thị khi rê chuột qua ảnh..."
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="modal-footer border-theme-top py-2.5 px-3.5 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-medium" data-bs-dismiss="modal">
                        Hủy
                    </button>
                    <button type="button" id="editor-image-save-btn" class="btn btn-editorial-primary btn-sm rounded-pill px-3.5 py-1 fw-semibold shadow-xs">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
