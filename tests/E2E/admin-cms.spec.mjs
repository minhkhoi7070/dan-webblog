import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, loginAdmin, monitorBrowser } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('admin can open all CMS index pages with QA records', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await loginAdmin(page);

    for (const path of ['/admin', '/admin/users', '/admin/posts', '/admin/categories', '/admin/tags', '/admin/comments']) {
        const response = await page.goto(path);
        expect(response.status(), path).toBe(200);
        await expect(page.locator('body')).toBeVisible();
    }

    await page.screenshot({ path: testInfo.outputPath('admin-cms.png'), fullPage: true });
    await assertLocalDiagnostics(testInfo, diagnostics);
});
