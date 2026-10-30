import { InteractionState } from './interaction-state';

const initializeInteractions = () => {
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
    const showToast = (message, type = 'success') => {
        let container = document.getElementById('global-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'global-toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1090';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        const isError = type === 'error' || type === 'danger';
        toast.className = `toast border-0 mb-2 shadow-lg ${isError ? 'bg-danger text-white' : 'bg-theme-surface text-theme border border-theme'}`;
        toast.setAttribute('role', isError ? 'alert' : 'status');
        toast.setAttribute('aria-atomic', 'true');
        const row = document.createElement('div');
        row.className = 'd-flex align-items-center';
        const content = document.createElement('div');
        content.className = 'toast-body';
        content.textContent = String(message);
        const close = document.createElement('button');
        close.type = 'button';
        close.className = `btn-close ${isError ? 'btn-close-white ' : ''}me-2 m-auto`;
        close.dataset.bsDismiss = 'toast';
        close.setAttribute('aria-label', 'Đóng thông báo');
        row.append(content, close);
        toast.appendChild(row);
        container.appendChild(toast);
        if (window.bootstrap?.Toast) {
            const instance = window.bootstrap.Toast.getOrCreateInstance(toast, { delay: 5000 });
            instance.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove(), { once: true });
        } else {
            toast.classList.add('show');
            close.addEventListener('click', () => toast.remove(), { once: true });
            window.setTimeout(() => toast.remove(), 5000);
        }
    };
    window.showToast = showToast;
    document.querySelectorAll('#global-toast-container .toast').forEach((toast) => {
        window.bootstrap?.Toast?.getOrCreateInstance(toast).show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove(), { once: true });
    });

    const errorFromResponse = async (response) => {
        const data = await response.json().catch(() => ({}));
        const messages = {
            401: 'Vui lòng đăng nhập lại để lưu thao tác.',
            403: 'Bạn không có quyền thực hiện thao tác này.',
            404: 'Nội dung này không còn khả dụng.',
            419: 'Phiên đăng nhập đã hết hạn. Hãy tải lại trang rồi thử lại.',
            422: 'Vui lòng kiểm tra lại nội dung.',
            429: 'Bạn thao tác quá nhanh. Vui lòng đợi một lúc rồi thử lại.',
        };
        const validationMessage = Object.values(data.errors || {}).flat().find((message) => typeof message === 'string');
        const error = new Error(data.message || validationMessage || messages[response.status] || 'Không thể lưu thao tác. Vui lòng thử lại.');
        error.status = response.status;
        return error;
    };

    const requestJson = async (url, options = {}) => {
        const controller = new AbortController();
        const timer = window.setTimeout(() => controller.abort(), 20000);
        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                cache: 'no-store',
                ...options,
                signal: controller.signal,
                headers: { 'Accept': 'application/json', ...options.headers },
            });
            if (!response.ok) throw await errorFromResponse(response);
            return await response.json();
        } catch (error) {
            if (error.status) throw error;
            throw new Error('Không thể kết nối. Thao tác chưa được xác nhận; hãy kiểm tra mạng rồi thử lại.');
        } finally {
            window.clearTimeout(timer);
        }
    };

    const definitions = {
        like: { id: 'postId', property: 'liked', count: 'likes_count' },
        favorite: { id: 'postId', property: 'saved' },
        follow: { id: 'authorId', property: 'following', count: 'followers_count' },
    };
    const states = new Map();
    const buttonSelector = '[data-action="like"], [data-action="favorite"], [data-action="follow"]';
    const buttonsFor = (action, id) => {
        if (action === 'follow') {
            return document.querySelectorAll(
                `[data-action="follow"][data-author-id="${CSS.escape(id)}"], [data-action="quick-follow-open"][data-author-id="${CSS.escape(id)}"]`
            );
        }
        return document.querySelectorAll(
            `[data-action="${action}"][data-post-id="${CSS.escape(id)}"]`
        );
    };

    const popoverState = {
        element: null,
        activeAuthorId: null,
        activeTrigger: null,
    };

    const getOrCreatePopover = () => {
        if (!popoverState.element) {
            const popover = document.createElement('div');
            popover.id = 'quick-follow-popover';
            popover.className = 'quick-follow-popover';
            popover.setAttribute('role', 'dialog');
            popover.setAttribute('aria-modal', 'false');
            popover.setAttribute('aria-labelledby', 'quick-follow-popover-title');

            const title = document.createElement('h3');
            title.id = 'quick-follow-popover-title';
            title.className = 'quick-follow-popover-title';

            const desc = document.createElement('p');
            desc.id = 'quick-follow-popover-desc';
            desc.className = 'quick-follow-popover-desc';

            const actions = document.createElement('div');
            actions.className = 'quick-follow-popover-actions';

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'quick-follow-popover-btn-close';
            closeBtn.textContent = 'Đóng';

            const confirmBtn = document.createElement('button');
            confirmBtn.type = 'button';
            confirmBtn.className = 'quick-follow-popover-btn-confirm';

            actions.append(closeBtn, confirmBtn);

            const error = document.createElement('div');
            error.className = 'quick-follow-popover-error d-none';
            error.setAttribute('role', 'alert');

            popover.append(title, desc, actions, error);
            document.body.appendChild(popover);
            popoverState.element = popover;

            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                closePopover();
            });

            confirmBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                if (!popoverState.activeTrigger) return;
                if (popoverState.activeTrigger.dataset.guest === 'true') {
                    window.location.assign(popoverState.activeTrigger.dataset.loginUrl || '/login');
                    return;
                }
                const authorId = popoverState.activeAuthorId;
                const key = `follow:${authorId}`;
                let entry = states.get(key);
                if (!entry) {
                    entry = createInteraction(popoverState.activeTrigger);
                    states.set(key, entry);
                }
                if (entry) {
                    confirmBtn.disabled = true;
                    const isCurrentlyFollowing = entry.state.snapshot.state;
                    confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 12px; height: 12px; border-width: 2px;"></span>${isCurrentlyFollowing ? 'Đang bỏ theo dõi...' : 'Đang theo dõi...'}`;
                    hidePopoverError();
                    try {
                        await entry.state.toggle();
                    } catch (err) {
                        // handled by onError callback in createInteraction
                    }
                }
            });
        }
        return popoverState.element;
    };

    const showPopoverError = (message) => {
        if (!popoverState.element) return;
        const errorEl = popoverState.element.querySelector('.quick-follow-popover-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('d-none');
        }
    };

    const hidePopoverError = () => {
        if (!popoverState.element) return;
        const errorEl = popoverState.element.querySelector('.quick-follow-popover-error');
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('d-none');
        }
    };

    const positionPopover = (trigger) => {
        if (!popoverState.element || !trigger) return;
        const rect = trigger.getBoundingClientRect();
        const popoverWidth = popoverState.element.offsetWidth || 260;
        const popoverHeight = popoverState.element.offsetHeight || 120;

        let top = rect.bottom + 8;
        let left = rect.left;

        if (left + popoverWidth > window.innerWidth - 12) {
            left = window.innerWidth - popoverWidth - 12;
        }
        if (left < 12) {
            left = 12;
        }

        if (top + popoverHeight > window.innerHeight - 12) {
            top = rect.top - popoverHeight - 8;
        }
        if (top < 12) {
            top = 12;
        }

        popoverState.element.style.top = `${Math.round(top)}px`;
        popoverState.element.style.left = `${Math.round(left)}px`;
    };

    const updateQuickFollowPopover = (authorId, snapshot) => {
        if (!popoverState.element || !popoverState.element.classList.contains('is-open')) return;
        if (popoverState.activeAuthorId !== authorId) return;

        const popover = popoverState.element;
        const title = popover.querySelector('.quick-follow-popover-title');
        const desc = popover.querySelector('.quick-follow-popover-desc');
        const confirmBtn = popover.querySelector('.quick-follow-popover-btn-confirm');
        const authorName = popoverState.activeTrigger?.dataset.authorName || 'tác giả';

        if (snapshot.state) {
            title.textContent = `Đang theo dõi ${authorName}`;
            desc.textContent = '';
            desc.classList.add('d-none');
            confirmBtn.className = 'quick-follow-popover-btn-confirm is-unfollow';
            confirmBtn.textContent = 'Bỏ theo dõi';
            confirmBtn.disabled = snapshot.pending;
        } else {
            title.textContent = `Theo dõi ${authorName}?`;
            desc.textContent = 'Nhận bài viết mới từ tác giả này.';
            desc.classList.remove('d-none');
            confirmBtn.className = 'quick-follow-popover-btn-confirm is-follow';
            confirmBtn.textContent = 'Theo dõi';
            confirmBtn.disabled = snapshot.pending;
        }

        if (snapshot.pending) {
            confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 12px; height: 12px; border-width: 2px;"></span>${snapshot.state ? 'Bỏ theo dõi' : 'Theo dõi'}`;
        }
        if (snapshot.uncertain) {
            showPopoverError('Không thể đồng bộ trạng thái. Vui lòng thử lại.');
        } else {
            hidePopoverError();
        }
        positionPopover(popoverState.activeTrigger);
    };

    const openQuickFollowPopover = (trigger) => {
        const authorId = trigger.dataset.authorId;
        const authorName = trigger.dataset.authorName || 'tác giả';
        const isGuest = trigger.dataset.guest === 'true';

        if (popoverState.element?.classList.contains('is-open') && popoverState.activeTrigger === trigger) {
            closePopover();
            return;
        }

        closePopover();

        popoverState.activeAuthorId = authorId;
        popoverState.activeTrigger = trigger;
        trigger.setAttribute('aria-expanded', 'true');

        const popover = getOrCreatePopover();
        const title = popover.querySelector('.quick-follow-popover-title');
        const desc = popover.querySelector('.quick-follow-popover-desc');
        const confirmBtn = popover.querySelector('.quick-follow-popover-btn-confirm');

        hidePopoverError();

        if (isGuest) {
            title.textContent = `Theo dõi ${authorName}`;
            desc.textContent = 'Đăng nhập để theo dõi tác giả.';
            desc.classList.remove('d-none');
            confirmBtn.className = 'quick-follow-popover-btn-confirm is-follow';
            confirmBtn.textContent = 'Đăng nhập';
            confirmBtn.disabled = false;
        } else {
            const key = `follow:${authorId}`;
            const isCurrentlyFollowing = states.has(key)
                ? states.get(key).state.snapshot.state
                : (trigger.dataset.following === 'true' || trigger.getAttribute('aria-pressed') === 'true');

            if (isCurrentlyFollowing) {
                title.textContent = `Đang theo dõi ${authorName}`;
                desc.textContent = '';
                desc.classList.add('d-none');
                confirmBtn.className = 'quick-follow-popover-btn-confirm is-unfollow';
                confirmBtn.textContent = 'Bỏ theo dõi';
                confirmBtn.disabled = false;
            } else {
                title.textContent = `Theo dõi ${authorName}?`;
                desc.textContent = 'Nhận bài viết mới từ tác giả này.';
                desc.classList.remove('d-none');
                confirmBtn.className = 'quick-follow-popover-btn-confirm is-follow';
                confirmBtn.textContent = 'Theo dõi';
                confirmBtn.disabled = false;
            }
        }

        popover.classList.add('is-open');
        positionPopover(trigger);
    };

    const closePopover = () => {
        if (!popoverState.element || !popoverState.element.classList.contains('is-open')) return;
        popoverState.element.classList.remove('is-open');
        if (popoverState.activeTrigger) {
            popoverState.activeTrigger.setAttribute('aria-expanded', 'false');
        }
        popoverState.activeAuthorId = null;
        popoverState.activeTrigger = null;
        hidePopoverError();
    };

    const renderInteraction = (action, id, snapshot) => {
        buttonsFor(action, id).forEach((button) => {
            button.setAttribute('aria-pressed', String(snapshot.state));
            button.setAttribute('aria-busy', String(snapshot.pending));
            button.dataset.syncUncertain = String(snapshot.uncertain);
            const icon = button.querySelector('svg');
            if (action === 'like' || action === 'favorite') {
                const activeClass = action === 'like' ? 'text-danger' : 'text-warning';
                button.classList.toggle(activeClass, snapshot.state);
                button.classList.toggle('text-theme-secondary', !snapshot.state);
                if (icon) icon.setAttribute('fill', snapshot.state ? 'currentColor' : 'none');
                const label = action === 'like'
                    ? snapshot.state ? 'Bỏ thích bài viết' : 'Thích bài viết'
                    : snapshot.state ? 'Bỏ lưu bài viết' : 'Lưu bài viết';
                button.setAttribute('aria-label', action === 'like' ? `${label}, ${snapshot.count || 0} lượt thích` : label);
                button.title = label;
                if (action === 'like') {
                    const count = button.querySelector('.like-count');
                    if (count) count.textContent = snapshot.count > 0 ? String(snapshot.count) : '';
                } else {
                    const label = button.querySelector('.favorite-label');
                    if (label) label.textContent = snapshot.state ? 'Đã lưu' : 'Lưu';
                    const card = button.closest('[data-favorite-entry]');
                    if (card) {
                        card.dataset.saved = String(snapshot.state);
                        const note = card.querySelector('[data-favorite-entry-status]');
                        if (note) note.textContent = snapshot.state ? 'Đã lưu' : 'Đã bỏ lưu · Nhấn dấu trang để lưu lại';
                    }
                }
            } else {
                if (button.classList.contains('threads-avatar-follow-btn')) {
                    button.classList.remove('d-none');
                    button.style.display = 'flex';
                    button.dataset.following = String(snapshot.state);
                    const authorName = button.dataset.authorName || '';
                    const label = snapshot.state ? `Đang theo dõi ${authorName}`.trim() : `Theo dõi ${authorName}`.trim();
                    button.setAttribute('aria-label', label);
                    button.setAttribute('title', label);

                    const plusIcon = button.querySelector('.follow-icon-plus');
                    const checkIcon = button.querySelector('.follow-icon-check');
                    if (plusIcon) plusIcon.classList.toggle('d-none', Boolean(snapshot.state));
                    if (checkIcon) checkIcon.classList.toggle('d-none', !snapshot.state);
                } else {
                    button.textContent = snapshot.state ? 'Đang theo dõi' : 'Theo dõi';
                    button.classList.toggle('btn-outline-theme', snapshot.state);
                    button.classList.toggle('btn-primary', !snapshot.state);
                    button.setAttribute('aria-label', `${snapshot.state ? 'Bỏ theo dõi' : 'Theo dõi'} ${button.dataset.authorName || ''}`.trim());
                }
            }
        });
        if (action === 'follow') {
            updateQuickFollowPopover(id, snapshot);
            if (snapshot.count !== null) {
                document.querySelectorAll(`[data-follower-count][data-author-id="${CSS.escape(id)}"]`).forEach((label) => {
                    label.textContent = String(snapshot.count);
                });
            }
        }
        if (action === 'favorite') {
            const total = document.querySelector('[data-favorites-total]');
            if (total) {
                const removed = document.querySelectorAll('[data-favorite-entry][data-saved="false"]').length;
                total.querySelector('[data-favorites-count]').textContent = String(Math.max(0, Number(total.dataset.favoritesTotal) - removed));
            }
        }
    };

    const createInteraction = (button) => {
        const action = button.dataset.action === 'quick-follow-open' ? 'follow' : button.dataset.action;
        const definition = definitions[action];
        const id = button.dataset[definition.id];
        const url = button.dataset.url;
        const initialCount = action === 'like' ? Number(button.dataset.count)
            : action === 'follow' ? Number(document.querySelector(`[data-follower-count][data-author-id="${CSS.escape(id)}"]`)?.textContent) : null;
        const mapResult = (data) => ({
            state: data[definition.property],
            count: definition.count && data[definition.count] !== undefined ? Number(data[definition.count]) : null,
        });
        const state = new InteractionState({
            state: button.getAttribute('aria-pressed') === 'true' || button.dataset.following === 'true',
            count: initialCount,
            mutate: async (desired) => mapResult(await requestJson(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Content-Type': 'application/json' },
                body: JSON.stringify({ [definition.property]: desired }),
            })),
            reconcile: async () => mapResult(await requestJson(url)),
            onChange: (snapshot) => renderInteraction(action, id, snapshot),
            onError: (error, { reconciled }) => {
                if (popoverState.activeAuthorId === id && popoverState.element?.classList.contains('is-open')) {
                    showPopoverError(error.message);
                } else {
                    showToast(`${error.message}${reconciled ? '' : ' Hãy tải lại trang để kiểm tra trạng thái mới nhất.'}`, 'error');
                }
                if (error.status === 401) window.location.assign(button.dataset.loginUrl || '/login');
            },
        });
        return { state, action, id, url };
    };

    let feedNavigation = null;
    const hydrateInteractions = (root = document, navigation = null) => {
        const seen = new Set();
        const targets = root.querySelectorAll(`${buttonSelector}, [data-action="quick-follow-open"]:not([data-guest="true"])`);
        targets.forEach((button) => {
            const action = button.dataset.action === 'quick-follow-open' ? 'follow' : button.dataset.action;
            const id = button.dataset[definitions[action].id];
            if (!id || !button.dataset.url) return;
            const key = `${action}:${id}`;
            if (seen.has(key)) return;
            seen.add(key);
            const existing = states.get(key);
            if (existing) {
                const count = action === 'like' ? Number(button.dataset.count)
                    : action === 'follow' ? Number(document.querySelector(`[data-follower-count][data-author-id="${CSS.escape(id)}"]`)?.textContent) : null;
                const loadedState = navigation?.revisions.get(key);
                const expectedRevision = navigation
                    ? loadedState?.pending ? -1 : loadedState?.revision ?? 0
                    : existing.state.revision;
                existing.state.hydrate(button.getAttribute('aria-pressed') === 'true' || button.dataset.following === 'true', count, expectedRevision);
            } else {
                states.set(key, createInteraction(button));
            }
        });
    };
    hydrateInteractions();
    document.addEventListener('blogmnm:feed-loading', (event) => {
        closePopover();
        feedNavigation = {
            id: event.detail?.navigationId,
            revisions: new Map(Array.from(states, ([key, entry]) => [key, {
                revision: entry.state.revision, pending: entry.state.pending,
            }])),
        };
    });
    document.addEventListener('blogmnm:feed-updated', (event) => {
        const navigation = event.detail?.navigationId === feedNavigation?.id ? feedNavigation : null;
        hydrateInteractions(event.detail?.root || document, navigation);
    });
    document.addEventListener('click', (event) => {
        const quickFollowBtn = event.target.closest('[data-action="quick-follow-open"]');
        if (quickFollowBtn) {
            event.preventDefault();
            event.stopPropagation();
            openQuickFollowPopover(quickFollowBtn);
            return;
        }

        if (popoverState.element && popoverState.element.classList.contains('is-open')) {
            if (!popoverState.element.contains(event.target)) {
                closePopover();
            }
        }

        const button = event.target.closest(buttonSelector);
        if (!button || !button.dataset.url) return;
        event.preventDefault();
        const action = button.dataset.action;
        const id = button.dataset[definitions[action].id];
        const key = `${action}:${id}`;
        if (!states.has(key)) states.set(key, createInteraction(button));
        states.get(key).state.toggle();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && popoverState.element?.classList.contains('is-open')) {
            const trigger = popoverState.activeTrigger;
            closePopover();
            trigger?.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (popoverState.activeTrigger) {
            positionPopover(popoverState.activeTrigger);
        }
    }, { passive: true });

    window.addEventListener('scroll', () => {
        if (popoverState.activeTrigger) {
            positionPopover(popoverState.activeTrigger);
        }
    }, { passive: true });

    window.addEventListener('popstate', () => closePopover());

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action="toggle-reply"]');
        if (!button) return;
        event.preventDefault();
        const form = document.getElementById(`reply-box-${button.dataset.commentId}`);
        if (!form) return;
        const expanded = form.classList.contains('d-none');
        form.classList.toggle('d-none', !expanded);
        button.setAttribute('aria-expanded', String(expanded));
        if (expanded) form.querySelector('textarea')?.focus();
    });

    const createRequestId = () => {
        if (window.crypto.randomUUID) return window.crypto.randomUUID();
        const bytes = window.crypto.getRandomValues(new Uint8Array(16));
        bytes[6] = (bytes[6] & 15) | 64;
        bytes[8] = (bytes[8] & 63) | 128;
        const hex = Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');
        return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
    };
    const submittingComments = new WeakSet();
    const pendingComments = new Set();
    const knownCommentIds = new Set(Array.from(document.querySelectorAll('#comments-list [id^="comment-"]'), (node) => node.id));
    let confirmedCommentsCount = Number(document.querySelector('.comment-count-badge')?.textContent) || 0;
    let latestServerCommentCount = confirmedCommentsCount;
    const updateCommentCounts = () => {
        const postId = document.getElementById('comments')?.dataset.postId;
        document.querySelectorAll(`[data-comment-count][data-post-id="${CSS.escape(postId || '')}"]`).forEach((label) => {
            const count = confirmedCommentsCount + pendingComments.size;
            label.textContent = label.classList.contains('comment-action-count') && count === 0 ? '' : String(count);
        });
    };
    const element = (tag, className, text) => {
        const node = document.createElement(tag);
        node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const addCommentActions = (card, comment) => {
        const template = document.getElementById('comment-delete-template');
        if (template) {
            const form = template.content.firstElementChild.cloneNode(true);
            form.action = form.action.replace('__COMMENT__', encodeURIComponent(comment.id));
            form.dataset.confirmTargetName = comment.user_name;
            form.dataset.confirmTargetMeta = comment.body.slice(0, 60);
            card.querySelector('[data-comment-heading]').appendChild(form);
        }
        if (comment.parent_id) return;
        const replyTemplate = document.getElementById('comment-reply-template');
        if (!replyTemplate) return;
        const form = replyTemplate.content.firstElementChild.cloneNode(true);
        form.id = `reply-box-${comment.id}`;
        form.querySelector('[name="parent_id"]').value = comment.id;
        const textarea = form.querySelector('textarea');
        textarea.id = `reply-body-${comment.id}`;
        const error = form.querySelector('.comment-error');
        error.id = `reply-error-${comment.id}`;
        textarea.setAttribute('aria-describedby', error.id);
        const button = element('button', 'btn btn-sm btn-link p-0 text-theme-secondary text-decoration-none small hover-accent', 'Trả lời ↵');
        button.type = 'button';
        button.dataset.action = 'toggle-reply';
        button.dataset.commentId = comment.id;
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-controls', form.id);
        card.append(button, form, element('div', 'replies-container'));
    };

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-form="comment-form"]');
        if (!form) return;
        event.preventDefault();
        if (submittingComments.has(form)) return;
        const textarea = form.querySelector('textarea[name="body"]');
        const error = form.querySelector('.comment-error');
        const submit = form.querySelector('[type="submit"]');
        const body = textarea.value.trim();
        if (error) error.textContent = '';
        textarea.removeAttribute('aria-invalid');
        if (body.length < 2 || body.length > 1000) {
            if (error) error.textContent = 'Bình luận phải có từ 2 đến 1000 ký tự.';
            textarea.setAttribute('aria-invalid', 'true');
            textarea.focus();
            return;
        }
        const parentId = form.querySelector('[name="parent_id"]')?.value || '';
        const parent = parentId ? document.getElementById(`comment-${parentId}`)?.closest('.comment-item') : null;
        const list = document.getElementById('comments-list');
        const destination = parent ? parent.querySelector('.replies-container') : list;
        if (!destination) return;

        let key = form.dataset.requestId;
        if (!key || form.dataset.requestBody !== body || form.dataset.requestParent !== parentId) {
            key = createRequestId();
            form.dataset.requestId = key;
            form.dataset.requestBody = body;
            form.dataset.requestParent = parentId;
        }
        const pending = element('div', parent ? 'reply-item p-3' : 'comment-item p-3 p-sm-4');
        pending.id = `pending-comment-${key}`;
        pending.setAttribute('aria-busy', 'true');
        const heading = element('div', 'd-flex align-items-center justify-content-between gap-2 mb-2');
        heading.dataset.commentHeading = '';
        const author = element('div', 'd-flex align-items-center gap-2');
        const avatar = document.getElementById('comment-avatar-template');
        if (avatar) author.appendChild(avatar.content.cloneNode(true));
        const identity = element('div', '');
        const name = element('span', 'fw-semibold small text-theme', document.getElementById('comments').dataset.userName || 'Bạn');
        const status = element('span', 'small text-theme-muted ms-2', 'Đang gửi…');
        status.setAttribute('role', 'status');
        identity.append(name, status);
        author.appendChild(identity);
        heading.appendChild(author);
        const content = element('p', 'small text-theme comment-body mb-2 lh-base', body);
        pending.append(heading, content);
        parent ? destination.appendChild(pending) : destination.prepend(pending);
        const placeholder = list.querySelector('[data-comments-empty]');
        if (placeholder) placeholder.hidden = true;
        if (parent) destination.classList.add('mt-3', 'ps-3', 'ps-sm-4', 'border-theme-left', 'd-flex', 'flex-column', 'gap-2');
        pendingComments.add(key);
        updateCommentCounts();
        submittingComments.add(form);
        if (submit) submit.disabled = true;

        const data = new FormData(form);
        data.set('body', body);
        data.set('client_request_id', key);
        try {
            const result = await requestJson(form.action, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken() }, body: data,
            });
            const comment = result.comment;
            if (!comment?.id) throw new Error('Máy chủ chưa xác nhận bình luận. Vui lòng thử lại.');
            const id = `comment-${comment.id}`;
            if (knownCommentIds.has(id)) pending.remove();
            else {
                knownCommentIds.add(id);
                confirmedCommentsCount++;
                pending.id = id;
                pending.removeAttribute('aria-busy');
                name.textContent = comment.user_name;
                status.textContent = comment.created_at;
                status.removeAttribute('role');
                content.textContent = comment.body;
                addCommentActions(pending, comment);
            }
            pendingComments.delete(key);
            if (Number.isFinite(result.comments_count)) latestServerCommentCount = Math.max(latestServerCommentCount, result.comments_count);
            if (pendingComments.size === 0) confirmedCommentsCount = Math.max(confirmedCommentsCount, latestServerCommentCount);
            if (textarea.value.trim() === body) {
                textarea.value = '';
                if (form.classList.contains('reply-form')) {
                    form.classList.add('d-none');
                    document.querySelector(`[aria-controls="${CSS.escape(form.id)}"]`)?.setAttribute('aria-expanded', 'false');
                }
            }
            delete form.dataset.requestId;
            delete form.dataset.requestBody;
            delete form.dataset.requestParent;
            if (placeholder) placeholder.remove();
        } catch (failure) {
            pending.remove();
            pendingComments.delete(key);
            if (placeholder && !list.querySelector('.comment-item')) placeholder.hidden = false;
            if (error) error.textContent = failure.message;
            if (failure.status === 422) textarea.setAttribute('aria-invalid', 'true');
        } finally {
            updateCommentCounts();
            submittingComments.delete(form);
            if (submit) submit.disabled = false;
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
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeInteractions, { once: true });
} else {
    initializeInteractions();
}
