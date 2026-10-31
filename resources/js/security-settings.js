/**
 * CloudBlog — Security & Account Settings Interactions
 *
 * Handles:
 * 1. Password Visibility Toggle (Show / Hide eye icon) with accessible ARIA state.
 * 2. Secure cleanup on collapse close (clears unsent password data from DOM).
 * 3. Anti-duplicate submission & loading states for password update.
 * 4. Resilient delegated event handling across AJAX navigation swaps.
 */

let initialized = false;

export function _resetSecuritySettingsForTesting() {
    initialized = false;
}

export function initSecuritySettings() {
    if (initialized) return;
    initialized = true;

    // 1. Password Visibility Toggle (Delegated on document)
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.password-toggle-btn');
        if (!toggleBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const targetId = toggleBtn.dataset.target;
        const container = toggleBtn.closest('.password-input-group');
        const input = (targetId ? document.getElementById(targetId) : null) || container?.querySelector('input');

        if (!input) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        const eyeOpen = toggleBtn.querySelector('.eye-open-icon');
        const eyeClosed = toggleBtn.querySelector('.eye-closed-icon');

        if (eyeOpen && eyeClosed) {
            eyeOpen.classList.toggle('d-none', isPassword);
            eyeClosed.classList.toggle('d-none', !isPassword);
        }

        toggleBtn.setAttribute('aria-label', isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
    });

    // 2. Safe Cleanup when Password Form Collapse Closes
    document.addEventListener('hidden.bs.collapse', (e) => {
        if (e.target && e.target.id === 'change-password-collapse') {
            const form = document.getElementById('password-update-form');
            if (!form) return;

            // Only clear values if there are no server-side validation error classes
            const hasErrors = form.querySelector('.is-invalid') !== null;
            if (!hasErrors) {
                form.reset();

                // Revert all inputs to type="password"
                form.querySelectorAll('input').forEach((input) => {
                    if (input.type === 'text' && input.name && input.name.includes('password')) {
                        input.type = 'password';
                    }
                });

                // Reset eye icons to default (eye open visible)
                form.querySelectorAll('.password-toggle-btn').forEach((btn) => {
                    const eyeOpen = btn.querySelector('.eye-open-icon');
                    const eyeClosed = btn.querySelector('.eye-closed-icon');
                    if (eyeOpen && eyeClosed) {
                        eyeOpen.classList.remove('d-none');
                        eyeClosed.classList.add('d-none');
                    }
                    btn.setAttribute('aria-label', 'Hiện mật khẩu');
                });
            }
        }
    });

    // 3. Form Submission Loading & Anti-Duplicate Guard
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('#password-update-form');
        if (!form) return;

        if (form.dataset.submitting === 'true') {
            e.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';
        const submitBtn = form.querySelector('#btn-update-password');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true" style="width: 14px; height: 14px; border-width: 2px;"></span>
                <span>Đang cập nhật...</span>
            `;
        }
    });

    // 4. Keyboard trigger for interactive rows (Enter / Space on role="button")
    document.addEventListener('keydown', (e) => {
        if (!e.target) return;
        const trigger = e.target.closest('.settings-row-interactive[role="button"]');
        if (trigger && (e.key === 'Enter' || e.key === ' ')) {
            // Prevent scrolling on Space
            e.preventDefault();
            trigger.click();
        }
    });
}
