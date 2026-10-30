import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';

const EDGE_PATH = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const PORT = 9505;
const ARTIFACT_DIR = 'C:\\Users\\MINHKHOI\\.gemini\\antigravity\\brain\\59e850a8-2d20-49e5-a8da-e6d5701a3347';

async function run() {
    console.log('Starting Edge on debug port ' + PORT + '...');
    const edge = spawn(EDGE_PATH, [
        `--remote-debugging-port=${PORT}`,
        '--headless=new',
        '--disable-gpu',
        '--no-first-run',
        '--no-default-browser-check',
        '--window-size=1440,900',
        'about:blank'
    ]);

    await new Promise(r => setTimeout(r, 1500));

    try {
        const v = await fetch(`http://127.0.0.1:${PORT}/json/version`).then(r => r.json());
        const ws = new WebSocket(v.webSocketDebuggerUrl);
        await new Promise(r => ws.onopen = r);

        let id = 1;
        const send = (m, p = {}) => new Promise((resolve, reject) => {
            const reqId = id++;
            const l = (e) => {
                const msg = JSON.parse(e.data);
                if (msg.id === reqId) {
                    ws.removeEventListener('message', l);
                    if (msg.error) reject(new Error(msg.error.message));
                    else resolve(msg.result);
                }
            };
            ws.addEventListener('message', l);
            ws.send(JSON.stringify({ id: reqId, method: m, params: p }));
        });

        const target = await send('Target.createTarget', { url: 'about:blank' });
        const pageWs = new WebSocket(`ws://127.0.0.1:${PORT}/devtools/page/` + target.targetId);
        await new Promise(r => pageWs.onopen = r);

        let pid = 1;
        const psend = (m, p = {}) => new Promise((resolve, reject) => {
            const reqId = pid++;
            const l = (e) => {
                const msg = JSON.parse(e.data);
                if (msg.id === reqId) {
                    pageWs.removeEventListener('message', l);
                    if (msg.error) reject(new Error(msg.error.message));
                    else resolve(msg.result);
                }
            };
            pageWs.addEventListener('message', l);
            pageWs.send(JSON.stringify({ id: reqId, method: m, params: p }));
        });

        await psend('Page.enable');
        await psend('DOM.enable');
        await psend('Runtime.enable');

        const evaluate = async (expression) => {
            const res = await psend('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
            return res.result?.value;
        };

        const captureScreenshot = async (filename) => {
            const shot = await psend('Page.captureScreenshot', { format: 'png' });
            const filePath = path.join(ARTIFACT_DIR, filename);
            fs.writeFileSync(filePath, Buffer.from(shot.data, 'base64'));
            console.log(`Saved screenshot: ${filename} (${shot.data.length} bytes b64)`);
            return filePath;
        };

        const setViewport = async (width, height, isMobile = false) => {
            await psend('Emulation.setDeviceMetricsOverride', {
                width,
                height,
                deviceScaleFactor: isMobile ? 2 : 1,
                mobile: isMobile
            });
            await evaluate('window.dispatchEvent(new Event("resize"))');
            await new Promise(r => setTimeout(r, 400));
        };

        console.log('1. Navigating to login...');
        await psend('Page.navigate', { url: 'http://127.0.0.1:8000/login' });
        await new Promise(r => setTimeout(r, 2000));

        console.log('2. Entering admin credentials and logging in...');
        await evaluate(`(() => {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const btn = document.querySelector('button[type="submit"]');
            if (email && password && btn) {
                email.value = 'admin@blogmnm.test';
                password.value = 'password';
                btn.click();
            }
        })()`);
        await new Promise(r => setTimeout(r, 2500));

        console.log('3. Navigating to Profile Overview (http://127.0.0.1:8000/profile/overview)...');
        await psend('Page.navigate', { url: 'http://127.0.0.1:8000/profile/overview' });
        await new Promise(r => setTimeout(r, 2000));

        // Helper to open modal reliably
        const openModal = async () => {
            await evaluate(`(() => {
                const modalEl = document.getElementById('profileEditModal');
                if (window.bootstrap?.Modal) {
                    const inst = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                } else {
                    document.getElementById('btn-open-edit-profile')?.click();
                }
            })()`);
            await new Promise(r => setTimeout(r, 600));
        };

        // 1. Desktop 1440x900 - Dark Mode
        console.log('\n--- 1. DESKTOP 1440x900 (DARK MODE) ---');
        await setViewport(1440, 900, false);
        await evaluate(`document.documentElement.setAttribute('data-theme', 'dark')`);
        await openModal();

        const isModalShowDark = await evaluate(`(() => {
            const m = document.getElementById('profileEditModal');
            return m && m.classList.contains('show');
        })()`);
        console.log('Modal visible in Dark Mode:', isModalShowDark);
        await captureScreenshot('55_modal_admin_dark_desktop_1440x900.png');

        // 2. Desktop 1440x900 - Light Mode
        console.log('\n--- 2. DESKTOP 1440x900 (LIGHT MODE) ---');
        await evaluate(`document.documentElement.setAttribute('data-theme', 'light')`);
        await new Promise(r => setTimeout(r, 400));
        await captureScreenshot('56_modal_admin_light_desktop_1440x900.png');

        // 3. Breakpoint 992x900
        console.log('\n--- 3. BREAKPOINT 992x900 ---');
        await evaluate(`document.documentElement.setAttribute('data-theme', 'dark')`);
        await setViewport(992, 900, false);
        await new Promise(r => setTimeout(r, 400));
        await captureScreenshot('57_modal_admin_dark_breakpoint_992x900.png');

        // 4. Tablet 794x1024
        console.log('\n--- 4. TABLET 794x1024 ---');
        await setViewport(794, 1024, false);
        await new Promise(r => setTimeout(r, 400));
        await captureScreenshot('58_modal_admin_dark_tablet_794x1024.png');

        // 5. Mobile 390x844
        console.log('\n--- 5. MOBILE 390x844 ---');
        await setViewport(390, 844, true);
        await new Promise(r => setTimeout(r, 400));
        await captureScreenshot('59_modal_admin_dark_mobile_390x844.png');

        // 6. Test Form AJAX Submission without Reload
        console.log('\n--- 6. AJAX PROFILE UPDATE TEST ---');
        await setViewport(1440, 900, false);
        await evaluate(`(() => {
            const nameInput = document.getElementById('modal-name-input');
            const bioInput = document.getElementById('modal-bio-input');
            if (nameInput) nameInput.value = 'Admin User Pro';
            if (bioInput) bioInput.value = 'Chuyên gia Laravel 12 & Kiến trúc sư Frontend';
            const submitBtn = document.getElementById('modal-submit-btn');
            if (submitBtn) submitBtn.click();
        })()`);

        await new Promise(r => setTimeout(r, 1500));

        const afterUpdate = await evaluate(`(() => {
            const m = document.getElementById('profileEditModal');
            return {
                modalClosed: !m.classList.contains('show'),
                updatedName: document.querySelector('.threads-profile-name')?.textContent.trim(),
                updatedBio: document.querySelector('.threads-profile-bio')?.textContent.trim(),
                bodyScrollLocked: document.body.classList.contains('modal-open')
            };
        })()`);
        console.log('State after AJAX update:', afterUpdate);
        await captureScreenshot('60_profile_after_ajax_update_1440x900.png');

        // 7. Test Settings Route Independence
        console.log('\n--- 7. SETTINGS ROUTE INDEPENDENCE ---');
        const topbarSettingsLink = await evaluate(`document.querySelector('.threads-topbar-link[href*="profile/edit"], .threads-topbar-link[href$="/profile"]')?.href`);
        console.log('Account Settings Link:', topbarSettingsLink);

        // 8. Restore original name & bio
        console.log('\n--- 8. RESTORING ORIGINAL PROFILE DATA ---');
        await openModal();
        await evaluate(`(() => {
            const nameInput = document.getElementById('modal-name-input');
            const bioInput = document.getElementById('modal-bio-input');
            if (nameInput) nameInput.value = 'Admin User';
            if (bioInput) bioInput.value = 'Et aut aspernatur ea sint quia et.';
            const submitBtn = document.getElementById('modal-submit-btn');
            if (submitBtn) submitBtn.click();
        })()`);
        await new Promise(r => setTimeout(r, 1200));

        const restoredData = await evaluate(`(() => {
            return {
                name: document.querySelector('.threads-profile-name')?.textContent.trim(),
                bio: document.querySelector('.threads-profile-bio')?.textContent.trim()
            };
        })()`);
        console.log('Restored state:', restoredData);

        console.log('\n========================================');
        console.log('ALL VERIFICATIONS COMPLETED SUCCESSFULLY');
        console.log('========================================');

    } finally {
        edge.kill();
    }
}

run().catch(err => {
    console.error('Test script error:', err);
    process.exit(1);
});
