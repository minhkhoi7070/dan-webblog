<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewerToAuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Viewer login redirects to route('home')
     */
    public function test_01_viewer_login_redirects_to_home(): void
    {
        $viewer = User::factory()->viewer()->create([
            'email' => 'viewer@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'viewer@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($viewer);
        $response->assertRedirect(route('home', absolute: false));
    }

    /**
     * 2. Author login redirects to route('home')
     */
    public function test_02_author_login_redirects_to_home(): void
    {
        $author = User::factory()->author()->create([
            'email' => 'author@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'author@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($author);
        $response->assertRedirect(route('home', absolute: false));
    }

    /**
     * 3. Admin login redirects to route('admin.dashboard')
     */
    public function test_03_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@blogmnm.test',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@blogmnm.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    /**
     * 4. Viewer sees "Trở thành tác giả" CTA on Home and Profile
     */
    public function test_04_viewer_sees_become_author_cta(): void
    {
        $viewer = User::factory()->viewer()->create();

        $homeResponse = $this->actingAs($viewer)->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Trở thành tác giả');
        $homeResponse->assertDontSee('Đăng bài mới');

        $profileResponse = $this->actingAs($viewer)->get(route('profile.show'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Trở thành tác giả');
    }

    /**
     * 5. Author sees "Viết bài" CTA on Home and does not see "Trở thành tác giả"
     */
    public function test_05_author_sees_write_post_cta(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Viết bài');
        $response->assertDontSee('Trở thành tác giả');
    }

    /**
     * 6. Viewer after becoming author can access route('posts.create')
     */
    public function test_06_viewer_after_becoming_author_can_access_create_post(): void
    {
        $viewer = User::factory()->viewer()->create();

        $upgradeResponse = $this->actingAs($viewer)->post(route('author.become'));
        $upgradeResponse->assertRedirect(route('posts.create'));

        $viewer->refresh();
        $this->assertSame('author', $viewer->role);

        $createResponse = $this->actingAs($viewer)->get(route('posts.create'));
        $createResponse->assertStatus(200);
    }

    /**
     * 7. Viewer cannot access route('posts.create') before becoming author
     */
    public function test_07_viewer_cannot_access_create_post_before_becoming_author(): void
    {
        $viewer = User::factory()->viewer()->create();

        $response = $this->actingAs($viewer)->get(route('posts.create'));
        $response->assertForbidden();
    }

    /**
     * 8. Viewer cannot self-escalate to admin via request payloads
     */
    public function test_08_viewer_cannot_self_escalate_to_admin(): void
    {
        $viewer = User::factory()->viewer()->create();

        // Attempt role escalation via become-author
        $this->actingAs($viewer)->post(route('author.become'), [
            'role' => 'admin',
        ]);

        $viewer->refresh();
        $this->assertSame('author', $viewer->role);
        $this->assertFalse($viewer->isAdmin());

        // Attempt role escalation via profile update
        $this->actingAs($viewer)->patch(route('profile.update'), [
            'name' => 'Viewer Hacker',
            'email' => $viewer->email,
            'role' => 'admin',
        ]);

        $viewer->refresh();
        $this->assertSame('author', $viewer->role);
        $this->assertFalse($viewer->isAdmin());
    }

    /**
     * 9. Author cannot self-publish posts (always stored as draft)
     */
    public function test_09_author_cannot_self_publish_posts(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'category_id' => $category->id,
            'title' => 'Self Publish Attempt Article',
            'body' => 'Detailed content body for testing post submission flow.',
            'status' => 'published',
        ]);

        $post = Post::where('title', 'Self Publish Attempt Article')->first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
    }

    /**
     * 10. Admin dashboard still functions and blocks viewer and author
     */
    public function test_10_admin_dashboard_still_functions_and_blocks_viewer_and_author(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();

        // Admin can access admin dashboard
        $adminResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminResponse->assertStatus(200);

        // Author is forbidden
        $authorResponse = $this->actingAs($author)->get(route('admin.dashboard'));
        $authorResponse->assertForbidden();

        // Viewer is forbidden
        $viewerResponse = $this->actingAs($viewer)->get(route('admin.dashboard'));
        $viewerResponse->assertForbidden();

        // Legacy /dashboard route redirects appropriately
        $adminDashboardRedirect = $this->actingAs($admin)->get(route('dashboard'));
        $adminDashboardRedirect->assertRedirect(route('admin.dashboard'));

        $viewerDashboardRedirect = $this->actingAs($viewer)->get(route('dashboard'));
        $viewerDashboardRedirect->assertRedirect(route('home'));
    }
}
