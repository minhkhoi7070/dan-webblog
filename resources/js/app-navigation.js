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
        anchor.hasAttribute('data-feed-type') ||
        anchor.classList.contains('feed-tab') ||
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

if (typeof window !== 'undefined') {
    window.syncActiveMenu = syncActiveMenu;
}

export function updateMobileHeader(targetUrl) {
    if (typeof document === 'undefined') return;
    const leftEl = document.getElementById('mobile-header-left');
    if (!leftEl) return;

    let path = '/';
    try {
        const u = targetUrl instanceof URL ? targetUrl : new URL(targetUrl, window.location.href);
        path = cleanPath(u.pathname);
    } catch {
        path = '/';
    }

    const isHome = (path === '/' || path === '/posts');
    let subTitle = null;

    if (path.startsWith('/posts/') && path !== '/posts/create' && path !== '/posts/stats') {
        subTitle = 'Chi tiết bài viết';
    } else if (path === '/posts/create') {
        subTitle = 'Viết bài mới';
    } else if (path.includes('/edit')) {
        subTitle = 'Chỉnh sửa bài';
    } else if (path === '/posts/stats' || path.startsWith('/author/posts')) {
        subTitle = 'Quản lý bài viết';
    } else if (path.startsWith('/author/comments')) {
        subTitle = 'Bình luận tác giả';
    } else if (path.startsWith('/following')) {
        subTitle = 'Đang theo dõi';
    } else if (path.startsWith('/favorites')) {
        subTitle = 'Bài viết đã lưu';
    } else if (path.startsWith('/activity')) {
        subTitle = 'Nhật ký hoạt động';
    } else if (path.startsWith('/profile')) {
        subTitle = 'Trang cá nhân';
    } else if (path === '/admin' || path === '/admin/dashboard') {
        subTitle = 'Bảng điều khiển';
    } else if (path.startsWith('/admin/posts')) {
        subTitle = 'Kiểm duyệt bài viết';
    } else if (path.startsWith('/admin/users')) {
        subTitle = 'Quản lý người dùng';
    } else if (path.startsWith('/admin/categories')) {
        subTitle = 'Chuyên mục bài viết';
    } else if (path.startsWith('/admin/tags')) {
        subTitle = 'Thẻ bài viết';
    } else if (path.startsWith('/admin/comments')) {
        subTitle = 'Quản lý bình luận';
    }

    if (isHome || !subTitle) {
        leftEl.innerHTML = `
            <a href="/" class="brand-link d-flex align-items-center gap-2 font-bold text-lg tracking-tight text-decoration-none flex-shrink-0" aria-label="CloudBlog Trang chủ" data-app-nav id="mobile-header-brand">
                <svg class="cloud-logo w-8 h-8 flex-shrink-0" style="width: 32px; height: 32px; max-width: 32px; max-height: 32px;" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="10" fill="#09090b"/>
                    <path d="M12 24C12 20.6863 14.6863 18 18 18C18.6868 18 19.3458 18.1154 19.96 18.3283C21.0371 15.7877 23.5593 14 26.5 14C30.6421 14 34 17.3579 34 21.5C34 21.6705 33.9943 21.8396 33.9831 22.007C34.5957 22.5694 35 23.3644 35 24.25C35 25.7688 33.7688 27 32.25 27H15C13.3431 27 12 25.6569 12 24Z" fill="#3b82f6"/>
                </svg>
                <span class="brand-text text-theme fw-bold" style="white-space: nowrap;">CloudBlog</span>
            </a>
        `;
    } else {
        leftEl.innerHTML = `
            <div class="d-flex align-items-center gap-2 min-w-0">
                <a href="javascript:history.length > 1 ? history.back() : window.location.href='/'"
                   class="btn btn-sm btn-link text-theme p-0 d-flex align-items-center justify-content-center rounded-3 text-decoration-none flex-shrink-0"
                   style="width: 40px; height: 40px;"
                   aria-label="Quay lại"
                   title="Quay lại"
                   id="mobile-header-back-btn">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <span class="fw-bold text-theme text-truncate" style="font-size: 16px; letter-spacing: -0.01em;" id="mobile-header-subpage-title">
                    ${subTitle}
                </span>
            </div>
        `;
    }
}

