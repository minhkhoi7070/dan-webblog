<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AjaxPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_feed_ajax_pagination_preserves_query_parameters(): void
    {
        $category = Category::factory()->create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);

        // Create 25 matching posts
        Post::factory()->count(25)->published()->create([
            'category_id' => $category->id,
            'title' => fn () => 'Laravel Masterclass '.fake()->unique()->word(),
        ]);

        $url = route('posts.index', [
            'q' => 'Laravel Masterclass',
            'category' => $category->slug,
            'page' => 2,
        ]);

        // Full page request
        $fullPage = $this->get($url);
        $fullPage->assertOk()
            ->assertViewIs('posts.index')
            ->assertSee('data-feed-shell', false)
            ->assertSee('id="feed-results"', false);

        // AJAX fragment request
        $ajax = $this->withHeader('X-BlogMNM-Feed', '1')->get($url);
        $ajax->assertOk()
            ->assertViewIs('posts.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertHeader('Vary', 'X-BlogMNM-Feed')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('page=1')
            ->assertSee('q=Laravel%20Masterclass')
            ->assertSee('category=cong-nghe')
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_following_feed_ajax_pagination_for_posts_and_authors(): void
    {
        $viewer = User::factory()->viewer()->create();
        $authors = User::factory()->count(25)->author()->create();

        foreach ($authors as $author) {
            $viewer->following()->attach($author->id);
            Post::factory()->published()->create(['user_id' => $author->id]);
        }

        // Test posts feed tab
        $postsUrl = route('following.index', ['tab' => 'feed', 'page' => 2]);
        $ajaxPosts = $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($postsUrl);

        $ajaxPosts->assertOk()
            ->assertViewIs('following.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false);

        // Test authors tab
        $authorsUrl = route('following.index', ['tab' => 'authors', 'page' => 2]);
        $ajaxAuthors = $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($authorsUrl);

        $ajaxAuthors->assertOk()
            ->assertViewIs('following.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false);
    }

    public function test_favorites_feed_ajax_pagination(): void
    {
        $viewer = User::factory()->viewer()->create();
        $posts = Post::factory()->count(25)->published()->create();

        foreach ($posts as $post) {
            $viewer->favoritePosts()->attach($post->id);
        }

        $url = route('favorites.index', ['page' => 2]);

        $ajax = $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($url);

        $ajax->assertOk()
            ->assertViewIs('favorites.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false);
    }

    public function test_author_profile_ajax_pagination(): void
    {
        $author = User::factory()->author()->create();
        Post::factory()->count(25)->published()->create(['user_id' => $author->id]);

        $url = route('authors.show', ['user' => $author, 'page' => 2]);

        $ajax = $this->withHeader('X-BlogMNM-Feed', '1')->get($url);

        $ajax->assertOk()
            ->assertViewIs('authors.partials.feed-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false);
    }

    public function test_author_posts_stats_and_comments_ajax_pagination(): void
    {
        $author = User::factory()->author()->create();
        $posts = Post::factory()->count(25)->create(['user_id' => $author->id]);

        foreach ($posts as $post) {
            Comment::factory()->count(2)->create(['post_id' => $post->id]);
        }

        // Stats pagination
        $statsUrl = route('author.posts.stats', ['page' => 2, 'status' => 'draft']);
        $ajaxStats = $this->actingAs($author)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($statsUrl);

        $ajaxStats->assertOk()
            ->assertViewIs('author.posts.partials.stats-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('status=draft');

        // Author comments pagination
        $commentsUrl = route('author.comments.index', ['page' => 2, 'status' => 'pending']);
        $ajaxComments = $this->actingAs($author)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($commentsUrl);

        $ajaxComments->assertOk()
            ->assertViewIs('author.comments.partials.comment-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('status=pending');
    }

    public function test_admin_cms_ajax_pagination_for_posts_users_comments_categories_tags(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Admin Posts
        Post::factory()->count(25)->create(['status' => 'published']);
        $adminPostsUrl = route('admin.posts.index', ['page' => 2, 'status' => 'published']);
        $ajaxPosts = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($adminPostsUrl);
        $ajaxPosts->assertOk()
            ->assertViewIs('admin.posts.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('status=published');

        // 2. Admin Users
        User::factory()->count(25)->viewer()->create();
        $adminUsersUrl = route('admin.users.index', ['page' => 2, 'role' => 'viewer']);
        $ajaxUsers = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($adminUsersUrl);
        $ajaxUsers->assertOk()
            ->assertViewIs('admin.users.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('role=viewer');

        // 3. Admin Comments
        Comment::factory()->count(25)->create(['status' => 'pending']);
        $adminCommentsUrl = route('admin.comments.index', ['page' => 2, 'status' => 'pending']);
        $ajaxComments = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($adminCommentsUrl);
        $ajaxComments->assertOk()
            ->assertViewIs('admin.comments.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('status=pending');

        // 4. Admin Categories
        Category::factory()->count(25)->create();
        $adminCatsUrl = route('admin.categories.index', ['page' => 2, 'q' => 'cat']);
        $ajaxCats = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($adminCatsUrl);
        $ajaxCats->assertOk()
            ->assertViewIs('admin.categories.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('q=cat');

        // 5. Admin Tags
        Tag::factory()->count(25)->create();
        $adminTagsUrl = route('admin.tags.index', ['page' => 2, 'q' => 'tag']);
        $ajaxTags = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($adminTagsUrl);
        $ajaxTags->assertOk()
            ->assertViewIs('admin.tags.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('q=tag');
    }

    public function test_pagination_boundary_and_empty_state(): void
    {
        // 1. Empty state when 0 posts exist
        $response = $this->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('posts.index'));

        $response->assertOk()
            ->assertViewIs('posts.partials.feed-results')
            ->assertSee('id="feed-results"', false)
            ->assertSee('Chưa có bài viết nào');

        // 2. Out of bounds page redirects to max page
        Post::factory()->count(10)->published()->create();
        $boundaryResponse = $this->get(route('posts.index', ['page' => 999]));
        $boundaryResponse->assertRedirect(route('posts.index', ['page' => 1]));
    }

    public function test_unauthorized_access_to_protected_paginated_routes(): void
    {
        $viewer = User::factory()->viewer()->create();

        // Guest redirected to login
        $this->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('admin.posts.index', ['page' => 2]))
            ->assertRedirect(route('login'));

        $this->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('author.posts.stats', ['page' => 2]))
            ->assertRedirect(route('login'));

        // Viewer forbidden from admin
        $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('admin.posts.index', ['page' => 2]))
            ->assertForbidden();

        // Viewer forbidden from author stats
        $this->actingAs($viewer)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('author.posts.stats', ['page' => 2]))
            ->assertForbidden();
    }

    public function test_admin_posts_ajax_status_tabs_filtering(): void
    {
        $admin = User::factory()->admin()->create();

        $pendingPost = Post::factory()->create(['status' => 'pending', 'title' => 'Bài viết chờ duyệt']);
        $publishedPost = Post::factory()->published()->create(['title' => 'Bài viết đã xuất bản']);
        $draftPost = Post::factory()->create(['status' => 'draft', 'title' => 'Bài viết bản nháp']);

        // 1. Filter by Pending
        $pendingResponse = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('admin.posts.index', ['status' => 'pending']));

        $pendingResponse->assertOk()
            ->assertViewIs('admin.posts.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('data-feed-current-status="pending"', false)
            ->assertSee($pendingPost->title)
            ->assertDontSee($publishedPost->title)
            ->assertDontSee($draftPost->title)
            ->assertDontSee('<!DOCTYPE html>', false);

        // 2. Filter by Published
        $publishedResponse = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('admin.posts.index', ['status' => 'published']));

        $publishedResponse->assertOk()
            ->assertSee('data-feed-current-status="published"', false)
            ->assertSee($publishedPost->title)
            ->assertDontSee($pendingPost->title);

        // 3. Status Counts JSON present in partial
        $pendingResponse->assertSee('data-feed-counts=', false);
    }

    public function test_admin_posts_ajax_search_and_category_filtering(): void
    {
        $admin = User::factory()->admin()->create();
        $catTech = Category::factory()->create(['name' => 'Công nghệ']);
        $catBiz = Category::factory()->create(['name' => 'Kinh doanh']);

        $matchPost = Post::factory()->create([
            'category_id' => $catTech->id,
            'title' => 'Tìm kiếm bài viết công nghệ AI',
            'status' => 'published',
        ]);
        $diffCatPost = Post::factory()->create([
            'category_id' => $catBiz->id,
            'title' => 'Tìm kiếm bài viết kinh doanh AI',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get(route('admin.posts.index', [
                'q' => 'công nghệ AI',
                'category_id' => $catTech->id,
            ]));

        $response->assertOk()
            ->assertViewIs('admin.posts.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee($matchPost->title)
            ->assertDontSee($diffCatPost->title)
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_admin_posts_combined_status_search_category_pagination(): void
    {
        $admin = User::factory()->admin()->create();
        $cat = Category::factory()->create(['name' => 'Lập trình']);

        // Create 25 matching posts
        Post::factory()->count(25)->create([
            'category_id' => $cat->id,
            'status' => 'published',
            'title' => fn () => 'Chuyên đề Laravel '.fake()->unique()->word(),
        ]);

        $url = route('admin.posts.index', [
            'status' => 'published',
            'q' => 'Chuyên đề Laravel',
            'category_id' => $cat->id,
            'page' => 2,
        ]);

        $response = $this->actingAs($admin)
            ->withHeader('X-BlogMNM-Feed', '1')
            ->get($url);

        $response->assertOk()
            ->assertViewIs('admin.posts.partials.table-results')
            ->assertHeader('X-BlogMNM-Feed', '1')
            ->assertSee('id="feed-results"', false)
            ->assertSee('data-feed-pagination', false)
            ->assertSee('status=published')
            ->assertSee('q=Chuy%C3%AAn%20%C4%91%E1%BB%81%20Laravel')
            ->assertSee('category_id='.$cat->id)
            ->assertDontSee('<!DOCTYPE html>', false);
    }
}
