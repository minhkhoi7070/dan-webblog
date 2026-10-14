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
    }
};

// Reading Experience Preferences (Font Size & Content Width)
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
        window.dispatchEvent(new CustomEvent('blogmnm:reading-font-changed', { detail: { size } }));
    },

    applyFontSize(size) {
        if (!size) size = this.getFontSize();
        document.documentElement.setAttribute('data-reading-font', size);
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
        window.dispatchEvent(new CustomEvent('blogmnm:reading-width-changed', { detail: { width } }));
    },

    applyWidth(width) {
        if (!width) width = this.getWidth();
        document.documentElement.setAttribute('data-reading-width', width);
    },

    init() {
        this.applyFontSize();
        this.applyWidth();
    }
};

window.BlogMNMTheme = BlogMNMTheme;
window.BlogMNMReading = BlogMNMReading;

if (typeof document !== 'undefined') {
    BlogMNMTheme.init();
    BlogMNMReading.init();
}

