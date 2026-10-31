<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice', absolute: false));

        $user = auth()->user();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertNull($user->email_verified_at);
        Notification::assertSentToTimes($user, EmailVerificationOtpNotification::class, 1);
        $this->assertDatabaseCount('email_otps', 1);

        $firstPage = $this->get('/verify-email');
        $firstPage->assertOk();
        $this->assertMatchesRegularExpression('/id="otp-expiry-container" data-seconds="(?:29[5-9]|300)"/', $firstPage->getContent());

        $firstContent = $firstPage->getContent();
        preg_match('/data-expires-at="(\d+)"/', $firstContent, $expiresMatches);
        preg_match('/data-server-time="(\d+)"/', $firstContent, $serverMatches);
        $this->assertNotEmpty($expiresMatches[1] ?? null);
        $this->assertNotEmpty($serverMatches[1] ?? null);
        $diffMs = (int) $expiresMatches[1] - (int) $serverMatches[1];
        $this->assertGreaterThanOrEqual(295000, $diffMs);
        $this->assertLessThanOrEqual(300000, $diffMs);

        $remaining = app(OtpService::class)->getExpiryRemaining($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION);
        $this->assertGreaterThanOrEqual(295, $remaining);

        $this->travel(30)->seconds();
        $reloadedPage = $this->get('/verify-email');
        $reloadedPage->assertOk();
        $this->assertMatchesRegularExpression('/id="otp-expiry-container" data-seconds="2(?:6[8-9]|70)"/', $reloadedPage->getContent());
        $this->assertDatabaseCount('email_otps', 1);
        Notification::assertSentToTimes($user, EmailVerificationOtpNotification::class, 1);
        $this->assertEqualsWithDelta(270, app(OtpService::class)->getExpiryRemaining($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION), 2);

        $this->travel(271)->seconds();
        $expiredPage = $this->get('/verify-email');
        $expiredPage->assertSee('id="otp-expiry-container" data-seconds="0"', false);
        $expiredPage->assertSee('00:00 (Đã hết hạn)');
        $this->assertDatabaseCount('email_otps', 1);
    }

    public function test_registration_sends_otp_when_registered_event_did_not_create_one(): void
    {
        Event::fake([Registered::class]);
        Notification::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'fallback@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('verification.notice', absolute: false));

        $user = User::where('email', 'fallback@example.com')->firstOrFail();
        Notification::assertSentToTimes($user, EmailVerificationOtpNotification::class, 1);
        $this->assertDatabaseCount('email_otps', 1);
        $this->assertGreaterThan(0, app(OtpService::class)->getExpiryRemaining($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION));
    }

    public function test_resending_after_cooldown_replaces_the_otp_and_refreshes_the_countdown(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'resend@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'resend@example.com')->firstOrFail();
        $firstOtp = $user->emailOtps()->firstOrFail();

        $this->travel(61)->seconds();
        $this->post('/email/verification-notification')->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentToTimes($user, EmailVerificationOtpNotification::class, 2);
        $this->assertNotNull($firstOtp->fresh()->consumed_at);
        $this->assertDatabaseCount('email_otps', 2);
        $this->assertMatchesRegularExpression(
            '/id="otp-expiry-container" data-seconds="(?:29[5-9]|300)"/',
            $this->get('/verify-email')->getContent()
        );
    }

    public function test_registration_validation_vietnamese_messages(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'Vui lòng nhập họ và tên.',
            'email' => 'Địa chỉ email không đúng định dạng.',
            'password' => 'Mật khẩu xác nhận không khớp.',
        ]);
    }

    public function test_registration_duplicate_email_vietnamese_message(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Địa chỉ email này đã được đăng ký tài khoản.',
        ]);
    }
}
