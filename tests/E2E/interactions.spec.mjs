import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser, qaScalar } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('viewer likes and saves a post with matching MySQL state', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/posts?q=QA%20Published%20Post');
    const card = page.locator('article[data-post-id]').filter({ hasText: 'QA Published Post' });
    const postId = await card.getAttribute('data-post-id');
    const viewerId = qaScalar("SELECT id FROM users WHERE email = 'viewer@e2e.test'");

    for (const [action, table] of [['like', 'likes'], ['favorite', 'favorites']]) {
        const button = card.locator(`[data-action="${action}"]`);
        const initial = await button.getAttribute('aria-pressed');
        await button.click();
        await expect(button).toHaveAttribute('aria-pressed', initial === 'true' ? 'false' : 'true');
        await expect.poll(() => qaScalar(`SELECT COUNT(*) FROM ${table} WHERE user_id = ${viewerId} AND post_id = ${postId}`))
            .toBe(initial === 'true' ? '0' : '1');
        await button.click();
        await expect(button).toHaveAttribute('aria-pressed', initial);
        await expect.poll(() => qaScalar(`SELECT COUNT(*) FROM ${table} WHERE user_id = ${viewerId} AND post_id = ${postId}`))
            .toBe(initial === 'true' ? '1' : '0');
    }

    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('quick follow popover closes on Escape and confirms only on click', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/posts?q=QA%20Published%20Post');
    const card = page.locator('article[data-post-id]').filter({ hasText: 'QA Published Post' });
    const trigger = card.locator('[data-action="quick-follow-open"]');
    const viewerId = qaScalar("SELECT id FROM users WHERE email = 'viewer@e2e.test'");
    const authorId = qaScalar("SELECT id FROM users WHERE email = 'author-a@e2e.test'");
    const count = () => qaScalar(`SELECT COUNT(*) FROM follows WHERE follower_id = ${viewerId} AND following_id = ${authorId}`);
    const initial = count();

    await trigger.click();
    await expect(page.locator('#quick-follow-popover')).toHaveClass(/is-open/);
    await page.keyboard.press('Escape');
    await expect(page.locator('#quick-follow-popover')).not.toHaveClass(/is-open/);
    expect(count()).toBe(initial);

    await trigger.click();
    await page.locator('#quick-follow-popover .quick-follow-popover-btn-confirm').click();
    await expect.poll(count).toBe(initial === '0' ? '1' : '0');
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('viewer comments on a published post', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page);
    await page.goto('/posts/qa-published-post');
    const body = `QA browser comment ${Date.now()}`;
    await page.locator('#comment-body').fill(body);
    await page.getByRole('button', { name: 'Đăng bình luận' }).click();
    await expect(page.getByText(body)).toBeVisible();
    await expect.poll(() => qaScalar(`SELECT COUNT(*) FROM comments WHERE body = '${body}'`)).toBe('1');
    await assertLocalDiagnostics(testInfo, diagnostics);
});
