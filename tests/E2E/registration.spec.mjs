import { expect, test } from '@playwright/test';
import { readFileSync } from 'node:fs';
import { assertLocalDiagnostics, assertQaServer, latestQaOtp, login, monitorBrowser, qaScalar, testPassword } from './support.mjs';

test.beforeEach(async ({ request }) => {
    await assertQaServer(request);
});

test('guest registers, verifies local OTP, and resets password with a second OTP', async ({ page }, testInfo) => {
    const diagnostics = monitorBrowser(page);
    const email = `qa-register-${Date.now()}@e2e.test`;
    await page.goto('/register');
    await page.locator('#name').fill('QA Registration');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(testPassword);
    await page.locator('#password_confirmation').fill(testPassword);
    await page.getByRole('button', { name: 'Đăng ký tài khoản' }).click();

    await expect(page).toHaveURL(/\/verify-email$/);
    const expiryTimer = page.locator('#otp-expiry-timer');
    await expect(expiryTimer).toHaveText(/^0[4-5]:\d{2}$/);
    const firstRemaining = Number(await page.locator('#otp-expiry-container').getAttribute('data-seconds'));
    await expect(expiryTimer).not.toHaveText(await expiryTimer.textContent());
    await page.reload();
    await expect(expiryTimer).toHaveText(/^0[4-5]:\d{2}$/);
    const reloadedRemaining = Number(await page.locator('#otp-expiry-container').getAttribute('data-seconds'));
    expect(reloadedRemaining).toBeLessThan(firstRemaining);
    await page.goto('/profile');
    await page.goBack();
    await expect(expiryTimer).toHaveText(/^0[4-5]:\d{2}$/);
    const mailLog = readFileSync(new URL('../../storage/e2e/logs/laravel.log', import.meta.url), 'utf8');
    expect(mailLog.split(`To: ${email}`).length - 1).toBe(1);
    expect(qaScalar(`SELECT role FROM users WHERE email = '${email}'`)).toBe('viewer');
    expect(qaScalar(`SELECT COUNT(*) FROM users WHERE email = '${email}' AND email_verified_at IS NULL`)).toBe('1');
    expect(qaScalar(`SELECT COUNT(*) FROM email_otps WHERE email = '${email}' AND purpose = 'email_verification' AND consumed_at IS NULL`)).toBe('1');
    expect(qaScalar(`SELECT COUNT(*) FROM email_otps WHERE email = '${email}' AND purpose = 'email_verification'`)).toBe('1');

    const otp = latestQaOtp(email);
    for (const [index, digit] of [...otp].entries()) {
        await page.locator(`[data-otp-slot="${index}"]`).fill(digit);
    }
    await page.getByRole('button', { name: 'Xác minh', exact: true }).click();
    await expect(page).not.toHaveURL(/\/verify-email(?:\?|$)/);
    expect(qaScalar(`SELECT COUNT(*) FROM users WHERE email = '${email}' AND email_verified_at IS NOT NULL`)).toBe('1');
    expect(qaScalar(`SELECT COUNT(*) FROM email_otps WHERE email = '${email}' AND purpose = 'email_verification' AND consumed_at IS NOT NULL`)).toBe('1');

    await page.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();
    await login(page, email);
    await page.locator('[aria-label="Đăng xuất khỏi tài khoản"]').first().click();

    await page.goto('/forgot-password');
    await page.locator('#email').fill(email);
    await page.getByRole('button', { name: 'Gửi mã xác thực OTP' }).click();
    await expect(page).toHaveURL(/\/forgot-password\/otp$/);
    const resetOtp = latestQaOtp(email);
    for (const [index, digit] of [...resetOtp].entries()) {
        await page.locator(`[data-otp-slot="${index}"]`).fill(digit);
    }
    await page.getByRole('button', { name: 'Xác thực mã' }).click();
    await expect(page).toHaveURL(/\/reset-password\//);
    const newPassword = `${testPassword}New`;
    await page.locator('#password').fill(newPassword);
    await page.locator('#password_confirmation').fill(newPassword);
    await page.getByRole('button', { name: 'Cập nhật mật khẩu' }).click();
    await expect(page).toHaveURL(/\/login(?:\?|$)/);
    expect(qaScalar(`SELECT COUNT(*) FROM email_otps WHERE email = '${email}' AND purpose = 'password_reset' AND consumed_at IS NOT NULL`)).toBe('1');
    await login(page, email, newPassword);
    await assertLocalDiagnostics(testInfo, diagnostics);
});
