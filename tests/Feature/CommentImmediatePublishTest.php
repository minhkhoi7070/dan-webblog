<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentImmediatePublishTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Viewer can create comment and it is approved immediately.
     */
    public function test_viewer_can_create_comment_and_it_is_approved_immediately(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Bình luận thử nghiệm của viewer hiển thị ngay lập tức.',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.body', 'Bình luận thử nghiệm của viewer hiển thị ngay lập tức.')
            ->assertJsonPath('comment.status', 'approved');

        $this->assertDatabaseHas('comments', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
            'parent_id' => null,
            'body' => 'Bình luận thử nghiệm của viewer hiển thị ngay lập tức.',
            'status' => 'approved',
        ]);
    }

    /**
     * 2. Author can create comment and it is approved immediately.
     */
    public function test_author_can_create_comment_and_it_is_approved_immediately(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($author)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Bình luận đóng góp của tác giả xuất bản ngay lập tức.',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.status', 'approved');

        $this->assertDatabaseHas('comments', [
            'user_id' => $author->id,
            'post_id' => $post->id,
            'status' => 'approved',
        ]);
    }

    /**
     * 3. Viewer can reply and reply is approved immediately.
     */
    public function test_viewer_can_reply_and_reply_is_approved_immediately(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $rootComment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'body' => 'Bình luận gốc',
        ]);

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Phản hồi bình luận được duyệt ngay lập tức.',
                'parent_id' => $rootComment->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.parent_id', $rootComment->id)
            ->assertJsonPath('comment.status', 'approved');

        $this->assertDatabaseHas('comments', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
            'parent_id' => $rootComment->id,
            'status' => 'approved',
        ]);
    }

    /**
     * 4. Guest cannot create comment.
     */
    public function test_guest_cannot_create_comment(): void
    {
        $post = Post::factory()->published()->create();

        // JSON request expects 401 Unauthorized
        $jsonResponse = $this->postJson(route('comments.store', $post->id), [
            'body' => 'Guest comment attempt',
        ]);
        $jsonResponse->assertStatus(401);

        // Standard web request redirects to login
        $webResponse = $this->post(route('comments.store', $post->id), [
            'body' => 'Guest comment attempt',
        ]);
        $webResponse->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'post_id' => $post->id,
            'body' => 'Guest comment attempt',
        ]);
    }

    /**
     * 5. Locked user cannot create comment.
     */
    public function test_locked_user_cannot_create_comment(): void
    {
        $lockedUser = User::factory()->locked()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($lockedUser)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Spam comment from locked user',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('comments', [
            'user_id' => $lockedUser->id,
            'body' => 'Spam comment from locked user',
        ]);
    }

    /**
     * 6. Newly created comment appears immediately in public post detail.
     */
    public function test_newly_created_comment_appears_immediately_in_public_post_detail(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();

        // Viewer creates comment
        $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Nội dung bình luận hiển thị trực tiếp công khai.',
            ])
            ->assertStatus(201);

        // Guest / Public visits post detail page
        $publicResponse = $this->get(route('posts.show', $post->slug));

        $publicResponse->assertOk();
        $publicResponse->assertSee('Nội dung bình luận hiển thị trực tiếp công khai.');
        $publicResponse->assertSee($viewer->name);
    }

    /**
     * 7. Newly created reply appears immediately.
     */
    public function test_newly_created_reply_appears_immediately(): void
    {
        $viewer1 = User::factory()->viewer()->create(['name' => 'Độc giả 1']);
        $viewer2 = User::factory()->viewer()->create(['name' => 'Độc giả 2']);
        $post = Post::factory()->published()->create();

        $rootComment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'user_id' => $viewer1->id,
            'body' => 'Bình luận thảo luận chính',
        ]);

        $this->actingAs($viewer2)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Nội dung câu trả lời xuất hiện ngay',
                'parent_id' => $rootComment->id,
            ])
            ->assertStatus(201);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('Bình luận thảo luận chính');
        $response->assertSee('Nội dung câu trả lời xuất hiện ngay');
        $response->assertSee('Độc giả 2');
    }

    /**
     * 8. Spam comment remains hidden from public.
     */
    public function test_spam_comment_remains_hidden_from_public(): void
    {
        $post = Post::factory()->published()->create();

        $approvedComment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'body' => 'Bình luận hợp lệ và bổ ích.',
        ]);

        $spamComment = Comment::factory()->spam()->create([
            'post_id' => $post->id,
            'body' => 'Spam link quảng cáo tiền ảo lừa đảo.',
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Bình luận hợp lệ và bổ ích.');
        $response->assertDontSee('Spam link quảng cáo tiền ảo lừa đảo.');
    }

    /**
     * 9. Admin can still manage comment after publication.
     */
    public function test_admin_can_still_manage_comment_after_publication(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create();
        $comment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'body' => 'Bình luận công khai chờ admin hậu kiểm.',
        ]);

        // 1. Admin accesses comments list
        $indexResponse = $this->actingAs($admin)->get(route('admin.comments.index'));
        $indexResponse->assertOk()
            ->assertSee('Bình luận công khai chờ admin hậu kiểm.');

        // 2. Admin marks comment as spam
        $spamResponse = $this->actingAs($admin)->post(route('admin.comments.spam', $comment));
        $spamResponse->assertRedirect();
        $this->assertSame('spam', $comment->fresh()->status);

        // 3. Admin deletes the comment
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.comments.destroy', $comment));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /**
     * 10. Author can moderate comments on own post.
     */
    public function test_author_can_moderate_comments_on_own_post(): void
    {
        $author = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
        ]);

        $comment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'body' => 'Bình luận không phù hợp trên bài viết của tác giả.',
        ]);

        $this->assertTrue($author->can('moderate', $comment));
        $this->assertTrue($author->can('delete', $comment));

        $response = $this->actingAs($author)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /**
     * 11. Author cannot moderate comments on another author's post.
     */
    public function test_author_cannot_moderate_comments_on_another_authors_post(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();

        $postA = Post::factory()->published()->create([
            'user_id' => $authorA->id,
        ]);

        $comment = Comment::factory()->approved()->create([
            'post_id' => $postA->id,
            'user_id' => $viewer->id,
            'body' => 'Bình luận trên bài của tác giả A.',
        ]);

        $this->assertFalse($authorB->can('moderate', $comment));
        $this->assertFalse($authorB->can('delete', $comment));

        // Author B attempts to delete comment on Author A's post
        $response = $this->actingAs($authorB)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    /**
     * 12. Creating a normal comment does NOT create a pending moderation item.
     */
    public function test_creating_a_normal_comment_does_not_create_a_pending_moderation_item(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();

        $initialPendingCount = Comment::where('status', 'pending')->count();

        $response = $this->actingAs($viewer)->postJson(route('comments.store', $post->id), [
            'body' => 'Bình luận này không đi vào hàng đợi pending.',
        ]);

        $response->assertStatus(201);

        $newComment = Comment::latest('id')->first();
        $this->assertNotNull($newComment);
        $this->assertSame('approved', $newComment->status);
        $this->assertTrue($newComment->isApproved());
        $this->assertFalse($newComment->isPending());

        $this->assertSame($initialPendingCount, Comment::where('status', 'pending')->count());
    }
}
