<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootstrapModalConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_view_renders_bootstrap_confirm_modal_and_toast(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['name' => 'Viewer User', 'role' => 'viewer', 'is_locked' => false]);
        $lockedAuthor = User::factory()->create(['role' => 'author', 'is_locked' => true]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('id="global-confirm-modal"', false);
        $response->assertSee('id="global-toast-container"', false);
        $response->assertSee('data-confirm="true"', false);
        $response->assertSee('data-confirm-title="Khóa tài khoản"', false);
        $response->assertSee('data-confirm-title="Mở khóa tài khoản"', false);
        $response->assertSee('data-confirm-target-name="'.$viewer->name.'"', false);
        $response->assertSee('data-confirm-target-meta="'.$viewer->email.'"', false);
        $response->assertDontSee('onsubmit="return confirm', false);
        $response->assertDontSee('window.confirm', false);
        $response->assertDontSee('window.alert', false);
    }

    public function test_admin_user_show_view_renders_bootstrap_confirm_attributes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer', 'is_locked' => false]);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $viewer));

        $response->assertOk();
        $response->assertSee('data-confirm="true"', false);
        $response->assertSee('data-confirm-title="Khóa tài khoản"', false);
        $response->assertSee($viewer->email, false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_admin_posts_index_and_show_render_bootstrap_confirm_and_prompt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'author']);
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'pending',
            'title' => 'Bài viết chờ duyệt thử nghiệm',
        ]);

        $indexResponse = $this->actingAs($admin)->get(route('admin.posts.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('data-confirm="true"', false);
        $indexResponse->assertSee('data-confirm-title="Duyệt bài viết"', false);
        $indexResponse->assertSee('data-confirm-title="Từ chối bài viết"', false);
        $indexResponse->assertSee('data-confirm-prompt="true"', false);
        $indexResponse->assertSee('data-confirm-prompt-name="rejection_reason"', false);
        $indexResponse->assertSee('data-confirm-title="Xóa bài viết"', false);
        $indexResponse->assertDontSee('prompt(', false);
        $indexResponse->assertDontSee('onsubmit="return confirm', false);

        $showResponse = $this->actingAs($admin)->get(route('admin.posts.show', $post));
        $showResponse->assertOk();
        $showResponse->assertSee('data-confirm-title="Duyệt bài viết"', false);
        $showResponse->assertSee('data-confirm-title="Từ chối phê duyệt"', false);
        $showResponse->assertSee('data-confirm-prompt="true"', false);
        $showResponse->assertSee('data-confirm-title="Xóa vĩnh viễn bài viết"', false);
        $showResponse->assertDontSee('prompt(', false);
        $showResponse->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_admin_categories_index_renders_bootstrap_confirm_modal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['name' => 'Công nghệ']);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('data-confirm="true"', false);
        $response->assertSee('data-confirm-title="Xóa chuyên mục"', false);
        $response->assertSee('data-confirm-target-name="Công nghệ"', false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_admin_comments_index_and_dashboard_render_bootstrap_confirm_modal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'author']);
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'published',
        ]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'body' => 'Bình luận thử nghiệm',
            'status' => 'pending',
        ]);

        $commentsResponse = $this->actingAs($admin)->get(route('admin.comments.index'));
        $commentsResponse->assertOk();
        $commentsResponse->assertSee('data-confirm="true"', false);
        $commentsResponse->assertSee('data-confirm-title="Xóa bình luận"', false);
        $commentsResponse->assertDontSee('onsubmit="return confirm', false);

        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('data-confirm="true"', false);
        $dashResponse->assertSee('data-confirm-title="Xóa bình luận"', false);
        $dashResponse->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_author_post_edit_renders_bootstrap_confirm(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
            'title' => 'Bài nháp của author',
        ]);

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        $response->assertSee('data-confirm="true"', false);
        $response->assertSee('data-confirm-title="Xóa bài viết"', false);
        $response->assertSee('data-confirm-target-name="Bài nháp của author"', false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_public_post_show_renders_bootstrap_confirm_for_comment_deletion(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $author = User::factory()->create(['role' => 'author']);
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'published',
        ]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'body' => 'Bình luận của viewer cần xóa',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($viewer)->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('data-confirm="true"', false);
        $response->assertSee('data-confirm-title="Xóa bình luận"', false);
        $response->assertSee('id="global-confirm-modal"', false);
        $response->assertDontSee('onsubmit="return confirm', false);
    }

    public function test_lock_and_unlock_user_business_logic_remains_functional(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create(['role' => 'viewer', 'is_locked' => false]);

        // Lock user
        $lockResponse = $this->actingAs($admin)->post(route('admin.users.lock', $viewer));
        $lockResponse->assertRedirect();
        $lockResponse->assertSessionHas('success');
        $this->assertTrue($viewer->fresh()->is_locked);

        // Unlock user
        $unlockResponse = $this->actingAs($admin)->post(route('admin.users.unlock', $viewer));
        $unlockResponse->assertRedirect();
        $unlockResponse->assertSessionHas('success');
        $this->assertFalse($viewer->fresh()->is_locked);
    }
}
