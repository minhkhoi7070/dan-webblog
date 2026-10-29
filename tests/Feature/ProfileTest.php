<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_upload_avatar_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('my-photo.jpg', 300, 300);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Nguyen Van A',
                'email' => $user->email,
                'avatar' => $file,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertNotNull($user->avatar_url);
        $this->assertStringContainsString('storage/avatars/', $user->avatar_url);
    }

    public function test_user_can_upload_png_and_webp_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $pngFile = UploadedFile::fake()->image('avatar.png', 200, 200);
        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $pngFile,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringEndsWith('.png', $user->avatar);

        $webpFile = UploadedFile::fake()->image('avatar.webp', 200, 200);
        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $webpFile,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringEndsWith('.webp', $user->avatar);
    }

    public function test_old_avatar_is_deleted_when_new_avatar_is_uploaded(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $firstFile = UploadedFile::fake()->image('first.jpg', 200, 200);
        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $firstFile,
            ]);

        $user->refresh();
        $oldAvatarPath = $user->avatar;
        Storage::disk('public')->assertExists($oldAvatarPath);

        $secondFile = UploadedFile::fake()->image('second.png', 200, 200);
        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $secondFile,
            ]);

        $user->refresh();
        $newAvatarPath = $user->avatar;

        $this->assertNotSame($oldAvatarPath, $newAvatarPath);
        Storage::disk('public')->assertExists($newAvatarPath);
        Storage::disk('public')->assertMissing($oldAvatarPath);
    }

    public function test_avatar_must_be_a_valid_image_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $file,
            ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_avatar_size_cannot_exceed_2mb(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // 2.5 MB = 2560 KB
        $largeFile = UploadedFile::fake()->image('huge.jpg')->size(2560);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $largeFile,
            ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_updating_profile_without_avatar_keeps_existing_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('my-avatar.jpg', 200, 200);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $user->refresh();
        $savedPath = $user->avatar;

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name Only',
            'email' => $user->email,
        ]);

        $user->refresh();
        $this->assertSame('Updated Name Only', $user->name);
        $this->assertSame($savedPath, $user->avatar);
        Storage::disk('public')->assertExists($savedPath);
    }

    public function test_ajax_profile_update_returns_json_with_avatar_url(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('ajax-avatar.jpg', 250, 250);

        $response = $this
            ->actingAs($user)
            ->patchJson('/profile', [
                'name' => 'AJAX User',
                'email' => $user->email,
                'avatar' => $file,
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'profile-updated',
                'message' => 'Cập nhật thông tin hồ sơ thành công!',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => ['id', 'name', 'email', 'avatar_url'],
            ]);

        $user->refresh();
        $this->assertSame('AJAX User', $user->name);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_profile_update_clears_email_verified_at_and_triggers_otp_when_email_changes(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => 'brandnew@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();

        $this->assertSame('brandnew@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, EmailVerificationOtpNotification::class);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
