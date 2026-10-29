import test from 'node:test';
import assert from 'node:assert/strict';
import { createFeedLoader } from '../../resources/js/feed-navigation.js';

test('createFeedLoader sends X-BlogMNM-Feed and Accept headers', async () => {
    let capturedHeaders = null;
    const fakeFetcher = async (url, options) => {
        capturedHeaders = options.headers;
        return {
            ok: true,
            status: 200,
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => '<div id="feed-results">content</div>',
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);
    const result = await loader('http://localhost/?category=cong-nghe');

    assert.equal(capturedHeaders['X-BlogMNM-Feed'], '1');
    assert.equal(capturedHeaders['Accept'], 'text/html');
    assert.equal(capturedHeaders['X-Requested-With'], 'XMLHttpRequest');
    assert.equal(result.html, '<div id="feed-results">content</div>');
    assert.equal(result.url, 'http://localhost/?category=cong-nghe');
});

test('createFeedLoader handles sequential pagination transitions: page 1 -> 2 -> 3', async () => {
    const urls = [];
    const fakeFetcher = async (url) => {
        urls.push(url);
        return {
            ok: true,
            status: 200,
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => `<div id="feed-results" data-page="${url}">page-content</div>`,
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);

    const r1 = await loader('http://localhost/posts?page=1');
    assert.equal(r1.url, 'http://localhost/posts?page=1');

    const r2 = await loader('http://localhost/posts?page=2');
    assert.equal(r2.url, 'http://localhost/posts?page=2');

    const r3 = await loader('http://localhost/posts?page=3');
    assert.equal(r3.url, 'http://localhost/posts?page=3');

    assert.deepEqual(urls, [
        'http://localhost/posts?page=1',
        'http://localhost/posts?page=2',
        'http://localhost/posts?page=3'
    ]);
});

test('createFeedLoader aborts earlier in-flight request when new request arrives (Race Condition guard)', async () => {
    const abortedSignals = [];
    let resolveFirst;
    const firstPromise = new Promise((resolve) => { resolveFirst = resolve; });

    const fakeFetcher = async (url, options) => {
        abortedSignals.push(options.signal);
        if (url.includes('page=2')) {
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
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => `<div id="feed-results">${url}</div>`,
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);

    // Click 1: Page 2 (slow)
    const p1 = loader('http://localhost/posts?page=2');

    // Click 2: Page 3 (immediate user click)
    const p2 = loader('http://localhost/posts?page=3');

    // Resolve first slow request afterwards
    resolveFirst();

    const [r1, r2] = await Promise.all([p1, p2]);

    // First request must return null (discarded) and its signal must be aborted
    assert.equal(r1, null);
    assert.equal(abortedSignals[0].aborted, true);

    // Second request must succeed with page 3
    assert.notEqual(r2, null);
    assert.match(r2.html, /page=3/);
});

test('createFeedLoader throws on HTTP error response', async () => {
    const fakeFetcher = async () => ({
        ok: false,
        status: 500,
        headers: new Map(),
    });

    const loader = createFeedLoader(fakeFetcher);
    await assert.rejects(
        () => loader('http://localhost/?category=error'),
        (err) => err.status === 500
    );
});

test('createFeedLoader loads Following tab via AJAX with X-BlogMNM-Feed header', async () => {
    let capturedHeaders = null;
    const fakeFetcher = async (url, options) => {
        capturedHeaders = options.headers;
        return {
            ok: true,
            status: 200,
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => '<div id="feed-results" aria-label="Bảng tin đang theo dõi">following-content</div>',
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);
    const result = await loader('http://localhost/following');

    assert.equal(capturedHeaders['X-BlogMNM-Feed'], '1');
    assert.equal(capturedHeaders['Accept'], 'text/html');
    assert.equal(capturedHeaders['X-Requested-With'], 'XMLHttpRequest');
    assert.match(result.html, /Bảng tin đang theo dõi/);
    assert.match(result.html, /following-content/);
    assert.equal(result.url, 'http://localhost/following');
});

test('rapid switching between For You and Following aborts previous request and resolves final tab', async () => {
    const abortedSignals = [];
    let resolveFirst;
    const firstPromise = new Promise((resolve) => { resolveFirst = resolve; });

    const fakeFetcher = async (url, options) => {
        abortedSignals.push(options.signal);
        if (url.includes('/following')) {
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
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => `<div id="feed-results" data-feed="${url}">feed-content</div>`,
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);

    // Click 1: Following tab (slow response)
    const p1 = loader('http://localhost/following');

    // Click 2: Back to For You tab (immediate response)
    const p2 = loader('http://localhost/posts');

    // Resolve first request later
    resolveFirst();

    const [r1, r2] = await Promise.all([p1, p2]);

    assert.equal(r1, null);
    assert.equal(abortedSignals[0].aborted, true);
    assert.notEqual(r2, null);
    assert.match(r2.html, /posts/);
});

test('createFeedLoader preserves query parameters during pagination requests', async () => {
    let requestedUrl = null;
    const fakeFetcher = async (url) => {
        requestedUrl = url;
        return {
            ok: true,
            status: 200,
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => '<div id="feed-results">results</div>',
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);
    const targetUrl = 'http://localhost/admin/users?role=admin&status=active&q=test&page=3';
    const result = await loader(targetUrl);

    assert.equal(requestedUrl, targetUrl);
    const parsed = new URL(result.url);
    assert.equal(parsed.searchParams.get('role'), 'admin');
    assert.equal(parsed.searchParams.get('status'), 'active');
    assert.equal(parsed.searchParams.get('q'), 'test');
    assert.equal(parsed.searchParams.get('page'), '3');
});

test('rapid status tabs switching aborts intermediate requests and resolves final tab', async () => {
    const urls = [];
    let resolveFirst;
    const slowPromise = new Promise((resolve) => { resolveFirst = resolve; });

    const fakeFetcher = async (url, options) => {
        urls.push(url);
        if (url.includes('status=pending')) {
            await slowPromise;
            if (options.signal.aborted) {
                const err = new Error('The operation was aborted');
                err.name = 'AbortError';
                throw err;
            }
        }
        return {
            ok: true,
            status: 200,
            headers: new Map([['X-BlogMNM-Feed', '1']]),
            text: async () => `<div id="feed-results" data-url="${url}">results</div>`,
            url,
        };
    };

    const loader = createFeedLoader(fakeFetcher);

    // Click 1: pending (slow)
    const p1 = loader('http://localhost/admin/posts?status=pending');
    // Click 2: draft (quick)
    const p2 = loader('http://localhost/admin/posts?status=draft');
    // Click 3: published (final target)
    const p3 = loader('http://localhost/admin/posts?status=published');

    resolveFirst();

    const [r1, r2, r3] = await Promise.all([p1, p2, p3]);

    assert.equal(r1, null);
    assert.equal(r2, null);
    assert.notEqual(r3, null);
    assert.match(r3.html, /status=published/);
});
