import { expect, test } from '@playwright/test';
import { assertQaServer, login, loginAdmin, testPassword } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

const csrf = (page) => page.locator('meta[name="csrf-token"]').getAttribute('content');

async function expectUser(page) {
    await page.goto('/profile');
    await expect(page.locator('input[name="email"]').first()).toHaveValue('viewer@e2e.test');
}

async function expectAdmin(page) {
    const response = await page.goto('/admin');
    expect(response.status()).toBe(200);
    await expect(page.locator('.sidebar-account__name')).toContainText('QA Admin');
}

test('two tabs share cookies and logout destroys only the selected session', async ({ page: userPage, context }) => {
    const adminPage = await context.newPage();
    expect(adminPage.context()).toBe(userPage.context());
    await login(userPage);
    await loginAdmin(adminPage);
    await expectUser(userPage);
    await expectAdmin(adminPage);

    const userToken = await csrf(userPage);
    const adminToken = await csrf(adminPage);
    expect(userToken).not.toBe(adminToken);
    const cookies = await context.cookies();
    expect(cookies.some((cookie) => cookie.name.endsWith('_admin') && cookie.path === '/admin' && cookie.httpOnly)).toBe(true);
    expect(cookies.filter((cookie) => cookie.name === 'XSRF-TOKEN').map((cookie) => cookie.path).sort()).toEqual(['/', '/admin']);

    const rejected = await context.request.post('/admin/logout', { form: { _token: userToken } });
    expect(rejected.status()).toBe(419);
    await expectAdmin(adminPage);
    expect(await csrf(adminPage)).toBe(adminToken);

    await userPage.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();
    await userPage.goto('/profile');
    await expect(userPage).toHaveURL(/\/login$/);
    await expectAdmin(adminPage);
    expect(await csrf(adminPage)).toBe(adminToken);

    await login(userPage);
    await expectUser(userPage);
    const renewedUserToken = await csrf(userPage);
    await adminPage.locator('form[action$="/admin/logout"] button[type="submit"]').first().click();
    await expect(adminPage).toHaveURL(/\/admin\/login$/);
    await adminPage.goto('/admin');
    await expect(adminPage).toHaveURL(/\/admin\/login$/);
    await expectUser(userPage);
    expect(await csrf(userPage)).toBe(renewedUserToken);
    const confirmation = await context.request.post('/confirm-password', {
        form: { _token: renewedUserToken, password: testPassword },
        maxRedirects: 0,
    });
    expect(confirmation.status()).toBe(302);
});

test('admin cookie cannot grant public viewer permissions and public login cannot enter administration', async ({ page, context }) => {
    await login(page);
    const adminPage = await context.newPage();
    await loginAdmin(adminPage);

    const refused = await page.goto('/posts/create');
    expect(refused.status()).toBe(403);
    await expectAdmin(adminPage);
    await adminPage.locator('.sidebar-container a[aria-label="Trang chủ"]').click();
    await expect(adminPage).toHaveURL(/\/$/);
    await expect(adminPage.locator('.sidebar-account__name')).toContainText('QA Viewer');
    await expectAdmin(adminPage);
    await adminPage.goto('/admin/users');
    await adminPage.goBack();
    await adminPage.goForward();
    expect((await adminPage.reload()).status()).toBe(200);
    await expectUser(page);

    await page.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();
    await page.goto('/login');
    await page.locator('#email').fill('admin@e2e.test');
    await page.locator('#password').fill(testPassword);
    await page.getByRole('button', { name: 'Đăng nhập', exact: true }).click();
    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByText('Email hoặc mật khẩu không hợp lệ.', { exact: true })).toBeVisible();
    await expectAdmin(adminPage);
    await page.goto('/posts/create');
    await expect(page).toHaveURL(/\/login$/);
});

test('remember me restores both accounts after session cookie loss and respects independent logout', async ({ page: userPage, context }) => {
    const adminPage = await context.newPage();
    for (const [page, path, email] of [[userPage, '/login', 'viewer@e2e.test'], [adminPage, '/admin/login', 'admin@e2e.test']]) {
        await page.goto(path);
        await page.locator('#email').fill(email);
        await page.locator('#password').fill(testPassword);
        await page.locator('input[name="remember"]').check();
        await page.locator('form button[type="submit"]').click();
        await expect(page).not.toHaveURL(/\/login$/);
    }

    const remembered = (await context.cookies()).filter((cookie) => cookie.name.startsWith('remember_'));
    expect(remembered).toHaveLength(2);
    expect(new Set(remembered.map((cookie) => cookie.name)).size).toBe(2);

    for (const cookie of await context.cookies()) {
        if (!cookie.name.startsWith('remember_')) {
            await context.clearCookies({ name: cookie.name, domain: cookie.domain, path: cookie.path });
        }
    }

    await expectUser(userPage);
    await expectAdmin(adminPage);
    await userPage.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();
    await expectAdmin(adminPage);
    await userPage.goto('/profile');
    await expect(userPage).toHaveURL(/\/login$/);
});
