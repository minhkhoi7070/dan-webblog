<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesPageRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorites_page_renders_minimalist_header_and_data_attributes(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post1 = Post::factory()->published()->create();
        $post2 = Post::factory()->published()->create();
        $viewer->favoritePosts()->attach([$post1->id, $post2->id]);

        $response = $this->actingAs($viewer)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('Bài viết đã lưu');
        $response->assertSee('data-favorites-total="2"', false);
        $response->assertSee('data-favorites-count', false);
        $response->assertSee('data-app-nav', false);
        $response->assertSee(route('posts.index'));
        $response->assertSee('data-feed-shell', false);
        $response->assertSee('data-feed-base-url="'.route('favorites.index').'"', false);
    }

    public function test_favorites_page_excludes_old_redundant_slogans_and_indicators(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $viewer->favoritePosts()->attach($post->id);

        $response = $this->actingAs($viewer)->get(route('favorites.index'));

        $response->assertOk();
        $content = $response->getContent();

        // Must NOT render old slogans and banners
        $this->assertStringNotContainsString('Danh sách lưu trữ cá nhân cho trải nghiệm đọc tập trung của bạn', $content);
        $this->assertStringNotContainsString('Nhấn dấu trang để lưu hoặc bỏ lưu bài viết', $content);
        $this->assertStringNotContainsString('Nhấn dấu trang để gỡ bỏ', $content);

        // Visual "Đã lưu" label above each post card must be visually-hidden
        $this->assertStringContainsString('class="visually-hidden" data-favorite-entry-status', $content);
    }

    public function test_favorites_empty_state_renders_correct_threads_minimalist_copy(): void
    {
        $viewer = User::factory()->viewer()->create();

        $response = $this->actingAs($viewer)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('Chưa có bài viết đã lưu');
        $response->assertSee('Những bài viết bạn lưu sẽ xuất hiện tại đây.');
        $response->assertSee('Khám phá bài viết');
        $response->assertSee('data-favorites-total="0"', false);
    }

    public function test_favorites_preserves_post_card_and_ajax_pagination_hooks(): void
    {
        $viewer = User::factory()->viewer()->create();
        $posts = Post::factory()->count(15)->published()->create();
        foreach ($posts as $p) {
            $viewer->favoritePosts()->attach($p->id);
        }

        $response = $this->actingAs($viewer)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('threads-post');
        $response->assertSee('data-feed-pagination', false);
        $response->assertSee('id="feed-skeleton-template"', false);
        $response->assertSee('data-favorite-entry', false);
        $response->assertSee('data-saved="true"', false);
    }
}
