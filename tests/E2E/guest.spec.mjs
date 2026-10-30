import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, monitorBrowser } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('guest filters the public feed and browser history restores it', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const home = await page.goto('/');
    expect(home.status()).toBe(200);
    await expect(page.locator('[data-feed-shell]')).toBeVisible();
    await expect(page.locator('article[data-post-id]').first()).toBeVisible();

    await page.locator('[data-feed-category-chip][data-feed-category="qa-technology"]').click();
    await expect(page).toHaveURL(/category=qa-technology/);
    await expect(page.locator('article[data-post-id]').first()).toBeVisible();

    await page.locator('[data-feed-category-chip][data-feed-category="qa-empty"]').click();
    await expect(page).toHaveURL(/category=qa-empty/);
    await expect(page.locator('article[data-post-id]')).toHaveCount(0);

    await page.goBack();
    await expect(page).toHaveURL(/category=qa-technology/);
    await page.goForward();
    await expect(page).toHaveURL(/category=qa-empty/);

    await page.screenshot({ path: testInfo.outputPath('guest-empty-category.png'), fullPage: true });
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('guest searches and opens the published post', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await page.goto('/');
    await page.locator('#feed-search-input').fill('QA Published Post');
    await page.locator('#feed-search-input').press('Enter');

    await expect(page).toHaveURL(/q=QA/);
    const post = page.locator('article[data-post-id]').filter({ hasText: 'QA Published Post' });
    await expect(post).toHaveCount(1);
    await post.getByRole('link', { name: /QA Published Post/ }).first().click();
    await expect(page).toHaveURL(/\/posts\/qa-published-post$/);
    await expect(page.getByRole('heading', { name: 'QA Published Post' })).toBeVisible();
    await page.screenshot({ path: testInfo.outputPath('guest-post-detail.png'), fullPage: true });

    await assertLocalDiagnostics(testInfo, diagnostics);
});
