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

class AuthorPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_view_create_page(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('posts.create'));

        $response->assertOk();
        $response->assertSee('Tạo bài viết mới');
        $response->assertSee('Lưu bản nháp');
    }

    public function test_author_can_create_draft_post_with_tags(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create(['name' => 'Laravel']);
        $tags = Tag::factory()->count(2)->create();

        $postData = [
            'title' => 'Building Clean Laravel Architectures',
            'category_id' => $category->id,
            'excerpt' => 'A comprehensive guide to clean architecture in Laravel.',
            'content' => 'Full detailed technical body content for the blog post.',
            'tags' => $tags->pluck('id')->all(),
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('Building Clean Laravel Architectures', $post->title);
        $this->assertSame($author->id, $post->user_id);
        $this->assertSame($category->id, $post->category_id);
        $this->assertSame('draft', $post->status);
        $this->assertCount(2, $post->tags);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success');
    }

    public function test_author_can_edit_and_update_own_post(): void
    {
        $author = User::factory()->author()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category1->id,
            'title' => 'Original Title',
            'status' => 'draft',
        ]);
        $post->tags()->sync([$tag1->id]);

        // Edit page
        $editResponse = $this->actingAs($author)->get(route('posts.edit', $post));
        $editResponse->assertOk();
        $editResponse->assertSee('Original Title');

        // Update post
        $updateData = [
            'title' => 'Updated Post Title',
            'category_id' => $category2->id,
            'excerpt' => 'Updated excerpt text.',
            'content' => 'Updated body content text.',
            'tags' => [$tag2->id],
        ];

        $updateResponse = $this->actingAs($author)->put(route('posts.update', $post), $updateData);

        $updateResponse->assertRedirect(route('posts.edit', $post));
        $updateResponse->assertSessionHas('success');

        $post->refresh();
        $this->assertSame('Updated Post Title', $post->title);
        $this->assertSame($category2->id, $post->category_id);
        $this->assertSame('Updated excerpt text.', $post->excerpt);
        $this->assertSame('Updated body content text.', $post->body);
        $this->assertSame([$tag2->id], $post->tags->pluck('id')->all());
    }

    public function test_author_can_delete_own_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
        ]);

        $response = $this->actingAs($author)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.stats'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_author_cannot_edit_other_authors_posts(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();

        $postA = Post::factory()->create(['user_id' => $authorA->id]);

        // Author B tries to view edit page
        $this->actingAs($authorB)->get(route('posts.edit', $postA))
            ->assertForbidden();

        // Author B tries to update
        $this->actingAs($authorB)->put(route('posts.update', $postA), [
            'title' => 'Hacked Title',
            'category_id' => $postA->category_id,
            'content' => 'Hacked content',
        ])->assertForbidden();

        // Author B tries to delete
        $this->actingAs($authorB)->delete(route('posts.destroy', $postA))
            ->assertForbidden();

        // Author B tries to submit
        $this->actingAs($authorB)->post(route('posts.submit', $postA))
            ->assertForbidden();

        $postA->refresh();
        $this->assertNotSame('Hacked Title', $postA->title);
    }

    public function test_author_cannot_publish_directly_or_tamper_workflow_fields(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Attempting to publish on create
        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Sneaky Direct Publish Attempt',
            'category_id' => $category->id,
            'content' => 'Content that author wants published immediately without admin review.',
            'status' => 'published',
            'publish_now' => 1,
            'reviewed_by' => 99,
            'reviewed_at' => now()->toDateTimeString(),
        ]);

        $createdPost = Post::first();
        $this->assertSame('draft', $createdPost->status);
        $this->assertNull($createdPost->published_at);

        // Attempting to publish on update
        $this->actingAs($author)->put(route('posts.update', $createdPost), [
            'title' => 'Sneaky Update Publish Attempt',
            'category_id' => $category->id,
            'content' => 'Content updated with attempt to force publish.',
            'status' => 'published',
            'publish_now' => 1,
            'reviewed_by' => 99,
        ]);

        $createdPost->refresh();
        $this->assertSame('draft', $createdPost->status);
        $this->assertNull($createdPost->published_at);
    }

    public function test_author_can_submit_draft_post_for_review(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($author)->post(route('posts.submit', $post));

        $response->assertRedirect();
        $post->refresh();
        $this->assertSame('pending', $post->status);
    }

    public function test_author_can_resubmit_rejected_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->rejected()->create([
            'user_id' => $author->id,
        ]);

        $this->assertSame('rejected', $post->status);

        // Resubmit transitions from rejected -> pending
        $response = $this->actingAs($author)->post(route('posts.submit', $post));

        $response->assertRedirect();
        $post->refresh();
        $this->assertSame('pending', $post->status);
    }

    public function test_author_cannot_submit_already_pending_or_published_post(): void
    {
        $author = User::factory()->author()->create();

        $pendingPost = Post::factory()->pending()->create(['user_id' => $author->id]);
        $this->actingAs($author)->post(route('posts.submit', $pendingPost))
            ->assertForbidden();

        $publishedPost = Post::factory()->published()->create(['user_id' => $author->id]);
        $this->actingAs($author)->post(route('posts.submit', $publishedPost))
            ->assertForbidden();
    }

    public function test_validation_rules_apply_to_post_creation(): void
    {
        $author = User::factory()->author()->create();

        // Empty submission triggers required errors
        $response = $this->actingAs($author)->post(route('posts.store'), []);

        $response->assertSessionHasErrors(['title', 'category_id', 'content']);

        // Invalid category_id
        $responseCategory = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Test Title',
            'category_id' => 999999,
            'content' => 'Some test content',
        ]);
        $responseCategory->assertSessionHasErrors(['category_id']);

        // Invalid tags
        $category = Category::factory()->create();
        $responseTags = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Test Title',
            'category_id' => $category->id,
            'content' => 'Some test content',
            'tags' => ['invalid-tag-format'],
        ]);
        $responseTags->assertSessionHasErrors(['tags.0']);

        // Invalid thumbnail file type
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
        $responseFile = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Test Title',
            'category_id' => $category->id,
            'content' => 'Some test content',
            'thumbnail' => $invalidFile,
        ]);
        $responseFile->assertSessionHasErrors(['thumbnail']);
    }

    public function test_thumbnail_upload_works_through_public_filesystem(): void
    {
        Storage::fake('public');

        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $file = UploadedFile::fake()->image('post-cover.jpg', 600, 400);

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with Image',
            'category_id' => $category->id,
            'content' => 'Body text with uploaded thumbnail image.',
            'thumbnail' => $file,
        ]);

        $post = Post::first();
        $this->assertNotNull($post->thumbnail);
        $this->assertStringStartsWith('storage/thumbnails/', $post->thumbnail);

        $relativePath = str_replace('storage/', '', $post->thumbnail);
        Storage::disk('public')->assertExists($relativePath);

        // Update post with replacement image
        $newFile = UploadedFile::fake()->image('new-cover.png', 800, 600);
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Post with Image Updated',
            'category_id' => $category->id,
            'content' => 'Body text with replacement thumbnail.',
            'thumbnail' => $newFile,
        ]);

        $post->refresh();
        $newRelativePath = str_replace('storage/', '', $post->thumbnail);
        $this->assertNotSame($relativePath, $newRelativePath);
        Storage::disk('public')->assertExists($newRelativePath);
        Storage::disk('public')->assertMissing($relativePath);

        // Delete post cleans up image
        $this->actingAs($author)->delete(route('posts.destroy', $post));
        Storage::disk('public')->assertMissing($newRelativePath);
    }

    public function test_author_dashboard_displays_all_eight_statistics_metrics(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // 1 Draft
        Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
            'views' => 10,
        ]);

        // 2 Pending
        Post::factory()->pending()->count(2)->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'views' => 20,
        ]);

        // 3 Published
        $publishedPosts = Post::factory()->published()->count(3)->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'views' => 100,
        ]);

        // 1 Rejected
        Post::factory()->rejected()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'views' => 5,
        ]);

        // Add comments and likes to published post
        $firstPost = $publishedPosts->first();
        Comment::factory()->count(4)->create(['post_id' => $firstPost->id]);

        $likers = User::factory()->count(6)->create();
        $firstPost->likers()->attach($likers->pluck('id'));

        $response = $this->actingAs($author)->get(route('posts.stats'));

        $response->assertOk();
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 7
                && $stats['draft'] === 1
                && $stats['pending'] === 2
                && $stats['published'] === 3
                && $stats['rejected'] === 1
                && $stats['views'] === 355 // 10 + 2*20 + 3*100 + 5
                && $stats['likes'] === 6
                && $stats['comments'] === 4;
        });

        // Test status filtering
        $draftResponse = $this->actingAs($author)->get(route('posts.stats', ['status' => 'draft']));
        $draftResponse->assertOk();
        $draftResponse->assertViewHas('posts', function ($posts) {
            return $posts->total() === 1;
        });
    }

    public function test_viewer_or_guest_cannot_access_author_cms(): void
    {
        $viewer = User::factory()->viewer()->create();

        // Guest redirected to login
        $this->get(route('posts.create'))->assertRedirect(route('login'));
        $this->get(route('posts.stats'))->assertRedirect(route('login'));

        // Viewer forbidden
        $this->actingAs($viewer)->get(route('posts.create'))->assertForbidden();
        $this->actingAs($viewer)->get(route('posts.stats'))->assertForbidden();
        $this->actingAs($viewer)->post(route('posts.store'), [
            'title' => 'Viewer Post Attempt',
            'category_id' => 1,
            'content' => 'Should be forbidden',
        ])->assertForbidden();
    }

    public function test_author_can_delete_spam_comment_on_own_post(): void
    {
        $author = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'body' => 'Spam comment on author post.',
        ]);

        $response = $this->actingAs($author)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_author_cannot_delete_comment_on_other_authors_post(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();

        $postA = Post::factory()->published()->create([
            'user_id' => $authorA->id,
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $postA->id,
            'user_id' => $viewer->id,
            'body' => 'Comment on Author A post.',
        ]);

        // Author B attempts to delete comment on Author A's post
        $response = $this->actingAs($authorB)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_author_inherits_viewer_social_capabilities(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        $post = Post::factory()->published()->create([
            'user_id' => $otherAuthor->id,
        ]);

        // 1. Author can like another post
        $this->actingAs($author)
            ->postJson(route('posts.like', $post->id))
            ->assertOk()
            ->assertJson(['liked' => true]);

        // 2. Author can favorite another post
        $this->actingAs($author)
            ->postJson(route('posts.favorite', $post->id))
            ->assertOk()
            ->assertJson(['saved' => true]);

        // 3. Author can follow another author
        $this->actingAs($author)
            ->postJson(route('authors.follow', $otherAuthor->id))
            ->assertOk()
            ->assertJson(['following' => true]);

        // 4. Author can comment on another post
        $this->actingAs($author)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Great technical article from another author!',
            ])->assertStatus(201);
    }
}
