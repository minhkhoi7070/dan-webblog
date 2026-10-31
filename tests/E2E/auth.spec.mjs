import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser, qaScalar } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('viewer logs in, sees private pages, and logs out', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/favorites');
    await expect(page).toHaveURL(/\/favorites$/);
    await page.goto('/following');
    await expect(page).toHaveURL(/\/following$/);
    await page.goto('/activity');
    await expect(page).toHaveURL(/\/activity$/);
    expect(qaScalar("SELECT role FROM users WHERE email = 'viewer@e2e.test'")).toBe('viewer');

    await page.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();
    await page.goto('/favorites');
    await expect(page).toHaveURL(/\/login(?:\?|$)/);
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('viewer cannot open the admin dashboard', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);
    await assertLocalDiagnostics(testInfo, diagnostics);
});
