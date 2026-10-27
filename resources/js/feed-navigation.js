/**
 * BlogMNM YouTube-Like & Threads-Like Seamless Navigation & AJAX Pagination
 *
 * Provides instant category/tab switching, AJAX pagination without full reload,
 * skeleton loading, AbortController race condition prevention, history synchronization,
 * scroll restoration, and interaction state hydration.
 */

export function createFeedLoader(fetcher = window.fetch) {
    let activeController = null;
    let sequence = 0;

    return async (url) => {
        if (activeController) {
            activeController.abort();
        }
        activeController = new AbortController();
        const requestSequence = ++sequence;

        try {
            const response = await fetcher(url, {
                headers: {
                    'X-BlogMNM-Feed': '1',
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeController.signal,
            });

            if (requestSequence !== sequence) {
                return null;
            }

            if (!response.ok) {
                const error = new Error('Không thể tải nội dung. Vui lòng thử lại.');
                error.status = response.status;
                throw error;
            }

            if (response.headers.get('X-BlogMNM-Feed') !== '1') {
                const error = new Error('Trang cần được mở lại để tiếp tục.');
                error.redirectUrl = response.url || url;
                throw error;
            }

            const html = await response.text();
            if (requestSequence !== sequence) {
                return null;
            }

            return { html, url: response.url || String(url), sequence: requestSequence };
        } catch (error) {
            if (error.name === 'AbortError' || requestSequence !== sequence) {
                return null;
            }
            throw error;
        }
    };
}

export function initFeedShell(shell) {
    if (!shell || shell.dataset.navigationReady === 'true') {
        return;
    }
    shell.dataset.navigationReady = 'true';

    const baseUrl = new URL(shell.dataset.feedBaseUrl || window.location.href, window.location.href);
    const homeUrl = new URL(shell.dataset.feedHomeUrl || '/', window.location.href);
    const targetSelector = shell.dataset.feedTarget || '#feed-results';
    const preserveScroll = shell.dataset.feedPreserveScroll === 'true';

    const status = shell.querySelector('[data-feed-status]') || document.getElementById('feed-status');
    const errorBox = shell.querySelector('[data-feed-error]') || document.getElementById('feed-error');
    const skeletonTemplate = shell.querySelector('template[data-feed-skeleton]') || document.getElementById('feed-skeleton-template');
    const load = createFeedLoader();

    let currentUrl = new URL(window.location.href);
    let previousCategory = currentUrl.searchParams.get('category') || '';
    let retryUrl = currentUrl;
    let navigationSequence = 0;

    const cleanPath = (path) => path.replace(/\/+$/, '') || '/';

    const supportsUrl = (url) => {
        if (url.origin !== window.location.origin) {
            return false;
        }
        const targetPath = cleanPath(url.pathname);
        const basePath = cleanPath(baseUrl.pathname);
        const homePath = cleanPath(homeUrl.pathname);
        const currentPath = cleanPath(window.location.pathname);

        return targetPath === basePath
            || targetPath === homePath
            || targetPath === currentPath;
    };

    /**
     * Optimistically sync category chips active styling
     */
    const syncCategoryChips = (activeCategory) => {
        shell.querySelectorAll('[data-feed-category-chip], nav[aria-label="Bộ lọc chuyên mục"] [data-feed-category]').forEach((link) => {
            const isActive = (link.dataset.feedCategory || '') === activeCategory;
            link.classList.toggle('btn-editorial-primary', isActive);
            link.classList.toggle('btn-outline-theme', !isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });

        // Sync hidden category input in search form if present
        const searchForm = shell.querySelector('form[role="search"]');
        if (searchForm) {
            let catInput = searchForm.querySelector('input[name="category"]');
            if (activeCategory) {
                if (!catInput) {
                    catInput = document.createElement('input');
                    catInput.type = 'hidden';
                    catInput.name = 'category';
                    searchForm.appendChild(catInput);
                }
                catInput.value = activeCategory;
            } else if (catInput) {
                catInput.remove();
            }
        }
    };

    /**
     * Optimistically sync tab switcher styling
     */
    const syncTabs = (targetUrl) => {
        const targetTab = targetUrl.searchParams.get('tab') || '';
        shell.querySelectorAll('a[data-feed-tab]').forEach((link) => {
            const linkTab = link.dataset.feedTab || '';
            const isActive = linkTab === targetTab;
            link.classList.toggle('btn-editorial-primary', isActive);
            link.classList.toggle('btn-outline-theme', !isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    /**
     * Render skeleton cards into target results container
     */
    const showSkeleton = () => {
        const resultsEl = shell.querySelector(targetSelector) || document.querySelector(targetSelector);
        if (!resultsEl) return;

        resultsEl.setAttribute('aria-busy', 'true');
        if (skeletonTemplate) {
            const clone = skeletonTemplate.content.cloneNode(true);
            resultsEl.innerHTML = '';
            resultsEl.appendChild(clone);
        } else {
            resultsEl.style.opacity = '0.5';
            resultsEl.style.pointerEvents = 'none';
        }
    };

    /**
     * Adjust scroll position smoothly without jarring header/sidebar
     */
    const adjustScrollPosition = () => {
        if (preserveScroll) return;

        const shellRect = shell.getBoundingClientRect();
        if (shellRect.top < 60) {
            const targetY = Math.max(0, window.scrollY + shellRect.top - 70);
            window.scrollTo({
                top: targetY,
                behavior: 'smooth',
            });
        }
    };

    /**
     * Navigate feed to target URL
     */
    const navigate = async (url, { historyMode = 'push', isPagination = false } = {}) => {
        const sequence = ++navigationSequence;
        const targetCategory = url.searchParams.get('category') || '';
        const prevCategoryBackup = previousCategory;

        retryUrl = url;
        if (errorBox) {
            errorBox.hidden = true;
        }

        // 1. Optimistic UI Updates
        syncCategoryChips(targetCategory);
        syncTabs(url);

        // 2. Accessibility announcement
        if (status) {
            if (isPagination) {
                const pageNum = url.searchParams.get('page') || '1';
                status.textContent = `Đang chuyển đến trang ${pageNum}…`;
            } else {
                status.textContent = 'Đang tải nội dung…';
            }
        }

        // 3. Render Skeleton Loading
        showSkeleton();

        // 4. Dispatch feed-loading event for interaction state hydration
        document.dispatchEvent(new CustomEvent('blogmnm:feed-loading', {
            detail: { navigationId: sequence },
        }));

        try {
            const result = await load(url.href);
            if (!result || sequence !== navigationSequence) {
                return;
            }

            const parsed = new DOMParser().parseFromString(result.html, 'text/html');
            const targetId = targetSelector.startsWith('#') ? targetSelector.slice(1) : 'feed-results';
            const nextResults = parsed.querySelector(targetSelector)
                || parsed.getElementById(targetId)
                || parsed.getElementById('feed-results');

            if (!nextResults) {
                throw new Error('Không thể hiển thị kết quả. Vui lòng thử lại.');
            }

            const resolvedUrl = new URL(result.url, window.location.href);
            if (!supportsUrl(resolvedUrl)) {
                window.location.assign(resolvedUrl.href);
                return;
            }

            // 5. Smoothly replace feed results
            const currentResults = shell.querySelector(targetSelector)
                || document.getElementById(targetId)
                || document.getElementById('feed-results');

            if (currentResults) {
                currentResults.replaceWith(nextResults);
            }
            nextResults.removeAttribute('aria-busy');
            nextResults.style.opacity = '1';
            nextResults.style.pointerEvents = '';

            // 6. Synchronize History & URL
            if (historyMode === 'push' && resolvedUrl.href !== window.location.href) {
                window.history.pushState({ blogmnmFeed: true, url: resolvedUrl.href, category: targetCategory }, '', resolvedUrl);
            } else if (historyMode === 'replace' && resolvedUrl.href !== window.location.href) {
                window.history.replaceState({ blogmnmFeed: true, url: resolvedUrl.href, category: targetCategory }, '', resolvedUrl);
            }

            currentUrl = resolvedUrl;
            previousCategory = targetCategory;
            syncCategoryChips(targetCategory);
            syncTabs(resolvedUrl);

            // 7. Scroll adjustment on pagination
            if (isPagination && historyMode !== 'none') {
                adjustScrollPosition();
            }

            // 8. Status announcement
            if (status) {
                const total = nextResults.dataset.feedTotal;
                if (total !== undefined) {
                    status.textContent = `${total} kết quả phù hợp.`;
                } else {
                    status.textContent = 'Đã cập nhật nội dung.';
                }
            }

            // 9. Hydrate interactions (Like, Favorite, Follow) on newly loaded elements
            document.dispatchEvent(new CustomEvent('blogmnm:feed-updated', {
                detail: { root: nextResults, navigationId: sequence },
            }));

        } catch (error) {
            if (sequence !== navigationSequence) {
                return;
            }

            if (error.redirectUrl) {
                window.location.assign(error.redirectUrl);
                return;
            }

            // Roll back optimistic UI
            syncCategoryChips(prevCategoryBackup);
            previousCategory = prevCategoryBackup;
            syncTabs(currentUrl);

            if (status) {
                status.textContent = '';
            }

            const currentResults = shell.querySelector(targetSelector) || document.getElementById('feed-results');
            if (currentResults) {
                currentResults.removeAttribute('aria-busy');
                currentResults.style.opacity = '1';
                currentResults.style.pointerEvents = '';
            }

            if (errorBox) {
                const messageEl = errorBox.querySelector('[data-feed-error-message]') || errorBox;
                if (messageEl) {
                    messageEl.textContent = error.message;
                }
                errorBox.hidden = false;
            }
        }
    };

    // Event Delegation: Intercept category chips, pagination links, tabs, and clear filters
    shell.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        // Retry button click
        const retryBtn = event.target.closest('[data-feed-retry]');
        if (retryBtn) {
            event.preventDefault();
            navigate(retryUrl);
            return;
        }

        // Pagination links
        const paginationLink = event.target.closest('[data-feed-pagination] a, .pagination a, a.page-link');
        if (paginationLink && !paginationLink.target && !paginationLink.hasAttribute('download')) {
            if (paginationLink.closest('.disabled, [aria-disabled="true"]')) {
                event.preventDefault();
                return;
            }

            const url = new URL(paginationLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                if (url.href === currentUrl.href) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                navigate(url, { historyMode: 'push', isPagination: true });
                return;
            }
        }

        // Tab switcher links (e.g. Following tab=feed vs tab=authors)
        const tabLink = event.target.closest('a[data-feed-tab]');
        if (tabLink && !tabLink.target && !tabLink.hasAttribute('download')) {
            const url = new URL(tabLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                if (url.href === currentUrl.href) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                navigate(url, { historyMode: 'push', isPagination: false });
                return;
            }
        }

        // Category link click (chips in top bar, or category pills in cards)
        const categoryLink = event.target.closest('a[data-feed-category]');
        if (categoryLink && !categoryLink.target && !categoryLink.hasAttribute('download')) {
            const url = new URL(categoryLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                const targetCategory = url.searchParams.get('category') || '';
                const activeCategory = currentUrl.searchParams.get('category') || '';

                if (targetCategory === activeCategory && url.pathname === currentUrl.pathname && !url.searchParams.has('page')) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                navigate(url, { historyMode: 'push', isPagination: false });
                return;
            }
        }

        // Clear filter link click
        const clearFilterLink = event.target.closest('a[data-feed-clear-filter]');
        if (clearFilterLink && !clearFilterLink.target && !clearFilterLink.hasAttribute('download')) {
            const url = new URL(clearFilterLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                event.preventDefault();
                navigate(url, { historyMode: 'push', isPagination: false });
            }
        }
    });

    // History API popstate (Browser Back / Forward)
    window.history.replaceState(
        { ...window.history.state, blogmnmFeed: true, url: window.location.href, category: previousCategory },
        '',
        window.location.href
    );

    window.addEventListener('popstate', (event) => {
        const url = new URL(window.location.href);
        if (supportsUrl(url) && event.state?.blogmnmFeed && !event.state?.appNav) {
            navigate(url, { historyMode: 'none', isPagination: url.searchParams.has('page') });
        }
    });
}

export function initFeedNavigation(root = document) {
    const shells = root.querySelectorAll('[data-feed-shell]');
    shells.forEach((shell) => initFeedShell(shell));
}

if (typeof window !== 'undefined') {
    window.initFeedNavigation = initFeedNavigation;
}
