<?php

namespace Tests\Feature\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Chào mừng trở lại');
        $response->assertSee('Đăng nhập để tiếp tục với CloudBlog');
        $response->assertSee('login-glass-card');
        $response->assertSee('form-control-glass');
        $response->assertSee('btn-glass-primary');
        $response->assertSee('Quay lại CloudBlog');
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

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_admin_public_login_is_rejected_with_the_generic_message(bool $locked): void
    {
        $admin = User::factory()->admin()->create(['is_locked' => $locked]);
        Event::fake([Login::class]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
            'remember' => true,
            'role' => 'viewer',
            'guard' => 'admin',
        ]);

        $response->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không hợp lệ.']);
        $response->assertCookieMissing(Auth::guard('web')->getRecallerName());
        $this->assertGuest('web');
        $this->assertGuest('admin');
        Event::assertNotDispatched(Login::class);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không hợp lệ.']);
    }

    public function test_unknown_email_uses_the_same_vietnamese_message_on_the_login_form(): void
    {
        $this->followingRedirects()->from('/login')->post('/login', [
            'email' => 'missing@example.test',
            'password' => 'password',
        ])->assertSee('Email hoặc mật khẩu không hợp lệ.')
            ->assertDontSee('These credentials do not match our records.');

        $this->assertGuest('web');
    }

    #[TestWith([[], ['email' => 'Email bắt buộc.', 'password' => 'Mật khẩu bắt buộc.']])]
    #[TestWith([['email' => 'invalid', 'password' => 'password'], ['email' => 'Email không đúng định dạng.']])]
    #[TestWith([['email' => ['invalid'], 'password' => ['invalid']], ['email' => 'Email phải là chuỗi ký tự.', 'password' => 'Mật khẩu phải là chuỗi ký tự.']])]
    public function test_login_validation_errors_are_in_vietnamese(array $credentials, array $errors): void
    {
        $this->post('/login', $credentials)->assertSessionHasErrors($errors);

        $this->assertGuest('web');
    }

    public function test_login_throttling_keeps_its_existing_limit_and_uses_vietnamese(): void
    {
        $this->freezeTime();
        $credentials = ['email' => 'missing@example.test', 'password' => 'password'];
        $request = LoginRequest::create('/login', 'POST', $credentials, server: ['REMOTE_ADDR' => '127.0.0.1']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', $credentials)->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không hợp lệ.']);
        }

        $this->assertSame(5, RateLimiter::attempts($request->throttleKey()));
        $this->post('/login', $credentials)->assertSessionHasErrors([
            'email' => 'Đăng nhập quá nhiều lần, vui lòng thử lại sau 60 giây.',
        ]);
        $this->assertGuest('web');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
