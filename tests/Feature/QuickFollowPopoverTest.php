<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickFollowPopoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_card_renders_quick_follow_open_trigger_for_unfollowed_author(): void
    {
        $author = User::factory()->create(['name' => 'John Doe']);
        $viewer = User::factory()->create(['name' => 'Jane Viewer']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->actingAs($viewer)->view('components.post-card', ['post' => $post]);

        $view->assertSee('threads-avatar-follow-btn');
        $view->assertSee('data-action="quick-follow-open"', false);
        $view->assertSee('data-following="false"', false);
        $view->assertSee('aria-pressed="false"', false);
        $view->assertSee('aria-haspopup="dialog"', false);
        $view->assertSee('follow-icon-plus', false);
        $view->assertSee('follow-icon-check', false);
    }

    public function test_post_card_renders_quick_follow_open_trigger_for_followed_author_without_hiding_button(): void
    {
        $author = User::factory()->create(['name' => 'John Doe']);
        $viewer = User::factory()->create(['name' => 'Jane Viewer']);
        $viewer->following()->attach($author->id);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->actingAs($viewer)->view('components.post-card', ['post' => $post]);

        $rendered = (string) $view;

        // Button itself must NOT have d-none
        $this->assertMatchesRegularExpression('/class="threads-avatar-follow-btn"\s*>/', $rendered);
        $this->assertStringContainsString('data-action="quick-follow-open"', $rendered);
        $this->assertStringContainsString('data-following="true"', $rendered);
        $this->assertStringContainsString('aria-pressed="true"', $rendered);
        $this->assertStringContainsString('title="Đang theo dõi John Doe"', $rendered);

        // Plus icon should have d-none, checkmark icon should not have d-none
        $this->assertMatchesRegularExpression('/follow-icon-plus\s+d-none/', $rendered);
        $this->assertMatchesRegularExpression('/follow-icon-check\s*(?!"[^"]*d-none)/', $rendered);
    }

    public function test_post_card_does_not_render_quick_follow_trigger_for_post_author(): void
    {
        $author = User::factory()->create(['name' => 'Self Author']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->actingAs($author)->view('components.post-card', ['post' => $post]);

        $view->assertDontSee('threads-avatar-follow-btn');
        $view->assertDontSee('data-action="quick-follow-open"', false);
    }

    public function test_post_card_renders_guest_quick_follow_trigger_with_login_metadata(): void
    {
        $author = User::factory()->create(['name' => 'Public Author']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->view('components.post-card', ['post' => $post]);

        $view->assertSee('threads-avatar-follow-btn');
        $view->assertSee('data-action="quick-follow-open"', false);
        $view->assertSee('data-guest="true"', false);
        $view->assertSee('data-login-url="'.route('login').'"', false);
        $view->assertSee('follow-icon-plus');
    }

    public function test_author_follow_and_unfollow_endpoints_return_correct_json_payload(): void
    {
        $author = User::factory()->create();
        $viewer = User::factory()->create();

        // 1. Follow
        $followResponse = $this->actingAs($viewer)
            ->postJson(route('authors.follow', $author->id), ['following' => true]);

        $followResponse->assertOk()
            ->assertJson([
                'following' => true,
            ]);

        $this->assertTrue($viewer->isFollowing($author));

        // 2. Unfollow
        $unfollowResponse = $this->actingAs($viewer)
            ->postJson(route('authors.follow', $author->id), ['following' => false]);

        $unfollowResponse->assertOk()
            ->assertJson([
                'following' => false,
            ]);

        $this->assertFalse($viewer->isFollowing($author));
    }

    public function test_cannot_follow_oneself_via_endpoint(): void
    {
        $author = User::factory()->create();

        $response = $this->actingAs($author)
            ->postJson(route('authors.follow', $author->id), ['following' => true]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot follow self.']);
    }

    public function test_guest_cannot_follow_via_endpoint(): void
    {
        $author = User::factory()->create();

        $response = $this->postJson(route('authors.follow', $author->id), ['following' => true]);

        $response->assertUnauthorized();
    }
}
