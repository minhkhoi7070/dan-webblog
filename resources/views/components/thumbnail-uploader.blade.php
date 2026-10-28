@props([
    'currentThumbnail' => null,
    'formId' => 'create-post-form',
    'fieldName' => 'thumbnail',
    'removeFieldName' => 'remove_thumbnail',
])

@php
    $initialUrl = null;
    if ($currentThumbnail) {
        $initialUrl = str_starts_with($currentThumbnail, 'http') ? $currentThumbnail : asset($currentThumbnail);
    }
@endphp

<div class="card border-theme bg-theme-surface shadow-xs rounded-xl thumbnail-uploader-widget" style="padding: 22px;"
     id="thumbnail-uploader-widget"
     data-initial-thumbnail="{{ $initialUrl ?? '' }}"
     data-form-id="{{ $formId }}">

    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-2">
        <label class="label-uppercase text-theme mb-0 d-flex align-items-center gap-1.5 fw-bold">
            <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Ảnh bìa
        </label>
        <span class="badge bg-surface-2 text-theme-muted border border-theme font-monospace small" id="thumbnail-indicator" style="font-size: 11px;">
            {{ $initialUrl ? 'Đã có ảnh' : 'Chưa có ảnh' }}
        </span>
    </div>

    <!-- Hidden file input & remove indicator associated with form -->
    <input
        type="file"
        name="{{ $fieldName }}"
        id="thumbnail-file-input"
        class="d-none"
        accept="image/jpeg,image/png,image/webp,image/jpg"
        form="{{ $formId }}"
    >
    <input
        type="hidden"
        name="{{ $removeFieldName }}"
        id="thumbnail-remove-input"
        value="0"
        form="{{ $formId }}"
    >

    <!-- 1. EMPTY DROP ZONE STATE (Shown when no image and not pending removal) -->
    <div
        id="thumbnail-drop-zone"
        class="border border-2 border-dashed border-theme rounded-3 p-3 text-center position-relative bg-surface-2 transition-colors cursor-pointer @if($initialUrl) d-none @endif"
        tabindex="0"
        role="button"
        aria-label="Tải ảnh bìa lên bài viết"
    >
        <div class="py-3">
            <svg class="mx-auto mb-2 text-theme-muted" style="width: 30px; height: 30px;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="small text-theme fw-medium mb-1">Kéo & thả hoặc nhấp để tải ảnh bìa</p>
            <p class="small text-theme-muted mb-0" style="font-size: 11.5px;">Hỗ trợ JPG, PNG, WEBP, tối đa 2MB</p>
        </div>
    </div>

    <!-- 2. ACTIVE PREVIEW STATE (Shown when an image is loaded, either initial or newly selected) -->
    <div id="thumbnail-preview-container" class="@if(!$initialUrl) d-none @endif">
        <div class="position-relative rounded-3 overflow-hidden border border-theme bg-surface-2 mb-2.5">
            <img
                id="thumbnail-preview-img"
                src="{{ $initialUrl ?? '#' }}"
                alt="Xem trước ảnh bìa"
                class="img-fluid w-100 d-block"
                style="max-height: 170px; object-fit: cover;"
            >
            <span
                id="thumbnail-badge-status"
                class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white border border-light border-opacity-25 shadow-xs"
                style="font-size: 11px; backdrop-filter: blur(4px);"
            >
                {{ $initialUrl ? 'Ảnh hiện tại' : 'Ảnh mới đã chọn' }}
            </span>
        </div>

        <!-- Action Toolbar -->
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex flex-wrap gap-1.5">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5"
                    id="btn-change-thumbnail"
                    title="Chọn file ảnh khác từ máy tính"
                >
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Thay ảnh</span>
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 d-none align-items-center gap-1 text-theme-muted"
                    id="btn-revert-thumbnail"
                    title="Hủy ảnh vừa chọn và quay về ảnh ban đầu"
                >
                    <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4M3 10l4-4M3 10l4 4" />
                    </svg>
                    <span>Giữ ảnh cũ</span>
                </button>
            </div>

            <button
                type="button"
                class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5"
                id="btn-remove-thumbnail"
                title="Gỡ ảnh bìa này"
            >
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Gỡ ảnh</span>
            </button>
        </div>
    </div>

    <!-- 3. PENDING REMOVAL STATE (Shown when author confirmed removing thumbnail) -->
    <div id="thumbnail-pending-removal" class="d-none">
        <div class="p-3 rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10 mb-2.5">
            <div class="d-flex align-items-start gap-2">
                <svg style="width: 18px; height: 18px; color: #ef4444; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <div class="small">
                    <div class="fw-semibold text-danger mb-0.5">Đã đánh dấu gỡ ảnh đại diện</div>
                    <div class="text-theme-secondary" style="font-size: 12px;">Ảnh đại diện sẽ bị gỡ khi bạn lưu bài viết.</div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button
                type="button"
                class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5"
                id="btn-undo-remove"
                title="Khôi phục lại ảnh đại diện ban đầu"
            >
                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4M3 10l4-4M3 10l4 4" />
                </svg>
                <span>Hoàn tác gỡ ảnh</span>
            </button>
            <button
                type="button"
                class="btn btn-sm btn-link text-theme-muted text-decoration-none small px-1"
                id="btn-choose-new-instead"
            >
                Chọn ảnh khác
            </button>
        </div>
    </div>

    <!-- Error validation feedback -->
    <div id="thumbnail-error-feedback" class="invalid-feedback d-none text-danger mt-2 small fw-medium"></div>
    @error($fieldName)
        <div class="invalid-feedback d-block text-danger mt-2 small fw-medium">{{ $message }}</div>
    @enderror
