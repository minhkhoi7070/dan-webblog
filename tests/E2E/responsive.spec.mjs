import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser } from './support.mjs';

const viewports = [
    { width: 390, height: 844 },
    { width: 794, height: 1024 },
    { width: 992, height: 900 },
    { width: 1440, height: 900 },
];

const roles = [
    { name: 'guest', pages: ['/', '/posts/qa-published-post'] },
    { name: 'viewer', email: 'viewer@e2e.test', pages: ['/', '/profile/overview', '/favorites', '/activity'] },
    { name: 'author', email: 'author-a@e2e.test', pages: ['/posts/create', '/posts/stats'] },
    { name: 'admin', email: 'admin@e2e.test', pages: ['/admin', '/admin/posts'] },
];

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

for (const viewport of viewports) {
    for (const role of roles) {
        test(`${role.name} pages fit ${viewport.width}×${viewport.height}`, async ({ page }, testInfo) => {
            const diagnostics = monitorBrowser(page);
            await page.setViewportSize(viewport);
            if (role.email) {
                await login(page, role.email);
            }

            const measurements = [];
            for (const path of role.pages) {
                const response = await page.goto(path);
                expect(response.status(), path).toBe(200);
                await expect(page.locator('body')).toBeVisible();
                const width = await page.evaluate(() => ({
                    viewport: window.innerWidth,
                    document: document.documentElement.scrollWidth,
                    body: document.body.scrollWidth,
                }));
                measurements.push({ path, ...width });
                expect(width.document, `${role.name} ${path}: horizontal overflow`).toBeLessThanOrEqual(viewport.width + 1);
                expect(width.body, `${role.name} ${path}: body horizontal overflow`).toBeLessThanOrEqual(viewport.width + 1);
            }

            await testInfo.attach('viewport-measurements', {
                body: JSON.stringify(measurements, null, 2),
                contentType: 'application/json',
            });
            await page.screenshot({ path: testInfo.outputPath(`${role.name}-${viewport.width}x${viewport.height}.png`), fullPage: true });
            await assertLocalDiagnostics(testInfo, diagnostics);
        });
    }
}
