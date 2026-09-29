// BlogMNM Theme Management System
export const BlogMNMTheme = {
    STORAGE_KEY: 'blogmnm-theme',

    getTheme() {
        try {
            return localStorage.getItem(this.STORAGE_KEY) || 'dark';
        } catch (e) {
            return 'dark';
        }
    },

    setTheme(theme) {
        if (theme !== 'light' && theme !== 'dark') {
            theme = 'dark';
        }
        try {
            localStorage.setItem(this.STORAGE_KEY, theme);
        } catch (e) {
            console.warn('Unable to persist theme to localStorage', e);
        }
        this.applyTheme(theme);
        window.dispatchEvent(new CustomEvent('blogmnm:theme-changed', { detail: { theme } }));
    },

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    toggle() {
        const next = this.getTheme() === 'dark' ? 'light' : 'dark';
        this.setTheme(next);
        return next;
    },

    init() {
        const theme = this.getTheme();
        this.applyTheme(theme);
    }
};

window.BlogMNMTheme = BlogMNMTheme;

if (typeof document !== 'undefined') {
    BlogMNMTheme.init();
}
