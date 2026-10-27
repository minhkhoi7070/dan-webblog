<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_page_load_includes_feed_shell_categories_and_results(): void
    {
        $tech = Category::factory()->create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);
        $post = Post::factory()->published()->create([
            'category_id' => $tech->id,
            'title' => 'Bài viết công nghệ mới nhất',
        ]);

        $response = $this->get(route('posts.index'));

        $response->assertOk()
            ->assertViewIs('posts.index')
            ->assertSee('data-feed-shell', false)
            ->assertSee('data-feed-category=""', false)
            ->assertSee('data-feed-category="cong-nghe"', false)
            ->assertSee('id="feed-results"', false)
            ->assertSee('id="feed-skeleton-template"', false)
            ->assertSee($post->title);
    }

    public function test_ajax_category_request_returns_only_partial_fragment_with_headers(): void
    {
        $category = Category::factory()->create(['name' => 'Trí tuệ nhân tạo', 'slug' => 'tri-tue-nhan-tao']);
        $post = Post::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Khám phá thế giới AI',
        ]);

        $response = $this->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('posts.index', ['category' => $category->slug]));

        $response->assertOk()
            ->assertViewIs('posts.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertHeader('Vary', 'X-BlogMNM-Feed')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('id="feed-results"', false)
            ->assertSee($post->title)
            ->assertDontSee('<!DOCTYPE html>', false)
            ->assertDontSee('feed-top-header', false)
            ->assertDontSee('main-layout', false);
    }

    public function test_category_filter_matches_only_posts_in_category_for_both_full_page_and_fragment(): void
    {
        $tech = Category::factory()->create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);
        $business = Category::factory()->create(['name' => 'Kinh doanh', 'slug' => 'kinh-doanh']);

        $techPost = Post::factory()->published()->create([
            'category_id' => $tech->id,
            'title' => 'Công nghệ lượng tử',
        ]);
        $bizPost = Post::factory()->published()->create([
            'category_id' => $business->id,
            'title' => 'Kinh tế thị trường',
        ]);

        $url = route('posts.index', ['category' => $tech->slug]);

        $fullPage = $this->get($url);
        $fragment = $this->withHeader('X-BlogMNM-Feed', '1')->get($url);

        $fullPage->assertOk()
            ->assertSee($techPost->title)
            ->assertDontSee($bizPost->title);

        $fragment->assertOk()
            ->assertSee($techPost->title)
            ->assertDontSee($bizPost->title);

        $this->assertSame(
            $fullPage->viewData('posts')->pluck('id')->all(),
            $fragment->viewData('posts')->pluck('id')->all()
        );
        $this->assertSame([$techPost->id], $fragment->viewData('posts')->pluck('id')->all());
    }

    public function test_all_categories_view_returns_all_published_posts(): void
    {
        $cat1 = Category::factory()->create(['slug' => 'cat-1']);
        $cat2 = Category::factory()->create(['slug' => 'cat-2']);

        $post1 = Post::factory()->published()->create(['category_id' => $cat1->id]);
        $post2 = Post::factory()->published()->create(['category_id' => $cat2->id]);
        $draft = Post::factory()->create(['category_id' => $cat1->id, 'status' => 'draft']);

        $response = $this->withHeader('X-BlogMNM-Feed', '1')->get(route('posts.index'));

        $response->assertOk();
        $returnedIds = $response->viewData('posts')->pluck('id')->all();

        $this->assertContains($post1->id, $returnedIds);
        $this->assertContains($post2->id, $returnedIds);
        $this->assertNotContains($draft->id, $returnedIds);
    }

    public function test_array_parameters_are_rejected_with_validation_error(): void
    {
        $this->getJson(route('posts.index', ['q' => ['invalid']]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('q');

        $this->getJson(route('posts.index', ['category' => ['invalid']]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category');
    }

    public function test_authenticated_user_interaction_state_is_present_in_fragment(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = Category::factory()->create(['slug' => 'test-cat']);
        $post = Post::factory()->published()->create(['category_id' => $category->id]);

        $post->likers()->attach($viewer);
        $post->favoritedBy()->attach($viewer);

        $response = $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('posts.index', ['category' => $category->slug]));

        $response->assertOk()
            ->assertSee('aria-pressed="true"', false);
    }

    public function test_empty_category_displays_empty_state_without_crashing(): void
    {
        $emptyCategory = Category::factory()->create(['name' => 'Chuyên mục trống', 'slug' => 'chuyen-muc-trong']);

        $response = $this->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('posts.index', ['category' => $emptyCategory->slug]));

        $response->assertOk()
            ->assertSee('Chưa có bài viết nào phù hợp')
            ->assertSee('Quay lại tất cả bài viết');
    }
}
