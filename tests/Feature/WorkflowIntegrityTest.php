<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WorkflowIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string, string}> */
    public static function interactionStates(): array
    {
        return [
            'like' => ['posts.like', 'liked', 'likes'],
            'favorite' => ['posts.favorite', 'saved', 'favorites'],
            'follow' => ['authors.follow', 'following', 'follows'],
        ];
    }

    #[DataProvider('interactionStates')]
    public function test_explicit_interaction_state_is_idempotent_and_readable(string $routeName, string $stateKey, string $table): void
    {
        $viewer = User::factory()->viewer()->create();
        $target = $routeName === 'authors.follow' ? User::factory()->author()->create() : Post::factory()->published()->create();

        $this->actingAs($viewer)->postJson(route($routeName, $target), [$stateKey => true])->assertOk()->assertJsonPath($stateKey, true);
        $this->postJson(route($routeName, $target), [$stateKey => true])->assertOk()->assertJsonPath($stateKey, true);
        $this->getJson(route($routeName, $target))->assertOk()->assertJsonPath($stateKey, true);

        $this->assertDatabaseCount($table, 1);
        $this->postJson(route($routeName, $target), [$stateKey => false])->assertOk()->assertJsonPath($stateKey, false);
        $this->assertDatabaseCount($table, 0);
    }

    public function test_invalid_explicit_state_returns_422_without_a_like(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();

        $this->actingAs($viewer)->postJson(route('posts.like', $post), ['liked' => 'perhaps'])->assertUnprocessable()->assertJsonValidationErrors('liked');

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_state_reads_require_authentication_and_reject_locked_sessions(): void
    {
        $post = Post::factory()->published()->create();
        $locked = User::factory()->locked()->create();

        $this->getJson(route('posts.like', $post))->assertUnauthorized();
        $this->actingAs($locked)->getJson(route('posts.favorite', $post))->assertForbidden();
    }

    public function test_state_read_does_not_expose_unpublished_post(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->create();

        $this->actingAs($viewer)->getJson(route('posts.like', $post))->assertNotFound();
    }

    public function test_comment_retry_returns_same_record_and_approved_count(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        Comment::factory()->spam()->create(['post_id' => $post->id]);
        $payload = ['body' => 'A comment with a safe retry.', 'client_request_id' => 'ac718ef0-946a-4eb1-bafe-d4e309c54670'];

        $first = $this->actingAs($viewer)->postJson(route('comments.store', $post), $payload);
        $retry = $this->postJson(route('comments.store', $post), $payload);

        $first->assertCreated()->assertJsonPath('comments_count', 1);
        $retry->assertCreated()->assertJsonPath('comment.id', $first->json('comment.id'))->assertJsonPath('comments_count', 1);
        $this->assertDatabaseCount('comments', 2);
    }

    public function test_reusing_comment_request_id_with_different_content_returns_422(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $requestId = 'd74b0876-5527-4739-ab25-606e85cd0a86';
        $this->actingAs($viewer)->postJson(route('comments.store', $post), ['body' => 'Original body.', 'client_request_id' => $requestId])->assertCreated();

        $this->postJson(route('comments.store', $post), ['body' => 'Changed body.', 'client_request_id' => $requestId])->assertUnprocessable();

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', ['body' => 'Original body.']);
    }

    public function test_reusing_another_users_request_id_does_not_return_their_comment(): void
    {
        $firstViewer = User::factory()->viewer()->create();
        $secondViewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $payload = ['body' => 'Original body.', 'client_request_id' => '566f0c44-a771-4e58-a690-1c1211792710'];
        $this->actingAs($firstViewer)->postJson(route('comments.store', $post), $payload)->assertCreated();

        $this->actingAs($secondViewer)->postJson(route('comments.store', $post), $payload)->assertUnprocessable()->assertJsonMissingPath('comment');

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_reply_to_reply_is_attached_to_root_and_retry_is_idempotent(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $root = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->approved()->create(['post_id' => $post->id, 'parent_id' => $root->id]);
        $payload = ['body' => 'Visible reply to the thread.', 'parent_id' => (string) $reply->id, 'client_request_id' => '29108062-75ec-46b6-ad23-3fcf42da48a4'];

        $first = $this->actingAs($viewer)->postJson(route('comments.store', $post), $payload);
        $retry = $this->postJson(route('comments.store', $post), $payload);

        $first->assertCreated()->assertJsonPath('comment.parent_id', $root->id);
        $retry->assertCreated()->assertJsonPath('comment.id', $first->json('comment.id'));
        $this->assertDatabaseCount('comments', 3);
        $this->get(route('posts.show', $post->slug))->assertSee('Visible reply to the thread.');
    }

    public function test_cross_post_reply_is_rejected_before_thread_normalization(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();
        $foreignRoot = Comment::factory()->approved()->create();
        $foreignReply = Comment::factory()->approved()->create(['post_id' => $foreignRoot->post_id, 'parent_id' => $foreignRoot->id]);

        $this->actingAs($viewer)->postJson(route('comments.store', $post), ['body' => 'Invalid cross-post reply.', 'parent_id' => $foreignReply->id])->assertUnprocessable()->assertJsonValidationErrors('parent_id');

        $this->assertDatabaseCount('comments', 2);
    }

    public function test_invalid_comment_request_id_is_rejected(): void
    {
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create();

        $this->actingAs($viewer)->postJson(route('comments.store', $post), ['body' => 'Valid body.', 'client_request_id' => 'not-a-uuid'])->assertUnprocessable()->assertJsonValidationErrors('client_request_id');

        $this->assertDatabaseCount('comments', 0);
    }

    /** @return array<string, array{string}> */
    public static function nonPendingStates(): array
    {
        return ['draft' => ['draft'], 'published' => ['published'], 'rejected' => ['rejected']];
    }

    #[DataProvider('nonPendingStates')]
    public function test_admin_cannot_approve_non_pending_post(string $status): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['status' => $status]);

        $this->actingAs($admin, 'admin')->postJson(route('admin.posts.approve', $post))->assertUnprocessable();

        $this->assertSame($status, $post->fresh()->status);
    }

    #[DataProvider('nonPendingStates')]
    public function test_admin_cannot_reject_non_pending_post(string $status): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['status' => $status]);

        $this->actingAs($admin, 'admin')->postJson(route('admin.posts.reject', $post), ['rejection_reason' => 'Needs editorial changes.'])->assertUnprocessable();

        $this->assertSame($status, $post->fresh()->status);
    }

    public function test_rejection_requires_reason_and_keeps_post_pending_on_error(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->pending()->create();

        $this->actingAs($admin, 'admin')->postJson(route('admin.posts.reject', $post))->assertUnprocessable()->assertJsonValidationErrors(['rejection_reason' => 'Vui lòng nhập lý do từ chối bài viết.']);

        $this->assertSame('pending', $post->fresh()->status);
    }

    public function test_approval_clears_previous_rejection_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->pending()->create(['rejection_reason' => 'Old rejection.']);

        $this->actingAs($admin, 'admin')->postJson(route('admin.posts.approve', $post))->assertOk()->assertJsonPath('status', 'published');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'status' => 'published', 'rejection_reason' => null, 'reviewed_by' => $admin->id]);
    }

    public function test_locked_viewer_cannot_become_author_from_existing_session(): void
    {
        $viewer = User::factory()->viewer()->locked()->create();

        $this->actingAs($viewer)->post(route('author.become'))->assertForbidden();

        $this->assertSame('viewer', $viewer->fresh()->role);
    }

    public function test_admin_comment_search_preserves_status_when_user_matches(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create(['name' => 'Matched Reader']);
        Comment::factory()->approved()->create(['user_id' => $viewer->id, 'body' => 'Approved comment.']);
        $spam = Comment::factory()->spam()->create(['user_id' => $viewer->id, 'body' => 'Spam comment.']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.comments.index', ['status' => 'spam', 'q' => 'Matched Reader']));

        $response->assertViewHas('comments', fn ($comments): bool => $comments->pluck('id')->all() === [$spam->id]);
    }

    public function test_admin_deleting_post_keeps_shared_thumbnail(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('thumbnails/shared.jpg', 'image');
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['thumbnail' => 'storage/thumbnails/shared.jpg']);
        $otherPost = Post::factory()->create(['thumbnail' => $post->thumbnail]);

        $this->actingAs($admin, 'admin')->delete(route('admin.posts.destroy', $post))->assertRedirect(route('admin.posts.index'));

        $this->assertModelMissing($post);
        $this->assertModelExists($otherPost);
        Storage::disk('public')->assertExists('thumbnails/shared.jpg');
    }
}