</div>

<!-- Modal xác nhận gỡ ảnh đại diện -->
<div class="modal fade" id="thumbnail-remove-confirm-modal" tabindex="-1" aria-labelledby="thumbnailRemoveConfirmLabel" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-theme bg-theme-surface shadow-lg text-theme" style="border-radius: var(--radius-lg, 14px);">
            <div class="modal-header border-theme-bottom py-2.5 px-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-danger">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                    <h6 class="modal-title fs-6 fw-bold text-theme m-0" id="thumbnailRemoveConfirmLabel">Xác nhận gỡ ảnh</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-3 small text-theme-secondary">
                <p class="mb-1 fw-medium text-theme">Bạn có chắc chắn muốn gỡ ảnh đại diện của bài viết này không?</p>
                <p class="mb-0 text-theme-muted" style="font-size: 11.5px;">Ảnh sẽ được đánh dấu chờ gỡ và chỉ chính thức xóa sau khi bạn bấm Lưu hoặc Gửi duyệt bài viết.</p>
            </div>
            <div class="modal-footer border-theme-top py-2 px-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" data-bs-dismiss="modal">
                    Hủy bỏ
                </button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1 fw-semibold" id="btn-confirm-remove-thumbnail">
                    Xác nhận gỡ ảnh
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('thumbnail-uploader-widget');
    if (!widget) return;

    const initialThumbnail = widget.getAttribute('data-initial-thumbnail') || '';
    const fileInput = document.getElementById('thumbnail-file-input');
    const removeInput = document.getElementById('thumbnail-remove-input');
    const dropZone = document.getElementById('thumbnail-drop-zone');
    const previewContainer = document.getElementById('thumbnail-preview-container');
    const previewImg = document.getElementById('thumbnail-preview-img');
    const badgeStatus = document.getElementById('thumbnail-badge-status');
    const indicator = document.getElementById('thumbnail-indicator');
    const pendingRemoval = document.getElementById('thumbnail-pending-removal');
    const btnChange = document.getElementById('btn-change-thumbnail');
    const btnRevert = document.getElementById('btn-revert-thumbnail');
    const btnRemove = document.getElementById('btn-remove-thumbnail');
    const btnUndoRemove = document.getElementById('btn-undo-remove');
    const btnChooseNew = document.getElementById('btn-choose-new-instead');
    const confirmModalEl = document.getElementById('thumbnail-remove-confirm-modal');
    const btnConfirmRemove = document.getElementById('btn-confirm-remove-thumbnail');
    const errorFeedback = document.getElementById('thumbnail-error-feedback');

    // Ensure confirm modal is rendered directly in body to avoid z-index/overflow conflicts
    if (confirmModalEl && confirmModalEl.parentElement !== document.body) {
        document.body.appendChild(confirmModalEl);
    }

    function clearError() {
        if (errorFeedback) {
            errorFeedback.textContent = '';
            errorFeedback.classList.add('d-none');
        }
    }

    function showError(msg) {
        if (errorFeedback) {
            errorFeedback.textContent = msg;
            errorFeedback.classList.remove('d-none');
        }
    }

    function markDirty() {
        if (typeof window.__postEditorSetDirty === 'function') {
            window.__postEditorSetDirty();
        }
    }

    function showEmptyState() {
        clearError();
        fileInput.value = '';
        removeInput.value = '0';
        dropZone.classList.remove('d-none');
        previewContainer.classList.add('d-none');
        pendingRemoval.classList.add('d-none');
        indicator.textContent = 'Chưa có ảnh';
        indicator.className = 'badge bg-surface-2 text-theme-muted border border-theme font-monospace small';
    }

    function showPreviewState(url, isNew = false) {
        clearError();
        removeInput.value = '0';
        previewImg.src = url;
        previewImg.style.display = 'block';
        dropZone.classList.add('d-none');
        pendingRemoval.classList.add('d-none');
        previewContainer.classList.remove('d-none');

        if (isNew) {
            badgeStatus.textContent = 'Ảnh mới đã chọn';
            indicator.textContent = 'Ảnh mới';
            indicator.className = 'badge bg-primary-subtle text-primary border border-primary border-opacity-25 font-monospace small';
            if (initialThumbnail) {
                btnRevert.classList.remove('d-none');
                btnRevert.classList.add('d-inline-flex');
            } else {
                btnRevert.classList.add('d-none');
                btnRevert.classList.remove('d-inline-flex');
            }
        } else {
            badgeStatus.textContent = 'Ảnh hiện tại';
            indicator.textContent = 'Đã có ảnh';
            indicator.className = 'badge bg-surface-2 text-theme-muted border border-theme font-monospace small';
            btnRevert.classList.add('d-none');
            btnRevert.classList.remove('d-inline-flex');
        }
    }

    function showPendingRemovalState() {
        clearError();
        fileInput.value = '';
        removeInput.value = '1';
        dropZone.classList.add('d-none');
        previewContainer.classList.add('d-none');
        pendingRemoval.classList.remove('d-none');
        indicator.textContent = 'Chờ gỡ ảnh';
        indicator.className = 'badge bg-danger-subtle text-danger border border-danger border-opacity-25 font-monospace small';
        markDirty();
    }

    // Trigger file picker
    function openFilePicker() {
        fileInput.click();
    }

    dropZone.addEventListener('click', openFilePicker);
    dropZone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openFilePicker();
        }
    });

    if (btnChange) btnChange.addEventListener('click', openFilePicker);
    if (btnChooseNew) btnChooseNew.addEventListener('click', openFilePicker);

    // Drag and drop support
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-primary', 'bg-surface-3');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-primary', 'bg-surface-3');
        });
    });

    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            fileInput.files = dt.files;
            handleFileSelected(dt.files[0]);
        }
    });

    // Handle file input change
    fileInput.addEventListener('change', function() {
        // If file dialog was cancelled or closed without choosing a file, do NOT lose existing preview!
        if (!fileInput.files || !fileInput.files[0]) {
            return;
        }
        handleFileSelected(fileInput.files[0]);
    });

    function handleFileSelected(file) {
        if (!file) return;

        // Validation: mime type
        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError('Định dạng file không hợp lệ. Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
            fileInput.value = '';
            return;
        }

        // Validation: max size 2MB
        if (file.size > 2 * 1024 * 1024) {
            showError('Dung lượng hình ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.');
            fileInput.value = '';
            return;
        }

        clearError();
        const reader = new FileReader();
        reader.onload = function(e) {
            showPreviewState(e.target.result, true);
            markDirty();
        };
        reader.readAsDataURL(file);
    }

    // Revert to initial image
    if (btnRevert) {
        btnRevert.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.value = '';
            if (initialThumbnail) {
                showPreviewState(initialThumbnail, false);
            } else {
                showEmptyState();
            }
            markDirty();
        });
    }

    // Remove button clicked
    if (btnRemove) {
        btnRemove.addEventListener('click', function(e) {
            e.preventDefault();
            // If user just chose a file in create mode and no initial thumbnail exists, clear immediately
            if (!initialThumbnail && fileInput.files && fileInput.files[0]) {
                showEmptyState();
                markDirty();
                return;
            }

            // Otherwise, show confirmation modal
            if (window.bootstrap && window.bootstrap.Modal && confirmModalEl) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                modal.show();
            } else {
                if (confirm('Bạn có chắc chắn muốn gỡ ảnh đại diện của bài viết này không? Ảnh sẽ chính thức bị gỡ khi bạn lưu bài viết.')) {
                    showPendingRemovalState();
                }
            }
        });
    }

    // Confirm removal modal button
    if (btnConfirmRemove) {
        btnConfirmRemove.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.bootstrap && window.bootstrap.Modal && confirmModalEl) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                modal.hide();
            }
            showPendingRemovalState();
        });
    }

    // Undo removal button
    if (btnUndoRemove) {
        btnUndoRemove.addEventListener('click', function(e) {
            e.preventDefault();
            if (initialThumbnail) {
                showPreviewState(initialThumbnail, false);
            } else {
                showEmptyState();
            }
            markDirty();
        });
    }
});
</script>

