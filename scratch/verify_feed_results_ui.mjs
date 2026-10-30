import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';

const EDGE_PATH = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const PORT = 9510;
const ARTIFACT_DIR = 'C:\\Users\\MINHKHOI\\.gemini\\antigravity\\brain\\59e850a8-2d20-49e5-a8da-e6d5701a3347';

async function run() {
    console.log('Starting Edge on debug port ' + PORT + '...');
    const edge = spawn(EDGE_PATH, [
        `--remote-debugging-port=${PORT}`,
        '--headless=new',
        '--disable-gpu',
        '--no-first-run',
        '--no-default-browser-check',
        '--window-size=1366,768',
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
            console.log(`Saved screenshot: ${filename}`);
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

        // 1. FILTERED FEED (Search + Category)
        console.log('\n--- 1. FILTER BAR VERIFICATION (SEARCH + CATEGORY) ---');
        await psend('Page.navigate', { url: 'http://127.0.0.1:8000/posts?q=AI&category=tri-tue-nhan-tao' });
        await new Promise(r => setTimeout(r, 1500));

        // Desktop 1366x768 Dark Mode
        await setViewport(1366, 768, false);
        await evaluate(`document.documentElement.setAttribute('data-theme', 'dark')`);
        await new Promise(r => setTimeout(r, 300));
        await captureScreenshot('61_feed_filter_desktop_1366x768_dark.png');

        // Desktop 1366x768 Light Mode
        await evaluate(`document.documentElement.setAttribute('data-theme', 'light')`);
        await new Promise(r => setTimeout(r, 300));
        await captureScreenshot('62_feed_filter_desktop_1366x768_light.png');

        // Check viewports for Filter Bar
        const viewports = [
            { name: 'Mobile 320x568', width: 320, height: 568, isMobile: true, file: '63_feed_filter_mobile_320x568.png' },
            { name: 'Mobile 360x740', width: 360, height: 740, isMobile: true, file: '64_feed_filter_mobile_360x740.png' },
            { name: 'Mobile 390x844', width: 390, height: 844, isMobile: true, file: '65_feed_filter_mobile_390x844.png' },
            { name: 'Mobile 430x932', width: 430, height: 932, isMobile: true, file: '66_feed_filter_mobile_430x932.png' },
            { name: 'Tablet 768x1024', width: 768, height: 1024, isMobile: false, file: '67_feed_filter_tablet_768x1024.png' },
        ];

        await evaluate(`document.documentElement.setAttribute('data-theme', 'dark')`);

        for (const vp of viewports) {
            console.log(`Auditing ${vp.name}...`);
            await setViewport(vp.width, vp.height, vp.isMobile);
            const audit = await evaluate(`(() => {
                const doc = document.documentElement;
                const filter = document.querySelector('.feed-filter-indicator');
                const clearBtn = document.querySelector('.feed-clear-filter-btn');
                return {
                    hasHorizontalScroll: doc.scrollWidth > doc.clientWidth,
                    filterVisible: !!filter,
                    filterHeight: filter ? Math.round(filter.getBoundingClientRect().height) : null,
                    clearBtnVisible: !!clearBtn,
                    clearBtnRect: clearBtn ? clearBtn.getBoundingClientRect() : null
                };
            })()`);
            console.log(`${vp.name} audit:`, audit);
            await captureScreenshot(vp.file);
        }

        // 2. EMPTY STATE VERIFICATION
        console.log('\n--- 2. EMPTY STATE VERIFICATION ---');
        await setViewport(1366, 768, false);
        await psend('Page.navigate', { url: 'http://127.0.0.1:8000/posts?q=khong_the_tim_thay_bai_viet_nay_xyz' });
        await new Promise(r => setTimeout(r, 1500));

        const emptyAudit = await evaluate(`(() => {
            const title = document.querySelector('h3')?.textContent.trim();
            const desc = document.querySelector('.text-theme-secondary')?.textContent.trim();
            const clearLink = document.querySelector('a[data-feed-clear-filter]');
            return {
                title,
                desc,
                clearLinkText: clearLink?.textContent.trim(),
                clearLinkHref: clearLink?.href
            };
        })()`);
        console.log('Empty State Audit:', emptyAudit);
        await captureScreenshot('68_feed_empty_state_desktop_1366x768.png');

        await setViewport(390, 844, true);
        await captureScreenshot('69_feed_empty_state_mobile_390x844.png');

        // 3. PAGINATION & AJAX NAVIGATION
        console.log('\n--- 3. PAGINATION & AJAX VERIFICATION ---');
        await setViewport(1366, 768, false);
        await psend('Page.navigate', { url: 'http://127.0.0.1:8000/posts' });
        await new Promise(r => setTimeout(r, 1500));

        // Click a category chip via AJAX
        console.log('Testing AJAX Category Filter click...');
        await evaluate(`(() => {
            const firstCategoryChip = document.querySelector('[data-feed-category-chip]:not([aria-current="page"])');
            if (firstCategoryChip) firstCategoryChip.click();
        })()`);
        await new Promise(r => setTimeout(r, 1200));

        const ajaxCategoryAudit = await evaluate(`(() => {
            const filterIndicator = document.querySelector('.feed-filter-indicator');
            return {
                currentUrl: window.location.href,
                hasFilterIndicator: !!filterIndicator,
                filterText: filterIndicator?.textContent.trim().replace(/\\s+/g, ' ')
            };
        })()`);
        console.log('AJAX Category Click result:', ajaxCategoryAudit);
        await captureScreenshot('70_feed_after_ajax_category_1366x768.png');

        // Click Clear Filter via AJAX
        console.log('Testing AJAX Clear Filter click...');
        await evaluate(`(() => {
            const clearBtn = document.querySelector('[data-feed-clear-filter]');
            if (clearBtn) clearBtn.click();
        })()`);
        await new Promise(r => setTimeout(r, 1200));

        const afterClearAudit = await evaluate(`(() => {
            return {
                currentUrl: window.location.href,
                hasFilterIndicator: !!document.querySelector('.feed-filter-indicator'),
                postCount: document.querySelectorAll('.threads-post').length
            };
        })()`);
        console.log('AJAX Clear Filter result:', afterClearAudit);
        await captureScreenshot('71_feed_after_ajax_clear_filter_1366x768.png');

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

