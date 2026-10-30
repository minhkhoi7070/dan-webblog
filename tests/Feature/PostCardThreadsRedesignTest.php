<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCardThreadsRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_card_renders_author_category_and_timestamp_on_header(): void
    {
        $category = Category::factory()->create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);
        $author = User::factory()->create(['name' => 'Đặng Minh Khôi']);
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết mẫu Threads',
            'body' => '<p>Nội dung bài viết mẫu trên CloudBlog.</p>',
        ]);

        $response = $this->get('/');
        $response->assertOk();

        // 1. Author, Category, and Timestamp must be rendered in header
        $response->assertSee('Đặng Minh Khôi');
        $response->assertSee('Công nghệ');
        $response->assertSee('·');
        $response->assertSee('Bài viết mẫu Threads');
    }

    public function test_post_action_bar_excludes_repost_button(): void
    {
        $post = Post::factory()->published()->create([
            'views' => 1250, // Views exist in DB
        ]);

        $response = $this->get('/');
        $response->assertOk();

        $content = $response->getContent();

        // Must NOT render Repost loop icon or "lượt tương tác" title
        $this->assertStringNotContainsString('lượt tương tác', $content);
        $this->assertStringNotContainsString('M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', $content);
    }

    public function test_post_action_bar_contains_like_comment_share_and_save(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        // 1. Authenticated user view: contains interactive data-actions
        $this->actingAs($user);
        $viewAuth = view('components.post-action-bar', ['post' => $post])->render();
        $this->assertStringContainsString('data-action="like"', $viewAuth);
        $this->assertStringContainsString('#comments', $viewAuth);
        $this->assertStringContainsString('Chia sẻ bài viết', $viewAuth);
        $this->assertStringContainsString('data-action="favorite"', $viewAuth);

        // 2. Guest view: contains login prompt links and share
        auth()->logout();
        $viewGuest = view('components.post-action-bar', ['post' => $post])->render();
        $this->assertStringContainsString('Đăng nhập để thích', $viewGuest);
        $this->assertStringContainsString('Chia sẻ bài viết', $viewGuest);
        $this->assertStringContainsString('Đăng nhập để lưu', $viewGuest);
    }

    public function test_share_button_renders_lucide_send_svg_and_has_no_fake_count(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        // Even if favorites exist, share must NOT display favorite count
        $user->favoritePosts()->attach($post->id);

        $viewHtml = view('components.post-action-bar', ['post' => $post])->render();

        // Lucide send polygon & line
        $this->assertStringContainsString('polygon points="22 2 15 22 11 13 2 9 22 2"', $viewHtml);
        $this->assertStringContainsString('line x1="22" y1="2" x2="11" y2="13"', $viewHtml);

        // Ensure share button does not display numbers
        $this->assertMatchesRegularExpression('/aria-label="Chia sẻ bài viết"[^>]*>[\s\S]*?<\/button>/', $viewHtml);
        $this->assertStringNotContainsString('<span class="small fw-medium">1</span>', $viewHtml);
    }

    public function test_like_and_comment_counts_only_display_when_greater_than_zero(): void
    {
        $postZero = Post::factory()->published()->create();

        $viewZero = view('components.post-action-bar', ['post' => $postZero])->render();

        // For zero counts, the count spans must be empty
        $this->assertStringNotContainsString('<span class="small fw-medium interaction-count like-count" aria-hidden="true">0</span>', $viewZero);

        // Now test with counts > 0
        $author = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $postWithCounts = Post::factory()->published()->create([
            'user_id' => $author->id,
        ]);
        $user1->likedPosts()->attach($postWithCounts->id);
        $user2->likedPosts()->attach($postWithCounts->id);
        Comment::factory()->create([
            'post_id' => $postWithCounts->id,
            'user_id' => $user1->id,
        ]);

        $postWithCounts->loadCount(['likers', 'comments']);

        $viewWithCounts = view('components.post-action-bar', ['post' => $postWithCounts])->render();
        $this->assertStringContainsString('>2<', $viewWithCounts); // 2 likes
        $this->assertStringContainsString('>1<', $viewWithCounts); // 1 comment
    }

    public function test_tags_are_rendered_with_proper_spacing_and_not_glued(): void
    {
        $tag1 = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $tag2 = Tag::factory()->create(['name' => 'VueJS', 'slug' => 'vuejs']);

        $post = Post::factory()->published()->create();
        $post->tags()->attach([$tag1->id, $tag2->id]);
        $post->load('tags');

        $view = view('components.post-card', ['post' => $post])->render();

        $this->assertStringContainsString('#Laravel', $view);
        $this->assertStringContainsString('#VueJS', $view);
        // Ensure there is distinct tag separation classes
        $this->assertStringContainsString('threads-hashtag-list', $view);
    }
}
