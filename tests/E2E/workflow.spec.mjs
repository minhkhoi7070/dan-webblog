import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, loginAdmin, monitorBrowser, qaScalar } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

async function fillNewPost(page, title) {
    await page.goto('/posts/create');
    await expect(page.locator('#tiptap-editor .ProseMirror')).toBeVisible();
    await page.locator('#title').fill(title);
    await page.locator('#category_id').selectOption({ label: 'QA Technology' });
    await page.locator('#tiptap-editor .ProseMirror').fill(`Browser QA content for ${title}`);
    await expect(page.locator('#content')).toHaveValue(/Browser QA content/);
}

test('author saves a TipTap draft and submits it for review', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA Author Draft ${Date.now()}`;
    await login(page, 'author-a@e2e.test');
    await fillNewPost(page, title);
    await page.locator('button[form="create-post-form"][value="draft"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE title = '${title}'`)).toBe('draft');
    const postId = qaScalar(`SELECT id FROM posts WHERE title = '${title}'`);

    await page.goto(`/posts/${postId}/edit`);
    await expect(page.locator('#tiptap-editor .ProseMirror')).toContainText('Browser QA content');
    await page.locator('button[form="edit-post-form"][value="submit"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('pending');
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('admin directly publishes a new post', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA Admin Publish ${Date.now()}`;
    await login(page, 'admin@e2e.test');
    await fillNewPost(page, title);
    await page.locator('button[form="create-post-form"][value="publish"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE title = '${title}'`)).toBe('published');
    expect(qaScalar(`SELECT COUNT(*) FROM posts WHERE title = '${title}' AND published_at IS NOT NULL`)).toBe('1');
    await page.goto('/');
    await expect(page.locator('article[data-post-id]').filter({ hasText: title })).toBeVisible();
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('admin approves an author submission through the moderation UI', async ({ browser, page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA Moderation ${Date.now()}`;
    await login(page, 'author-b@e2e.test');
    await fillNewPost(page, title);
    await page.locator('button[form="create-post-form"][value="submit"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE title = '${title}'`)).toBe('pending');
    const postId = qaScalar(`SELECT id FROM posts WHERE title = '${title}'`);

    const adminContext = await browser.newContext({ baseURL: testInfo.project.use.baseURL });
    const adminPage = await adminContext.newPage();
    const adminDiagnostics = monitorBrowser(adminPage);
    await loginAdmin(adminPage);
    await adminPage.goto(`/admin/posts/${postId}`);
    await adminPage.locator('button.admin-post-action--approve').click();
    await expect(adminPage.locator('#global-confirm-modal')).toBeVisible();
    await adminPage.locator('#confirm-modal-submit').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('published');
    expect(qaScalar(`SELECT COUNT(*) FROM posts WHERE id = ${postId} AND published_at IS NOT NULL`)).toBe('1');
    await assertLocalDiagnostics(testInfo, diagnostics);
    await assertLocalDiagnostics(testInfo, adminDiagnostics);
    await adminContext.close();
});

test('admin submit request cannot move a published author post back to pending', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const postId = qaScalar("SELECT id FROM posts WHERE slug = 'qa-published-post'");
    const publishedAt = qaScalar(`SELECT published_at FROM posts WHERE id = ${postId}`);
    await login(page, 'admin@e2e.test');
    await page.goto('/posts/stats');

    const status = await page.evaluate(async (id) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`/posts/${id}/submit`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
        });

        return response.status;
    }, postId);

    expect(status).toBe(422);
    expect(qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('published');
    expect(qaScalar(`SELECT published_at FROM posts WHERE id = ${postId}`)).toBe(publishedAt);
    const publicResponse = await page.goto('/posts/qa-published-post');
    expect(publicResponse.status()).toBe(200);
    await expect(page.locator('body')).toContainText('QA Published Post');
    await assertLocalDiagnostics(testInfo, diagnostics, [{ path: `/posts/${postId}/submit`, status: 422 }]);
});

test('empty rich text is rejected before an author post is saved', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA Empty Rich Text ${Date.now()}`;
    const categoryId = qaScalar("SELECT id FROM categories WHERE slug = 'qa-technology'");
    await login(page, 'author-a@e2e.test');
    await page.goto('/posts/create');

    const response = await page.evaluate(async ({ title, categoryId }) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const result = await fetch('/posts', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ title, category_id: categoryId, content: '<p><br></p>', action: 'submit' }),
        });

        return { status: result.status, errors: (await result.json()).errors };
    }, { title, categoryId });

    expect(response.status).toBe(422);
    expect(response.errors.body).toBeDefined();
    expect(qaScalar(`SELECT COUNT(*) FROM posts WHERE title = '${title}'`)).toBe('0');
    await assertLocalDiagnostics(testInfo, diagnostics, [{ path: '/posts', status: 422 }]);
});
