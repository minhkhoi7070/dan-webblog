/**
 * BlogMNM YouTube-Like & Threads-Like Persistent App Shell & Seamless Navigation Router
 *
 * Provides instantaneous transitions across sidebar/header navigation without full page reloads.
 * Preserves Sidebar, Mobile Nav, Theme, Audio/Video, Global Toast/Modals, and App State.
 * Swaps only `<main data-app-content>` via DOMParser, handles Back/Forward popstate,
 * scroll restoration, optimistic menu active state sync, and coordinates with FeedNavigation.
 */

const cleanPath = (path) => path.replace(/\/+$/, '') || '/';

const getCurrentLocation = () => {
    if (typeof window !== 'undefined' && window.location) {
        return new URL(window.location.href);
    }
    return new URL('http://localhost');
};

export function isEligibleLink(anchor, event = null) {
    if (!anchor || anchor.tagName !== 'A' || !anchor.href) {
        return false;
    }

    // Ignore modifier keys & non-left clicks
    if (event && (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || (event.button !== undefined && event.button !== 0))) {
        return false;
    }

    // Ignore downloads, external targets, explicit opt-outs
    if (anchor.hasAttribute('download') || anchor.target === '_blank' || anchor.getAttribute('rel')?.includes('external')) {
        return false;
    }
    if (anchor.closest('[data-no-app-nav]')) {
        return false;
    }

    // Disregard feed pagination/filter/tab links (delegated to feed-navigation.js)
    if (
        anchor.closest('[data-feed-pagination]') ||
        anchor.hasAttribute('data-feed-category') ||
        anchor.hasAttribute('data-feed-tab') ||
        anchor.classList.contains('page-link')
    ) {
        return false;
    }

    const currentUrl = getCurrentLocation();
    let targetUrl;
    try {
        targetUrl = new URL(anchor.href, currentUrl.href);
    } catch {
        return false;
    }

    // Must be same origin and http/https
    if (targetUrl.origin !== currentUrl.origin || !['http:', 'https:'].includes(targetUrl.protocol)) {
        return false;
    }

    // Exclude auth routes (login, register, logout)
    const targetPath = cleanPath(targetUrl.pathname);
    if (targetPath === '/login' || targetPath === '/register' || targetPath === '/logout' || targetPath.startsWith('/admin/login')) {
        return false;
    }

    // Must be a designated navigation link:
    // - In Sidebar (.sidebar-link, a.sidebar-account__top, a[aria-label="BlogMNM Trang chủ"])
    // - In Mobile Nav (.mobile-bottom-nav-link)
    // - Or explicitly marked with [data-app-nav]
    const isSidebarLink = Boolean(anchor.closest('.sidebar-container') && (anchor.classList.contains('sidebar-link') || anchor.classList.contains('sidebar-account__top') || anchor.getAttribute('aria-label') === 'BlogMNM Trang chủ'));
    const isMobileNavLink = Boolean(anchor.closest('.mobile-bottom-nav') && anchor.classList.contains('mobile-bottom-nav-link'));
    const isAppNavMarked = anchor.hasAttribute('data-app-nav') || Boolean(anchor.closest('[data-app-nav]'));
    const isActionNav = anchor.classList.contains('btn-cancel-action') || anchor.classList.contains('dashboard-create-btn');

    if (!isSidebarLink && !isMobileNavLink && !isAppNavMarked && !isActionNav) {
        return false;
    }

    // Check if user is navigating to exact same URL
    if (targetPath === cleanPath(currentUrl.pathname) && targetUrl.search === currentUrl.search) {
        if (targetUrl.hash) {
            if (typeof document !== 'undefined') {
                const hashEl = document.querySelector(targetUrl.hash);
                if (hashEl) {
                    if (typeof hashEl.focus === 'function') hashEl.focus();
                    if (typeof hashEl.scrollIntoView === 'function') {
                        hashEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
            return false;
        }

        // Click on current menu item scrolls smoothly to top (like Threads & Twitter)
        if (typeof window !== 'undefined' && typeof window.scrollTo === 'function') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        return false;
    }

    return true;
}

export function syncActiveMenu(doc = null, targetUrl = null) {
    if (typeof document === 'undefined') return;

    const currentUrl = getCurrentLocation();
    const url = targetUrl ? new URL(targetUrl.href, currentUrl.href) : currentUrl;
    const targetPath = cleanPath(url.pathname);

    // Strategy 1: Accurate server-rendered active states from parsed doc
    if (doc) {
        const newActiveSidebar = doc.querySelector('.sidebar-container .sidebar-link.active');
        if (newActiveSidebar) {
            const activeHref = newActiveSidebar.getAttribute('href');
            document.querySelectorAll('.sidebar-container .sidebar-link').forEach((link) => {
                link.classList.toggle('active', link.getAttribute('href') === activeHref);
            });
        }

        const newActiveMobile = doc.querySelector('.mobile-bottom-nav .mobile-bottom-nav-link.active');
        if (newActiveMobile) {
            const activeHref = newActiveMobile.getAttribute('href');
            document.querySelectorAll('.mobile-bottom-nav .mobile-bottom-nav-link').forEach((link) => {
                link.classList.toggle('active', link.getAttribute('href') === activeHref);
            });
        }
        return;
    }

    // Strategy 2: Optimistic client-side heuristic before server response
    document.querySelectorAll('.sidebar-container .sidebar-link, .mobile-bottom-nav .mobile-bottom-nav-link').forEach((link) => {
        const linkHref = link.getAttribute('href');
        if (!linkHref) return;

        let linkUrl;
        try {
            linkUrl = new URL(linkHref, currentUrl.href);
        } catch {
            return;
        }

        const linkPath = cleanPath(linkUrl.pathname);
        let isActive = false;

        if (targetPath === '/' || targetPath === '/posts') {
            if (url.searchParams.has('q')) {
                isActive = linkUrl.hash.includes('search') || link.getAttribute('title') === 'Tìm kiếm';
            } else if (!url.searchParams.has('category') && !url.searchParams.has('tag')) {
                isActive = (linkPath === '/' || linkPath === '/posts') && !linkUrl.hash.includes('search');
            }
        } else {
            isActive = linkPath === targetPath;
        }

        link.classList.toggle('active', isActive);
    });
}

export function createAppNavigator(options = {}) {
    const fetcher = options.fetcher || (typeof window !== 'undefined' ? window.fetch.bind(window) : async () => {});
    const getContentContainer = options.getContentContainer || (() => (typeof document !== 'undefined' ? document.querySelector('main[data-app-content], #main-content') : null));
    const getProgressBar = options.getProgressBar || (() => (typeof document !== 'undefined' ? document.getElementById('app-nav-progress') : null));

    let activeController = null;
    let navSequence = 0;

    const navigate = async (targetUrl, { historyMode = 'push', scrollX = 0, scrollY = 0 } = {}) => {
        const currentUrl = getCurrentLocation();
        const url = new URL(targetUrl.href, currentUrl.href);

        // Abort previous in-flight navigation
        if (activeController) {
            activeController.abort();
        }
        activeController = new AbortController();
        const currentSeq = ++navSequence;

        const container = getContentContainer();
        const progressBar = getProgressBar();

        // 1. Optimistic active menu & loading indicators
        syncActiveMenu(null, url);
        if (progressBar) {
            progressBar.classList.remove('is-done');
            progressBar.classList.add('is-loading');
        }
        if (container) {
            container.classList.add('is-page-loading');
            container.setAttribute('aria-busy', 'true');
        }

        try {
            const response = await fetcher(url.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-BlogMNM-App-Nav': '1',
                    'Accept': 'text/html',
                },
                signal: activeController.signal,
            });

            if (currentSeq !== navSequence) {
                return null;
            }

            // Fallback to normal navigation if server redirect / error
            if (!response.ok || response.status >= 400) {
                if (typeof window !== 'undefined' && window.location) {
                    window.location.assign(response.url || url.href);
                }
                return null;
            }

            const html = await response.text();
            if (currentSeq !== navSequence) {
                return null;
            }

            // 2. Parse new HTML
            let doc = null;
            let newMain = null;
            if (typeof DOMParser !== 'undefined') {
                doc = new DOMParser().parseFromString(html, 'text/html');
                newMain = doc.querySelector('main[data-app-content], #main-content');
            } else if (options.parseHtml) {
                const parsed = options.parseHtml(html);
                doc = parsed.doc;
                newMain = parsed.main;
            }

            if (!newMain) {
                if (typeof window !== 'undefined' && window.location) {
                    window.location.assign(response.url || url.href);
                }
                return { url: url.href, sequence: currentSeq };
            }

            // 3. Update document title
            if (doc?.title && typeof document !== 'undefined') {
                document.title = doc.title;
            }

            // 4. Swap Main Content Area
            if (container) {
                container.innerHTML = newMain.innerHTML;
                container.className = newMain.className;
                container.setAttribute('data-app-content', '');
            }

            // 4b. Initialize TipTap Post Editor if newly swapped content contains #tiptap-editor
            if (container && typeof container.querySelector === 'function' && container.querySelector('#tiptap-editor')) {
                try {
                    const { initPostEditor } = await import('./editor.js');
                    initPostEditor(container);
                } catch (err) {
                    console.error('Failed to initialize Post Editor:', err);
                }
            } else if (typeof window !== 'undefined' && typeof window.destroyPostEditor === 'function') {
                window.destroyPostEditor();
            }

            // 5. Sync verified server active menu
            if (doc) {
                syncActiveMenu(doc, url);
            }

            // 6. History and Scroll Restoration
            if (typeof window !== 'undefined' && window.history) {
                if (historyMode === 'push') {
                    window.history.replaceState(
                        { ...window.history.state, appNav: true, url: window.location.href, scrollX: window.scrollX, scrollY: window.scrollY },
                        '',
                        window.location.href
                    );
                    window.history.pushState(
                        { appNav: true, url: url.href, scrollX: 0, scrollY: 0 },
                        '',
                        url.href
                    );
                    if (typeof window.scrollTo === 'function') {
                        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
                    }
                } else if (historyMode === 'replace') {
                    window.history.replaceState(
                        { appNav: true, url: url.href, scrollX: 0, scrollY: 0 },
                        '',
                        url.href
                    );
                } else if (historyMode === 'none') {
                    if (typeof window.scrollTo === 'function') {
                        window.scrollTo({ top: scrollY || 0, left: scrollX || 0, behavior: 'instant' });
                    }
                }
            }

            // 7. Focus hash target if navigated to #search or similar
            if (url.hash && typeof document !== 'undefined') {
                const targetEl = document.querySelector(url.hash);
                if (targetEl) {
                    if (typeof targetEl.focus === 'function') targetEl.focus();
                    if (typeof targetEl.scrollIntoView === 'function') {
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }

            // 8. Re-hydrate Feed Navigation & Optimistic UI
            if (typeof document !== 'undefined') {
                if (typeof window !== 'undefined' && typeof window.initFeedNavigation === 'function' && container) {
                    window.initFeedNavigation(container);
                }
                document.dispatchEvent(new CustomEvent('blogmnm:feed-updated', {
                    detail: { root: container, navigationId: currentSeq }
                }));
                document.dispatchEvent(new CustomEvent('blogmnm:page-navigated', {
                    detail: { url: url.href, title: typeof document !== 'undefined' ? document.title : '', root: container }
                }));
            }

            return { url: url.href, title: doc?.title || '', sequence: currentSeq };
        } catch (error) {
            if (error.name === 'AbortError' || currentSeq !== navSequence) {
                return null;
            }
            if (typeof window !== 'undefined' && window.location) {
                window.location.assign(url.href);
            }
            return null;
        } finally {
            if (currentSeq === navSequence) {
                if (progressBar) {
                    progressBar.classList.remove('is-loading');
                    progressBar.classList.add('is-done');
                    if (typeof window !== 'undefined') {
                        window.setTimeout(() => progressBar.classList.remove('is-done'), 350);
                    }
                }
                if (container) {
                    container.classList.remove('is-page-loading');
                    container.removeAttribute('aria-busy');
                }
            }
        }
    };

    return { navigate };
}

export function confirmNavigationLeave({
    title = 'Rời khỏi trang soạn thảo?',
    message = 'Bạn có thay đổi chưa được lưu trên bài viết này. Nếu rời đi, toàn bộ nội dung chưa lưu sẽ bị mất.',
    confirmText = 'Rời trang',
    cancelText = 'Tiếp tục chỉnh sửa',
} = {}) {
    return new Promise((resolve) => {
        if (typeof document === 'undefined') {
            resolve(true);
            return;
        }

        const modalEl = document.getElementById('global-confirm-modal');
        if (!modalEl || typeof window === 'undefined' || !window.bootstrap?.Modal) {
            const fallback = window.confirm(`${title}\n\n${message}`);
            resolve(fallback);
            return;
        }

        const titleEl = document.getElementById('confirm-modal-title');
        const messageEl = document.getElementById('confirm-modal-message');
        const cancelBtn = document.getElementById('confirm-modal-cancel');
        const submitBtn = document.getElementById('confirm-modal-submit');
        const submitTextEl = document.getElementById('confirm-modal-submit-text');
        const targetBoxEl = document.getElementById('confirm-modal-target-box');
        const descBoxEl = document.getElementById('confirm-modal-description-box');
        const promptBoxEl = document.getElementById('confirm-modal-prompt-box');
        const headerIconEl = document.getElementById('confirm-modal-header-icon');

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;
        if (cancelBtn) cancelBtn.textContent = cancelText;
        if (submitTextEl) submitTextEl.textContent = confirmText;
        if (submitBtn) {
            submitBtn.className = 'btn btn-sm rounded-pill px-4 fw-bold d-inline-flex align-items-center gap-2 btn-danger';
        }
        if (targetBoxEl) targetBoxEl.classList.add('d-none');
        if (descBoxEl) descBoxEl.classList.add('d-none');
        if (promptBoxEl) promptBoxEl.classList.add('d-none');
        if (headerIconEl) {
            headerIconEl.className = 'badge rounded-circle p-2 d-flex align-items-center justify-content-center bg-warning-subtle text-warning';
            headerIconEl.innerHTML = `<svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
        }

        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
        let resolved = false;

        const cleanupAndResolve = (result) => {
            if (resolved) return;
            resolved = true;
            submitBtn?.removeEventListener('click', onConfirm);
            cancelBtn?.removeEventListener('click', onCancel);
            modalEl.removeEventListener('hidden.bs.modal', onHidden);

            if (cancelBtn) cancelBtn.textContent = 'Hủy';
            if (submitTextEl) submitTextEl.textContent = 'Xác nhận';

            resolve(result);
        };

        const onConfirm = () => {
            modalInstance.hide();
            cleanupAndResolve(true);
        };

        const onCancel = () => {
            modalInstance.hide();
            cleanupAndResolve(false);
        };

        const onHidden = () => {
            cleanupAndResolve(false);
        };

        submitBtn?.addEventListener('click', onConfirm);
        cancelBtn?.addEventListener('click', onCancel);
        modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });

        modalInstance.show();
    });
}

export function initAppNavigation(root = document) {
    if (typeof window === 'undefined') return;

    const navigator = createAppNavigator();
    window.BlogMNMAppNavigator = navigator;

    // Delegate clicks on eligible navigation links
    root.addEventListener('click', async (event) => {
        const anchor = event.target.closest('a');
        if (!anchor) return;

        if (isEligibleLink(anchor, event)) {
            event.preventDefault();

            // Unsaved changes protection
            if (typeof window.isPostEditorDirty === 'function' && window.isPostEditorDirty()) {
                const leaveConfirmed = await confirmNavigationLeave();
                if (!leaveConfirmed) {
                    return;
                }
                if (typeof window.destroyPostEditor === 'function') {
                    window.destroyPostEditor();
                }
            }

            const targetUrl = new URL(anchor.href, window.location.href);
            navigator.navigate(targetUrl, { historyMode: 'push' });
        }
    });

    // Save initial state with scroll position
    window.history.replaceState(
        { ...window.history.state, appNav: true, url: window.location.href, scrollX: window.scrollX, scrollY: window.scrollY },
        '',
        window.location.href
    );

    // Global popstate handler for Back / Forward
    window.addEventListener('popstate', async (event) => {
        // If state belongs strictly to an internal feed fragment (e.g. pagination inside a feed), let feed-navigation handle it
        if (event.state?.blogmnmFeed && !event.state?.appNav) {
            return;
        }

        // Guard against navigating back/forward while editor has unsaved changes
        if (typeof window.isPostEditorDirty === 'function' && window.isPostEditorDirty()) {
            const leaveConfirmed = await confirmNavigationLeave();
            if (!leaveConfirmed) {
                // Restore history state to keep browser URL in sync with currently active editor
                window.history.pushState(
                    { appNav: true, url: window.location.href, scrollX: window.scrollX, scrollY: window.scrollY },
                    '',
                    window.location.href
                );
                return;
            }
            if (typeof window.destroyPostEditor === 'function') {
                window.destroyPostEditor();
            }
        }

        const url = new URL(window.location.href);
        navigator.navigate(url, {
            historyMode: 'none',
            scrollX: event.state?.scrollX || 0,
            scrollY: event.state?.scrollY || 0,
        });
    });
}
