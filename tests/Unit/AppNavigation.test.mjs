import test from 'node:test';
import assert from 'node:assert/strict';
import { isEligibleLink, createAppNavigator, updateMobileHeader } from '../../resources/js/app-navigation.js';

function createMockElement(tagName = 'a', classes = [], attributes = {}) {
    return {
        tagName: tagName.toUpperCase(),
        classList: {
            contains: (c) => classes.includes(c),
            toggle: (c, val) => {},
            add: (c) => {},
            remove: (c) => {},
        },
        attributes,
        getAttribute: (attr) => attributes[attr] || null,
        hasAttribute: (attr) => Boolean(attributes[attr]),
        setAttribute: (attr, val) => { attributes[attr] = val; },
        removeAttribute: (attr) => { delete attributes[attr]; },
        closest: (selector) => {
            if (selector === '[data-no-app-nav]' && attributes['data-no-app-nav']) return true;
            if (selector === '[data-feed-pagination]' && attributes['data-feed-pagination']) return true;
            if (selector === '.sidebar-container' && attributes['data-in-sidebar']) return true;
            if (selector === '.mobile-bottom-nav' && attributes['data-in-mobile']) return true;
            if (selector === '[data-app-nav]' && attributes['data-app-nav']) return true;
            return null;
        },
        href: attributes.href || '',
        target: attributes.target || '',
        innerHTML: '',
        className: classes.join(' '),
    };
}

test('isEligibleLink validates same-origin sidebar navigation links', () => {
    const link = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/favorites',
        'data-in-sidebar': 'true',
    });

    const isEligible = isEligibleLink(link, { button: 0 });
    assert.equal(isEligible, true);
});

test('isEligibleLink rejects modifier keys (Ctrl/Meta/Shift/Alt)', () => {
    const link = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/favorites',
        'data-in-sidebar': 'true',
    });

    assert.equal(isEligibleLink(link, { button: 0, ctrlKey: true }), false);
    assert.equal(isEligibleLink(link, { button: 0, metaKey: true }), false);
    assert.equal(isEligibleLink(link, { button: 0, shiftKey: true }), false);
    assert.equal(isEligibleLink(link, { button: 0, altKey: true }), false);
    assert.equal(isEligibleLink(link, { button: 1 }), false); // middle click
});

test('isEligibleLink rejects target blank, downloads, and data-no-app-nav', () => {
    const blankLink = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/favorites',
        target: '_blank',
        'data-in-sidebar': 'true',
    });
    assert.equal(isEligibleLink(blankLink, { button: 0 }), false);

    const downloadLink = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/favorites',
        download: 'file.pdf',
        'data-in-sidebar': 'true',
    });
    assert.equal(isEligibleLink(downloadLink, { button: 0 }), false);

    const noAppLink = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/posts/create',
        'data-no-app-nav': 'true',
        'data-in-sidebar': 'true',
    });
    assert.equal(isEligibleLink(noAppLink, { button: 0 }), false);
});

test('isEligibleLink excludes feed-pagination links (delegated to feed-navigation.js)', () => {
    const paginationLink = createMockElement('a', ['page-link'], {
        href: 'http://localhost/posts?page=2',
        'data-feed-pagination': 'true',
    });
    assert.equal(isEligibleLink(paginationLink, { button: 0 }), false);

    const categoryChip = createMockElement('a', ['category-chip'], {
        href: 'http://localhost/?category=cong-nghe',
        'data-feed-category': 'cong-nghe',
    });
    assert.equal(isEligibleLink(categoryChip, { button: 0 }), false);

    const feedTabLink = createMockElement('a', ['feed-tab'], {
        href: 'http://localhost/following',
        'data-feed-type': 'following',
    });
    assert.equal(isEligibleLink(feedTabLink, { button: 0 }), false);
});

