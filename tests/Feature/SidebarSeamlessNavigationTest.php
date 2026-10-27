<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarSeamlessNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_persistent_app_shell_elements_and_progress_bar_render_on_all_pages(): void
    {
        $viewer = User::factory()->viewer()->create();

        // 1. Home
        $homeResponse = $this->actingAs($viewer)->get(route('home'));
        $homeResponse->assertOk()
            ->assertSee('id="app-nav-progress"', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('data-app-content', false)
            ->assertSee('sidebar-container', false);

        // 2. Favorites
        $favResponse = $this->actingAs($viewer)->get(route('favorites.index'));
        $favResponse->assertOk()
            ->assertSee('id="app-nav-progress"', false)
            ->assertSee('data-app-content', false)
            ->assertSee('sidebar-container', false);

        // 3. Activity
        $actResponse = $this->actingAs($viewer)->get(route('activity.index'));
        $actResponse->assertOk()
            ->assertSee('id="app-nav-progress"', false)
            ->assertSee('data-app-content', false)
            ->assertSee('sidebar-container', false);

        // 4. Following
        $folResponse = $this->actingAs($viewer)->get(route('following.index'));
        $folResponse->assertOk()
            ->assertSee('id="app-nav-progress"', false)
            ->assertSee('data-app-content', false)
            ->assertSee('sidebar-container', false);
    }

    public function test_sidebar_editor_link_is_seamlessly_navigable_without_data_no_app_nav(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('home'));
        $response->assertOk();

        // Check sidebar navigation classes
        $response->assertSee('sidebar-link', false);

        // Editor creation link must NOT be opted out with data-no-app-nav
        $response->assertDontSee('data-no-app-nav', false);
        $response->assertSee(route('posts.create'), false);
    }

    public function test_author_can_fetch_create_post_via_seamless_app_nav_ajax(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-BlogMNM-App-Nav' => '1',
            ])
            ->get(route('posts.create'));

        $response->assertOk()
            ->assertSee('data-app-content', false)
            ->assertSee('id="tiptap-editor"', false)
            ->assertSee('id="create-post-form"', false);
    }

    public function test_app_nav_ajax_request_returns_valid_html_with_main_content(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $viewer->favoritePosts()->attach($post->id);

        $response = $this->actingAs($viewer)
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'X-BlogMNM-App-Nav' => '1',
            ])
            ->get(route('favorites.index'));

        $response->assertOk()
            ->assertSee('data-app-content', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('Bài viết đã lưu');
    }
}
