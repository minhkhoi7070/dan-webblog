import { expect, test } from '@playwright/test';

test('Chromium renders a local document', async ({ page }) => {
    await page.goto('data:text/html,<h1>Chromium QA smoke</h1>');

    await expect(page.getByRole('heading', { name: 'Chromium QA smoke' })).toBeVisible();
});