if (typeof window !== 'undefined') {
    window.updateMobileHeader = updateMobileHeader;
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
            updateMobileHeader(url);

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

            // Automatically close mobile drawer if open
            if (typeof document !== 'undefined') {
                const drawerEl = document.getElementById('mobileAppDrawer');
                if (drawerEl && typeof window !== 'undefined' && window.bootstrap?.Offcanvas) {
                    const inst = window.bootstrap.Offcanvas.getInstance(drawerEl);
                    if (inst) inst.hide();
                }
            }

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
        const url = new URL(window.location.href);

        // If currently inside a feed shell that handles this URL, delegate to feed navigation
        const currentFeedShell = document.querySelector('[data-feed-shell]');
        if (currentFeedShell) {
            const baseUrlAttr = currentFeedShell.getAttribute('data-feed-base-url');
            const baseUrl = baseUrlAttr ? new URL(baseUrlAttr, window.location.href) : null;
            const isHomeFeed = currentFeedShell.hasAttribute('data-feed-home-url') || currentFeedShell.hasAttribute('data-feed-following-url');
            const targetPath = cleanPath(url.pathname);

            const isHandledByFeed = (isHomeFeed && (targetPath === '/' || targetPath === '/posts' || targetPath === '/following'))
                || (baseUrl && targetPath === cleanPath(baseUrl.pathname));

            if (isHandledByFeed) {
                return;
            }
        }

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

        updateMobileHeader(url);

        navigator.navigate(url, {
            historyMode: 'none',
            scrollX: event.state?.scrollX || 0,
            scrollY: event.state?.scrollY || 0,
        });
    });

    // Mobile Virtual Keyboard Handling: hide fixed bottom navigation when typing
    if (typeof window !== 'undefined') {
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                const currentHeight = window.visualViewport.height;
                const isKeyboardOpen = (window.innerHeight - currentHeight > 150) || (currentHeight < window.innerHeight * 0.75);
                document.body.classList.toggle('keyboard-open', isKeyboardOpen);
            });
        }

        if (typeof document !== 'undefined') {
            document.addEventListener('focusin', (e) => {
                if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable)) {
                    if (window.innerWidth < 768) {
                        document.body.classList.add('keyboard-open');
                    }
                }
            });

            document.addEventListener('focusout', (e) => {
                if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable)) {
                    setTimeout(() => {
                        const active = document.activeElement;
                        if (!active || (active.tagName !== 'INPUT' && active.tagName !== 'TEXTAREA' && !active.isContentEditable)) {
                            document.body.classList.remove('keyboard-open');
                        }
                    }, 100);
                }
            });

            // Ensure drawer closes cleanly and backdrop/scroll-lock is cleaned up when resizing past mobile breakpoint
            const handleDesktopBreakpoint = () => {
                if (window.innerWidth >= 768) {
                    const drawerEl = document.getElementById('mobileAppDrawer');
                    if (drawerEl) {
                        drawerEl.classList.remove('show');
                        drawerEl.style.removeProperty('visibility');
                        drawerEl.setAttribute('aria-hidden', 'true');
                        drawerEl.removeAttribute('aria-modal');
                        drawerEl.removeAttribute('role');
                        if (window.bootstrap?.Offcanvas) {
                            const inst = window.bootstrap.Offcanvas.getInstance(drawerEl);
                            if (inst) {
                                try {
                                    inst.hide();
                                } catch (_) {}
                            }
                        }
                    }
                    document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                    if (!document.querySelector('.modal.show')) {
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');
                        document.body.classList.remove('modal-open');
                    }
                }
            };
            window.addEventListener('resize', handleDesktopBreakpoint);
            if (window.matchMedia) {
                try {
                    window.matchMedia('(min-width: 768px)').addEventListener('change', handleDesktopBreakpoint);
                } catch (_) {}
            }
        }
    }
}
