/**
 * BlogMNM Interactions: Like, Favorite, Follow, and Comments Optimistic Engine
 * Threads-like instant visual response (<50ms) with queue-based race condition protection.
 * Integrated with Bootstrap 5 and BlogMNM Monochrome Theme.
 */
document.addEventListener('DOMContentLoaded', () => {
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const escapeHtml = (text) => {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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

    // =========================================================================
    // STATE QUEUE ENGINE: Handles Desired States, Race Conditions & Mutexes
    // =========================================================================
    const interactionQueues = new Map();

    // -------------------------------------------------------------------------
    // 1. OPTIMISTIC LIKE ACTION (Threads-like Heart & Number Ticker)
    // -------------------------------------------------------------------------
    const updateLikeCountWithAnimation = (container, newCount, direction = 'up', shouldAnimate = true) => {
        if (!container) return;

        const currentSpan = container.querySelector('.like-count-value');
        const oldText = currentSpan ? currentSpan.textContent.trim() : container.textContent.trim();
        const newText = newCount > 0 ? String(newCount) : '';

        if (oldText === newText) return;

        if (!shouldAnimate) {
            container.innerHTML = newText ? `<span class="like-count-value">${escapeHtml(newText)}</span>` : '';
            return;
        }

        // If starting from empty (0 -> 1)
        if (!oldText) {
            container.innerHTML = `<span class="like-count-value count-slide-in-up">${escapeHtml(newText)}</span>`;
            setTimeout(() => {
                const el = container.querySelector('.like-count-value');
                if (el) el.classList.remove('count-slide-in-up');
            }, 200);
            return;
        }

        // If going to empty (1 -> 0)
        if (!newText) {
            if (currentSpan) {
                currentSpan.className = 'like-count-value count-slide-out-down';
                setTimeout(() => {
                    if (container.querySelector('.like-count-value') === currentSpan) {
                        container.innerHTML = '';
                    }
                }, 190);
            } else {
                container.innerHTML = '';
            }
            return;
        }

        // Direction based on up (like) or down (unlike)
        const outClass = direction === 'up' ? 'count-slide-out-up' : 'count-slide-out-down';
        const inClass = direction === 'up' ? 'count-slide-in-up' : 'count-slide-in-down';

        // Clear any previous exiting spans if user clicked rapidly
        container.querySelectorAll('.is-exiting').forEach((el) => el.remove());

        if (currentSpan) {
            currentSpan.className = `like-count-value is-exiting ${outClass}`;
        }

        const nextSpan = document.createElement('span');
        nextSpan.className = `like-count-value ${inClass}`;
        nextSpan.textContent = newText;
        container.appendChild(nextSpan);

        setTimeout(() => {
            if (currentSpan && currentSpan.parentNode === container) {
                currentSpan.remove();
            }
            nextSpan.className = 'like-count-value';
        }, 200);
    };

    const applyLikeUI = (postId, liked, count, shouldAnimate = true) => {
        const allBtns = document.querySelectorAll(`[data-action="like"][data-post-id="${postId}"]`);
        allBtns.forEach((btn) => {
            btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
            btn.setAttribute('aria-label', liked ? 'Bỏ thích bài viết' : 'Thích bài viết');
            btn.setAttribute('title', liked ? 'Bỏ thích' : 'Thích');

            const countSpan = btn.querySelector('.like-count');
            if (countSpan) {
                const direction = liked ? 'up' : 'down';
                updateLikeCountWithAnimation(countSpan, count, direction, shouldAnimate);
            }

            const icon = btn.querySelector('.like-icon') || btn.querySelector('svg');
            if (liked) {
                btn.classList.add('text-danger');
                btn.classList.remove('text-secondary', 'text-theme-secondary');
                if (icon) {
                    icon.setAttribute('fill', 'currentColor');
                    if (shouldAnimate) {
                        icon.classList.remove('like-heart-pop');
                        // Trigger reflow to restart animation reliably on repeated rapid clicks
                        void icon.offsetWidth;
                        icon.classList.add('like-heart-pop');
                    }
                }
            } else {
                btn.classList.remove('text-danger');
                btn.classList.add('text-theme-secondary');
                if (icon) {
                    icon.setAttribute('fill', 'none');
                    icon.classList.remove('like-heart-pop');
                }
            }
        });
    };

    const dispatchLikeSync = async (postId, url) => {
        const key = `like:${postId}`;
        const queue = interactionQueues.get(key);
        if (!queue || queue.inFlight) return;

        // If server is already in desired state, no request needed
        if (queue.desiredState === queue.lastConfirmedState) {
            return;
        }

        queue.inFlight = true;
        const targetState = queue.desiredState;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ desired_state: targetState }),
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
            queue.lastConfirmedState = data.liked;

            // Reconcile server count if user didn't change mind while request was in-flight
            if (queue.desiredState === data.liked) {
                queue.currentCount = data.likes_count;
                queue.rollbackState = data.liked;
                queue.rollbackCount = data.likes_count;
                applyLikeUI(postId, data.liked, data.likes_count, false);
            }
        } catch (error) {
            console.error('Like error:', error);
            // Rollback only if user intent still matches the failed request
            if (queue.desiredState === targetState) {
                queue.desiredState = queue.rollbackState;
                queue.currentCount = queue.rollbackCount;
                applyLikeUI(postId, queue.rollbackState, queue.rollbackCount, false);
                showToast(error.message || 'Không thể lưu lượt thích. Vui lòng thử lại.', 'error');
            }
        } finally {
            queue.inFlight = false;
            // If user clicked again while in-flight, immediately sync the newest desired state
            if (queue.desiredState !== queue.lastConfirmedState) {
                dispatchLikeSync(postId, url);
            }
        }
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="like"]');
        if (!btn) return;

        // If it's a guest link, let normal navigation to /login proceed
        if (btn.tagName === 'A' && btn.getAttribute('href')?.includes('/login')) {
            return;
        }

        const postId = btn.getAttribute('data-post-id');
        const url = btn.getAttribute('data-url');
        if (!postId || !url) return;

        e.preventDefault();

        const key = `like:${postId}`;
        let queue = interactionQueues.get(key);

        // Determine current optimistic state
        const currentState = queue
            ? queue.desiredState
            : (btn.getAttribute('aria-pressed') === 'true' || btn.classList.contains('text-danger'));

        const nextState = !currentState;

        // Calculate count
        let currentCount = 0;
        if (queue && typeof queue.currentCount === 'number') {
            currentCount = queue.currentCount;
        } else {
            const countSpan = btn.querySelector('.like-count');
            currentCount = countSpan ? (parseInt(countSpan.textContent.trim(), 10) || 0) : 0;
        }

        const nextCount = nextState ? currentCount + 1 : Math.max(0, currentCount - 1);

        // 1. Instant visual update (<10ms)
        applyLikeUI(postId, nextState, nextCount, true);

        // 2. Queue state update
        if (!queue) {
            queue = {
                desiredState: nextState,
                currentCount: nextCount,
                inFlight: false,
                lastConfirmedState: currentState,
                rollbackState: currentState,
                rollbackCount: currentCount,
            };
            interactionQueues.set(key, queue);
        } else {
            queue.desiredState = nextState;
            queue.currentCount = nextCount;
        }

        // 3. Dispatch background sync
        dispatchLikeSync(postId, url);
    });

    // -------------------------------------------------------------------------
    // 2. OPTIMISTIC FAVORITE / SAVE ACTION
    // -------------------------------------------------------------------------
    const applyFavoriteUI = (postId, saved, shouldAnimate = true) => {
        const allBtns = document.querySelectorAll(`[data-action="favorite"][data-post-id="${postId}"]`);
        allBtns.forEach((btn) => {
            btn.setAttribute('aria-pressed', saved ? 'true' : 'false');
            btn.setAttribute('aria-label', saved ? 'Xóa khỏi danh sách lưu' : 'Lưu bài viết');
            btn.setAttribute('title', saved ? 'Đã lưu' : 'Lưu bài viết');

            const label = btn.querySelector('.favorite-label');
            if (label) {
                label.textContent = saved ? 'Đã lưu' : 'Lưu';
            }

            const icon = btn.querySelector('.favorite-icon') || btn.querySelector('svg');
            if (saved) {
                btn.classList.add('text-warning');
                btn.classList.remove('text-secondary', 'text-theme-secondary');
                if (icon) {
                    icon.setAttribute('fill', 'currentColor');
                    if (shouldAnimate) {
                        icon.classList.remove('bookmark-pop');
                        void icon.offsetWidth;
                        icon.classList.add('bookmark-pop');
                    }
                }
            } else {
                btn.classList.remove('text-warning');
                btn.classList.add('text-theme-secondary');
                if (icon) {
                    icon.setAttribute('fill', 'none');
                    icon.classList.remove('bookmark-pop');
                }
            }

            // On /favorites page, visually dim the card without removing it or causing layout shift
            const isFavoritesPage = window.location.pathname.includes('/favorites');
            if (isFavoritesPage) {
                const card = btn.closest('.post-card') || btn.closest('article');
                if (card) {
                    if (!saved) {
                        card.setAttribute('data-unsaved', 'true');
                    } else {
                        card.removeAttribute('data-unsaved');
                    }
                }
            }
        });
    };

    const dispatchFavoriteSync = async (postId, url) => {
        const key = `favorite:${postId}`;
        const queue = interactionQueues.get(key);
        if (!queue || queue.inFlight) return;

        if (queue.desiredState === queue.lastConfirmedState) {
            return;
        }

        queue.inFlight = true;
        const targetState = queue.desiredState;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ desired_state: targetState }),
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
            queue.lastConfirmedState = data.saved;

            if (queue.desiredState === data.saved) {
                queue.rollbackState = data.saved;
                applyFavoriteUI(postId, data.saved, false);
            }
        } catch (error) {
            console.error('Favorite error:', error);
            if (queue.desiredState === targetState) {
                queue.desiredState = queue.rollbackState;
                applyFavoriteUI(postId, queue.rollbackState, false);
                showToast(error.message || 'Không thể lưu bài viết. Vui lòng thử lại.', 'error');
            }
        } finally {
            queue.inFlight = false;
            if (queue.desiredState !== queue.lastConfirmedState) {
                dispatchFavoriteSync(postId, url);
            }
        }
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="favorite"]');
        if (!btn) return;

        if (btn.tagName === 'A' && btn.getAttribute('href')?.includes('/login')) {
            return;
        }

        const postId = btn.getAttribute('data-post-id');
        const url = btn.getAttribute('data-url');
        if (!postId || !url) return;

        e.preventDefault();

        const key = `favorite:${postId}`;
        let queue = interactionQueues.get(key);

        const currentState = queue
            ? queue.desiredState
            : (btn.getAttribute('aria-pressed') === 'true' || btn.classList.contains('text-warning'));

        const nextState = !currentState;

        // 1. Instant visual update
        applyFavoriteUI(postId, nextState, true);

        // 2. Queue state update
        if (!queue) {
            queue = {
                desiredState: nextState,
                inFlight: false,
                lastConfirmedState: currentState,
                rollbackState: currentState,
            };
            interactionQueues.set(key, queue);
        } else {
            queue.desiredState = nextState;
        }

        // 3. Dispatch background sync
        dispatchFavoriteSync(postId, url);
    });

    // -------------------------------------------------------------------------
    // 3. OPTIMISTIC FOLLOW ACTION
    // -------------------------------------------------------------------------
    const applyFollowUI = (authorId, following) => {
        const allBtns = document.querySelectorAll(`[data-action="follow"][data-author-id="${authorId}"]`);
        allBtns.forEach((btn) => {
            btn.setAttribute('aria-pressed', following ? 'true' : 'false');
            btn.setAttribute('aria-label', following ? 'Hủy theo dõi tác giả' : 'Theo dõi tác giả');
            btn.textContent = following ? 'Đang theo dõi' : 'Theo dõi';

            if (following) {
                btn.classList.remove('btn-primary', 'btn-editorial-primary');
                btn.classList.add('btn-outline-theme');
            } else {
                btn.classList.remove('btn-outline-theme');
                btn.classList.add('btn-primary', 'btn-editorial-primary');
            }
        });

        // Reconcile author profile follower count
        const followerCountEl = document.getElementById('follower-count');
        if (followerCountEl) {
            const current = parseInt(followerCountEl.textContent.trim(), 10) || 0;
            followerCountEl.textContent = following ? current + 1 : Math.max(0, current - 1);
        }
    };

    const dispatchFollowSync = async (authorId, url) => {
        const key = `follow:${authorId}`;
        const queue = interactionQueues.get(key);
        if (!queue || queue.inFlight) return;

        if (queue.desiredState === queue.lastConfirmedState) {
            return;
        }

        queue.inFlight = true;
        const targetState = queue.desiredState;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ desired_state: targetState }),
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
            queue.lastConfirmedState = data.following;

            if (queue.desiredState === data.following) {
                queue.rollbackState = data.following;
                applyFollowUI(authorId, data.following);
            }
        } catch (error) {
            console.error('Follow error:', error);
            if (queue.desiredState === targetState) {
                queue.desiredState = queue.rollbackState;
                applyFollowUI(authorId, queue.rollbackState);
                showToast(error.message || 'Không thể cập nhật theo dõi. Vui lòng thử lại.', 'error');
            }
        } finally {
            queue.inFlight = false;
            if (queue.desiredState !== queue.lastConfirmedState) {
                dispatchFollowSync(authorId, url);
            }
        }
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="follow"]');
        if (!btn) return;

        if (btn.tagName === 'A' && btn.getAttribute('href')?.includes('/login')) {
            return;
        }

        const authorId = btn.getAttribute('data-author-id');
        const url = btn.getAttribute('data-url');
        if (!authorId || !url) return;

        e.preventDefault();

        const key = `follow:${authorId}`;
        let queue = interactionQueues.get(key);

        const currentState = queue
            ? queue.desiredState
            : (btn.getAttribute('aria-pressed') === 'true' || btn.classList.contains('btn-outline-theme'));

        const nextState = !currentState;

        // 1. Instant visual update
        applyFollowUI(authorId, nextState);

        // 2. Queue state update
        if (!queue) {
            queue = {
                desiredState: nextState,
                inFlight: false,
                lastConfirmedState: currentState,
                rollbackState: currentState,
            };
            interactionQueues.set(key, queue);
        } else {
            queue.desiredState = nextState;
        }

        // 3. Dispatch background sync
        dispatchFollowSync(authorId, url);
    });

    // -------------------------------------------------------------------------
    // 4. INLINE REPLY TOGGLE
    // -------------------------------------------------------------------------
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

    // -------------------------------------------------------------------------
    // 5. OPTIMISTIC COMMENT & REPLY SUBMISSION
    // -------------------------------------------------------------------------
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('[data-form="comment-form"]');
        if (!form) return;
        e.preventDefault();

        const textarea = form.querySelector('textarea[name="body"]');
        const errorContainer = form.querySelector('.comment-error');
        const submitBtn = form.querySelector('button[type="submit"]');
        const bodyText = textarea ? textarea.value.trim() : '';

        if (!bodyText) {
            if (textarea) textarea.focus();
            return;
        }

        if (errorContainer) errorContainer.textContent = '';

        const formData = new FormData(form);
        const parentId = formData.get('parent_id');
        const userName = form.getAttribute('data-user-name') || 'Bạn';
        const initial = userName.charAt(0).toUpperCase();

        // 1. Create Optimistic Preview Element
        const tempId = `temp-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
        const commentsContainer = document.getElementById('comments-list');
        let tempElement = null;

        if (parentId) {
            const parentCommentEl = document.getElementById(`comment-${parentId}`);
            let repliesContainer = parentCommentEl ? parentCommentEl.querySelector('.replies-container') : null;
            if (!repliesContainer && parentCommentEl) {
                repliesContainer = document.createElement('div');
                repliesContainer.className = 'replies-container mt-3 ps-3 ps-sm-4 border-theme-left d-flex flex-column gap-2';
                parentCommentEl.appendChild(repliesContainer);
            }
            if (repliesContainer) {
                tempElement = document.createElement('div');
                tempElement.id = `comment-${tempId}`;
                tempElement.className = 'reply-item p-3 comment-optimistic';
                tempElement.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle brand-gradient fw-bold d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px; font-size: 11px;" data-theme-preserve>
                                ${initial}
                            </div>
                            <span class="fw-bold small text-theme" style="font-size: 12.5px;">${escapeHtml(userName)}</span>
                            <span class="text-theme-muted small" style="font-size: 11px;">&bull; Vừa xong</span>
                            <span class="comment-pending-indicator text-theme-muted ms-1"><span class="comment-pending-dot"></span>Đang gửi...</span>
                        </div>
                    </div>
                    <p class="small text-theme mb-0 ps-4 lh-base">${escapeHtml(bodyText)}</p>
                `;
                repliesContainer.appendChild(tempElement);
                tempElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } else if (commentsContainer) {
            const emptyPlaceholder = commentsContainer.querySelector('.empty-state, .text-center.py-4');
            if (emptyPlaceholder) {
                emptyPlaceholder.remove();
            }

            tempElement = document.createElement('div');
            tempElement.id = `comment-${tempId}`;
            tempElement.className = 'comment-item p-3 p-sm-4 comment-optimistic mb-3';
            tempElement.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle brand-gradient fw-bold d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 13px;" data-theme-preserve>
                            ${initial}
                        </div>
                        <div>
                            <span class="fw-bold small text-theme">${escapeHtml(userName)}</span>
                            <span class="small text-theme-muted ms-2" style="font-size: 11px;">Vừa xong</span>
                            <span class="comment-pending-indicator text-theme-muted ms-2"><span class="comment-pending-dot"></span>Đang gửi...</span>
                        </div>
                    </div>
                </div>
                <p class="small text-theme mb-2 ps-4 ms-2 lh-base">${escapeHtml(bodyText)}</p>
            `;
            commentsContainer.prepend(tempElement);
            tempElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // 2. Increment comment badges optimistically
        const counterEls = document.querySelectorAll('.comment-count-badge');
        counterEls.forEach((el) => {
            const count = parseInt(el.textContent.trim(), 10) || 0;
            el.textContent = count + 1;
        });

        // 3. Clear textarea & close reply box immediately
        if (textarea) textarea.value = '';
        if (form.classList.contains('reply-form')) {
            form.classList.add('d-none');
        }

        // 4. Background Server Sync
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
                const data = await response.json().catch(() => ({}));
                const errorMsg = data.errors?.body?.[0] || data.errors?.parent_id?.[0] || 'Vui lòng kiểm tra lại nội dung bình luận.';
                throw new Error(errorMsg);
            }

            if (!response.ok) {
                throw new Error('Không thể gửi bình luận. Vui lòng thử lại.');
            }

            const data = await response.json();

            // 5. Confirm optimistic element with server ID
            if (tempElement && data.comment) {
                tempElement.id = `comment-${data.comment.id}`;
                tempElement.classList.remove('comment-optimistic');
                const pendingBadge = tempElement.querySelector('.comment-pending-indicator');
                if (pendingBadge) {
                    pendingBadge.remove();
                }

                // If root comment, attach inline reply action button
                if (!parentId) {
                    const actionContainer = document.createElement('div');
                    actionContainer.className = 'ps-4 ms-2';
                    actionContainer.innerHTML = `
                        <button type="button"
                                data-action="toggle-reply"
                                data-comment-id="${data.comment.id}"
                                class="btn btn-sm btn-link p-0 text-theme-secondary text-decoration-none small hover-accent">
                            Trả lời &crarr;
                        </button>
                    `;
                    tempElement.appendChild(actionContainer);
                }
            }
        } catch (error) {
            console.error('Comment error:', error);

            // Rollback optimistic element
            if (tempElement) {
                tempElement.remove();
            }

            // Decrement badge back
            counterEls.forEach((el) => {
                const count = parseInt(el.textContent.trim(), 10) || 1;
                el.textContent = Math.max(0, count - 1);
            });

            // Restore user's input so they never lose what was typed
            if (textarea) {
                textarea.value = bodyText;
                textarea.focus();
            }

            // Re-open reply box if it was a reply
            if (form.classList.contains('reply-form')) {
                form.classList.remove('d-none');
            }

            if (errorContainer) {
                errorContainer.textContent = error.message;
            } else {
                showToast(error.message, 'error');
            }
        }
    });

    // =========================================================================
    // 6. GLOBAL BOOTSTRAP CONFIRM MODAL ENGINE (Preserved for Admin/CMS actions)
    // =========================================================================
    let currentConfirmForm = null;
    let currentPromptName = null;

    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-confirm="true"]');
        if (!form) return;

        if (form.dataset.confirmed === 'true') {
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
        confirmModalEl.addEventListener('hide.bs.modal', (e) => {
            if (confirmSubmitBtn && confirmSubmitBtn.disabled) {
                e.preventDefault();
            }
        });

        confirmModalEl.addEventListener('shown.bs.modal', () => {
            const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
            if (promptBoxEl && !promptBoxEl.classList.contains('d-none') && promptInputEl) {
                promptInputEl.focus();
            } else if (confirmSubmitBtn && !confirmSubmitBtn.disabled) {
                confirmSubmitBtn.focus();
            }
        });

        confirmModalEl.addEventListener('hidden.bs.modal', () => {
            currentConfirmForm = null;
            currentPromptName = null;
            if (confirmSubmitBtn) confirmSubmitBtn.disabled = false;
            if (confirmCancelBtn) confirmCancelBtn.disabled = false;
            if (closeBtnEl) closeBtnEl.disabled = false;
            const spinnerEl = document.getElementById('confirm-modal-spinner');
            if (spinnerEl) spinnerEl.classList.add('d-none');
            const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
            if (promptBoxEl) promptBoxEl.classList.add('d-none');
            const targetBoxEl = document.getElementById('confirm-modal-target-box');
            if (targetBoxEl) targetBoxEl.classList.add('d-none');
            const descBoxEl = document.getElementById('confirm-modal-description-box');
            if (descBoxEl) descBoxEl.classList.add('d-none');
            if (promptInputEl) {
                promptInputEl.value = '';
                promptInputEl.placeholder = '';
                promptInputEl.classList.remove('is-invalid');
            }
            if (promptErrorEl) promptErrorEl.classList.add('d-none');
        });
    }
});
