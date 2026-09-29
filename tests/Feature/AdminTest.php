<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // 1. ROUTE ACCESS CONTROL TESTS
    // =========================================================================

    public function test_guest_is_redirected_to_login_when_accessing_admin_routes(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.posts.index'))->assertRedirect(route('login'));
        $this->get(route('admin.comments.index'))->assertRedirect(route('login'));
    }

    public function test_viewer_and_author_receive_403_on_admin_routes(): void
    {
        $viewer = User::factory()->viewer()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->pending()->create();

        foreach ([$viewer, $author] as $user) {
            $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.categories.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.posts.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.comments.index'))->assertForbidden();

            // Direct moderation attempts
            $this->actingAs($user)->post(route('admin.posts.approve', $post))->assertForbidden();
            $this->actingAs($user)->post(route('admin.posts.reject', $post))->assertForbidden();
        }
    }

    public function test_admin_can_access_admin_dashboard_and_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Admin Control Center');
        $response->assertSee('Thống kê Tổng thể Hệ thống');
    }

    // =========================================================================
    // 2. DASHBOARD METRICS TESTS
    // =========================================================================

    public function test_dashboard_displays_all_required_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $author1 = User::factory()->author()->create();
        $author2 = User::factory()->author()->create();
        $viewer1 = User::factory()->viewer()->create();
        $viewer2 = User::factory()->viewer()->create();
        $viewer3 = User::factory()->viewer()->create();
        $category = Category::factory()->create();

        // 7 Posts total
        $post1 = Post::factory()->create(['user_id' => $author1->id, 'category_id' => $category->id, 'status' => 'draft', 'views' => 10]);
        Post::factory()->pending()->count(2)->create(['user_id' => $author1->id, 'category_id' => $category->id, 'views' => 20]);
        Post::factory()->published()->count(3)->create(['user_id' => $author2->id, 'category_id' => $category->id, 'views' => 50]);
        Post::factory()->rejected()->create(['user_id' => $author2->id, 'category_id' => $category->id, 'views' => 5]);

        // 6 Comments total on existing posts and users
        Comment::factory()->pending()->count(4)->create(['post_id' => $post1->id, 'user_id' => $viewer1->id]);
        Comment::factory()->approved()->count(2)->create(['post_id' => $post1->id, 'user_id' => $viewer2->id]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['users'] === 6 // 1 admin + 2 authors + 3 viewers
                && $metrics['authors'] === 2
                && $metrics['viewers'] === 3
                && $metrics['posts'] === 7 // 1 draft + 2 pending + 3 published + 1 rejected
                && $metrics['draft'] === 1
                && $metrics['pending'] === 2
                && $metrics['published'] === 3
                && $metrics['rejected'] === 1
                && $metrics['comments'] === 6 // 4 pending + 2 approved
                && $metrics['pending_comments'] === 4
                && $metrics['views'] === 205; // 10 + 2*20 + 3*50 + 5
        });
    }

    // =========================================================================
    // 3. USER MANAGEMENT TESTS (view, search, lock, unlock)
    // =========================================================================

    public function test_admin_can_view_and_search_users(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'name' => 'Nguyen Van A',
            'email' => 'nguyenvana@example.com',
        ]);
        User::factory()->create([
            'name' => 'Tran Thi B',
            'email' => 'tranthib@example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'Nguyen Van A']));
        $response->assertOk();
        $response->assertSee('nguyenvana@example.com');
        $response->assertDontSee('tranthib@example.com');
    }

    public function test_admin_can_lock_and_unlock_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create(['is_locked' => false]);

        // Lock user
        $lockResponse = $this->actingAs($admin)->post(route('admin.users.lock', $targetUser));
        $lockResponse->assertRedirect();
        $this->assertTrue($targetUser->fresh()->is_locked);

        // Unlock user
        $unlockResponse = $this->actingAs($admin)->post(route('admin.users.unlock', $targetUser));
        $unlockResponse->assertRedirect();
        $this->assertFalse($targetUser->fresh()->is_locked);
    }

    public function test_admin_cannot_lock_own_account(): void
    {
        $admin = User::factory()->admin()->create(['is_locked' => false]);

        $response = $this->actingAs($admin)->post(route('admin.users.lock', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertFalse($admin->fresh()->is_locked);
    }

    // =========================================================================
    // 4. CATEGORY CRUD TESTS
    // =========================================================================

    public function test_admin_can_crud_categories(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Create
        $createResponse = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Machine Learning',
            'slug' => 'machine-learning',
        ]);
        $createResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Machine Learning', 'slug' => 'machine-learning']);

        $category = Category::where('slug', 'machine-learning')->first();
        $this->assertNotNull($category);

        // 2. Read / Index
        $this->actingAs($admin)->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Machine Learning');

        // 3. Update
        $updateResponse = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Deep Learning & AI',
            'slug' => 'deep-learning-ai',
        ]);
        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Deep Learning & AI']);

        // 4. Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $deleteResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_creation_validation_rules_apply(): void
    {
        $admin = User::factory()->admin()->create();

        // Missing name
        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => '',
        ]);
        $response->assertSessionHasErrors(['name']);

        // Duplicate slug
        Category::factory()->create(['slug' => 'existing-slug']);
        $dupResponse = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'slug' => 'existing-slug',
        ]);
        $dupResponse->assertSessionHasErrors(['slug']);
    }

    // =========================================================================
    // 5. POST MODERATION TESTS (Approve, Reject, Resubmit)
    // =========================================================================

    public function test_admin_can_approve_pending_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->pending()->create([
            'user_id' => $author->id,
            'title' => 'Article Pending Review',
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.posts.approve', $post));

        $response->assertRedirect();
        $post->refresh();

        $this->assertSame('published', $post->status);
        $this->assertSame($admin->id, $post->reviewed_by);
        $this->assertNotNull($post->reviewed_at);
        $this->assertNotNull($post->published_at);
    }

    public function test_admin_can_reject_pending_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->pending()->create([
            'user_id' => $author->id,
            'title' => 'Article Violating Guidelines',
        ]);

        $rejectionReason = 'Nội dung bài viết thiếu trích dẫn nguồn uy tín và vi phạm quy chuẩn biên tập.';

        $response = $this->actingAs($admin)->post(route('admin.posts.reject', $post), [
            'rejection_reason' => $rejectionReason,
        ]);

        $response->assertRedirect();
        $post->refresh();

        $this->assertSame('rejected', $post->status);
        $this->assertSame($rejectionReason, $post->rejection_reason);
        $this->assertSame($admin->id, $post->reviewed_by);
        $this->assertNotNull($post->reviewed_at);
    }

    public function test_author_can_resubmit_rejected_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->rejected()->create([
            'user_id' => $author->id,
            'rejection_reason' => 'Vui lòng bổ sung thêm phần kết luận.',
        ]);

        $this->assertSame('rejected', $post->status);

        // Author edits and resubmits
        $response = $this->actingAs($author)->post(route('posts.submit', $post));

        $response->assertRedirect();
        $post->refresh();

        $this->assertSame('pending', $post->status);
    }

    public function test_admin_can_delete_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.posts.destroy', $post));

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    // =========================================================================
    // 6. COMMENTS MODERATION TESTS (view, approve, spam, delete)
    // =========================================================================

    public function test_admin_can_moderate_comments(): void
    {
        $admin = User::factory()->admin()->create();
        $comment = Comment::factory()->pending()->create([
            'body' => 'Bình luận cần kiểm duyệt.',
        ]);

        // 1. Approve
        $approveResponse = $this->actingAs($admin)->post(route('admin.comments.approve', $comment));
        $approveResponse->assertRedirect();
        $this->assertSame('approved', $comment->fresh()->status);

        // 2. Spam
        $spamResponse = $this->actingAs($admin)->post(route('admin.comments.spam', $comment));
        $spamResponse->assertRedirect();
        $this->assertSame('spam', $comment->fresh()->status);

        // 3. Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.comments.destroy', $comment));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
