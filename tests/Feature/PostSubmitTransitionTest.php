<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSubmitTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_submit_an_authors_published_post_and_counters_stay_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->published()->create([
            'title' => 'Still Public After Invalid Submit',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDay(),
        ]);
        $publishedAt = $post->published_at->toDateTimeString();
        $reviewedAt = $post->reviewed_at->toDateTimeString();

        $this->actingAs($admin)->postJson(route('posts.submit', $post))->assertUnprocessable();

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertSame($publishedAt, $post->published_at->toDateTimeString());
        $this->assertSame($reviewedAt, $post->reviewed_at->toDateTimeString());
        $this->assertSame($admin->id, $post->reviewed_by);
        $this->get(route('home'))->assertSee('Still Public After Invalid Submit');
        $this->get(route('posts.stats'))->assertViewHas('stats', fn (array $stats): bool => $stats['published'] === 1 && $stats['pending'] === 0);
    }

    public function test_admin_cannot_submit_their_own_already_published_post_again(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->for($admin)->published()->create();
        $publishedAt = $post->published_at->toDateTimeString();

        $this->actingAs($admin)->postJson(route('posts.submit', $post))->assertUnprocessable();

        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame($publishedAt, $post->fresh()->published_at->toDateTimeString());
    }

    public function test_author_cannot_resubmit_an_already_pending_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->pending()->create();

        $this->actingAs($author)->postJson(route('posts.submit', $post))->assertForbidden();

        $this->assertSame('pending', $post->fresh()->status);
    }

    public function test_admin_can_publish_own_rejected_post_without_review_queue(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->for($admin)->rejected()->create(['rejection_reason' => 'Earlier feedback']);

        $this->actingAs($admin)->postJson(route('posts.submit', $post))->assertOk()->assertJsonPath('status', 'published');

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertNull($post->rejection_reason);
        $this->assertDatabaseCount('posts', 1);
    }

    public function test_admin_cannot_submit_an_authors_pending_post_instead_of_moderating_it(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->pending()->create();

        $this->actingAs($admin)->postJson(route('posts.submit', $post))->assertUnprocessable();

        $this->assertSame('pending', $post->fresh()->status);
    }
}
