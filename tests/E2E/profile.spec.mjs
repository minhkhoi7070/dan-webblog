import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('viewer default avatar is served as SVG and renders in the profile modal', async ({ page, request }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const asset = await request.get('/images/default-avatar.svg');
    expect(asset.status()).toBe(200);
    expect(asset.headers()['content-type']).toMatch(/^image\/svg\+xml(?:;|$)/);

    await login(page);
    await page.goto('/profile/overview');
    const preview = page.locator('#modal-avatar-preview-img');
    await expect(preview).toHaveAttribute('src', /\/images\/default-avatar\.svg$/);
    await expect.poll(() => preview.evaluate((image) => image.complete && image.naturalWidth > 0)).toBe(true);
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('viewer opens and dismisses the edit profile modal with Escape', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/profile/overview');

    const modal = page.locator('#profileEditModal');
    const scrollBefore = await page.evaluate(() => window.scrollY);
    await page.evaluate(() => {
        window.__qaProfileModalShown = false;
        document.querySelector('#profileEditModal').addEventListener('shown.bs.modal', () => {
            window.__qaProfileModalShown = true;
        }, { once: true });
    });
    await page.locator('#btn-open-edit-profile').click();
    await expect(modal).toBeVisible();
    await expect.poll(() => page.evaluate(() => window.__qaProfileModalShown)).toBe(true);
    await expect(page.locator('#modal-name-input')).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(modal).toBeHidden();
    expect(await page.evaluate(() => window.scrollY)).toBe(scrollBefore);

    await page.screenshot({ path: testInfo.outputPath('profile-modal-dismissed.png'), fullPage: true });
    await assertLocalDiagnostics(testInfo, diagnostics);
});
