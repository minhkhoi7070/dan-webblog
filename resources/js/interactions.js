/**
 * BlogMNM Interactions: Like, Favorite, Follow, and Comments AJAX Engine
 * Modern Dark Social Design Tokens
 */
document.addEventListener('DOMContentLoaded', () => {
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const showToast = (message, type = 'info') => {
        const existing = document.getElementById('toast-notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'toast-notification';
        toast.className = `fixed bottom-6 right-6 z-50 px-4 py-3 rounded-2xl shadow-xl text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 flex items-center gap-2.5 border backdrop-blur-md ${
            type === 'error'
                ? 'bg-rose-950/90 text-rose-200 border-rose-800/60 shadow-rose-950/30'
                : 'bg-zinc-900/95 text-zinc-100 border-zinc-800 shadow-black/40'
        }`;

        const iconSvg = type === 'error'
            ? `<svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
            : `<svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;

        toast.innerHTML = `${iconSvg}<span>${message}</span>`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    };

    // 1. LIKE ACTION
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-action="like"]');
        if (!btn) return;
        e.preventDefault();

        const url = btn.getAttribute('data-url');
        if (!url) return;

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');

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
                    el.classList.add('text-rose-500');
                    el.classList.remove('text-zinc-400', 'hover:text-rose-400');
                    if (icon) {
                        icon.classList.add('fill-current');
                        icon.classList.remove('fill-none');
                    }
                } else {
                    el.classList.remove('text-rose-500');
                    el.classList.add('text-zinc-400', 'hover:text-rose-400');
                    if (icon) {
                        icon.classList.remove('fill-current');
                        icon.classList.add('fill-none');
                    }
                }
            });

            showToast(data.liked ? 'Đã thích bài viết!' : 'Đã bỏ thích bài viết.');
        } catch (error) {
            console.error('Like error:', error);
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
        btn.classList.add('opacity-50', 'cursor-not-allowed');

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
                    el.classList.add('text-amber-400');
                    el.classList.remove('text-zinc-400', 'hover:text-amber-400');
                    if (icon) {
                        icon.classList.add('fill-current');
                        icon.classList.remove('fill-none');
                    }
                    if (label) label.textContent = 'Đã lưu';
                } else {
                    el.classList.remove('text-amber-400');
                    el.classList.add('text-zinc-400', 'hover:text-amber-400');
                    if (icon) {
                        icon.classList.remove('fill-current');
                        icon.classList.add('fill-none');
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
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
        btn.classList.add('opacity-50', 'cursor-not-allowed');

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
                    el.classList.remove('bg-[var(--color-text)]', 'text-[var(--color-bg)]', 'bg-white', 'text-zinc-950', 'hover:bg-zinc-200');
                    el.classList.add('border', 'border-[var(--color-border)]', 'bg-transparent', 'text-[var(--color-text-secondary)]');
                } else {
                    el.textContent = 'Theo dõi';
                    el.classList.remove('border', 'border-[var(--color-border)]', 'bg-transparent', 'text-[var(--color-text-secondary)]', 'border-zinc-700', 'text-zinc-300');
                    el.classList.add('bg-[var(--color-text)]', 'text-[var(--color-bg)]');
                }
            });

            // If follower count element exists on author profile
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
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
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
            replyBox.classList.toggle('hidden');
            const textarea = replyBox.querySelector('textarea');
            if (!replyBox.classList.contains('hidden') && textarea) {
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

            // Clear textarea
            if (textarea) textarea.value = '';

            // If it was a reply form, hide it
            if (form.classList.contains('reply-form')) {
                form.classList.add('hidden');
            }

            // Append new comment HTML smoothly into DOM
            const parentId = formData.get('parent_id');
            const commentsContainer = document.getElementById('comments-list');
            const initial = (data.comment.user_name || 'U').charAt(0).toUpperCase();

            if (parentId) {
                // Nested reply
                const parentCommentEl = document.getElementById(`comment-${parentId}`);
                let repliesContainer = parentCommentEl ? parentCommentEl.querySelector('.replies-container') : null;
                if (!repliesContainer && parentCommentEl) {
                    repliesContainer = document.createElement('div');
                    repliesContainer.className = 'replies-container mt-4 pl-6 sm:pl-8 space-y-3 border-l border-[var(--color-border)]';
                    parentCommentEl.appendChild(repliesContainer);
                }
                if (repliesContainer) {
                    const replyCard = document.createElement('div');
                    replyCard.className = 'p-3.5 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)] text-xs sm:text-sm';
                    replyCard.innerHTML = `
                        <div class="flex items-center gap-2 mb-1.5">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0" data-theme-preserve>
                                ${initial}
                            </div>
                            <span class="font-semibold text-[var(--color-text)] text-xs">${data.comment.user_name}</span>
                            <span class="text-[11px] text-[var(--color-text-secondary)]">&bull; Vừa xong</span>
                        </div>
                        <p class="text-xs sm:text-sm text-[var(--color-text)] pl-8 leading-relaxed">${data.comment.body}</p>
                    `;
                    repliesContainer.appendChild(replyCard);
                }
            } else if (commentsContainer) {
                // Top-level comment
                const newCommentEl = document.createElement('div');
                newCommentEl.className = 'p-4 sm:p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] transition-all duration-300';
                newCommentEl.id = `comment-${data.comment.id}`;
                newCommentEl.innerHTML = `
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold flex items-center justify-center text-xs shrink-0" data-theme-preserve>
                                ${initial}
                            </div>
                            <div>
                                <span class="font-semibold text-sm text-[var(--color-text)]">${data.comment.user_name}</span>
                                <span class="text-xs text-[var(--color-text-secondary)] ml-2">Vừa xong</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-[var(--color-text)] leading-relaxed pl-10">${data.comment.body}</p>
                `;
                commentsContainer.prepend(newCommentEl);
            }

            // Update comment counter
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
});
