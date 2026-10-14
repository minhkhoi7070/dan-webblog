<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. GET /admin/login -> 200
     */
    public function test_01_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Quản trị viên');
        $response->assertSee('Admin');
        $response->assertDontSee('Đăng ký ngay');
    }

    /**
     * 2. Admin login thành công -> /admin/dashboard
     */
    public function test_02_admin_login_successful_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'admin@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    /**
     * 3. Viewer login qua /admin/login -> bị từ chối
     */
    public function test_03_viewer_login_via_admin_login_is_rejected(): void
    {
        $viewer = User::factory()->viewer()->create([
            'email' => 'viewer@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'viewer@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertSame('Tài khoản này không có quyền quản trị.', session('errors')->first('email'));
    }

    /**
     * 4. Author login qua /admin/login -> bị từ chối
     */
    public function test_04_author_login_via_admin_login_is_rejected(): void
    {
        $author = User::factory()->author()->create([
            'email' => 'author@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'author@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertSame('Tài khoản này không có quyền quản trị.', session('errors')->first('email'));
    }

    /**
     * 5. Admin truy cập /admin/dashboard -> 200
     */
    public function test_05_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * 6. Viewer truy cập /admin/dashboard -> 403 hoặc redirect
     */
    public function test_06_viewer_accessing_admin_dashboard_is_forbidden(): void
    {
        $viewer = User::factory()->viewer()->create();

        $response = $this->actingAs($viewer)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    /**
     * 7. Author truy cập /admin/dashboard -> 403 hoặc redirect
     */
    public function test_07_author_accessing_admin_dashboard_is_forbidden(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    /**
     * 8. Sai password -> login thất bại
     */
    public function test_08_admin_login_fails_with_wrong_password(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'admin@blogmnm.test',
            'password' => 'incorrect-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * 9. Locked admin -> không đăng nhập được
     */
    public function test_09_locked_admin_cannot_login(): void
    {
        User::factory()->admin()->locked()->create([
            'email' => 'locked-admin@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'locked-admin@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('khóa', session('errors')->first('email'));
    }

    /**
     * 10. CSRF middleware is active on admin login routes
     */
    public function test_10_csrf_protection_is_active_on_admin_login_routes(): void
    {
        $route = app('router')->getRoutes()->getByName('admin.login.store');

        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();

        $this->assertContains('web', $middleware);
    }
}
