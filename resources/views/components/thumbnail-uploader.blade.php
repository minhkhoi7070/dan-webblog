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

<div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl thumbnail-uploader-widget"
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