test('isEligibleLink excludes external domains and auth routes', () => {
    const externalLink = createMockElement('a', ['sidebar-link'], {
        href: 'https://google.com',
        'data-in-sidebar': 'true',
    });
    assert.equal(isEligibleLink(externalLink, { button: 0 }), false);

    const loginLink = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/login',
        'data-in-sidebar': 'true',
    });
    assert.equal(isEligibleLink(loginLink, { button: 0 }), false);
});

test('createAppNavigator aborts earlier in-flight navigation when new link is clicked (Race Condition Guard)', async () => {
    const abortedSignals = [];
    let resolveFirst;
    const firstPromise = new Promise((resolve) => { resolveFirst = resolve; });

    const fakeFetcher = async (url, options) => {
        abortedSignals.push(options.signal);
        if (url.includes('favorites')) {
            await firstPromise;
            if (options.signal.aborted) {
                const err = new Error('The operation was aborted');
                err.name = 'AbortError';
                throw err;
            }
        }
        return {
            ok: true,
            status: 200,
            text: async () => `<main id="main-content">content for ${url}</main>`,
            url,
        };
    };

    const mockMain = {
        innerHTML: 'initial',
        className: 'main-layout',
        setAttribute: () => {},
        classList: { add: () => {}, remove: () => {} },
        removeAttribute: () => {},
    };

    const navigator = createAppNavigator({
        fetcher: fakeFetcher,
        getContentContainer: () => mockMain,
        getProgressBar: () => null,
        parseHtml: (html) => ({
            doc: { title: 'Test Title' },
            main: { innerHTML: html, className: 'main-layout' },
        }),
    });

    // Rapid navigation: Favorites -> Activity
    const p1 = navigator.navigate(new URL('http://localhost/favorites'), { historyMode: 'none' });
    const p2 = navigator.navigate(new URL('http://localhost/activity'), { historyMode: 'none' });

    resolveFirst();

    const [r1, r2] = await Promise.all([p1, p2]);

    // First request must be null (aborted/discarded)
    assert.equal(r1, null);
    assert.equal(abortedSignals[0].aborted, true);

    // Second request must succeed with activity URL
    assert.notEqual(r2, null);
    assert.equal(r2.url, 'http://localhost/activity');
});

test('isEligibleLink allows mobile bottom navigation links', () => {
    const link = createMockElement('a', ['mobile-bottom-nav-link'], {
        href: 'http://localhost/favorites',
        'data-in-mobile': 'true',
    });

    assert.equal(isEligibleLink(link, { button: 0 }), true);
});

test('isEligibleLink allows mobile drawer links with data-app-nav', () => {
    const link = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/activity',
        'data-app-nav': 'true',
    });

    assert.equal(isEligibleLink(link, { button: 0 }), true);
});

test('updateMobileHeader updates header left container for subpages and home', () => {
    const mockLeftEl = {
        innerHTML: '',
    };

    globalThis.document = {
        getElementById: (id) => id === 'mobile-header-left' ? mockLeftEl : null,
    };
    globalThis.window = {
        location: { href: 'http://localhost/' },
    };

    // Subpage: /favorites
    updateMobileHeader('http://localhost/favorites');
    assert.match(mockLeftEl.innerHTML, /Bài viết đã lưu/);
    assert.match(mockLeftEl.innerHTML, /mobile-header-back-btn/);

    // Subpage: /admin/posts
    updateMobileHeader('http://localhost/admin/posts');
    assert.match(mockLeftEl.innerHTML, /Kiểm duyệt bài viết/);

    // Subpage: /posts/create
    updateMobileHeader('http://localhost/posts/create');
    assert.match(mockLeftEl.innerHTML, /Viết bài mới/);

    // Home feed
    updateMobileHeader('http://localhost/');
    assert.match(mockLeftEl.innerHTML, /mobile-header-brand/);
    assert.match(mockLeftEl.innerHTML, /CloudBlog/);

    // Cleanup globals
    delete globalThis.document;
    delete globalThis.window;
});
