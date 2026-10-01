/**
 * BlogMNM Interactions: Like, Favorite, Follow, and Comments AJAX Engine
 * Integrated with Bootstrap 5 and BlogMNM Monochrome Theme
 */
document.addEventListener('DOMContentLoaded', () => {
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const showToast = (message, type = 'success') => {
        let container = document.getElementById('global-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'global-toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        const isError = type === 'error' || type === 'danger';
        const isWarning = type === 'warning';
        const bgClass = isError ? 'bg-danger text-white' : (isWarning ? 'bg-warning text-dark' : 'bg-success text-white');
        const closeClass = isWarning ? 'btn-close' : 'btn-close btn-close-white';

        const iconSvg = isError
            ? `<svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
            : (isWarning
                ? `<svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
                : `<svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`);

        toastEl.className = `toast align-items-center border-0 ${bgClass} mb-2 shadow-lg`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    ${iconSvg}
                    <span>${message}</span>
                </div>
                <button type="button" class="${closeClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button>
            </div>
        `;

        container.appendChild(toastEl);

        if (window.bootstrap && window.bootstrap.Toast) {
            const bsToast = new window.bootstrap.Toast(toastEl, { delay: 4000 });
            bsToast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        } else {
            setTimeout(() => toastEl.remove(), 4000);
        }
    };
    window.showToast = showToast;

    // Auto-show any server-side rendered toasts in container
    document.querySelectorAll('#global-toast-container .toast').forEach((el) => {
        if (window.bootstrap && window.bootstrap.Toast) {
            const t = window.bootstrap.Toast.getOrCreateInstance(el);
            t.show();
            el.addEventListener('hidden.bs.toast', () => el.remove());
        }
    });

    // 1. LIKE ACTION
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="like"]');
        if (!btn) return;
        e.preventDefault();

        const url = btn.getAttribute('data-url');
        if (!url) return;

        btn.disabled = true;
        btn.classList.add('opacity-50');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.message || 'Lỗi khi thích bài viết.');
            }

            const data = await response.json();
            const postId = btn.getAttribute('data-post-id');
            const allLikeBtns = document.querySelectorAll(`[data-action="like"][data-post-id="${postId}"]`);

            allLikeBtns.forEach(el => {
                const countSpan = el.querySelector('.like-count');
                const icon = el.querySelector('svg');

                if (countSpan) {
                    countSpan.textContent = data.likes_count > 0 ? data.likes_count : '';
                }

                if (data.liked) {
                    el.classList.add('text-danger');
                    el.classList.remove('text-secondary', 'text-theme-secondary');
                    if (icon) {
                        icon.setAttribute('fill', 'currentColor');
                    }
                } else {
                    el.classList.remove('text-danger');
                    el.classList.add('text-theme-secondary');
                    if (icon) {
                        icon.setAttribute('fill', 'none');
                    }
                }
            });

            showToast(data.liked ? 'Đã thích bài viết!' : 'Đã bỏ thích bài viết.');
        } catch (error) {
            console.error('Like error:', error);
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
    });

    // 2. FAVORITE / BOOKMARK ACTION
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="favorite"]');
        if (!btn) return;
        e.preventDefault();

        const url = btn.getAttribute('data-url');
        if (!url) return;

        btn.disabled = true;
        btn.classList.add('opacity-50');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.message || 'Lỗi khi lưu bài viết.');
            }

            const data = await response.json();
            const postId = btn.getAttribute('data-post-id');
            const allFavBtns = document.querySelectorAll(`[data-action="favorite"][data-post-id="${postId}"]`);

            allFavBtns.forEach(el => {
                const icon = el.querySelector('svg');
                const label = el.querySelector('.favorite-label');

                if (data.saved) {
                    el.classList.add('text-warning');
                    el.classList.remove('text-secondary', 'text-theme-secondary');
                    if (icon) {
                        icon.setAttribute('fill', 'currentColor');
                    }
                    if (label) label.textContent = 'Đã lưu';
                } else {
                    el.classList.remove('text-warning');
                    el.classList.add('text-theme-secondary');
                    if (icon) {
                        icon.setAttribute('fill', 'none');
                    }
                    if (label) label.textContent = 'Lưu';
                }
            });

            showToast(data.saved ? 'Đã lưu vào danh sách đọc!' : 'Đã bỏ lưu bài viết.');
        } catch (error) {
            console.error('Favorite error:', error);
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
    });

    // 3. FOLLOW AUTHOR ACTION
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="follow"]');
        if (!btn) return;
        e.preventDefault();

        const url = btn.getAttribute('data-url');
        if (!url) return;

        btn.disabled = true;
        btn.classList.add('opacity-50');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (response.status === 422) {
                const data = await response.json();
                throw new Error(data.message || 'Không thể theo dõi chính mình.');
            }

            if (!response.ok) {
                throw new Error('Lỗi khi thực hiện thao tác.');
            }

            const data = await response.json();
            const authorId = btn.getAttribute('data-author-id');
            const allFollowBtns = document.querySelectorAll(`[data-action="follow"][data-author-id="${authorId}"]`);

            allFollowBtns.forEach(el => {
                if (data.following) {
                    el.textContent = 'Đang theo dõi';
                    el.classList.remove('btn-primary');
                    el.classList.add('btn-outline-theme');
                } else {
                    el.textContent = 'Theo dõi';
                    el.classList.remove('btn-outline-theme');
                    el.classList.add('btn-primary');
                }
            });

            const followerCountEl = document.getElementById('follower-count');
            if (followerCountEl) {
                let current = parseInt(followerCountEl.textContent, 10) || 0;
                followerCountEl.textContent = data.following ? current + 1 : Math.max(0, current - 1);
            }

            showToast(data.following ? 'Đã theo dõi tác giả!' : 'Đã bỏ theo dõi tác giả.');
        } catch (error) {
            console.error('Follow error:', error);
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
    });

    // 4. INLINE REPLY TOGGLE
    document.addEventListener('click', (e) => {
        const replyToggle = e.target.closest('[data-action="toggle-reply"]');
        if (!replyToggle) return;
        e.preventDefault();

        const commentId = replyToggle.getAttribute('data-comment-id');
        const replyBox = document.getElementById(`reply-box-${commentId}`);
        if (replyBox) {
            replyBox.classList.toggle('d-none');
            const textarea = replyBox.querySelector('textarea');
            if (!replyBox.classList.contains('d-none') && textarea) {
                textarea.focus();
            }
        }
    });

    // 5. AJAX COMMENT FORM SUBMISSION
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('[data-form="comment-form"]');
        if (!form) return;
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const textarea = form.querySelector('textarea[name="body"]');
        const errorContainer = form.querySelector('.comment-error');

        if (errorContainer) errorContainer.textContent = '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (response.status === 422) {
                const data = await response.json();
                const errorMsg = data.errors?.body?.[0] || data.errors?.parent_id?.[0] || 'Vui lòng kiểm tra lại nội dung bình luận.';
                if (errorContainer) {
                    errorContainer.textContent = errorMsg;
                } else {
                    showToast(errorMsg, 'error');
                }
                return;
            }

            if (!response.ok) {
                throw new Error('Không thể gửi bình luận. Vui lòng thử lại.');
            }

            const data = await response.json();

            if (textarea) textarea.value = '';

            if (form.classList.contains('reply-form')) {
                form.classList.add('d-none');
            }

            const parentId = formData.get('parent_id');
            const commentsContainer = document.getElementById('comments-list');
            const initial = (data.comment.user_name || 'U').charAt(0).toUpperCase();

            if (parentId) {
                const parentCommentEl = document.getElementById(`comment-${parentId}`);
                let repliesContainer = parentCommentEl ? parentCommentEl.querySelector('.replies-container') : null;
                if (!repliesContainer && parentCommentEl) {
                    repliesContainer = document.createElement('div');
                    repliesContainer.className = 'replies-container mt-3 ps-3 ps-sm-4 border-theme-left d-flex flex-column gap-2';
                    parentCommentEl.appendChild(repliesContainer);
                }
                if (repliesContainer) {
                    const replyCard = document.createElement('div');
                    replyCard.className = 'p-3 rounded-3 bg-theme-surface border border-theme text-sm';
                    replyCard.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="rounded-circle brand-gradient fw-bold d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px; font-size: 11px;" data-theme-preserve>
                                ${initial}
                            </div>
                            <span class="fw-semibold text-theme small">${data.comment.user_name}</span>
                            <span class="text-theme-secondary small">&bull; Vừa xong</span>
                        </div>
                        <p class="small text-theme mb-0 ps-4 lh-base">${data.comment.body}</p>
                    `;
                    repliesContainer.appendChild(replyCard);
                }
            } else if (commentsContainer) {
                const emptyPlaceholder = commentsContainer.querySelector('.text-center.py-4');
                if (emptyPlaceholder) {
                    emptyPlaceholder.remove();
                }

                const newCommentEl = document.createElement('div');
                newCommentEl.className = 'p-3 p-sm-4 rounded-4 bg-theme-surface border border-theme mb-3';
                newCommentEl.id = `comment-${data.comment.id}`;
                newCommentEl.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle brand-gradient fw-bold d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 13px;" data-theme-preserve>
                                ${initial}
                            </div>
                            <div>
                                <span class="fw-semibold text-theme small">${data.comment.user_name}</span>
                                <span class="text-theme-secondary small ms-2">Vừa xong</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-theme mb-0 ps-4 ms-2 lh-base">${data.comment.body}</p>
                `;
                commentsContainer.prepend(newCommentEl);
            }

            const counterEls = document.querySelectorAll('.comment-count-badge');
            counterEls.forEach(el => {
                const count = parseInt(el.textContent, 10) || 0;
                el.textContent = count + 1;
            });

            showToast('Đã đăng bình luận thành công!');
        } catch (error) {
            console.error('Comment error:', error);
            showToast(error.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
            }
        }
    });

    // 6. GLOBAL BOOTSTRAP CONFIRM MODAL ENGINE
    let currentConfirmForm = null;
    let currentPromptName = null;

    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-confirm="true"]');
        if (!form) return;

        if (form.dataset.confirmed === 'true') {
            // Form was already confirmed by user in modal, allow submission
            return;
        }

        e.preventDefault();
        currentConfirmForm = form;

        const modalEl = document.getElementById('global-confirm-modal');
        if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) {
            form.dataset.confirmed = 'true';
            form.requestSubmit ? form.requestSubmit() : form.submit();
            return;
        }

        // Read attributes from form
        const title = form.getAttribute('data-confirm-title') || 'Xác nhận thao tác';
        const message = form.getAttribute('data-confirm-message') || 'Bạn có chắc chắn muốn thực hiện thao tác này không?';
        const targetName = form.getAttribute('data-confirm-target-name');
        const targetMeta = form.getAttribute('data-confirm-target-meta');
        const description = form.getAttribute('data-confirm-description');
        const btnText = form.getAttribute('data-confirm-btn-text') || 'Xác nhận';
        const btnClass = form.getAttribute('data-confirm-btn-class') || 'btn-danger';
        const type = form.getAttribute('data-confirm-type') || 'danger';
        const isPrompt = form.getAttribute('data-confirm-prompt') === 'true';
        currentPromptName = form.getAttribute('data-confirm-prompt-name') || 'rejection_reason';
        const promptPlaceholder = form.getAttribute('data-confirm-prompt-placeholder') || 'Nhập thông tin yêu cầu...';
        const promptLabel = form.getAttribute('data-confirm-prompt-label') || 'Lý do từ chối:';

        // Elements
        const titleEl = document.getElementById('confirm-modal-title');
        const messageEl = document.getElementById('confirm-modal-message');
        const targetBoxEl = document.getElementById('confirm-modal-target-box');
        const targetNameEl = document.getElementById('confirm-modal-target-name');
        const targetMetaEl = document.getElementById('confirm-modal-target-meta');
        const descBoxEl = document.getElementById('confirm-modal-description-box');
        const descEl = document.getElementById('confirm-modal-description');
        const submitBtn = document.getElementById('confirm-modal-submit');
        const submitTextEl = document.getElementById('confirm-modal-submit-text');
        const spinnerEl = document.getElementById('confirm-modal-spinner');
        const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
        const promptLabelEl = document.getElementById('confirm-modal-prompt-label');
        const promptInputEl = document.getElementById('confirm-modal-prompt-input');
        const promptErrorEl = document.getElementById('confirm-modal-prompt-error');
        const headerIconEl = document.getElementById('confirm-modal-header-icon');

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;

        if (targetName && targetBoxEl) {
            targetBoxEl.classList.remove('d-none');
            if (targetNameEl) targetNameEl.textContent = targetName;
            if (targetMeta && targetMetaEl) {
                targetMetaEl.textContent = targetMeta;
                targetMetaEl.classList.remove('d-none');
            } else if (targetMetaEl) {
                targetMetaEl.classList.add('d-none');
            }
        } else if (targetBoxEl) {
            targetBoxEl.classList.add('d-none');
        }

        if (description && descBoxEl) {
            descBoxEl.classList.remove('d-none');
            if (descEl) descEl.textContent = description;
        } else if (descBoxEl) {
            descBoxEl.classList.add('d-none');
        }

        if (isPrompt && promptBoxEl) {
            promptBoxEl.classList.remove('d-none');
            if (promptLabelEl) promptLabelEl.textContent = promptLabel;
            if (promptInputEl) {
                promptInputEl.placeholder = promptPlaceholder;
                promptInputEl.value = '';
                promptInputEl.classList.remove('is-invalid');
            }
            if (promptErrorEl) promptErrorEl.classList.add('d-none');
        } else if (promptBoxEl) {
            promptBoxEl.classList.add('d-none');
        }

        // Configure submit button
        if (submitBtn) {
            submitBtn.className = `btn btn-sm rounded-pill px-4 fw-bold d-inline-flex align-items-center gap-2 ${btnClass}`;
            submitBtn.disabled = false;
        }
        if (submitTextEl) submitTextEl.textContent = btnText;
        if (spinnerEl) spinnerEl.classList.add('d-none');

        // Configure icon
        if (headerIconEl) {
            if (type === 'success') {
                headerIconEl.className = 'badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-success-subtle text-success';
                headerIconEl.innerHTML = `<svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
            } else if (type === 'warning') {
                headerIconEl.className = 'badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-warning-subtle text-warning';
                headerIconEl.innerHTML = `<svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
            } else {
                headerIconEl.className = 'badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-danger-subtle text-danger';
                headerIconEl.innerHTML = `<svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;
            }
        }

        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    });

    const confirmSubmitBtn = document.getElementById('confirm-modal-submit');
    const confirmCancelBtn = document.getElementById('confirm-modal-cancel');
    const confirmModalEl = document.getElementById('global-confirm-modal');
    const promptInputEl = document.getElementById('confirm-modal-prompt-input');
    const promptErrorEl = document.getElementById('confirm-modal-prompt-error');
    const closeBtnEl = confirmModalEl ? confirmModalEl.querySelector('.btn-close') : null;

    if (confirmSubmitBtn) {
        confirmSubmitBtn.addEventListener('click', () => {
            if (!currentConfirmForm) return;

            const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
            if (promptBoxEl && !promptBoxEl.classList.contains('d-none')) {
                const val = promptInputEl ? promptInputEl.value.trim() : '';
                if (!val) {
                    if (promptInputEl) promptInputEl.classList.add('is-invalid');
                    if (promptErrorEl) promptErrorEl.classList.remove('d-none');
                    if (promptInputEl) promptInputEl.focus();
                    return;
                }

                let formInput = currentConfirmForm.querySelector(`[name="${currentPromptName}"]`);
                if (!formInput) {
                    formInput = document.createElement('input');
                    formInput.type = 'hidden';
                    formInput.name = currentPromptName;
                    currentConfirmForm.appendChild(formInput);
                }
                formInput.value = val;
            }

            // Set loading state - disable submit, cancel, and close buttons
            confirmSubmitBtn.disabled = true;
            if (confirmCancelBtn) confirmCancelBtn.disabled = true;
            if (closeBtnEl) closeBtnEl.disabled = true;
            const spinnerEl = document.getElementById('confirm-modal-spinner');
            const submitTextEl = document.getElementById('confirm-modal-submit-text');
            if (spinnerEl) spinnerEl.classList.remove('d-none');
            if (submitTextEl) submitTextEl.textContent = 'Đang xử lý...';

            currentConfirmForm.dataset.confirmed = 'true';
            currentConfirmForm.requestSubmit ? currentConfirmForm.requestSubmit() : currentConfirmForm.submit();
        });
    }

    if (confirmModalEl) {
        // Prevent accidental closing (ESC or backdrop) while submission is in progress
        confirmModalEl.addEventListener('hide.bs.modal', (e) => {
            if (confirmSubmitBtn && confirmSubmitBtn.disabled) {
                e.preventDefault();
            }
        });

        // Accessibility: auto-focus input or confirm button when modal is shown
        confirmModalEl.addEventListener('shown.bs.modal', () => {
            const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
            if (promptBoxEl && !promptBoxEl.classList.contains('d-none') && promptInputEl) {
                promptInputEl.focus();
            } else if (confirmSubmitBtn && !confirmSubmitBtn.disabled) {
                confirmSubmitBtn.focus();
            }
        });

        // Reset state after modal is hidden
        confirmModalEl.addEventListener('hidden.bs.modal', () => {
            currentConfirmForm = null;
            currentPromptName = null;
            if (confirmSubmitBtn) confirmSubmitBtn.disabled = false;
            if (confirmCancelBtn) confirmCancelBtn.disabled = false;
            if (closeBtnEl) closeBtnEl.disabled = false;
            const spinnerEl = document.getElementById('confirm-modal-spinner');
            if (spinnerEl) spinnerEl.classList.add('d-none');
            if (promptInputEl) {
                promptInputEl.value = '';
                promptInputEl.classList.remove('is-invalid');
            }
            if (promptErrorEl) promptErrorEl.classList.add('d-none');
        });
    }
});
