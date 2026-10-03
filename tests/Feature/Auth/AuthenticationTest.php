<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Chào mừng trở lại');
        $response->assertSee('Đăng nhập để tiếp tục với BlogMNM');
        $response->assertSee('login-glass-card');
        $response->assertSee('form-control-glass');
        $response->assertSee('btn-glass-primary');
        $response->assertSee('Quay lại BlogMNM');
        $response->assertSee('Ghi nhớ đăng nhập');
        $response->assertSee('Đăng ký ngay');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_author_login_redirects_to_home(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->post('/login', [
            'email' => $author->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($author);
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
