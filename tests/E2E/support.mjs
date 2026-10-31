import { execFileSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { expect } from '@playwright/test';

const environmentFile = resolve(import.meta.dirname, '../../.env.e2e');

function environmentValue(name) {
    const line = readFileSync(environmentFile, 'utf8')
        .split(/\r?\n/)
        .find((entry) => entry.startsWith(`${name}=`));

    if (!line) {
        throw new Error(`Missing ${name} in ignored E2E environment file`);
    }

    return line.slice(name.length + 1);
}

export const testPassword = environmentValue('E2E_TEST_PASSWORD');

export function latestQaOtp(email) {
    const mailLog = readFileSync(resolve(import.meta.dirname, '../../storage/e2e/logs/laravel.log'), 'utf8');
    const offset = mailLog.lastIndexOf(`To: ${email}`);
    if (offset < 0) {
        throw new Error('QA mail log has no message for the test recipient');
    }

    const headers = mailLog.slice(offset, mailLog.indexOf('MIME-Version:', offset));
    const code = headers.match(/\b(\d{6})\b/);
    if (!code) {
        throw new Error('QA mail log has no six-digit OTP in the message subject');
    }

    return code[1];
}

export async function assertQaServer(request) {
    const response = await request.get('/__e2e/health');
    expect(response.status()).toBe(200);

    const health = await response.json();
    expect(health.app_env).toBe('e2e');
    expect(health.database_connection).toBe('mysql');
    expect(health.database_name).toBe('webblog_qa');
    expect(health.database_user).toBe('webblog_qa@localhost');
    expect(health.storage_root.replaceAll('\\', '/')).toMatch(/\/storage\/e2e\/app\/public$/);
    expect(health.session_driver).toBe('file');
    expect(health.mail_transport).toBe('log');
}

export function qaScalar(sql) {
    if (!/^SELECT\s/i.test(sql) || sql.includes(';')) {
        throw new Error('Only a single read-only QA SELECT is allowed');
    }

    return execFileSync('mysql', [
        '-h', '127.0.0.1',
        '-u', 'webblog_qa',
        '-D', 'webblog_qa',
        '-N', '-e', sql,
    ], {
        encoding: 'utf8',
        env: { ...process.env, MYSQL_PWD: environmentValue('DB_PASSWORD') },
    }).trim();
}

export async function login(page, email = 'viewer@e2e.test', password = testPassword, path = '/login') {
    await page.goto(path);
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.getByRole('button', { name: path === '/admin/login' ? 'Đăng nhập Quản trị viên' : 'Đăng nhập', exact: true }).click();
    await expect(page).not.toHaveURL(/\/login(?:\?|$)/);
}

export async function loginAdmin(page) {
    await login(page, 'admin@e2e.test', testPassword, '/admin/login');
}

export function monitorBrowser(page) {
    const consoleErrors = [];
    const pageErrors = [];
    const networkFailures = [];

    page.on('console', (message) => {
        if (message.type() === 'error') {
            consoleErrors.push({ text: message.text(), url: message.location().url });
        }
    });
    page.on('pageerror', (error) => pageErrors.push(error.message));
    page.on('requestfailed', (request) => networkFailures.push(`${request.method()} ${request.url()}: ${request.failure()?.errorText}`));

    return { consoleErrors, pageErrors, networkFailures };
}

export async function assertLocalDiagnostics(testInfo, diagnostics, allowedHttpErrors = []) {
    await testInfo.attach('browser-diagnostics', {
        body: JSON.stringify(diagnostics, null, 2),
        contentType: 'application/json',
    });

    const origin = new URL(testInfo.project.use.baseURL).origin;
    expect(diagnostics.pageErrors).toEqual([]);
    expect(diagnostics.networkFailures.filter((failure) => failure.includes(origin) && ! failure.includes('net::ERR_ABORTED'))).toEqual([]);
    expect(diagnostics.consoleErrors.filter((error) => error.url.startsWith(origin)
        && ! allowedHttpErrors.some((allowed) => error.url.endsWith(allowed.path)
            && error.text.includes(`status of ${allowed.status}`)))).toEqual([]);
}
