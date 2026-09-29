<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_published_posts(): void
    {
        $publishedPost = Post::factory()->published()->create([
            'title' => 'Sample Published Post Title',
        ]);

        // Home / Listing
        $response = $this->get(route('posts.index'));
        $response->assertOk();
        $response->assertSee('Sample Published Post Title');

        // Post Detail
        $detailResponse = $this->get(route('posts.show', $publishedPost->slug));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Sample Published Post Title');
        $detailResponse->assertSee($publishedPost->user->name);
    }

    public function test_guest_cannot_see_unpublished_posts(): void
    {
        $draftPost = Post::factory()->create([
            'title' => 'Secret Draft Post',
            'status' => 'draft',
        ]);

        $pendingPost = Post::factory()->pending()->create([
            'title' => 'Pending Review Post',
        ]);

        $rejectedPost = Post::factory()->rejected()->create([
            'title' => 'Rejected Review Post',
        ]);

        // Listing should not show unpublished posts
        $indexResponse = $this->get(route('posts.index'));
        $indexResponse->assertOk();
        $indexResponse->assertDontSee('Secret Draft Post');
        $indexResponse->assertDontSee('Pending Review Post');
        $indexResponse->assertDontSee('Rejected Review Post');

        // Direct detail page access should return 404
        $this->get(route('posts.show', $draftPost->slug))->assertNotFound();
        $this->get(route('posts.show', $pendingPost->slug))->assertNotFound();
        $this->get(route('posts.show', $rejectedPost->slug))->assertNotFound();
    }

    public function test_search_works(): void
    {
        Post::factory()->published()->create([
            'title' => 'Mastering Laravel 12 Framework',
            'body' => 'Deep dive into service container and routing.',
        ]);

        Post::factory()->published()->create([
            'title' => 'Introduction to Flutter Mobile',
            'body' => 'Build cross-platform applications with Dart.',
        ]);

        $response = $this->get(route('posts.index', ['q' => 'Laravel 12']));

        $response->assertOk();
        $response->assertSee('Mastering Laravel 12 Framework');
        $response->assertDontSee('Introduction to Flutter Mobile');
    }

    public function test_pagination_keeps_query_parameter(): void
    {
        // 9 items per page, create 15 items matching the search query
        Post::factory()->count(15)->published()->create([
            'title' => fn () => 'KeywordMatch '.fake()->unique()->sentence(),
        ]);

        $response = $this->get(route('posts.index', ['q' => 'KeywordMatch']));

        $response->assertOk();
        // Check that page 2 link exists and preserves ?q=KeywordMatch
        $response->assertSee('page=2');
        $response->assertSee('q=KeywordMatch');
    }

    public function test_category_filter_works(): void
    {
        $techCategory = Category::factory()->create([
            'name' => 'Technology',
            'slug' => 'technology',
        ]);

        $foodCategory = Category::factory()->create([
            'name' => 'Food & Cooking',
            'slug' => 'food-cooking',
        ]);

        $techPost = Post::factory()->published()->create([
            'category_id' => $techCategory->id,
            'title' => 'Tech Gadgets 2026',
        ]);

        $foodPost = Post::factory()->published()->create([
            'category_id' => $foodCategory->id,
            'title' => 'Delicious Pasta Recipe',
        ]);

        $response = $this->get(route('posts.index', ['category' => 'technology']));

        $response->assertOk();
        $response->assertSee('Tech Gadgets 2026');
        $response->assertDontSee('Delicious Pasta Recipe');
    }

    public function test_post_detail_increments_views(): void
    {
        $post = Post::factory()->published()->create([
            'views' => 10,
        ]);

        $this->assertEquals(10, $post->views);

        // First visit increments view to 11
        $this->get(route('posts.show', $post->slug))->assertOk();
        $this->assertEquals(11, $post->fresh()->views);

        // Second visit increments view to 12
        $this->get(route('posts.show', $post->slug))->assertOk();
        $this->assertEquals(12, $post->fresh()->views);
    }

    public function test_guest_can_view_author_profile_and_posts(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Jane TechAuthor',
            'bio' => 'Senior Backend Developer & Writer',
        ]);

        $authorPost = Post::factory()->published()->create([
            'user_id' => $author->id,
            'title' => 'Jane First Article',
        ]);

        $response = $this->get(route('authors.show', $author));

        $response->assertOk();
        $response->assertSee('Jane TechAuthor');
        $response->assertSee('Senior Backend Developer & Writer');
        $response->assertSee('Jane First Article');
    }
}
