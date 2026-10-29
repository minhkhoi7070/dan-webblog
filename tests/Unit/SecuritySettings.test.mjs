import test from 'node:test';
import assert from 'node:assert/strict';
import { initSecuritySettings, _resetSecuritySettingsForTesting } from '../../resources/js/security-settings.js';

test('Password Toggle: toggles type between password and text and switches eye icons', () => {
    const listeners = {};
    global.document = {
        addEventListener: (event, cb) => {
            listeners[event] = listeners[event] || [];
            listeners[event].push(cb);
        },
        dispatchEvent: (event) => {
            const handlers = listeners[event.type] || [];
            handlers.forEach(h => h(event));
        },
        getElementById: (id) => mockInput,
    };

    const eyeOpen = {
        classList: {
            toggle: (cls, force) => {
                if (force) eyeOpenClasses.add(cls);
                else eyeOpenClasses.delete(cls);
            },
            remove: (cls) => eyeOpenClasses.delete(cls),
            add: (cls) => eyeOpenClasses.add(cls),
        }
    };
    const eyeOpenClasses = new Set();

    const eyeClosed = {
        classList: {
            toggle: (cls, force) => {
                if (force) eyeClosedClasses.add(cls);
                else eyeClosedClasses.delete(cls);
            },
            remove: (cls) => eyeClosedClasses.delete(cls),
            add: (cls) => eyeClosedClasses.add(cls),
        }
    };
    const eyeClosedClasses = new Set(['d-none']);

    const mockInput = {
        type: 'password',
        id: 'test-password',
        name: 'password',
    };

    let ariaLabel = 'Hiện mật khẩu';
    const mockButton = {
        dataset: { target: 'test-password' },
        closest: (selector) => {
            if (selector === '.password-toggle-btn') return mockButton;
            return null;
        },
        querySelector: (selector) => {
            if (selector === '.eye-open-icon') return eyeOpen;
            if (selector === '.eye-closed-icon') return eyeClosed;
            return null;
        },
        setAttribute: (k, v) => {
            if (k === 'aria-label') ariaLabel = v;
        },
    };

    initSecuritySettings();

    // Trigger click on toggle button
    const clickHandler = listeners['click']?.[0];
    assert.ok(clickHandler, 'Click listener should be registered');

    // 1st click: should reveal password
    clickHandler({
        target: mockButton,
        preventDefault: () => {},
        stopPropagation: () => {},
    });

    assert.equal(mockInput.type, 'text');
    assert.equal(ariaLabel, 'Ẩn mật khẩu');
    assert.ok(eyeOpenClasses.has('d-none'), 'eye-open should be hidden');
    assert.ok(!eyeClosedClasses.has('d-none'), 'eye-closed should be visible');

    // 2nd click: should hide password
    clickHandler({
        target: mockButton,
        preventDefault: () => {},
        stopPropagation: () => {},
    });

    assert.equal(mockInput.type, 'password');
    assert.equal(ariaLabel, 'Hiện mật khẩu');
    assert.ok(!eyeOpenClasses.has('d-none'), 'eye-open should be visible');
    assert.ok(eyeClosedClasses.has('d-none'), 'eye-closed should be hidden');
});

test('Form Submission: prevents duplicate submit and adds loading spinner', () => {
    _resetSecuritySettingsForTesting();
    const listeners = {};
    global.document = {
        addEventListener: (event, cb) => {
            listeners[event] = listeners[event] || [];
            listeners[event].push(cb);
        },
    };

    initSecuritySettings();

    let btnDisabled = false;
    let btnHtml = '<span>Cập nhật mật khẩu</span>';
    const mockSubmitBtn = {
        id: 'btn-update-password',
        disabled: false,
        set disabled(val) { btnDisabled = val; },
        get disabled() { return btnDisabled; },
        set innerHTML(val) { btnHtml = val; },
        get innerHTML() { return btnHtml; },
    };

    const mockForm = {
        id: 'password-update-form',
        dataset: {},
        closest: (sel) => sel === '#password-update-form' ? mockForm : null,
        querySelector: (sel) => sel === '#btn-update-password' ? mockSubmitBtn : null,
    };

    // Click submit
    const submitHandler = listeners['submit']?.[0];
    assert.ok(submitHandler);

    let defaultPrevented = false;
    submitHandler({
        target: mockForm,
        preventDefault: () => { defaultPrevented = true; },
    });

    assert.equal(mockForm.dataset.submitting, 'true');
    assert.equal(mockSubmitBtn.disabled, true);
    assert.ok(mockSubmitBtn.innerHTML.includes('Đang cập nhật...'));
    assert.equal(defaultPrevented, false);

    // Second submit attempt must be prevented
    defaultPrevented = false;
    submitHandler({
        target: mockForm,
        preventDefault: () => { defaultPrevented = true; },
    });
    assert.equal(defaultPrevented, true, 'Duplicate submission should be aborted');
});
