<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Notifications\PasswordResetOtpNotification;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Quên mật khẩu');
    }

    public function test_password_reset_otp_can_be_requested_for_existing_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertRedirect(route('password.otp'));
        $this->assertEquals('user@example.com', session('password_reset_email'));

        Notification::assertSentTo($user, PasswordResetOtpNotification::class, function ($notification) {
            $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $notification->otp);

            return true;
        });

        $this->assertDatabaseHas('email_otps', [
            'email' => 'user@example.com',
            'purpose' => OtpService::PURPOSE_PASSWORD_RESET,
            'consumed_at' => null,
        ]);
    }

    public function test_password_reset_request_for_nonexistent_email_does_not_reveal_enumeration(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        // Anti-enumeration: still redirects to OTP screen without error
        $response->assertRedirect(route('password.otp'));
        Notification::assertNothingSent();
    }

    public function test_otp_screen_can_be_rendered_when_email_in_session(): void
    {
        $response = $this->withSession(['password_reset_email' => 'user@example.com'])
            ->get('/forgot-password/otp');

        $response->assertStatus(200);
        $response->assertSee('Nhập mã OTP đặt lại mật khẩu');
        $response->assertSee('user@example.com');
    }

    public function test_otp_screen_redirects_to_request_when_no_email_in_session(): void
    {
        $response = $this->get('/forgot-password/otp');

        $response->assertRedirect(route('password.request'));
    }

    public function test_password_reset_otp_can_be_verified_granting_reset_token(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'user@example.com']);
        $otpService = app(OtpService::class);
        $otp = $otpService->generate('user@example.com', OtpService::PURPOSE_PASSWORD_RESET, $user);

        $response = $this->withSession(['password_reset_email' => 'user@example.com'])
            ->post('/forgot-password/verify-otp', [
                'otp' => $otp,
            ]);

        $response->assertSessionHas('password_reset_auth');
        $resetAuth = session('password_reset_auth');
        $this->assertEquals('user@example.com', $resetAuth['email']);

        $response->assertRedirect();
        $this->assertStringContainsString('reset-password/', $response->headers->get('Location'));
    }

    public function test_password_reset_otp_fails_with_invalid_code(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $otpService = app(OtpService::class);
        $otpService->generate('user@example.com', OtpService::PURPOSE_PASSWORD_RESET, $user);

        $response = $this->withSession(['password_reset_email' => 'user@example.com'])
            ->post('/forgot-password/verify-otp', [
                'otp' => '000000',
            ]);

        $response->assertSessionHasErrors('otp');
        $this->assertNull(session('password_reset_auth'));
    }

    public function test_password_reset_otp_fails_after_5_attempts(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $otpService = app(OtpService::class);
        $otpService->generate('user@example.com', OtpService::PURPOSE_PASSWORD_RESET, $user);

        for ($i = 0; $i < 5; $i++) {
            $this->withSession(['password_reset_email' => 'user@example.com'])
                ->post('/forgot-password/verify-otp', ['otp' => '999999']);
        }

        // 6th attempt with ANY code should fail because max attempts is exceeded
        $response = $this->withSession(['password_reset_email' => 'user@example.com'])
            ->post('/forgot-password/verify-otp', ['otp' => '999999']);

        $response->assertSessionHasErrors('otp');
    }

    public function test_password_cannot_be_reset_without_valid_session_token(): void
    {
        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('password.request'));
    }

    public function test_password_can_be_reset_with_valid_verified_token(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = 'valid-reset-token-sample';
        $tokenHash = hash('sha256', $token.config('app.key'));

        $response = $this->withSession([
            'password_reset_auth' => [
                'email' => 'user@example.com',
                'token_hash' => $tokenHash,
                'expires_at' => now()->addMinutes(15)->timestamp,
            ],
        ])->post('/reset-password', [
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Verify password updated in DB
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
        $this->assertFalse(Hash::check('OldPassword123!', $user->password));

        // Verify notification sent
        Notification::assertSentTo($user, PasswordChangedNotification::class);
    }

    public function test_password_reset_does_not_verify_unverified_user_email(): void
    {
        Notification::fake();

        $unverifiedUser = User::factory()->unverified()->create([
            'email' => 'unverified@example.com',
        ]);
        $this->assertNull($unverifiedUser->email_verified_at);

        $token = 'sample-token-123';
        $tokenHash = hash('sha256', $token.config('app.key'));

        $this->withSession([
            'password_reset_auth' => [
                'email' => 'unverified@example.com',
                'token_hash' => $tokenHash,
                'expires_at' => now()->addMinutes(15)->timestamp,
            ],
        ])->post('/reset-password', [
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $unverifiedUser->refresh();
        $this->assertNull($unverifiedUser->email_verified_at);
        $this->assertFalse($unverifiedUser->hasVerifiedEmail());
    }

    public function test_password_reset_otp_cannot_be_used_for_email_verification(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'user@example.com']);
        $otpService = app(OtpService::class);

        // Generate password reset OTP
        $resetOtp = $otpService->generate('user@example.com', OtpService::PURPOSE_PASSWORD_RESET, $user);

        // Try verifying it for email verification
        $result = $otpService->verify('user@example.com', OtpService::PURPOSE_EMAIL_VERIFICATION, $resetOtp);

        $this->assertFalse($result['success']);
        $this->assertEquals(OtpService::STATUS_NOT_FOUND, $result['status']);
    }
}
