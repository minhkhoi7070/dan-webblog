/**
 * BlogMNM — Profile Avatar & Real-time Synchronization
 * Handles instant client-side preview, validation, cancellation,
 * memory-safe object URL management, AJAX form submission, and
 * multi-component avatar synchronization without full-page reload.
 */

let activePreviewUrl = null;

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
};

/**
 * Synchronize avatar across all visible components for a given user ID
 * (Sidebar, Mobile Header, Mobile Drawer, Bottom Nav, Comments, Feed Cards, Profile)
 */
export function syncUserAvatar(userId, newAvatarUrl, userName = '') {
    if (!userId || !newAvatarUrl) return;

    // Cache-busting URL to prevent browser caching of previous image
    const timestamp = Date.now();
    const cleanUrl = newAvatarUrl.split('?')[0];
    const cacheBustedUrl = `${cleanUrl}?t=${timestamp}`;

    // 1. Update all <x-avatar> wrappers with matching user ID
    const wrappers = document.querySelectorAll(`[data-user-avatar="${userId}"]`);
    wrappers.forEach((wrapper) => {
        const inner = wrapper.querySelector('.avatar-inner') || wrapper;
        let img = inner.querySelector('.avatar-img') || inner.querySelector('img');
        const initials = inner.querySelector('.avatar-initials') || inner.querySelector('span');

        if (img) {
            img.src = cacheBustedUrl;
            img.classList.remove('d-none');
            img.classList.add('d-block');
            if (initials) {
                initials.classList.add('d-none');
            }
        } else {
            // If component previously rendered text initials without an img
            img = document.createElement('img');
            img.src = cacheBustedUrl;
            img.alt = userName || 'Avatar';
            img.className = 'w-100 h-100 d-block avatar-img';
            img.style.objectFit = 'cover';
            img.style.aspectRatio = '1 / 1';
            img.style.borderRadius = '50%';
            img.loading = 'lazy';
            img.onerror = function () {
                this.classList.add('d-none');
                if (initials) initials.classList.remove('d-none');
            };

            if (initials) {
                initials.classList.add('d-none');
                inner.insertBefore(img, initials);
            } else {
                inner.appendChild(img);
            }
        }
    });

    // 2. Update Settings page preview if present
    const settingsPreviewImg = document.getElementById('avatar-preview-img');
    const settingsPlaceholder = document.getElementById('avatar-preview-placeholder');
    if (settingsPreviewImg) {
        settingsPreviewImg.src = cacheBustedUrl;
        settingsPreviewImg.dataset.originalSrc = cacheBustedUrl;
        settingsPreviewImg.classList.remove('d-none');
        settingsPreviewImg.classList.add('d-block');
        if (settingsPlaceholder) {
            settingsPlaceholder.classList.add('d-none');
            settingsPlaceholder.classList.remove('d-flex');
        }
    }

    // 3. Dispatch global custom event for other subsystems
    if (typeof document !== 'undefined') {
        document.dispatchEvent(new CustomEvent('blogmnm:avatar-updated', {
            detail: { userId, avatarUrl: cacheBustedUrl, userName }
        }));
    }
}

if (typeof window !== 'undefined') {
    window.syncUserAvatar = syncUserAvatar;
}

/**
 * Initialize global delegated event handlers for Profile & Avatar
 */
