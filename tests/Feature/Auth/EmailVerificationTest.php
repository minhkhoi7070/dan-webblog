<?php

namespace Tests\Feature\Auth;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
        $response->assertSee('Xác minh email');
        $response->assertSee($user->email);
    }

    public function test_verified_user_visiting_verification_screen_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_link_cannot_be_used_by_different_user(): void
    {
        $userA = User::factory()->unverified()->create();
        $userB = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $userA->id, 'hash' => sha1($userA->email)]
        );

        $response = $this->actingAs($userB)->get($verificationUrl);

        $response->assertForbidden();
        $this->assertFalse($userA->fresh()->hasVerifiedEmail());
        $this->assertFalse($userB->fresh()->hasVerifiedEmail());
    }

    public function test_expired_verification_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($expiredUrl);

        $response->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_notification_can_be_resent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertSentTo($user, EmailVerificationOtpNotification::class);
        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');
    }

    public function test_unverified_user_cannot_like_post(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->published()->create(['category_id' => $category->id]);

        $response = $this->actingAs($unverifiedUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('posts.like', $post));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $unverifiedUser->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_unverified_user_cannot_favorite_post(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->published()->create(['category_id' => $category->id]);

        $response = $this->actingAs($unverifiedUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('posts.favorite', $post));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $unverifiedUser->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_unverified_user_cannot_follow_author(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $author = User::factory()->author()->create();

        $response = $this->actingAs($unverifiedUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('authors.follow', $author));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $unverifiedUser->id,
            'following_id' => $author->id,
        ]);
    }

    public function test_unverified_user_cannot_create_comment(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->published()->create(['category_id' => $category->id]);

        $response = $this->actingAs($unverifiedUser)
            ->withHeaders(['Accept' => 'application/json'])
            ->postJson(route('comments.store', $post), [
                'content' => 'Bình luận thử nghiệm',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('comments', [
            'user_id' => $unverifiedUser->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_unverified_author_cannot_access_post_creation(): void
    {
        $unverifiedAuthor = User::factory()->author()->unverified()->create();

        $response = $this->actingAs($unverifiedAuthor)->get(route('posts.create'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_admin_cannot_access_admin_dashboard(): void
    {
        $unverifiedAdmin = User::factory()->admin()->unverified()->create();

        $response = $this->actingAs($unverifiedAdmin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_can_access_profile_to_update_email(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();

        $response = $this->actingAs($unverifiedUser)->get(route('profile.edit'));

        $response->assertStatus(200);
    }

    public function test_updating_email_in_profile_resets_verification_and_resends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'old@example.com']);
        $this->assertTrue($user->hasVerifiedEmail());

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();
        $this->assertEquals('new@example.com', $user->email);
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, EmailVerificationOtpNotification::class);
    }

    public function test_vietnamese_verification_email_content(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Nguyễn Văn A']);
        $notification = new EmailVerificationOtpNotification('123456');
        $mail = $notification->toMail($user);

        $this->assertEquals('[CloudBlog] Xác minh địa chỉ email: 123456', $mail->subject);
        $this->assertEquals('Xin chào Nguyễn Văn A,', $mail->greeting);
        $this->assertStringContainsString('123456', $mail->introLines[2]);
    }

    public function test_registration_handles_mail_delivery_failure_gracefully(): void
    {
        Event::listen(Registered::class, function () {
            throw new TransportException('SMTP connection refused');
        });

        $response = $this->post('/register', [
            'name' => 'Offline User',
            'email' => 'offline@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('warning');

        $user = User::where('email', 'offline@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertNull($user->email_verified_at);
    }

    public function test_resending_notification_handles_mail_delivery_failure_gracefully(): void
    {
        $user = User::factory()->unverified()->create();

        Notification::shouldReceive('send')
            ->once()
            ->andThrow(new TransportException('SMTP auth failed'));

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_unauthenticated_user_opening_verification_link_is_redirected_to_login_and_then_verified_after_login(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'guestverify@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 1. Guest clicks link in external browser/client -> redirected to /login
        $guestResponse = $this->get($verificationUrl);
        $guestResponse->assertRedirect(route('login'));
        $this->assertGuest();

        // 2. Guest logs in -> redirected to intended signed URL and email is verified
        $loginResponse = $this->post('/login', [
            'email' => 'guestverify@example.com',
            'password' => 'Secret123!',
        ]);

        $loginResponse->assertRedirect($verificationUrl);

        $verifyResponse = $this->actingAs($user)->get($verificationUrl);
        $verifyResponse->assertRedirect(route('dashboard', absolute: false).'?verified=1');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_can_be_verified_using_6_digit_otp(): void
    {
        $user = User::factory()->unverified()->create();
        $otpService = app(OtpService::class);
        $otp = $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user);

        Event::fake([Verified::class]);

        $response = $this->actingAs($user)->post('/verify-email', [
            'otp' => $otp,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_email_verification_fails_with_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create();
        $otpService = app(OtpService::class);
        $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user);

        $response = $this->actingAs($user)->post('/verify-email', [
            'otp' => '000000',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_fails_after_5_wrong_attempts(): void
    {
        $user = User::factory()->unverified()->create();
        $otpService = app(OtpService::class);
        $otp = $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user);

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post('/verify-email', ['otp' => '999999']);
        }

        // Even with the CORRECT OTP, 6th attempt fails because max attempts was reached
        $response = $this->actingAs($user)->post('/verify-email', ['otp' => $otp]);

        $response->assertSessionHasErrors('otp');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_resending_otp_invalidates_previous_otp(): void
    {
        $user = User::factory()->unverified()->create();
        $otpService = app(OtpService::class);

        $firstOtp = $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user);

        // Force generate second OTP
        $secondOtp = $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user, force: true);

        // First OTP should now be consumed / invalid
        $firstResult = $otpService->verify($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $firstOtp);
        $this->assertFalse($firstResult['success']);

        // Second OTP should succeed
        $secondResult = $otpService->verify($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $secondOtp);
        $this->assertTrue($secondResult['success']);
    }

    public function test_resending_otp_respects_cooldown(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $otpService = app(OtpService::class);
        $otpService->generate($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION, $user);

        // Immediate resend within 60s should trigger warning and not send mail
        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        $response->assertSessionHas('warning');
        Notification::assertNothingSent();
    }
}
