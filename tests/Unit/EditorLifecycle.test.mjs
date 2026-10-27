import test from 'node:test';
import assert from 'node:assert/strict';
import { isEligibleLink, confirmNavigationLeave } from '../../resources/js/app-navigation.js';
import { isPostEditorDirty, setPostEditorDirty, destroyPostEditor } from '../../resources/js/editor.js';

function createMockElement(tagName = 'a', classes = [], attributes = {}) {
    return {
        tagName: tagName.toUpperCase(),
        classList: {
            contains: (c) => classes.includes(c),
            toggle: () => {},
            add: (c) => { if (!classes.includes(c)) classes.push(c); },
            remove: (c) => {
                const idx = classes.indexOf(c);
                if (idx !== -1) classes.splice(idx, 1);
            },
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
            if (selector === '.btn-cancel-action' && classes.includes('btn-cancel-action')) return true;
            if (selector === '.dashboard-create-btn' && classes.includes('dashboard-create-btn')) return true;
            return null;
        },
        href: attributes.href || '',
        target: attributes.target || '',
        innerHTML: '',
        className: classes.join(' '),
    };
}

test('isEligibleLink allows sidebar "Viết bài" route when data-no-app-nav is removed', () => {
    const createPostSidebarLink = createMockElement('a', ['sidebar-link'], {
        href: 'http://localhost/posts/create',
        'data-in-sidebar': 'true',
    });

    const isEligible = isEligibleLink(createPostSidebarLink, { button: 0 });
    assert.equal(isEligible, true);
});

test('isEligibleLink allows cancel buttons and dashboard create button via action nav', () => {
    const cancelBtn = createMockElement('a', ['btn-cancel-action', 'btn'], {
        href: 'http://localhost/posts/stats',
    });
    assert.equal(isEligibleLink(cancelBtn, { button: 0 }), true);

    const dashboardCreateBtn = createMockElement('a', ['dashboard-create-btn'], {
        href: 'http://localhost/posts/create',
    });
    assert.equal(isEligibleLink(dashboardCreateBtn, { button: 0 }), true);

    const explicitAppNav = createMockElement('a', [], {
        href: 'http://localhost/posts/stats',
        'data-app-nav': 'true',
    });
    assert.equal(isEligibleLink(explicitAppNav, { button: 0 }), true);
});

test('Editor dirty state lifecycle: tracks unsaved changes and resets on destroy', () => {
    // 1. Initial state must be clean
    destroyPostEditor();
    assert.equal(isPostEditorDirty(), false);

    // 2. Setting dirty state
    setPostEditorDirty(true);
    assert.equal(isPostEditorDirty(), true);

    // 3. Destroying editor must cleanup and reset dirty state
    destroyPostEditor();
    assert.equal(isPostEditorDirty(), false);
});

test('confirmNavigationLeave uses fallback window.confirm when Bootstrap modal is unavailable', async () => {
    let confirmCalled = false;
    let confirmPrompt = '';

    global.window = {
        confirm: (msg) => {
            confirmCalled = true;
            confirmPrompt = msg;
            return true;
        },
    };
    global.document = {
        getElementById: () => null,
    };

    const result = await confirmNavigationLeave({
        title: 'Rời khỏi trang?',
        message: 'Bạn có thay đổi chưa lưu.',
        confirmText: 'Rời trang',
        cancelText: 'Tiếp tục chỉnh sửa',
    });

    assert.equal(confirmCalled, true);
    assert.equal(result, true);
    assert.match(confirmPrompt, /chưa lưu/);
});

test('confirmNavigationLeave resolves to false when user chooses to stay ("Tiếp tục chỉnh sửa")', async () => {
    global.window = {
        confirm: () => false,
    };
    global.document = {
        getElementById: () => null,
    };

    const result = await confirmNavigationLeave({
        title: 'Rời khỏi trang?',
        message: 'Bạn có thay đổi chưa lưu.',
        confirmText: 'Rời trang',
        cancelText: 'Tiếp tục chỉnh sửa',
    });

    assert.equal(result, false);
});

test('Editor can undergo consecutive navigation cycles (Viết bài <-> Trang chủ <-> Viết bài) cleanly', () => {
    for (let cycle = 1; cycle <= 5; cycle++) {
        // Entering editor
        destroyPostEditor();
        assert.equal(isPostEditorDirty(), false, `Cycle ${cycle}: start clean`);

        // User types content
        setPostEditorDirty(true);
        assert.equal(isPostEditorDirty(), true, `Cycle ${cycle}: dirty when editing`);

        // User leaves editor and it is destroyed
        destroyPostEditor();
        assert.equal(isPostEditorDirty(), false, `Cycle ${cycle}: clean after navigation away`);
    }
});
