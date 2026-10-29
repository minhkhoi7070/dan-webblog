<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_and_account_section_is_rendered_in_settings_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
        $response->assertSee('Bảo mật & tài khoản', false);
        $response->assertSee('Đổi mật khẩu');
        $response->assertSee('Phiên đăng nhập');
        $response->assertSee('Xóa tài khoản');
        $response->assertSee('id="change-password-collapse"', false);
        $response->assertSee('id="confirm-user-deletion"', false);
        $response->assertSee('name="current_password"', false);
        $response->assertSee('name="password_confirmation"', false);

        // Assert collapsed by default (no "show" class on collapse container when no errors)
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/class="collapse\s*"\s+id="change-password-collapse"/', $content);
    }

    public function test_password_form_collapsible_is_expanded_when_validation_errors_occur(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->put('/password', [
                'current_password' => 'WrongPassword',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response->assertOk();
        $response->assertSee('id="change-password-collapse"', false);
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/class="collapse\s+show"\s+id="change-password-collapse"/', $content);
    }

    public function test_password_form_collapsible_is_expanded_when_password_updated_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->put('/password', [
                'current_password' => 'CurrentPassword123!',
                'password' => 'BrandNewPassword123!',
                'password_confirmation' => 'BrandNewPassword123!',
            ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('BrandNewPassword123!', $user->fresh()->password));

        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/class="collapse\s+show"\s+id="change-password-collapse"/', $content);
        $response->assertSee('Mật khẩu đã được cập nhật thành công!');
    }

    public function test_password_update_requires_matching_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123!'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'CurrentPassword123!',
                'password' => 'BrandNewPassword123!',
                'password_confirmation' => 'Mismatch123!',
            ]);

        $response->assertSessionHasErrorsIn('updatePassword', 'password');
        $this->assertFalse(Hash::check('BrandNewPassword123!', $user->fresh()->password));
    }

    public function test_user_can_logout_via_session_row(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_delete_account_modal_requires_password_and_fails_on_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'WrongSecret',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $response->assertRedirect('/profile');
        $this->assertNotNull($user->fresh());

        $followResponse = $this->actingAs($user)->get('/profile');
        $followResponse->assertSee('id="confirm-user-deletion"', false);
    }

    public function test_delete_account_succeeds_with_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'Secret123!',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
