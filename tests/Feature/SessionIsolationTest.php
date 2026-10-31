<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<string, string> */
    private array $browserCookies = [];

    public function test_user_logout_preserves_admin_identity_session_and_csrf(): void
    {
        $user = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $this->login($user, '/login');
        $webCookie = $this->browserCookies[config('session.cookie')];
        $this->login($admin, '/admin/login');
        $this->assertSame($webCookie, $this->browserCookies[config('session.cookie')]);

        $this->browserRequest('GET', '/profile')->assertSee($user->email)->assertDontSee($admin->email);
        $this->browserRequest('GET', '/admin')->assertOk();
        $adminId = $this->app['request']->session()->getId();
        $adminToken = $this->app['request']->session()->token();
        $this->assertAuthenticatedAs($admin, 'admin');

        $this->browserRequest('POST', '/logout')->assertRedirect('/');
        $this->browserRequest('GET', '/profile')->assertRedirect(route('login'));
        $this->browserRequest('GET', '/admin')->assertOk();

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertSame($adminId, $this->app['request']->session()->getId());
        $this->assertSame($adminToken, $this->app['request']->session()->token());
    }

    public function test_admin_logout_preserves_user_identity_session_and_csrf(): void
    {
        $user = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();
        $this->login($admin, '/admin/login');
        $this->login($user, '/login');
        $this->browserRequest('GET', '/profile')->assertSee($user->email);
        $userId = $this->app['request']->session()->getId();
        $userToken = $this->app['request']->session()->token();

        $this->browserRequest('POST', '/admin/logout')->assertRedirect(route('admin.login'));
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.login'));
        $this->browserRequest('GET', '/profile')->assertSee($user->email)->assertDontSee($admin->email);

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertSame($userId, $this->app['request']->session()->getId());
        $this->assertSame($userToken, $this->app['request']->session()->token());
    }

    public function test_public_requests_cannot_borrow_admin_permissions(): void
    {
        $user = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();
        $this->login($user, '/login');
        $this->login($admin, '/admin/login');

        $this->browserRequest('GET', '/posts/create')->assertForbidden();
        $this->browserRequest('GET', '/admin/posts')->assertOk();
        $this->browserRequest('GET', '/profile')->assertSee($user->email)->assertDontSee($admin->email);
    }

    #[TestWith(['admin@example.test', 'password'])]
    #[TestWith(['admin@example.test', 'wrong-password'])]
    #[TestWith(['missing@example.test', 'password'])]
    public function test_rejected_public_login_preserves_an_active_admin_session(string $email, string $password): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@example.test']);
        $this->login($admin, '/admin/login');
        $adminSessionId = $this->app['request']->session()->getId();
        $adminToken = $this->app['request']->session()->token();

        $this->browserRequest('POST', '/login', [
            'email' => $email,
            'password' => $password,
            'remember' => true,
        ])->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không hợp lệ.']);

        $this->assertGuest('web');
        $this->assertArrayNotHasKey(Auth::guard('web')->getRecallerName(), $this->browserCookies);
        $this->browserRequest('GET', '/posts/create')->assertRedirect(route('login'));
        $this->browserRequest('GET', '/admin')->assertOk();
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertSame($adminSessionId, $this->app['request']->session()->getId());
        $this->assertSame($adminToken, $this->app['request']->session()->token());
    }

    public function test_failed_admin_login_does_not_destroy_user_session_or_issue_remember_cookie(): void
    {
        $user = User::factory()->author()->create();
        $this->login($user, '/login');
        $webCookie = $this->browserCookies[config('session.cookie')];

        $this->browserRequest('POST', '/admin/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
            'role' => 'admin',
            'guard' => 'admin',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
        $this->assertArrayNotHasKey(Auth::guard('admin')->getRecallerName(), $this->browserCookies);
        $this->assertSame($webCookie, $this->browserCookies[config('session.cookie')]);
        $this->browserRequest('GET', '/profile')->assertSee($user->email);
    }

    public function test_session_ids_and_tokens_rotate_on_admin_login_and_logout(): void
    {
        $admin = User::factory()->admin()->create();
        $this->browserRequest('GET', '/admin/login')->assertOk();
        $oldId = $this->app['request']->session()->getId();
        $oldToken = $this->app['request']->session()->token();

        $this->login($admin, '/admin/login');
        $this->assertNotSame($oldId, $this->app['request']->session()->getId());
        $this->assertNotSame($oldToken, $this->app['request']->session()->token());
        $authenticatedId = $this->app['request']->session()->getId();
        $authenticatedToken = $this->app['request']->session()->token();

        $this->browserRequest('POST', '/admin/logout')->assertRedirect();
        $this->assertNotSame($authenticatedId, $this->app['request']->session()->getId());
        $this->assertNotSame($authenticatedToken, $this->app['request']->session()->token());
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.login'));
    }

    public function test_both_remember_cookies_restore_only_their_own_account(): void
    {
        $user = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $this->login($user, '/login', remember: true);
        $this->login($admin, '/admin/login', remember: true);

        unset($this->browserCookies[config('session.cookie')], $this->browserCookies[config('session.cookie').'_admin']);

        $this->browserRequest('GET', '/profile')->assertSee($user->email);
        $this->browserRequest('GET', '/admin')->assertOk();
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->browserRequest('POST', '/admin/logout')->assertRedirect();

        unset($this->browserCookies[config('session.cookie')], $this->browserCookies[config('session.cookie').'_admin']);

        $this->browserRequest('GET', '/profile')->assertSee($user->email);
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.login'));
    }

    public function test_locked_user_cannot_login_or_destroy_the_admin_session(): void
    {
        $admin = User::factory()->admin()->create();
        $lockedUser = User::factory()->viewer()->locked()->create();
        $this->login($admin, '/admin/login');

        $this->browserRequest('POST', '/login', [
            'email' => $lockedUser->email,
            'password' => 'password',
            'remember' => true,
        ])->assertSessionHasErrors('email');

        $this->assertGuest('web');
        $this->assertArrayNotHasKey(Auth::guard('web')->getRecallerName(), $this->browserCookies);
        $this->browserRequest('GET', '/admin')->assertOk();
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_otp_verification_uses_the_admin_identity_without_verifying_the_public_user(): void
    {
        Notification::fake();
        $user = User::factory()->viewer()->unverified()->create();
        $admin = User::factory()->admin()->unverified()->create();
        $this->login($user, '/login');
        $this->login($admin, '/admin/login');
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.verification.notice'));
        $this->browserRequest('GET', '/admin/verify-email')
            ->assertSee(route('admin.verification.otp.verify'))
            ->assertSee(route('admin.verification.send'))
            ->assertSee(route('admin.logout'));
        $this->browserRequest('POST', '/admin/email/verification-notification')->assertRedirect();
        Notification::assertSentTo($admin, EmailVerificationOtpNotification::class);
        Notification::assertNotSentTo($user, EmailVerificationOtpNotification::class);
        $otp = app(OtpService::class)->generate(
            email: $admin->email,
            purpose: OtpService::PURPOSE_EMAIL_VERIFICATION,
            user: $admin,
            force: true,
        );

        $this->browserRequest('POST', '/admin/verify-email', ['otp' => $otp])->assertRedirect(route('admin.dashboard'));

        $this->assertTrue($admin->fresh()->hasVerifiedEmail());
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->browserRequest('GET', '/admin')->assertOk();
        $this->browserRequest('GET', '/profile')->assertSee($user->email);
    }

    public function test_admin_signed_verification_link_cannot_verify_the_public_user(): void
    {
        $user = User::factory()->viewer()->unverified()->create();
        $admin = User::factory()->admin()->unverified()->create();
        $this->login($user, '/login');
        $this->login($admin, '/admin/login');
        $url = URL::temporarySignedRoute('admin.verification.verify', now()->addMinutes(10), [
            'id' => $admin->id,
            'hash' => sha1($admin->email),
        ]);

        $this->browserRequest('GET', $url)->assertRedirect(route('admin.dashboard', absolute: false).'?verified=1');

        $this->assertTrue($admin->fresh()->hasVerifiedEmail());
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_old_admin_cookies_cannot_restore_sessions_destroyed_by_login_or_logout(): void
    {
        $admin = User::factory()->admin()->create();
        $this->browserRequest('GET', '/admin/login')->assertOk();
        $guestCookies = $this->browserCookies;
        $this->login($admin, '/admin/login');
        $authenticatedCookies = $this->browserCookies;

        $this->browserCookies = $guestCookies;
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.login'));
        $this->browserCookies = $authenticatedCookies;
        $this->browserRequest('POST', '/admin/logout')->assertRedirect();
        $this->browserCookies = $authenticatedCookies;
        $this->browserRequest('GET', '/admin')->assertRedirect(route('admin.login'));
    }

    public function test_expired_user_session_does_not_expire_a_recent_admin_session(): void
    {
        $user = User::factory()->viewer()->create();
        $admin = User::factory()->admin()->create();
        config(['session.lifetime' => 120]);
        $this->login($user, '/login');
        $this->travel(119)->minutes();
        $this->login($admin, '/admin/login');
        $this->travel(2)->minutes();

        $this->browserRequest('GET', '/profile')->assertRedirect(route('login'));
        $this->browserRequest('GET', '/admin')->assertOk();
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    private function login(User $user, string $path, bool $remember = false): void
    {
        $this->browserRequest('POST', $path, [
            'email' => $user->email,
            'password' => 'password',
            'remember' => $remember,
        ])->assertRedirect();
    }

    /**
     * Exercise one cookie jar with fresh guard/session state, as in a browser.
     *
     * @param  array<string, mixed>  $data
     */
    private function browserRequest(string $method, string $path, array $data = []): TestResponse
    {
        Auth::forgetGuards();
        $this->app['session']->driver()->flush();

        if ($this->app->bound('session.admin')) {
            $this->app['session.admin']->driver()->flush();
        }

        $response = $this->call($method, $path, $data, $this->browserCookies);

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getExpiresTime() !== 0 && $cookie->getExpiresTime() < time()) {
                unset($this->browserCookies[$cookie->getName()]);
            } else {
                $this->browserCookies[$cookie->getName()] = $cookie->getValue();
            }
        }

        $this->app['cookie']->flushQueuedCookies();

        return $response;
    }
}
