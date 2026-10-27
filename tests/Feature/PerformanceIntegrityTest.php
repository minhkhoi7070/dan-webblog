<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\PostContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_figure_and_caption_styles_cannot_overlay_the_page_or_load_remote_css_urls(): void
    {
        $html = '<figure style="position:fixed;inset:0;background:url(https://example.com/track)"><img src="/images/photo.jpg"><figcaption style="position:fixed;z-index:99999">A safe caption</figcaption></figure>';

        $clean = PostContentService::cleanHtml($html);

        $this->assertStringContainsString('A safe caption', $clean);
        $this->assertStringContainsString('<figure', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('example.com', $clean);
    }

    public function test_figure_and_caption_styles_preserve_safe_typography(): void
    {
        $html = '<figure style="text-align:center;position:fixed"><figcaption style="color:#123456;text-align:right">Caption</figcaption></figure>';

        $clean = PostContentService::cleanHtml($html);

        $this->assertStringContainsString('text-align: center', $clean);
        $this->assertStringContainsString('color: #123456; text-align: right', $clean);
        $this->assertStringNotContainsString('position', $clean);
    }

    public function test_ten_cards_read_viewer_state_in_one_query_without_per_card_lookups(): void
    {
        $viewer = User::factory()->viewer()->create();
        $posts = Post::factory()->published()->count(10)->create();
        $posts[0]->likers()->attach($viewer);
        $posts[0]->favoritedBy()->attach($viewer);

        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $loadedPosts = Post::withViewerState($viewer)->orderBy('id')->get();
            $states = $loadedPosts->map(fn (Post $post): array => [
                $post->isLikedBy($viewer), $post->isSavedBy($viewer),
            ])->all();
            $queryCount = count(DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
        }

        $this->assertSame([true, true], $states[0]);
        $this->assertSame([false, false], $states[9]);
        $this->assertSame(1, $queryCount);
    }

    public function test_preloaded_state_for_one_viewer_is_not_reused_for_another_user(): void
    {
        $viewer = User::factory()->viewer()->create();
        $otherViewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $post->likers()->attach($viewer);
        $post->favoritedBy()->attach($viewer);
        $loadedPost = Post::withViewerState($viewer)->findOrFail($post->id);

        $this->assertTrue($loadedPost->isLikedBy($viewer));
        $this->assertTrue($loadedPost->isSavedBy($viewer));
        $this->assertFalse($loadedPost->isLikedBy($otherViewer));
        $this->assertFalse($loadedPost->isSavedBy($otherViewer));
        $this->assertFalse($loadedPost->isLikedBy(null));
        $this->assertFalse($loadedPost->isSavedBy(null));
    }

    public function test_profile_renders_three_authored_cards_without_queries_in_each_card(): void
    {
        $author = User::factory()->author()->create();
        Post::factory()->published()->count(3)->create(['user_id' => $author->id]);

        $this->actingAs($author);
        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $response = $this->get(route('profile.show'));
            $queryCount = count(DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
        }

        $response->assertOk()->assertViewHas('userPosts', fn ($posts): bool => $posts->count() === 3);
        $this->assertLessThanOrEqual(8, $queryCount);
    }

    public function test_author_metrics_are_scoped_and_do_not_load_all_post_bodies(): void
    {
        $author = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create(['user_id' => $author->id, 'views' => 12]);
        $post->likers()->attach($viewer);
        Comment::factory()->approved()->count(2)->create(['post_id' => $post->id, 'user_id' => $viewer->id]);
        Post::factory()->published()->count(3)->create(['views' => 100]);

        $this->actingAs($author);
        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $response = $this->get(route('posts.stats'));
            $queryCount = count(DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
        }

        $response->assertOk()->assertViewHas('stats', [
            'total' => 1, 'draft' => 0, 'pending' => 0, 'published' => 1,
            'rejected' => 0, 'views' => 12, 'likes' => 1, 'comments' => 2,
        ]);
        $this->assertLessThanOrEqual(8, $queryCount);
    }

    public function test_empty_admin_content_metrics_are_zero_and_use_bounded_queries(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $response = $this->get(route('admin.dashboard'));
            $queryCount = count(DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
        }

        $response->assertOk()->assertViewHas('metrics', [
            'users' => 1, 'authors' => 0, 'viewers' => 0, 'posts' => 0,
            'draft' => 0, 'pending' => 0, 'published' => 0, 'rejected' => 0,
            'comments' => 0, 'pending_comments' => 0, 'spam_comments' => 0,
            'moderation_comments' => 0, 'views' => 0,
        ]);
        $this->assertLessThanOrEqual(5, $queryCount);
    }
}
