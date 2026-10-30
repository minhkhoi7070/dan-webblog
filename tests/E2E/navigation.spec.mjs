import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('sidebar AJAX navigation and Back/Forward keep working without document reloads', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/');
    const documentRequests = [];
    page.on('request', (request) => {
        if (request.isNavigationRequest() && request.resourceType() === 'document') {
            documentRequests.push(request.url());
        }
    });

    await page.locator('a[aria-label="Bài viết đã lưu"]').first().click();
    await expect(page).toHaveURL(/\/favorites$/);
    await expect(page.locator('[data-feed-shell]')).toBeVisible();

    await page.locator('a[aria-label="Nhật ký hoạt động"]').first().click();
    await expect(page).toHaveURL(/\/activity$/);
    await expect(page.locator('#activity-stream')).toBeVisible();

    await page.locator('a[aria-label="Đang theo dõi"]').first().click();
    await expect(page).toHaveURL(/\/following$/);
    await page.goBack();
    await expect(page).toHaveURL(/\/activity$/);
    await page.goForward();
    await expect(page).toHaveURL(/\/following$/);

    expect(documentRequests).toEqual([]);
    await assertLocalDiagnostics(testInfo, diagnostics);
});
