import { existsSync } from 'node:fs';
import { resolve } from 'node:path';
import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, loginAdmin, monitorBrowser, qaScalar } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('uploaded WebM remains playable after author submission and admin approval', async ({ browser, page, request }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA Video Published ${Date.now()}`;
    await login(page, 'author-a@e2e.test');
    await page.goto('/posts/create');
    await page.locator('#title').fill(title);
    await page.locator('#category_id').selectOption({ label: 'QA Technology' });
    await page.locator('[data-editor-action="video"]').click();

    const uploadResponse = page.waitForResponse((response) => response.url().endsWith('/posts/upload-video'));
    await page.locator('#editor-video-file-input').setInputFiles(resolve(import.meta.dirname, 'fixtures/qa-clip.webm'));
    const upload = await uploadResponse;
    expect(upload.status()).toBe(201);
    const { filename, url } = await upload.json();
    const qaPath = resolve(import.meta.dirname, '../../storage/e2e/app/public/posts/videos', filename);
    expect(existsSync(qaPath)).toBe(true);

    await page.locator('#editor-video-save-btn').click();
    await expect(page.locator('#tiptap-editor video source')).toHaveAttribute('src', url);
    await expect(page.locator('#content')).toHaveValue(new RegExp(filename.replace('.', '\\.')));
    await page.locator('button[form="create-post-form"][value="draft"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE title = '${title}'`)).toBe('draft');
    const postId = qaScalar(`SELECT id FROM posts WHERE title = '${title}'`);
    const slug = qaScalar(`SELECT slug FROM posts WHERE id = ${postId}`);
    const draftBody = qaScalar(`SELECT body FROM posts WHERE id = ${postId}`);
    expect(draftBody).toContain(url);
    expect(draftBody).toContain('<source');

    await page.goto(`/posts/${postId}/edit`);
    await expect(page.locator('#tiptap-editor video source')).toHaveAttribute('src', url);
    await page.locator('button[form="edit-post-form"][value="submit"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('pending');
    const pendingBody = qaScalar(`SELECT body FROM posts WHERE id = ${postId}`);
    expect(pendingBody).toContain(url);
    expect(pendingBody).toContain('<source');

    const adminContext = await browser.newContext({ baseURL: testInfo.project.use.baseURL });
    const adminPage = await adminContext.newPage();
    await loginAdmin(adminPage);
    await adminPage.goto(`/admin/posts/${postId}`);
    await adminPage.locator('button.admin-post-action--approve').click();
    await expect(adminPage.locator('#global-confirm-modal')).toBeVisible();
    await adminPage.locator('#confirm-modal-submit').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('published');
    await adminContext.close();

    expect(qaScalar(`SELECT status FROM posts WHERE id = ${postId}`)).toBe('published');
    expect(qaScalar(`SELECT body FROM posts WHERE id = ${postId}`)).toBe(pendingBody);
    const publicResponse = await page.goto(`/posts/${slug}`);
    expect(publicResponse.status()).toBe(200);
    const video = page.locator('.editorial-content video');
    await expect(video).toBeVisible();
    await expect(video.locator('source')).toHaveAttribute('src', url);
    await expect.poll(() => video.evaluate((element) => element.readyState > 0 || Boolean(element.error))).toBe(true);
    const mediaState = await video.evaluate((element) => ({
        readyState: element.readyState,
        error: element.error?.code ?? null,
        duration: element.duration,
        width: element.getBoundingClientRect().width,
        height: element.getBoundingClientRect().height,
    }));
    expect(mediaState.error).toBeNull();
    expect(mediaState.readyState).toBeGreaterThan(0);
    expect(mediaState.width).toBeGreaterThan(0);
    expect(mediaState.height).toBeGreaterThan(0);
    expect(existsSync(qaPath)).toBe(true);
    const mediaResponse = await request.get(url);
    expect(mediaResponse.status()).toBe(200);
    expect(mediaResponse.headers()['content-type']).toMatch(/^video\/webm/);
    const rangeResponse = await request.get(url, { headers: { Range: 'bytes=0-99' } });
    expect([200, 206]).toContain(rangeResponse.status());
    expect(rangeResponse.headers()['content-type']).toMatch(/^video\/webm/);

    for (const viewport of [{ width: 390, height: 844 }, { width: 794, height: 1024 }, { width: 1440, height: 900 }]) {
        await page.setViewportSize(viewport);
        await expect(video).toBeVisible();
        expect(await video.evaluate((element) => element.getBoundingClientRect().width)).toBeLessThanOrEqual(viewport.width);
    }

    await page.reload();
    await expect(video.locator('source')).toHaveAttribute('src', url);
    await expect.poll(() => video.evaluate((element) => element.readyState > 0 || Boolean(element.error))).toBe(true);
    expect(await video.evaluate((element) => element.error?.code ?? null)).toBeNull();
    await testInfo.attach('media-state', { body: JSON.stringify(mediaState), contentType: 'application/json' });
    await assertLocalDiagnostics(testInfo, diagnostics);
});

test('YouTube embed still survives a draft editor reload', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const title = `QA YouTube Draft ${Date.now()}`;
    const embedUrl = 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ';
    await login(page, 'author-a@e2e.test');
    await page.goto('/posts/create');
    await page.locator('#title').fill(title);
    await page.locator('#category_id').selectOption({ label: 'QA Technology' });
    await page.locator('[data-editor-action="video"]').click();
    await page.locator('#tab-video-youtube').click();
    await expect(page.locator('#tab-video-upload-pane')).not.toHaveClass(/\bshow\b/);
    await page.locator('#editor-video-url').fill('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    await page.locator('#editor-video-save-btn').click();
    await expect(page.locator('#tiptap-editor iframe')).toHaveAttribute('src', embedUrl);
    await page.locator('button[form="create-post-form"][value="draft"]').click();
    await expect.poll(() => qaScalar(`SELECT status FROM posts WHERE title = '${title}'`)).toBe('draft');
    const postId = qaScalar(`SELECT id FROM posts WHERE title = '${title}'`);
    expect(qaScalar(`SELECT body FROM posts WHERE id = ${postId}`)).toContain(embedUrl);

    await page.goto(`/posts/${postId}/edit`);
    await expect(page.locator('#tiptap-editor iframe')).toHaveAttribute('src', embedUrl);
    await assertLocalDiagnostics(testInfo, diagnostics);
});
