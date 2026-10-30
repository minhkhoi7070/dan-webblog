/**
 * CloudBlog Tablet Sidebar Toggle Controller
 *
 * Controls expanding/collapsing the collapsed tablet sidebar (768px <= viewport < 992px)
 * into a full navigation sidebar overlay.
 *
 * Features:
 * - A11y: aria-expanded, aria-controls, keyboard navigation (Escape closes & returns focus)
 * - Backdrop overlay with smooth transition
 * - Zero feed layout jitter / shift
 * - Auto-collapse on link navigation while expanded
 * - Auto-reset on resize to Desktop (>= 992px) or Mobile (< 768px)
 * - Safe against race conditions and rapid double-clicks
 */

let isInitialized = false;

export function initTabletSidebar() {
    if (typeof document === 'undefined') {
        return;
    }

    const toggleBtn = document.getElementById('tabletSidebarToggle');
    const sidebar = document.getElementById('appSidebar') || document.querySelector('.sidebar-container');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!toggleBtn || !sidebar) {
        return;
    }

    if (isInitialized) {
        return;
    }
    isInitialized = true;

    const isTabletViewport = () => {
        const width = window.innerWidth;
        return width >= 768 && width < 992;
    };

    const isExpanded = () => sidebar.classList.contains('sidebar-expanded');

    const updateUI = (expanded) => {
        sidebar.classList.toggle('sidebar-expanded', expanded);
        toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        toggleBtn.setAttribute('aria-label', expanded ? 'Thu gọn menu' : 'Mở rộng menu');
        toggleBtn.setAttribute('title', expanded ? 'Thu gọn menu' : 'Mở rộng menu');

        const menuIcon = toggleBtn.querySelector('.sidebar-toggle-icon--menu');
        const closeIcon = toggleBtn.querySelector('.sidebar-toggle-icon--close');
        if (menuIcon && closeIcon) {
            menuIcon.classList.toggle('d-none', expanded);
            closeIcon.classList.toggle('d-none', !expanded);
        }

        if (backdrop) {
            if (expanded) {
                backdrop.classList.remove('d-none');
                // Force layout reflow so transition runs
                void backdrop.offsetWidth;
                backdrop.classList.add('show');
            } else {
                backdrop.classList.remove('show');
                setTimeout(() => {
                    if (!isExpanded() && backdrop) {
                        backdrop.classList.add('d-none');
                    }
                }, 200);
            }
        }
    };

    const expandSidebar = () => {
        if (!isTabletViewport()) {
            return;
        }
        updateUI(true);
    };

    const collapseSidebar = (returnFocus = false) => {
        if (!isExpanded()) {
            return;
        }
        updateUI(false);
        if (returnFocus && toggleBtn && typeof toggleBtn.focus === 'function') {
            toggleBtn.focus();
        }
    };

    const toggleSidebar = () => {
        if (isExpanded()) {
            collapseSidebar();
        } else {
            expandSidebar();
        }
    };

    // Toggle button click listener
    toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });

    // Backdrop click listener
    if (backdrop) {
        backdrop.addEventListener('click', (e) => {
            e.preventDefault();
            collapseSidebar();
        });
    }

    // Escape key listener for accessibility
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isExpanded()) {
            e.preventDefault();
            collapseSidebar(true);
        }
    });

    // Collapse overlay when navigating via links inside sidebar
    sidebar.addEventListener('click', (e) => {
        const link = e.target.closest('.sidebar-link, .sidebar-account__top');
        if (link && isExpanded() && isTabletViewport()) {
            collapseSidebar();
        }
    });

    // Responsive resize listener: reset state if resized outside tablet breakpoint
    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isTabletViewport() && isExpanded()) {
                collapseSidebar();
            }
        }, 100);
    });

    // Global helper object
    window.BlogMNMTabletSidebar = {
        toggle: toggleSidebar,
        expand: expandSidebar,
        collapse: collapseSidebar,
        isExpanded,
    };
}