export function initProfileAvatar() {
    if (typeof window === 'undefined' || typeof document === 'undefined') return;

    // Attach syncUserAvatar to window for global access
    window.syncUserAvatar = syncUserAvatar;

    // A. File Selection & Instant Client-side Preview
    document.addEventListener('change', (e) => {
        if (!e.target || e.target.id !== 'avatar-input') return;

        const input = e.target;
        const file = input.files?.[0];
        const errorFeedback = document.getElementById('avatar-error-feedback');
        const cancelBtn = document.getElementById('btn-cancel-avatar');
        const previewImg = document.getElementById('avatar-preview-img');
        const placeholder = document.getElementById('avatar-preview-placeholder');

        const clearError = () => {
            if (errorFeedback) {
                errorFeedback.textContent = '';
                errorFeedback.classList.add('d-none');
                errorFeedback.classList.remove('d-block');
            }
        };

        const showError = (msg) => {
            if (errorFeedback) {
                errorFeedback.textContent = msg;
                errorFeedback.classList.remove('d-none');
                errorFeedback.classList.add('d-block');
            }
            if (typeof window.showToast === 'function') {
                window.showToast(msg, 'error');
            }
        };

        clearError();

        if (!file) return;

        // Size check: Max 2MB (2048 KB = 2,097,152 bytes)
        const MAX_SIZE = 2 * 1024 * 1024;
        if (file.size > MAX_SIZE) {
            showError('Dung lượng ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.');
            input.value = '';
            return;
        }

        // Format check: JPG, JPEG, PNG, WebP
        const validMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const fileName = (file.name || '').toLowerCase();
        const validExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
        const hasValidExt = validExtensions.some(ext => fileName.endsWith(ext));

        if (!validMimeTypes.includes(file.type) && !hasValidExt) {
            showError('Chỉ chấp nhận định dạng ảnh JPG, JPEG, PNG hoặc WebP.');
            input.value = '';
            return;
        }

        // Revoke previous in-flight object URL to prevent memory leaks
        if (activePreviewUrl) {
            URL.revokeObjectURL(activePreviewUrl);
            activePreviewUrl = null;
        }

        activePreviewUrl = URL.createObjectURL(file);

        if (previewImg) {
            previewImg.src = activePreviewUrl;
            previewImg.classList.remove('d-none');
            previewImg.classList.add('d-block');
        }

        if (placeholder) {
            placeholder.classList.add('d-none');
            placeholder.classList.remove('d-flex');
        }

        if (cancelBtn) {
            cancelBtn.classList.remove('d-none');
        }
    });

    // B. Cancel File Selection
    document.addEventListener('click', (e) => {
        const cancelBtn = e.target.closest('#btn-cancel-avatar');
        if (!cancelBtn) return;

        e.preventDefault();

        const input = document.getElementById('avatar-input');
        if (input) input.value = '';

        if (activePreviewUrl) {
            URL.revokeObjectURL(activePreviewUrl);
            activePreviewUrl = null;
        }

        const previewImg = document.getElementById('avatar-preview-img');
        const placeholder = document.getElementById('avatar-preview-placeholder');
        const originalSrc = previewImg?.dataset.originalSrc;

        if (originalSrc) {
            previewImg.src = originalSrc;
            previewImg.classList.remove('d-none');
            previewImg.classList.add('d-block');
            if (placeholder) {
                placeholder.classList.add('d-none');
                placeholder.classList.remove('d-flex');
            }
        } else {
            if (previewImg) {
                previewImg.src = '';
                previewImg.classList.add('d-none');
                previewImg.classList.remove('d-block');
            }
            if (placeholder) {
                placeholder.classList.remove('d-none');
                placeholder.classList.add('d-flex');
            }
        }

        cancelBtn.classList.add('d-none');

        const errorFeedback = document.getElementById('avatar-error-feedback');
        if (errorFeedback) {
            errorFeedback.textContent = '';
            errorFeedback.classList.add('d-none');
            errorFeedback.classList.remove('d-block');
        }
    });

    // C. Keyboard Accessibility (Enter or Space triggers file picker)
    document.addEventListener('keydown', (e) => {
        if (!e.target) return;
        const trigger = e.target.closest('#avatar-picker-trigger, #btn-choose-avatar');
        if (trigger && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            document.getElementById('avatar-input')?.click();
        }
    });

    // D. Profile Form Submission via Fetch API (Seamless with Traditional Fallback)
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('#profile-update-form');
        if (!form) return;

        // Prevent double submit
        if (form.dataset.submitting === 'true') {
            e.preventDefault();
            return;
        }

        e.preventDefault();
        form.dataset.submitting = 'true';

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Lưu thay đổi';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span>
                <span>Đang lưu...</span>
            `;
        }

        // Clear previous ajax error states
        form.querySelectorAll('.invalid-feedback.ajax-feedback').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        const avatarErrorEl = document.getElementById('avatar-error-feedback');
        if (avatarErrorEl) {
            avatarErrorEl.textContent = '';
            avatarErrorEl.classList.add('d-none');
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            if (response.ok) {
                const data = await response.json();

                // If email changed and requires OTP verification
                if (data.redirect) {
                    window.location.assign(data.redirect);
                    return;
                }

                // Synchronize avatar across the entire system
                if (data.user) {
                    if (data.user.avatar_url) {
                        syncUserAvatar(data.user.id, data.user.avatar_url, data.user.name);
                    }

                    // Update form field original states
                    const previewImg = document.getElementById('avatar-preview-img');
                    if (previewImg && data.user.avatar_url) {
                        previewImg.dataset.originalSrc = data.user.avatar_url;
                    }
                }

                // Hide Cancel button since changes are now saved
                const cancelBtn = document.getElementById('btn-cancel-avatar');
                if (cancelBtn) cancelBtn.classList.add('d-none');

                // Clear input file reference
                const avatarInput = document.getElementById('avatar-input');
                if (avatarInput) avatarInput.value = '';

                // Revoke object URL
                if (activePreviewUrl) {
                    URL.revokeObjectURL(activePreviewUrl);
                    activePreviewUrl = null;
                }

                // Show success toast
                if (typeof window.showToast === 'function') {
                    window.showToast(data.message || 'Cập nhật thông tin hồ sơ thành công!', 'success');
                }

                // Show saved indicator message
                const savedMsg = document.getElementById('profile-saved-msg');
                if (savedMsg) {
                    savedMsg.classList.remove('d-none');
                    savedMsg.classList.add('d-inline-flex');
                    setTimeout(() => {
                        savedMsg.classList.add('d-none');
                        savedMsg.classList.remove('d-inline-flex');
                    }, 3500);
                }
            } else if (response.status === 422) {
                const data = await response.json();
                const errors = data.errors || {};

                for (const [field, messages] of Object.entries(errors)) {
                    if (field === 'avatar') {
                        const avatarErr = document.getElementById('avatar-error-feedback');
                        if (avatarErr) {
                            avatarErr.textContent = messages[0];
                            avatarErr.classList.remove('d-none');
                            avatarErr.classList.add('d-block');
                        }
                    } else {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errDiv = document.createElement('div');
                            errDiv.className = 'invalid-feedback ajax-feedback d-block mt-1';
                            errDiv.style.fontSize = '12px';
                            errDiv.textContent = messages[0];
                            input.parentElement.appendChild(errDiv);
                        }
                    }
                }

                if (typeof window.showToast === 'function') {
                    window.showToast(data.message || 'Vui lòng kiểm tra lại thông tin hồ sơ.', 'error');
                }
            } else if (response.status === 419) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Phiên đăng nhập đã hết hạn. Vui lòng tải lại trang.', 'error');
                }
            } else {
                // Unexpected server response — fallback to standard submit
                form.dataset.submitting = 'false';
                form.submit();
                return;
            }
        } catch (err) {
            console.error('Profile update error, falling back to native submit:', err);
            form.dataset.submitting = 'false';
            form.submit();
            return;
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
            form.dataset.submitting = 'false';
        }
    });
}
