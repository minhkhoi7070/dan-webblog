<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDirectPublishTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Admin creates a post and publishes immediately -> status is published.
     */
    public function test_admin_creates_and_publishes_post_directly(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Admin Direct Published Article',
            'category_id' => $category->id,
            'content' => '<p>This is a high quality article written and published directly by Admin.</p>',
            'excerpt' => 'Admin article excerpt.',
            'tags' => $tags->pluck('id')->toArray(),
            'action' => 'publish',
        ]);

        $post = Post::where('title', 'Admin Direct Published Article')->first();
        $this->assertNotNull($post);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertNull($post->rejection_reason);
        $this->assertSame($admin->id, $post->user_id);
        $this->assertSame($category->id, $post->category_id);
        $this->assertCount(2, $post->tags);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success', 'Bài viết đã được xuất bản thành công.');
    }

    /**
     * Admin creating with action=submit also directly publishes without self-approval.
     */
    public function test_admin_creates_with_submit_action_also_publishes_directly(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Admin Published Via Submit Action',
            'category_id' => $category->id,
            'content' => '<p>Content published directly.</p>',
            'action' => 'submit',
        ]);

        $post = Post::where('title', 'Admin Published Via Submit Action')->first();
        $this->assertNotNull($post);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame($admin->id, $post->user_id);
    }

    /**
     * 2. Admin saves draft -> status is draft.
     */
    public function test_admin_can_save_draft_post(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Admin Draft Article',
            'category_id' => $category->id,
            'content' => '<p>Draft content in progress.</p>',
            'action' => 'draft',
        ]);

        $post = Post::where('title', 'Admin Draft Article')->first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
        $this->assertSame($admin->id, $post->user_id);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success', 'Bài viết đã được tạo thành công dưới dạng Bản nháp (Draft).');
    }

    /**
     * 3. Admin publishes their own draft post directly via update.
     */
    public function test_admin_publishes_own_draft_post_directly_via_update(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Initial Admin Draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Admin Draft Now Published',
            'category_id' => $category->id,
            'content' => '<p>Updated and ready to publish immediately.</p>',
            'action' => 'publish',
        ]);

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame('Admin Draft Now Published', $post->title);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success', 'Bài viết đã được xuất bản thành công.');
    }

    /**
     * Admin publishes their own draft post directly via posts.submit route.
     */
    public function test_admin_publishes_own_draft_post_directly_via_submit_route(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create([
            'user_id' => $admin->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('posts.submit', $post));

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * 4. Admin edits already published post -> retains published status.
     */
    public function test_admin_editing_already_published_post_retains_published_status(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $originalPublishedAt = now()->subDays(2)->startOfSecond();

        $post = Post::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Original Published Title',
            'status' => 'published',
            'published_at' => $originalPublishedAt,
        ]);

        $response = $this->actingAs($admin)->put(route('posts.update', $post), [
            'title' => 'Updated Published Title',
            'category_id' => $category->id,
            'content' => '<p>Content updated without altering published state.</p>',
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertSame($originalPublishedAt->toDateTimeString(), $post->published_at->toDateTimeString());
        $this->assertSame('Updated Published Title', $post->title);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success', 'Bài viết đã được cập nhật thành công.');
    }

    /**
     * 5. Author creates a draft post -> status is draft.
     */
    public function test_author_can_create_draft_post(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Author Draft Post',
            'category_id' => $category->id,
            'content' => '<p>Author content in progress.</p>',
            'action' => 'draft',
        ]);

        $post = Post::where('title', 'Author Draft Post')->first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
    }

    /**
     * 6. Author sends post for review -> status is pending.
     */
    public function test_author_submitting_post_goes_to_pending(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Author Article Submitted For Review',
            'category_id' => $category->id,
            'content' => '<p>Author submits article to be reviewed by Admin.</p>',
            'action' => 'submit',
        ]);

        $post = Post::where('title', 'Author Article Submitted For Review')->first();
        $this->assertNotNull($post);
        $this->assertSame('pending', $post->status);
        $this->assertNull($post->published_at);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success', 'Bài viết đã được gửi đến Admin để kiểm duyệt thành công (Đang chờ Admin duyệt).');
    }

    /**
     * 7. Author attempts to submit with action=publish or status=published cannot bypass.
     */
    public function test_author_cannot_bypass_approval_via_publish_tampering(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Attempt on create with action=publish and status=published
        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Author Sneaky Publish Create',
            'category_id' => $category->id,
            'content' => '<p>Attempting direct publish by author.</p>',
            'action' => 'publish',
            'status' => 'published',
            'publish_now' => 1,
        ]);

        $post = Post::where('title', 'Author Sneaky Publish Create')->first();
        $this->assertNotNull($post);
        $this->assertSame('pending', $post->status);
        $this->assertNull($post->published_at);

        // Attempt on update with action=publish and status=published
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Author Sneaky Publish Update',
            'category_id' => $category->id,
            'content' => '<p>Attempting direct publish update by author.</p>',
            'action' => 'publish',
            'status' => 'published',
        ]);

        $post->refresh();
        $this->assertSame('pending', $post->status);
        $this->assertNull($post->published_at);
    }

    /**
     * 8. Viewer cannot create or publish posts.
     */
    public function test_viewer_is_forbidden_from_creating_or_publishing_posts(): void
    {
        $viewer = User::factory()->viewer()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($viewer)->post(route('posts.store'), [
            'title' => 'Viewer Post Attempt',
            'category_id' => $category->id,
            'content' => '<p>Viewer has no rights.</p>',
            'action' => 'publish',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['title' => 'Viewer Post Attempt']);
    }

    /**
     * 9. Admin published post does not appear in pending queue and appears in published list.
     */
    public function test_admin_published_post_does_not_appear_in_pending_queue(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Admin Direct Public Article',
            'category_id' => $category->id,
            'content' => '<p>This should be published immediately.</p>',
            'action' => 'publish',
        ]);

        $post = Post::where('title', 'Admin Direct Public Article')->firstOrFail();

        // Check Admin moderation pending queue
        $pendingResponse = $this->actingAs($admin, 'admin')->get(route('admin.posts.index', ['status' => 'pending']));
        $pendingResponse->assertOk();
        $pendingResponse->assertDontSee($post->title);

        // Check Admin moderation published list
        $publishedResponse = $this->actingAs($admin, 'admin')->get(route('admin.posts.index', ['status' => 'published']));
        $publishedResponse->assertOk();
        $publishedResponse->assertSee($post->title);
    }

    /**
     * 10. Admin published post appears in public home feed.
     */
    public function test_admin_published_post_appears_in_public_feed(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Super Live Article By Admin',
            'category_id' => $category->id,
            'content' => '<p>This appears instantly on the front page.</p>',
            'action' => 'publish',
        ]);

        $feedResponse = $this->get(route('posts.index'));
        $feedResponse->assertOk();
        $feedResponse->assertSee('Super Live Article By Admin');
    }

    /**
     * 11. Validation failure prevents publication.
     */
    public function test_validation_failure_prevents_publication(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('posts.store'), [
            'title' => '',
            'category_id' => '',
            'content' => '',
            'action' => 'publish',
        ]);

        $response->assertSessionHasErrors(['title', 'category_id', 'content']);
        $this->assertSame(0, Post::count());
    }

    /**
     * 12. Editor UI renders "Đăng bài ngay" for Admin and "Gửi bài viết để Admin duyệt" for Author.
     */
    public function test_editor_ui_button_renders_correctly_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // 1. Admin on Create Post Page
        $adminCreateResponse = $this->actingAs($admin)->get(route('posts.create'));
        $adminCreateResponse->assertOk();
        $adminCreateResponse->assertSee('Đăng bài ngay');
        $adminCreateResponse->assertDontSee('Gửi bài viết để Admin duyệt');

        // 2. Author on Create Post Page
        $authorCreateResponse = $this->actingAs($author)->get(route('posts.create'));
        $authorCreateResponse->assertOk();
        $authorCreateResponse->assertSee('Gửi bài viết để Admin duyệt');
        $authorCreateResponse->assertDontSee('Đăng bài ngay');

        // 3. Admin on Edit Draft Post Page
        $adminDraft = Post::factory()->create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);
        $adminEditResponse = $this->actingAs($admin)->get(route('posts.edit', $adminDraft));
        $adminEditResponse->assertOk();
        $adminEditResponse->assertSee('Đăng bài ngay');
        $adminEditResponse->assertDontSee('Gửi bài viết để Admin duyệt');

        // 4. Author on Edit Draft Post Page
        $authorDraft = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);
        $authorEditResponse = $this->actingAs($author)->get(route('posts.edit', $authorDraft));
        $authorEditResponse->assertOk();
        $authorEditResponse->assertSee('Gửi bài viết để Admin duyệt');
        $authorEditResponse->assertDontSee('Đăng bài ngay');
    }
}
