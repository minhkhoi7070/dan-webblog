// BlogMNM Theme Management System (Light / Dark / System)
export const BlogMNMTheme = {
    STORAGE_KEY: 'blogmnm-theme',

    getMode() {
        try {
            return localStorage.getItem(this.STORAGE_KEY) || 'dark';
        } catch (e) {
            return 'dark';
        }
    },

    getResolvedTheme() {
        const mode = this.getMode();
        if (mode === 'system') {
            return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
        }
        return mode === 'light' ? 'light' : 'dark';
    },

    // Backward-compatible alias
    getTheme() {
        return this.getResolvedTheme();
    },

    setTheme(mode) {
        if (mode !== 'light' && mode !== 'dark' && mode !== 'system') {
            mode = 'system';
        }
        try {
            localStorage.setItem(this.STORAGE_KEY, mode);
        } catch (e) {
            console.warn('Unable to persist theme to localStorage', e);
        }
        this.applyTheme(mode);
        window.dispatchEvent(new CustomEvent('blogmnm:theme-changed', {
            detail: { mode, theme: this.getResolvedTheme() }
        }));
    },

    applyTheme(mode) {
        if (!mode) mode = this.getMode();
        const resolved = mode === 'system'
            ? ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light')
            : mode;

        document.documentElement.setAttribute('data-theme', resolved);
        document.documentElement.setAttribute('data-theme-mode', mode);

        if (resolved === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        this.updateAria();
    },

    updateAria() {
        if (typeof document === 'undefined') return;
        const resolved = this.getResolvedTheme();
        const lightBtn = document.querySelector('.sidebar-theme-toggle__btn--light');
        const darkBtn = document.querySelector('.sidebar-theme-toggle__btn--dark');
        const compactBtn = document.querySelector('.sidebar-theme-compact__btn');

        if (lightBtn && darkBtn) {
            lightBtn.setAttribute('aria-pressed', resolved === 'light' ? 'true' : 'false');
            darkBtn.setAttribute('aria-pressed', resolved === 'dark' ? 'true' : 'false');
        }
        if (compactBtn) {
            const nextTheme = resolved === 'dark' ? 'Sáng' : 'Tối';
            compactBtn.setAttribute('aria-label', `Chuyển sang giao diện ${nextTheme}`);
            compactBtn.setAttribute('title', `Chuyển sang giao diện ${nextTheme}`);
        }
    },

    handleKeydown(e) {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            e.preventDefault();
            this.setTheme('light');
            document.querySelector('.sidebar-theme-toggle__btn--light')?.focus();
        } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            e.preventDefault();
            this.setTheme('dark');
            document.querySelector('.sidebar-theme-toggle__btn--dark')?.focus();
        }
    },

    toggle() {
        const currentResolved = this.getResolvedTheme();
        const nextMode = currentResolved === 'dark' ? 'light' : 'dark';
        this.setTheme(nextMode);
        return nextMode;
    },

    init() {
        const mode = this.getMode();
        this.applyTheme(mode);

        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.getMode() === 'system') {
                    this.applyTheme('system');
                    window.dispatchEvent(new CustomEvent('blogmnm:theme-changed', {
                        detail: { mode: 'system', theme: this.getResolvedTheme() }
                    }));
                }
            });
        }

        if (typeof document !== 'undefined') {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.updateAria());
            } else {
                this.updateAria();
            }
        }
    }
};

// Reading Experience Preferences (Font Size & Content Width)
export function syncReadingSettingsUI() {
    if (typeof document === 'undefined') return;

    const currentFont = BlogMNMReading.getFontSize();
    const currentWidth = BlogMNMReading.getWidth();

    document.querySelectorAll('#reading-font-group [data-font]').forEach((btn) => {
        const isCurrent = btn.getAttribute('data-font') === currentFont;
        btn.classList.toggle('active', isCurrent);
        btn.setAttribute('aria-checked', isCurrent ? 'true' : 'false');
    });

    document.querySelectorAll('#reading-width-group [data-width]').forEach((btn) => {
        const isCurrent = btn.getAttribute('data-width') === currentWidth;
        btn.classList.toggle('active', isCurrent);
        btn.setAttribute('aria-checked', isCurrent ? 'true' : 'false');
    });
}

export const BlogMNMReading = {
    FONT_KEY: 'blogmnm-reading-font',
    WIDTH_KEY: 'blogmnm-reading-width',

    getFontSize() {
        try {
            return localStorage.getItem(this.FONT_KEY) || 'md';
        } catch (e) {
            return 'md';
        }
    },

    setFontSize(size) {
        if (!['sm', 'md', 'lg'].includes(size)) {
            size = 'md';
        }
        try {
            localStorage.setItem(this.FONT_KEY, size);
        } catch (e) {
            console.warn('Unable to persist reading font size to localStorage', e);
        }
        this.applyFontSize(size);
        syncReadingSettingsUI();
        window.dispatchEvent(new CustomEvent('blogmnm:reading-font-changed', { detail: { size } }));
    },

    applyFontSize(size) {
        if (!size) size = this.getFontSize();
        if (typeof document !== 'undefined') {
            document.documentElement.setAttribute('data-reading-font', size);
        }
    },

    getWidth() {
        try {
            return localStorage.getItem(this.WIDTH_KEY) || 'standard';
        } catch (e) {
            return 'standard';
        }
    },

    setWidth(width) {
        if (!['standard', 'comfortable', 'wide'].includes(width)) {
            width = 'standard';
        }
        try {
            localStorage.setItem(this.WIDTH_KEY, width);
        } catch (e) {
            console.warn('Unable to persist reading width to localStorage', e);
        }
        this.applyWidth(width);
        syncReadingSettingsUI();
        window.dispatchEvent(new CustomEvent('blogmnm:reading-width-changed', { detail: { width } }));
    },

    applyWidth(width) {
        if (!width) width = this.getWidth();
        if (typeof document !== 'undefined') {
            document.documentElement.setAttribute('data-reading-width', width);
        }
    },

    init() {
        this.applyFontSize();
        this.applyWidth();
        syncReadingSettingsUI();
    }
};

if (typeof window !== 'undefined') {
    window.BlogMNMTheme = BlogMNMTheme;
    window.BlogMNMReading = BlogMNMReading;
    window.syncReadingSettingsUI = syncReadingSettingsUI;
}

if (typeof document !== 'undefined') {
    BlogMNMTheme.init();
    BlogMNMReading.init();

    // Delegated click handler for Reading Settings controls (resilient to AJAX Navigation)
    document.addEventListener('click', (e) => {
        const fontBtn = e.target.closest('#reading-font-group [data-font]');
        if (fontBtn) {
            e.preventDefault();
            const size = fontBtn.getAttribute('data-font');
            BlogMNMReading.setFontSize(size);
            return;
        }

        const widthBtn = e.target.closest('#reading-width-group [data-width]');
        if (widthBtn) {
            e.preventDefault();
            const width = widthBtn.getAttribute('data-width');
            BlogMNMReading.setWidth(width);
            return;
        }
    });

    // Re-sync active states after AJAX Navigation or Feed Swaps
    document.addEventListener('blogmnm:page-navigated', () => {
        BlogMNMReading.applyFontSize();
        BlogMNMReading.applyWidth();
        syncReadingSettingsUI();
    });

    document.addEventListener('blogmnm:feed-updated', () => {
        syncReadingSettingsUI();
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            syncReadingSettingsUI();
        });
    } else {
        syncReadingSettingsUI();
    }
}

