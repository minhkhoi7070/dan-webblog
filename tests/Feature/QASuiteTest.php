<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QASuiteTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // AUTH TESTS
    // =========================================================================

    public function test_auth_01_user_registration_with_valid_data(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('home', absolute: false));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'viewer',
        ]);
    }

    public function test_auth_02_user_registration_validation_errors(): void
    {
        // Missing name, invalid email, mismatched password
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_auth_03_user_registration_cannot_escalate_role(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'hacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('viewer', $user->role);
    }

    public function test_auth_04_user_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('home', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_auth_05_user_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_auth_06_user_login_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'spam@example.com',
                'password' => 'wrong',
            ]);
        }

        // 6th attempt must be throttled
        $response = $this->post(route('login'), [
            'email' => 'spam@example.com',
            'password' => 'wrong',
        ]);

        $response->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Too many login attempts', $errorMessage);
    }

    public function test_auth_07_locked_user_login_attempt(): void
    {
        $lockedUser = User::factory()->create([
            'email' => 'locked@example.com',
            'password' => bcrypt('Secret123!'),
            'is_locked' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'locked@example.com',
            'password' => 'Secret123!',
        ]);

        // Assert validation error on email mentioning account locked
        $response->assertSessionHasErrors(['email']);
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('khóa', $errorMessage);

        // Assert user is NOT authenticated
        $this->assertGuest();

        // Assert dashboard cannot be accessed
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_auth_07_unlocked_user_can_still_login_normally(): void
    {
        $unlockedUser = User::factory()->create([
            'email' => 'unlocked@example.com',
            'password' => bcrypt('Secret123!'),
            'is_locked' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'unlocked@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('home', absolute: false));
        $this->assertAuthenticatedAs($unlockedUser);
    }

    public function test_auth_08_user_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    // =========================================================================
    // ROLE & ACCESS CONTROL TESTS (ROLE-11 FIX VERIFICATION)
    // =========================================================================

    public function test_role_01_guest_can_access_public_routes_only(): void
    {
        $post = Post::factory()->published()->create();

        $this->get(route('home'))->assertOk();
        $this->get(route('posts.index'))->assertOk();
        $this->get(route('posts.show', $post->slug))->assertOk();
        $this->get(route('authors.show', $post->user))->assertOk();
    }

    public function test_role_02_guest_cannot_view_unpublished_post(): void
    {
        $draft = Post::factory()->create(['status' => 'draft']);
        $pending = Post::factory()->pending()->create();
        $rejected = Post::factory()->rejected()->create();

        $this->get(route('posts.show', $draft->slug))->assertNotFound();
        $this->get(route('posts.show', $pending->slug))->assertNotFound();
        $this->get(route('posts.show', $rejected->slug))->assertNotFound();
    }

    public function test_role_03_viewer_cannot_access_author_cms(): void
    {
        $viewer = User::factory()->viewer()->create();

        $this->actingAs($viewer)->get(route('posts.create'))->assertForbidden();
        $this->actingAs($viewer)->get(route('posts.stats'))->assertForbidden();
        $this->actingAs($viewer)->post(route('posts.store'), [
            'title' => 'Viewer Post Attempt',
            'category_id' => 1,
            'content' => 'Test content',
        ])->assertForbidden();
    }

    public function test_role_04_author_cannot_access_admin_panel(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($author)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($author)->get(route('admin.posts.index'))->assertForbidden();
    }

    public function test_role_11_locked_author_cannot_create_update_submit_or_delete(): void
    {
        $lockedAuthor = User::factory()->author()->create(['is_locked' => true]);
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $lockedAuthor->id, 'status' => 'draft']);

        // 1. Cannot view create form
        $this->actingAs($lockedAuthor)->get(route('posts.create'))->assertForbidden();

        // 2. Cannot store new post
        $this->actingAs($lockedAuthor)->post(route('posts.store'), [
            'title' => 'Locked Author Post Attempt',
            'category_id' => $category->id,
            'content' => 'Should be rejected because account is locked.',
        ])->assertForbidden();

        // 3. Cannot view edit form
        $this->actingAs($lockedAuthor)->get(route('posts.edit', $post))->assertForbidden();

        // 4. Cannot update post
        $this->actingAs($lockedAuthor)->put(route('posts.update', $post), [
            'title' => 'Locked Author Update Attempt',
            'category_id' => $category->id,
            'content' => 'Should be rejected.',
        ])->assertForbidden();

        // 5. Cannot submit post
        $this->actingAs($lockedAuthor)->post(route('posts.submit', $post))->assertForbidden();

        // 6. Cannot delete post
        $this->actingAs($lockedAuthor)->delete(route('posts.destroy', $post))->assertForbidden();

        // Post status remains unchanged
        $this->assertSame('draft', $post->fresh()->status);
    }

    public function test_role_11_unlocked_author_can_perform_post_operations(): void
    {
        $author = User::factory()->author()->create(['is_locked' => false]);
        $category = Category::factory()->create();

        // 1. Can view create form
        $this->actingAs($author)->get(route('posts.create'))->assertOk();

        // 2. Can store new post
        $storeResponse = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Unlocked Author Post',
            'category_id' => $category->id,
            'content' => 'Content of unlocked post.',
        ]);
        $storeResponse->assertRedirect();
        $post = Post::where('title', 'Unlocked Author Post')->first();
        $this->assertNotNull($post);

        // 3. Can view edit form
        $this->actingAs($author)->get(route('posts.edit', $post))->assertOk();

        // 4. Can update post
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Unlocked Author Post Updated',
            'category_id' => $category->id,
            'content' => 'Content updated.',
        ])->assertRedirect();

        // 5. Can submit post
        $this->actingAs($author)->post(route('posts.submit', $post))->assertRedirect();
        $this->assertSame('pending', $post->fresh()->status);
    }

    // =========================================================================
    // COMMENT MODERATION & PUBLIC VISIBILITY TESTS (INT-10 FIX VERIFICATION)
    // =========================================================================

    public function test_int_10_approved_comments_and_replies_render_publicly(): void
    {
        $post = Post::factory()->published()->create();
        $approvedComment = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'body' => 'This is an approved root comment.',
        ]);
        $approvedReply = Comment::factory()->approved()->reply($approvedComment)->create([
            'body' => 'This is an approved child reply.',
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('This is an approved root comment.');
        $response->assertSee('This is an approved child reply.');
    }

    public function test_int_10_pending_and_spam_comments_and_replies_hidden_publicly(): void
    {
        $post = Post::factory()->published()->create();

        // Pending root comment
        $pendingComment = Comment::factory()->pending()->create([
            'post_id' => $post->id,
            'body' => 'This is a pending root comment waiting for approval.',
        ]);

        // Spam root comment
        $spamComment = Comment::factory()->spam()->create([
            'post_id' => $post->id,
            'body' => 'Spam cryptocurrency advertisement.',
        ]);

        // Approved root comment with pending & spam replies
        $approvedParent = Comment::factory()->approved()->create([
            'post_id' => $post->id,
            'body' => 'Legitimate parent comment.',
        ]);
        Comment::factory()->pending()->reply($approvedParent)->create([
            'body' => 'Pending reply under approved parent.',
        ]);
        Comment::factory()->spam()->reply($approvedParent)->create([
            'body' => 'Spam reply under approved parent.',
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('Legitimate parent comment.');

        // Verify pending and spam are hidden from public
        $response->assertDontSee('This is a pending root comment waiting for approval.');
        $response->assertDontSee('Spam cryptocurrency advertisement.');
        $response->assertDontSee('Pending reply under approved parent.');
        $response->assertDontSee('Spam reply under approved parent.');
    }

    public function test_int_10_admin_can_still_moderate_pending_and_spam_comments(): void
    {
        $admin = User::factory()->admin()->create();
        $pendingComment = Comment::factory()->pending()->create(['body' => 'Moderate me.']);

        $response = $this->actingAs($admin)->get(route('admin.comments.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee('Moderate me.');

        // Approve
        $this->actingAs($admin)->post(route('admin.comments.approve', $pendingComment));
        $this->assertSame('approved', $pendingComment->fresh()->status);
    }

    // =========================================================================
    // PEER ADMIN LOCKOUT TESTS (SEC-05 FIX VERIFICATION)
    // =========================================================================

    public function test_sec_05_admin_cannot_lock_self(): void
    {
        $admin = User::factory()->admin()->create(['is_locked' => false]);

        $response = $this->actingAs($admin)->post(route('admin.users.lock', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertFalse($admin->fresh()->is_locked);
    }

    public function test_sec_05_admin_cannot_lock_another_admin(): void
    {
        $adminA = User::factory()->admin()->create(['name' => 'Admin A', 'is_locked' => false]);
        $adminB = User::factory()->admin()->create(['name' => 'Admin B', 'is_locked' => false]);

        $response = $this->actingAs($adminA)->post(route('admin.users.lock', $adminB));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertFalse($adminB->fresh()->is_locked);
    }

    public function test_sec_05_admin_can_lock_author_and_viewer(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create(['is_locked' => false]);
        $viewer = User::factory()->viewer()->create(['is_locked' => false]);

        // Lock author
        $this->actingAs($admin)->post(route('admin.users.lock', $author))->assertRedirect();
        $this->assertTrue($author->fresh()->is_locked);

        // Lock viewer
        $this->actingAs($admin)->post(route('admin.users.lock', $viewer))->assertRedirect();
        $this->assertTrue($viewer->fresh()->is_locked);

        // Unlock author
        $this->actingAs($admin)->post(route('admin.users.unlock', $author))->assertRedirect();
        $this->assertFalse($author->fresh()->is_locked);
    }

    // =========================================================================
    // CATEGORY DELETION & POST INTEGRITY TESTS (SEC-06 FIX VERIFICATION)
    // =========================================================================

    public function test_sec_06_case_a_empty_category_deletion_succeeds(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Empty Category']);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_sec_06_case_b_category_with_posts_deletion_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Tech Category']);
        $post = Post::factory()->create([
            'category_id' => $category->id,
            'title' => 'Important Tech Post',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');

        // Category must still exist
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        // Post must remain completely intact
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'category_id' => $category->id,
            'title' => 'Important Tech Post',
        ]);
    }

    public function test_sec_06_case_d_explicit_post_deletion_cleans_up_thumbnail(): void
    {
        $storage = Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Store file on fake public disk
        $file = UploadedFile::fake()->image('thumbnail.jpg', 600, 400);
        $path = $file->store('thumbnails', 'public');
        $thumbnailPath = 'storage/'.$path;

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => $thumbnailPath,
        ]);

        $storage->assertExists($path);

        // Explicit deletion of post
        $this->actingAs($author)->delete(route('posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $storage->assertMissing($path);
    }

    // =========================================================================
    // POST WORKFLOW & MODERATION TESTS
    // =========================================================================

    public function test_post_01_create_draft_and_sync_tags(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'My Draft Post',
            'category_id' => $category->id,
            'content' => 'Draft content here.',
            'tags' => [$tag->id],
        ]);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);
        $this->assertSame(0, $post->views);
        $this->assertCount(1, $post->tags);
        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_post_02_post_detail_increments_views(): void
    {
        $post = Post::factory()->published()->create(['views' => 5]);

        $this->get(route('posts.show', $post->slug))->assertOk();
        $this->assertSame(6, $post->fresh()->views);
    }

    public function test_post_03_author_can_update_own_post(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id, 'category_id' => $category->id]);

        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'category_id' => $category->id,
            'content' => 'Updated content',
        ]);

        $response->assertRedirect(route('posts.edit', $post));
        $this->assertSame('Updated Title', $post->fresh()->title);
    }

    public function test_post_04_author_can_delete_own_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($author)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.stats'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_post_05_author_can_submit_draft_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author->id, 'status' => 'draft']);

        $response = $this->actingAs($author)->post(route('posts.submit', $post));

        $response->assertRedirect();
        $this->assertSame('pending', $post->fresh()->status);
    }

    public function test_post_06_admin_can_approve_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->pending()->create();

        $response = $this->actingAs($admin)->post(route('admin.posts.approve', $post));

        $response->assertRedirect();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame($admin->id, $post->fresh()->reviewed_by);
        $this->assertNotNull($post->fresh()->reviewed_at);
        $this->assertNotNull($post->fresh()->published_at);
    }

    public function test_post_07_admin_can_reject_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->pending()->create();

        $response = $this->actingAs($admin)->post(route('admin.posts.reject', $post), [
            'rejection_reason' => 'Does not meet standards',
        ]);

        $response->assertRedirect();
        $this->assertSame('rejected', $post->fresh()->status);
        $this->assertSame('Does not meet standards', $post->fresh()->rejection_reason);
    }

    public function test_post_08_author_can_resubmit_rejected_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->rejected()->create(['user_id' => $author->id]);

        $response = $this->actingAs($author)->post(route('posts.submit', $post));

        $response->assertRedirect();
        $this->assertSame('pending', $post->fresh()->status);
    }

    // =========================================================================
    // INTERACTION TESTS
    // =========================================================================

    public function test_int_01_like_post_exact_json(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)->postJson(route('posts.like', $post));

        $response->assertOk()->assertExactJson([
            'liked' => true,
            'likes_count' => 1,
        ]);
    }

    public function test_int_02_favorite_post_exact_json(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)->postJson(route('posts.favorite', $post));

        $response->assertOk()->assertExactJson([
            'saved' => true,
        ]);
    }

    public function test_int_03_follow_author_exact_json(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->author()->create();

        $response = $this->actingAs($viewer)->postJson(route('authors.follow', $author));

        $response->assertOk()->assertExactJson([
            'following' => true,
        ]);
    }

    public function test_int_04_user_cannot_follow_self(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->postJson(route('authors.follow', $author));

        $response->assertStatus(422);
    }

    public function test_int_05_create_comment_and_reply_validation(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        // Top level comment
        $commentResponse = $this->actingAs($viewer)->postJson(route('comments.store', $post), [
            'body' => 'Great post!',
        ]);
        $commentResponse->assertStatus(201);
        $commentId = $commentResponse->json('comment.id');

        // Valid reply
        $replyResponse = $this->actingAs($viewer)->postJson(route('comments.store', $post), [
            'body' => 'I agree!',
            'parent_id' => $commentId,
        ]);
        $replyResponse->assertStatus(201);

        // Invalid reply to parent belonging to other post
        $otherPost = Post::factory()->published()->create();
        $otherComment = Comment::factory()->create(['post_id' => $otherPost->id]);

        $invalidReply = $this->actingAs($viewer)->postJson(route('comments.store', $post), [
            'body' => 'Cross-post reply attempt',
            'parent_id' => $otherComment->id,
        ]);
        $invalidReply->assertStatus(422)->assertJsonValidationErrors(['parent_id']);
    }

    // =========================================================================
    // SECURITY TESTS
    // =========================================================================

    public function test_sec_01_upload_validation_rejects_non_image(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $fakePdf = UploadedFile::fake()->create('malicious.pdf', 500);

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with PDF',
            'category_id' => $category->id,
            'content' => 'Some content',
            'thumbnail' => $fakePdf,
        ]);

        $response->assertSessionHasErrors(['thumbnail']);
    }

    public function test_sec_02_upload_validation_rejects_oversized_file(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        // 3MB image > 2048KB limit
        $oversizedImage = UploadedFile::fake()->image('huge.png')->size(3072);

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with Huge Image',
            'category_id' => $category->id,
            'content' => 'Some content',
            'thumbnail' => $oversizedImage,
        ]);

        $response->assertSessionHasErrors(['thumbnail']);
    }

    public function test_sec_03_author_cannot_force_published_status_on_create_or_update(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Create
        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Sneaky Publish Post',
            'category_id' => $category->id,
            'content' => 'Content here',
            'status' => 'published',
        ]);
        $post = Post::first();
        $this->assertSame('draft', $post->status);

        // Update
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Sneaky Update Post',
            'category_id' => $category->id,
            'content' => 'Content here',
            'status' => 'published',
        ]);
        $this->assertSame('draft', $post->fresh()->status);
    }

    public function test_sec_04_author_cannot_modify_other_author_post(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $authorA->id]);

        $this->actingAs($authorB)->get(route('posts.edit', $post))->assertForbidden();
        $this->actingAs($authorB)->put(route('posts.update', $post), [
            'title' => 'Hijacked',
            'category_id' => $post->category_id,
            'content' => 'Hijacked content',
        ])->assertForbidden();
        $this->actingAs($authorB)->delete(route('posts.destroy', $post))->assertForbidden();
        $this->actingAs($authorB)->post(route('posts.submit', $post))->assertForbidden();
    }
}
