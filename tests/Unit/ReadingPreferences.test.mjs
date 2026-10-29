import test from 'node:test';
import assert from 'node:assert/strict';
import { BlogMNMReading, syncReadingSettingsUI } from '../../resources/js/theme.js';

// Setup minimal DOM mocks for Node.js test environment
const createMockDocument = () => {
    const storage = {};
    global.localStorage = {
        getItem: (k) => storage[k] || null,
        setItem: (k, v) => { storage[k] = String(v); },
        removeItem: (k) => { delete storage[k]; },
        clear: () => { Object.keys(storage).forEach(k => delete storage[k]); },
    };

    const docAttrs = {};
    const docElement = {
        setAttribute: (k, v) => { docAttrs[k] = String(v); },
        getAttribute: (k) => docAttrs[k] || null,
        removeAttribute: (k) => { delete docAttrs[k]; },
    };

    const createButton = (type, val) => {
        let classes = new Set(['settings-segmented-btn']);
        let attrs = { [type]: val };
        return {
            getAttribute: (k) => attrs[k] || null,
            setAttribute: (k, v) => { attrs[k] = String(v); },
            classList: {
                toggle: (cls, force) => {
                    if (force) classes.add(cls);
                    else classes.delete(cls);
                },
                contains: (cls) => classes.has(cls),
            },
        };
    };

    const fontButtons = [
        createButton('data-font', 'sm'),
        createButton('data-font', 'md'),
        createButton('data-font', 'lg'),
    ];

    const widthButtons = [
        createButton('data-width', 'standard'),
        createButton('data-width', 'comfortable'),
        createButton('data-width', 'wide'),
    ];

    global.document = {
        documentElement: docElement,
        querySelectorAll: (selector) => {
            if (selector.includes('#reading-font-group')) return fontButtons;
            if (selector.includes('#reading-width-group')) return widthButtons;
            return [];
        },
        addEventListener: () => {},
    };

    global.window = {
        dispatchEvent: () => {},
        addEventListener: () => {},
    };

    return { storage, docAttrs, fontButtons, widthButtons };
};

test('Reading Preferences: defaults to md font and standard width', () => {
    const env = createMockDocument();
    assert.equal(BlogMNMReading.getFontSize(), 'md');
    assert.equal(BlogMNMReading.getWidth(), 'standard');
});

test('Reading Preferences: changes and persists font size (sm, md, lg)', () => {
    const env = createMockDocument();

    // 1. Set to small (15px)
    BlogMNMReading.setFontSize('sm');
    assert.equal(BlogMNMReading.getFontSize(), 'sm');
    assert.equal(env.storage['blogmnm-reading-font'], 'sm');
    assert.equal(env.docAttrs['data-reading-font'], 'sm');

    // Button active state sync
    syncReadingSettingsUI();
    assert.equal(env.fontButtons[0].classList.contains('active'), true);
    assert.equal(env.fontButtons[1].classList.contains('active'), false);
    assert.equal(env.fontButtons[2].classList.contains('active'), false);

    // 2. Set to large (20px)
    BlogMNMReading.setFontSize('lg');
    assert.equal(BlogMNMReading.getFontSize(), 'lg');
    assert.equal(env.storage['blogmnm-reading-font'], 'lg');
    assert.equal(env.docAttrs['data-reading-font'], 'lg');

    syncReadingSettingsUI();
    assert.equal(env.fontButtons[0].classList.contains('active'), false);
    assert.equal(env.fontButtons[1].classList.contains('active'), false);
    assert.equal(env.fontButtons[2].classList.contains('active'), true);

    // 3. Fallback for invalid value
    BlogMNMReading.setFontSize('huge');
    assert.equal(BlogMNMReading.getFontSize(), 'md');
});

test('Reading Preferences: changes and persists reading width (standard, comfortable, wide)', () => {
    const env = createMockDocument();

    // 1. Standard (680px)
    BlogMNMReading.setWidth('standard');
    assert.equal(BlogMNMReading.getWidth(), 'standard');
    assert.equal(env.storage['blogmnm-reading-width'], 'standard');
    assert.equal(env.docAttrs['data-reading-width'], 'standard');

    syncReadingSettingsUI();
    assert.equal(env.widthButtons[0].classList.contains('active'), true);
    assert.equal(env.widthButtons[1].classList.contains('active'), false);
    assert.equal(env.widthButtons[2].classList.contains('active'), false);

    // 2. Comfortable (760px)
    BlogMNMReading.setWidth('comfortable');
    assert.equal(BlogMNMReading.getWidth(), 'comfortable');
    assert.equal(env.storage['blogmnm-reading-width'], 'comfortable');
    assert.equal(env.docAttrs['data-reading-width'], 'comfortable');

    syncReadingSettingsUI();
    assert.equal(env.widthButtons[0].classList.contains('active'), false);
    assert.equal(env.widthButtons[1].classList.contains('active'), true);
    assert.equal(env.widthButtons[2].classList.contains('active'), false);

    // 3. Wide (860px)
    BlogMNMReading.setWidth('wide');
    assert.equal(BlogMNMReading.getWidth(), 'wide');
    assert.equal(env.storage['blogmnm-reading-width'], 'wide');
    assert.equal(env.docAttrs['data-reading-width'], 'wide');

    syncReadingSettingsUI();
    assert.equal(env.widthButtons[0].classList.contains('active'), false);
    assert.equal(env.widthButtons[1].classList.contains('active'), false);
    assert.equal(env.widthButtons[2].classList.contains('active'), true);

    // 4. Fallback for invalid value
    BlogMNMReading.setWidth('ultrawide');
    assert.equal(BlogMNMReading.getWidth(), 'standard');
});

