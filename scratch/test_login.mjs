async function testAdmin() {
    const getRes = await fetch('http://127.0.0.1:8000/login');
    const rawCookies = getRes.headers.getSetCookie();
    const cookieHeader = rawCookies.map(c => c.split(';')[0]).join('; ');
    const html = await getRes.text();
    const tokenMatch = html.match(/name="_token" value="([^"]+)"/);
    const token = tokenMatch ? tokenMatch[1] : '';

    const params = new URLSearchParams();
    params.append('_token', token);
    params.append('email', 'admin@blogmnm.test');
    params.append('password', 'password');

    const postRes = await fetch('http://127.0.0.1:8000/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cookie': cookieHeader
        },
        body: params.toString(),
        redirect: 'manual'
    });

    console.log('Login POST Status:', postRes.status);
    console.log('Location:', postRes.headers.get('location'));
    const postRawCookies = postRes.headers.getSetCookie();
    const postCookieHeader = postRawCookies.length ? postRawCookies.map(c => c.split(';')[0]).join('; ') : cookieHeader;

    const profileRes = await fetch('http://127.0.0.1:8000/profile/overview', {
        headers: { 'Cookie': postCookieHeader },
        redirect: 'manual'
    });
    console.log('Profile Status:', profileRes.status, 'Location:', profileRes.headers.get('location'));
    const profileHtml = await profileRes.text();
    console.log('Includes btn-open-edit-profile:', profileHtml.includes('btn-open-edit-profile'));
    console.log('Includes profileEditModal:', profileHtml.includes('profileEditModal'));
    console.log('Profile user name in page:', profileHtml.includes('Admin User'));
}
testAdmin();
