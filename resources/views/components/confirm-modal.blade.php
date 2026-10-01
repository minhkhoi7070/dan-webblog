<div class="modal fade" id="global-confirm-modal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="global-confirm-modal-header" aria-describedby="confirm-modal-message">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-theme bg-theme-surface shadow-lg text-theme" style="border-radius: 1.25rem;">
            <!-- Header -->
            <div class="modal-header border-theme-bottom d-flex align-items-center justify-content-between px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span id="confirm-modal-header-icon" class="badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 32px; height: 32px;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <h5 class="modal-title h6 fw-bold text-theme m-0" id="global-confirm-modal-header">Xác nhận thao tác</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <h4 class="h5 fw-bold text-theme mb-2" id="confirm-modal-title"></h4>
                    <p class="small text-theme-secondary mb-0" id="confirm-modal-message"></p>
                </div>

                <!-- Target Object Detail Container -->
                <div id="confirm-modal-target-box" class="p-3 rounded-3 bg-theme-surface-hover border border-theme mb-3 d-none">
                    <div class="fw-semibold small text-theme" id="confirm-modal-target-name"></div>
                    <div class="small text-theme-secondary mt-1 text-break" id="confirm-modal-target-meta"></div>
                </div>

                <!-- Description / Consequence Container -->
                <div id="confirm-modal-description-box" class="small text-theme-secondary mb-3 d-none">
                    <div class="d-flex align-items-start gap-2 p-2 rounded-2 bg-body-tertiary border text-body" style="font-size: 0.8rem;">
                        <svg class="text-secondary flex-shrink-0 mt-0.5" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="confirm-modal-description"></span>
                    </div>
                </div>

                <!-- Optional Prompt Input (e.g. rejection reason) -->
                <div id="confirm-modal-prompt-box" class="mb-2 d-none">
                    <label for="confirm-modal-prompt-input" class="form-label small fw-semibold text-theme mb-1" id="confirm-modal-prompt-label">Lý do từ chối:</label>
                    <textarea id="confirm-modal-prompt-input" class="form-control form-control-sm" rows="3" placeholder="Nhập lý do cụ thể..."></textarea>
                    <div class="text-danger small mt-1 d-none" id="confirm-modal-prompt-error">Vui lòng nhập lý do.</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-theme-top px-4 py-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold" data-bs-dismiss="modal" id="confirm-modal-cancel">
                    Hủy
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-bold d-inline-flex align-items-center gap-2 btn-danger" id="confirm-modal-submit">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="confirm-modal-spinner"></span>
                    <span id="confirm-modal-submit-text">Xác nhận</span>
                </button>
            </div>
        </div>
    </div>
</div>
