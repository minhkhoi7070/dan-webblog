<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])->get('/test-admin-only', function () {
            return response('admin-ok');
        });

        Route::middleware(['web', 'auth', 'role:author,admin'])->get('/test-author-or-admin', function () {
            return response('author-or-admin-ok');
        });
    }

    public function test_admin_can_access_admin_only_route(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/test-admin-only');

        $response->assertOk();
        $response->assertSee('admin-ok');
    }

    public function test_author_cannot_access_admin_only_route(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get('/test-admin-only');

        $response->assertForbidden();
    }

    public function test_viewer_cannot_access_admin_only_route(): void
    {
        $viewer = User::factory()->create();

        $response = $this->actingAs($viewer)->get('/test-admin-only');

        $response->assertForbidden();
    }

    public function test_author_can_access_author_or_admin_route(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get('/test-author-or-admin');

        $response->assertOk();
        $response->assertSee('author-or-admin-ok');
    }

    public function test_admin_can_access_author_or_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/test-author-or-admin');

        $response->assertOk();
        $response->assertSee('author-or-admin-ok');
    }

    public function test_viewer_cannot_access_author_or_admin_route(): void
    {
        $viewer = User::factory()->create();

        $response = $this->actingAs($viewer)->get('/test-author-or-admin');

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_protected_role_routes(): void
    {
        $this->get('/test-admin-only')->assertRedirect('/login');
        $this->get('/test-author-or-admin')->assertRedirect('/login');
    }
}
