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
    const followingUrl = new URL(shell.dataset.feedFollowingUrl || '/following', window.location.href);
    const targetSelector = shell.dataset.feedTarget || '#feed-results';
    const preserveScroll = shell.dataset.feedPreserveScroll === 'true';

    const status = shell.querySelector('[data-feed-status]') || document.getElementById('feed-status');
    const errorBox = shell.querySelector('[data-feed-error]') || document.getElementById('feed-error');
    const skeletonTemplate = shell.querySelector('template[data-feed-skeleton]') || document.getElementById('feed-skeleton-template');
    const categoriesNav = shell.querySelector('[data-feed-categories-nav], nav[aria-label="Bộ lọc chuyên mục"]');
    const load = createFeedLoader();

    let currentUrl = new URL(window.location.href);
    let previousCategory = currentUrl.searchParams.get('category') || '';
    let retryUrl = currentUrl;
    let navigationSequence = 0;
    let skeletonTimer = null;

    const cleanPath = (path) => path.replace(/\/+$/, '') || '/';

    const isFeedPath = (path) => {
        const p = cleanPath(path);
        return p === '/' || p === '/posts' || p === '/following';
    };

    const isFollowingUrl = (url) => {
        const p = cleanPath(url.pathname);
        return p === '/following' || p === cleanPath(followingUrl.pathname);
    };

    const isHomeOrFollowingShell = isFeedPath(cleanPath(baseUrl.pathname));

    const supportsUrl = (url) => {
        if (url.origin !== window.location.origin) {
            return false;
        }
        const targetPath = cleanPath(url.pathname);
        const basePath = cleanPath(baseUrl.pathname);

        if (isHomeOrFollowingShell) {
            const homePath = cleanPath(homeUrl.pathname);
            const followingPath = cleanPath(followingUrl.pathname);
            return targetPath === basePath
                || targetPath === homePath
                || targetPath === followingPath
                || isFeedPath(targetPath);
        }

        return targetPath === basePath;
    };

    /**
     * Optimistically sync top feed tabs ("Dành cho bạn" vs "Đang theo dõi")
     */
    const syncTopTabs = (targetUrl) => {
        const isFollowing = isFollowingUrl(targetUrl);
        shell.querySelectorAll('[data-feed-for-you-composer]').forEach((composer) => {
            composer.classList.toggle('d-none', isFollowing);
        });
        shell.querySelectorAll('[data-following-view-switcher]').forEach((switcher) => {
            switcher.classList.toggle('d-none', !isFollowing);
        });
        shell.querySelectorAll('.feed-tab, [data-feed-type]').forEach((tab) => {
            const type = tab.dataset.feedType || (tab.getAttribute('href')?.includes('/following') ? 'following' : 'for-you');
            const isActive = isFollowing ? (type === 'following') : (type === 'for-you');
            tab.classList.toggle('active', isActive);
            tab.setAttribute('aria-selected', String(isActive));
            if (isActive) {
                tab.setAttribute('tabindex', '0');
            } else {
                tab.removeAttribute('tabindex');
            }
        });
    };

    /**
     * Show / hide category filter navigation depending on active tab
     */
    const syncCategoryNav = (targetUrl) => {
        if (!categoriesNav) return;
        const isFollowing = isFollowingUrl(targetUrl);
        categoriesNav.classList.toggle('d-none', isFollowing);
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
     * Optimistically sync view switcher sub-tabs and status tabs
     */
    const syncTabs = (targetUrl) => {
        const targetStatus = targetUrl.searchParams.get('status') || '';
        const targetTab = targetUrl.searchParams.get('tab') || '';

        shell.querySelectorAll('a[data-feed-tab]').forEach((link) => {
            const linkTab = link.dataset.feedTab ?? '';
            const isStatusTab = link.hasAttribute('data-feed-status-tab')
                || Boolean(link.closest('[data-feed-status-tabs]'))
                || ['pending', 'published', 'draft', 'rejected', 'approved', 'spam'].includes(linkTab)
                || (linkTab === '' && Boolean(targetUrl.searchParams.has('status') || link.closest('[data-feed-status-tabs]')));

            let isActive = false;
            if (isStatusTab) {
                isActive = linkTab === targetStatus;
            } else {
                isActive = linkTab === (targetTab || 'feed');
            }

            const activeClass = link.dataset.activeClass || 'btn-editorial-primary text-white';
            const inactiveClass = link.dataset.inactiveClass || 'btn-outline-theme text-theme-secondary';

            const activeList = activeClass.split(/\s+/).filter(Boolean);
            const inactiveList = inactiveClass.split(/\s+/).filter(Boolean);

            if (isActive) {
                link.classList.remove(...inactiveList);
                link.classList.add(...activeList);
                link.setAttribute('aria-current', 'page');
                link.setAttribute('aria-selected', 'true');
            } else {
                link.classList.remove(...activeList);
                link.classList.add(...inactiveList);
                link.removeAttribute('aria-current');
                link.setAttribute('aria-selected', 'false');
            }
        });

        // Sync hidden status input in filter forms if present
        const filterForm = shell.querySelector('form[data-feed-filter-form], form[role="search"]');
        if (filterForm) {
            let statusInput = filterForm.querySelector('input[name="status"]');
            if (targetStatus) {
                if (!statusInput) {
                    statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    filterForm.prepend(statusInput);
                }
                statusInput.value = targetStatus;
            } else if (statusInput) {
                statusInput.value = '';
            }
        }
    };

    /**
     * Synchronize search and select form inputs with target URL parameters
     */
    const syncFilterFormInputs = (targetUrl) => {
        const filterForm = shell.querySelector('form[data-feed-filter-form], form[role="search"]');
        if (!filterForm) return;

        const q = targetUrl.searchParams.get('q') || targetUrl.searchParams.get('search') || '';
        const qInput = filterForm.querySelector('input[name="q"], input[name="search"]');
        if (qInput && qInput.value !== q) {
            qInput.value = q;
        }

        filterForm.querySelectorAll('select').forEach((select) => {
            const val = targetUrl.searchParams.get(select.name) || '';
            if (select.value !== val) {
                select.value = val;
            }
        });

        const status = targetUrl.searchParams.get('status') || '';
        const statusInput = filterForm.querySelector('input[name="status"]');
        if (statusInput && statusInput.value !== status) {
            statusInput.value = status;
        }
    };

    /**
     * Synchronize sidebar and mobile bottom nav active state without full reload
     */
    const syncSidebarAndMobileNav = (targetUrl) => {
        if (typeof window !== 'undefined' && typeof window.syncActiveMenu === 'function') {
            window.syncActiveMenu(null, targetUrl);
            return;
        }
        if (typeof document === 'undefined') return;
        const targetPath = cleanPath(targetUrl.pathname);
        const isFollowing = isFollowingUrl(targetUrl);
        document.querySelectorAll('.sidebar-container .sidebar-link, .mobile-drawer .sidebar-link, .mobile-bottom-nav .mobile-bottom-nav-link').forEach((link) => {
            const href = link.getAttribute('href');
            if (!href) return;
            try {
                const linkUrl = new URL(href, window.location.href);
                const linkPath = cleanPath(linkUrl.pathname);
                if (isFollowing) {
                    link.classList.toggle('active', linkPath === '/following');
                } else if (targetPath === '/' || targetPath === '/posts') {
                    if (!targetUrl.searchParams.has('q') && !targetUrl.searchParams.has('category') && !targetUrl.searchParams.has('tag')) {
                        link.classList.toggle('active', (linkPath === '/' || linkPath === '/posts') && !linkUrl.hash.includes('search'));
                    }
                }
            } catch {
                // Ignore invalid URLs
            }
        });
    };

    /**
     * Update browser tab document title smoothly
     */
    const syncDocumentTitle = (targetUrl) => {
        if (typeof document === 'undefined') return;
        if (!isHomeOrFollowingShell) {
            return;
        }
        const appName = shell.dataset.feedAppName || 'CloudBlog';
        if (isFollowingUrl(targetUrl)) {
            document.title = `${appName} — Bài viết từ tác giả đang theo dõi`;
        } else {
            const cat = targetUrl.searchParams.get('category');
            const q = targetUrl.searchParams.get('q');
            if (!cat && !q) {
                document.title = `${appName} — Khám phá tin tức & góc nhìn công nghệ`;
            }
        }
    };

    /**
     * Render skeleton cards into target results container (used on delayed threshold)
     */
    const showSkeleton = (targetEl) => {
        const resultsEl = targetEl || shell.querySelector(targetSelector) || document.querySelector(targetSelector);
        if (!resultsEl) return;

        resultsEl.setAttribute('aria-busy', 'true');
        if (skeletonTemplate) {
            const clone = skeletonTemplate.content.cloneNode(true);
            resultsEl.innerHTML = '';
            resultsEl.appendChild(clone);
        } else {
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
    const navigate = async (url, { historyMode = 'push', isPagination = false, isTopTab = false } = {}) => {
        const sequence = ++navigationSequence;
        const targetCategory = url.searchParams.get('category') || '';
        const prevCategoryBackup = previousCategory;
        const isFollowingTarget = isFollowingUrl(url);

        retryUrl = url;
        if (errorBox) {
            errorBox.hidden = true;
        }

        // 1. Optimistic UI Updates
        syncTopTabs(url);
        syncCategoryNav(url);
        syncCategoryChips(isFollowingTarget ? '' : targetCategory);
        syncTabs(url);
        syncSidebarAndMobileNav(url);

        // 2. Accessibility announcement
        if (status) {
            if (isPagination) {
                const pageNum = url.searchParams.get('page') || '1';
                status.textContent = `Đang chuyển đến trang ${pageNum}…`;
            } else if (isTopTab || isFollowingTarget) {
                status.textContent = isFollowingTarget ? 'Đang tải bài viết từ tác giả đang theo dõi…' : 'Đang tải bài viết dành cho bạn…';
            } else {
                status.textContent = 'Đang tải nội dung…';
            }
        }

        if (skeletonTimer) {
            clearTimeout(skeletonTimer);
            skeletonTimer = null;
        }

        const targetId = targetSelector.startsWith('#') ? targetSelector.slice(1) : 'feed-results';
        const currentResults = shell.querySelector(targetSelector)
            || document.getElementById(targetId)
            || document.getElementById('feed-results');

        // Maintain current height during loading transition to prevent height collapse & scroll jump
        if (currentResults) {
            const currentHeight = currentResults.offsetHeight;
            if (currentHeight > 0) {
                currentResults.style.minHeight = `${currentHeight}px`;
            }
            currentResults.setAttribute('aria-busy', 'true');
        }

        // Delay skeleton injection by 160ms threshold: if network request returns quickly, skip skeleton completely (no flicker!)
        skeletonTimer = setTimeout(() => {
            if (sequence === navigationSequence) {
                const liveResults = shell.querySelector(targetSelector)
                    || document.getElementById(targetId)
                    || document.getElementById('feed-results');
                if (liveResults && liveResults.getAttribute('aria-busy') === 'true') {
                    showSkeleton(liveResults);
                }
            }
        }, 160);

        // 4. Dispatch feed-loading event for interaction state hydration
        document.dispatchEvent(new CustomEvent('blogmnm:feed-loading', {
            detail: { navigationId: sequence },
        }));

        try {
            const result = await load(url.href);
            if (!result || sequence !== navigationSequence) {
                return;
            }

            if (skeletonTimer) {
                clearTimeout(skeletonTimer);
                skeletonTimer = null;
            }

            const parsed = new DOMParser().parseFromString(result.html, 'text/html');
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
            const activeResults = shell.querySelector(targetSelector)
                || document.getElementById(targetId)
                || document.getElementById('feed-results');

            if (activeResults) {
                activeResults.replaceWith(nextResults);
            }
            nextResults.removeAttribute('aria-busy');
            nextResults.style.opacity = '1';
            nextResults.style.pointerEvents = '';

            // Cleanly release minHeight on next paint so natural height takes over smoothly (no empty white space)
            requestAnimationFrame(() => {
                if (nextResults) {
                    nextResults.style.minHeight = '';
                }
            });

            // 6. Synchronize History & URL
            const nextState = {
                ...window.history.state,
                blogmnmFeed: true,
                feedScope: isHomeOrFollowingShell ? 'home' : cleanPath(baseUrl.pathname),
                url: resolvedUrl.href,
                category: isFollowingTarget ? '' : targetCategory,
            };

            if (historyMode === 'push' && resolvedUrl.href !== window.location.href) {
                window.history.pushState(nextState, '', resolvedUrl);
            } else if (historyMode === 'replace' && resolvedUrl.href !== window.location.href) {
                window.history.replaceState(nextState, '', resolvedUrl);
            }

            currentUrl = resolvedUrl;
            previousCategory = isFollowingTarget ? '' : targetCategory;
            syncTopTabs(resolvedUrl);
            syncCategoryNav(resolvedUrl);
            syncCategoryChips(isFollowingTarget ? '' : targetCategory);
            syncTabs(resolvedUrl);
            syncFilterFormInputs(resolvedUrl);
            syncSidebarAndMobileNav(resolvedUrl);
            syncDocumentTitle(resolvedUrl);

            // Sync status counts if provided by response fragment
            if (nextResults.dataset.feedCounts) {
                try {
                    const counts = JSON.parse(nextResults.dataset.feedCounts);
                    shell.querySelectorAll('[data-feed-count]').forEach((span) => {
                        const key = span.dataset.feedCount;
                        if (counts[key] !== undefined) {
                            span.textContent = `(${new Intl.NumberFormat().format(counts[key])})`;
                        }
                    });
                } catch {
                    // Ignore parse error
                }
            }

            // 7. Scroll adjustment on pagination ONLY (keep scroll intact when switching top tabs)
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
            if (skeletonTimer) {
                clearTimeout(skeletonTimer);
                skeletonTimer = null;
            }

            if (sequence !== navigationSequence) {
                return;
            }

            if (error.redirectUrl) {
                window.location.assign(error.redirectUrl);
                return;
            }

            // Roll back optimistic UI
            syncTopTabs(currentUrl);
            syncCategoryNav(currentUrl);
            syncCategoryChips(prevCategoryBackup);
            previousCategory = prevCategoryBackup;
            syncTabs(currentUrl);
            syncFilterFormInputs(currentUrl);
            syncSidebarAndMobileNav(currentUrl);
            syncDocumentTitle(currentUrl);

            if (status) {
                status.textContent = '';
            }

            const currentResults = shell.querySelector(targetSelector) || document.getElementById('feed-results');
            if (currentResults) {
                currentResults.removeAttribute('aria-busy');
                currentResults.style.minHeight = '';
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

    // Event Delegation: Intercept top feed tabs, category chips, pagination links, and clear filters
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

        // Top Feed Tabs (e.g. "Dành cho bạn" vs "Đang theo dõi")
        const topTabLink = event.target.closest('a.feed-tab, a[data-feed-type]');
        if (topTabLink && !topTabLink.target && !topTabLink.hasAttribute('download')) {
            const url = new URL(topTabLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                if (url.href === currentUrl.href) {
                    event.preventDefault();
                    return;
                }

                event.preventDefault();
                navigate(url, { historyMode: 'push', isPagination: false, isTopTab: true });
                return;
            }
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

        // View switcher sub-tab links and status tab links
        const tabLink = event.target.closest('a[data-feed-tab]');
        if (tabLink && !tabLink.target && !tabLink.hasAttribute('download')) {
            const url = new URL(tabLink.href, window.location.href);
            if (supportsUrl(url) && !url.hash) {
                // If filter form has search query or category, ensure it is retained on status switch
                const filterForm = shell.querySelector('form[data-feed-filter-form]');
                if (filterForm) {
                    const qInput = filterForm.querySelector('input[name="q"], input[name="search"]');
                    if (qInput && qInput.value.trim() && !url.searchParams.has('q') && !url.searchParams.has('search')) {
                        url.searchParams.set(qInput.name || 'q', qInput.value.trim());
                    }
                    const catSelect = filterForm.querySelector('select[name="category_id"]');
                    if (catSelect && catSelect.value && !url.searchParams.has('category_id')) {
                        url.searchParams.set('category_id', catSelect.value);
                    }
                }

                // Reset page on status tab switch
                url.searchParams.delete('page');

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

    // Filter form submission (Search & Dropdowns)
    shell.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-feed-filter-form], form[role="search"]');
        if (!form || form.method.toUpperCase() !== 'GET') {
            return;
        }
        if (event.defaultPrevented) {
            return;
        }
        event.preventDefault();

        const formAction = form.getAttribute('action') || window.location.href;
        const formUrl = new URL(formAction, window.location.href);
        const formData = new FormData(form);

        for (const [key, val] of formData.entries()) {
            if (typeof val === 'string' && val.trim() !== '') {
                formUrl.searchParams.set(key, val.trim());
            } else {
                formUrl.searchParams.delete(key);
            }
        }

        // Reset page to 1 on new search/filter submission
        formUrl.searchParams.delete('page');

        if (supportsUrl(formUrl)) {
            navigate(formUrl, { historyMode: 'push', isPagination: false });
        } else {
            window.location.assign(formUrl.href);
        }
    });

    // Auto-submit dropdowns in filter forms
    shell.addEventListener('change', (event) => {
        const select = event.target.closest('form[data-feed-filter-form] select[data-feed-auto-submit]');
        if (select && select.form) {
            if (typeof select.form.requestSubmit === 'function') {
                select.form.requestSubmit();
            } else {
                select.form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        }
    });

    // Initialize styling on shell ready
    syncTopTabs(currentUrl);
    syncCategoryNav(currentUrl);
    syncTabs(currentUrl);
    syncFilterFormInputs(currentUrl);

    // History API popstate (Browser Back / Forward)
    window.history.replaceState(
        {
            ...window.history.state,
            blogmnmFeed: true,
            feedScope: isHomeOrFollowingShell ? 'home' : cleanPath(baseUrl.pathname),
            url: window.location.href,
            category: previousCategory,
        },
        '',
        window.location.href
    );

    const onPopState = () => {
        if (typeof document !== 'undefined' && !document.contains(shell)) {
            window.removeEventListener('popstate', onPopState);
            return;
        }
        const url = new URL(window.location.href);
        if (supportsUrl(url)) {
            navigate(url, { historyMode: 'none', isPagination: url.searchParams.has('page') });
        }
    };

    window.addEventListener('popstate', onPopState);
}

export function initFeedNavigation(root = document) {
    if (!root) return;
    if (root.hasAttribute && root.hasAttribute('data-feed-shell')) {
        initFeedShell(root);
    }
    const shells = root.querySelectorAll ? root.querySelectorAll('[data-feed-shell]') : [];
    shells.forEach((shell) => initFeedShell(shell));
}

if (typeof window !== 'undefined') {
    window.initFeedNavigation = initFeedNavigation;
}
