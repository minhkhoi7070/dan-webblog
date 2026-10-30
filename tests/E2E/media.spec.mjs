import { existsSync } from 'node:fs';
import { resolve } from 'node:path';
import { expect, test } from '@playwright/test';
import { assertLocalDiagnostics, assertQaServer, login, monitorBrowser } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

async function deleteVideoFromBrowser(page, filename) {
    return page.evaluate(async (name) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch('/posts/delete-video', {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ filename: name }),
        });

        return response.status;
    }, filename);
}

test('author uploads a QA video, another author cannot delete it, and uploader can', async ({ browser, page, request }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    await login(page, 'author-a@e2e.test');
    await page.goto('/posts/create');
    await page.locator('[data-editor-action="video"]').click();
    await expect(page.locator('#editor-video-modal')).toBeVisible();

    const uploadResponse = page.waitForResponse((response) => response.url().endsWith('/posts/upload-video'));
    await page.locator('#editor-video-file-input').setInputFiles(resolve(import.meta.dirname, 'fixtures/qa-clip.webm'));
    const upload = await uploadResponse;
    expect(upload.status()).toBe(201);
    const { filename, url } = await upload.json();
    expect(filename).toMatch(/^[a-zA-Z0-9_-]+\.webm$/);
    const qaPath = resolve(import.meta.dirname, '../../storage/e2e/app/public/posts/videos', filename);
    expect(existsSync(qaPath)).toBe(true);
    expect((await request.get(url)).status()).toBe(200);

    const otherContext = await browser.newContext();
    const otherPage = await otherContext.newPage();
    await login(otherPage, 'author-b@e2e.test');
    await otherPage.goto('/posts/create');
    expect(await deleteVideoFromBrowser(otherPage, filename)).toBe(403);
    expect(existsSync(qaPath)).toBe(true);
    await otherContext.close();

    expect(await deleteVideoFromBrowser(page, filename)).toBe(200);
    expect(existsSync(qaPath)).toBe(false);
    expect((await request.get(url)).status()).toBe(404);
    await assertLocalDiagnostics(testInfo, diagnostics);
});
