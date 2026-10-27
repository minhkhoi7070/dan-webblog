<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTagWorkflowTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // TAG SYSTEM TESTS
    // =========================================================================

    public function test_author_can_select_and_save_multiple_tags_successfully(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $tag2 = Tag::factory()->create(['name' => 'VueJS', 'slug' => 'vuejs']);
        $tag3 = Tag::factory()->create(['name' => 'Tailwind', 'slug' => 'tailwind']);

        $postData = [
            'title' => 'Building Modern Apps with Laravel and Vue',
            'category_id' => $category->id,
            'excerpt' => 'A detailed guide to fullstack architecture.',
            'content' => '<p>Comprehensive technical content about Laravel & Vue ecosystem.</p>',
            'tags' => [$tag1->id, $tag2->id, $tag3->id],
            'action' => 'draft',
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::first();
        $this->assertNotNull($post);
        $response->assertRedirect(route('posts.edit', $post));

        $this->assertCount(3, $post->tags);
        $this->assertEqualsCanonicalizing(
            [$tag1->id, $tag2->id, $tag3->id],
            $post->tags->pluck('id')->all()
        );
    }

    public function test_tags_are_preserved_when_reopening_post_for_editing(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);
        $post->tags()->sync($tags->pluck('id')->all());

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        foreach ($tags as $tag) {
            $response->assertSee('#'.$tag->name);
        }
    }

    public function test_editing_post_can_add_and_remove_tags(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tagKeep = Tag::factory()->create(['name' => 'KeepMe']);
        $tagRemove = Tag::factory()->create(['name' => 'RemoveMe']);
        $tagAdd = Tag::factory()->create(['name' => 'AddMe']);

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Initial Title',
            'status' => 'draft',
        ]);
        $post->tags()->sync([$tagKeep->id, $tagRemove->id]);

        $this->assertCount(2, $post->tags);

        // Update post: remove tagRemove, add tagAdd, keep tagKeep
        $updateData = [
            'title' => 'Updated Title with New Tags',
            'category_id' => $category->id,
            'content' => '<p>Updated post body content.</p>',
            'tags' => [$tagKeep->id, $tagAdd->id],
            'action' => 'draft',
        ];

        $response = $this->actingAs($author)->put(route('posts.update', $post), $updateData);

        $response->assertRedirect(route('posts.edit', $post));

        $post->refresh();
        $this->assertCount(2, $post->tags);
        $this->assertTrue($post->tags->contains($tagKeep));
        $this->assertTrue($post->tags->contains($tagAdd));
        $this->assertFalse($post->tags->contains($tagRemove));
    }

    public function test_duplicate_tags_are_not_saved_for_post(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        // Pass duplicates in array: [tag1, tag1, tag2]
        $postData = [
            'title' => 'Post with Duplicate Tag Submissions',
            'category_id' => $category->id,
            'content' => '<p>Post content.</p>',
            'tags' => [$tag1->id, $tag1->id, $tag2->id],
            'action' => 'draft',
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertCount(2, $post->tags);
    }

    public function test_invalid_or_non_existent_tag_ids_are_rejected(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // 1. Non-existent integer ID
        $responseNonExistent = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Test Title',
            'category_id' => $category->id,
            'content' => '<p>Content</p>',
            'tags' => [999999],
        ]);
        $responseNonExistent->assertSessionHasErrors(['tags.0']);

        // 2. Non-integer string format
        $responseInvalidFormat = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Test Title',
            'category_id' => $category->id,
            'content' => '<p>Content</p>',
            'tags' => ['invalid-tag-format'],
        ]);
        $responseInvalidFormat->assertSessionHasErrors(['tags.0']);
    }

    public function test_exceeding_max_tag_limit_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(11)->create();

        // Submitting 11 tags (limit is 10)
        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with too many tags',
            'category_id' => $category->id,
            'content' => '<p>Content</p>',
            'tags' => $tags->pluck('id')->all(),
        ]);

        $response->assertSessionHasErrors(['tags']);
        $this->assertDatabaseEmpty('posts');
    }

    public function test_author_and_admin_can_create_new_tags_via_api(): void
    {
        $author = User::factory()->author()->create();

        // 1. Author creates tag with hashtag and whitespace
        $response = $this->actingAs($author)->postJson(route('tags.store'), [
            'name' => '  #NuxtJS 3  ',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'tag' => [
                    'name' => 'NuxtJS 3',
                    'slug' => 'nuxtjs-3',
                ],
            ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'NuxtJS 3',
            'slug' => 'nuxtjs-3',
        ]);

        // 2. Submitting again returns the existing tag without creating duplicate
        $responseDuplicate = $this->actingAs($author)->postJson(route('tags.store'), [
            'name' => '#nuxtjs 3',
        ]);

        $responseDuplicate->assertStatus(201)
            ->assertJson([
                'success' => true,
                'tag' => [
                    'name' => 'NuxtJS 3',
                    'slug' => 'nuxtjs-3',
                ],
            ]);

        $this->assertSame(1, Tag::where('slug', 'nuxtjs-3')->count());
    }

    public function test_viewer_and_guest_cannot_create_or_manage_tags(): void
    {
        // Guest attempts to create tag
        $this->postJson(route('tags.store'), [
            'name' => 'GuestTag',
        ])->assertUnauthorized();

        $viewer = User::factory()->viewer()->create();

        // Viewer attempts to create tag
        $this->actingAs($viewer)->postJson(route('tags.store'), [
            'name' => 'ForbiddenTag',
        ])->assertForbidden();
    }

    public function test_guest_cannot_view_unpublished_post_via_direct_url(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $draftPost = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
            'slug' => 'secret-draft-post',
        ]);

        $pendingPost = Post::factory()->pending()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'secret-pending-post',
        ]);

        // Guest visiting draft
        $this->get(route('posts.show', $draftPost->slug))->assertNotFound();

        // Guest visiting pending
        $this->get(route('posts.show', $pendingPost->slug))->assertNotFound();
    }

    public function test_admin_tag_management_requires_admin_role(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $viewer = User::factory()->viewer()->create();

        $tag = Tag::factory()->create(['name' => 'TechTag', 'slug' => 'techtag']);

        // Author is forbidden from admin tag CRUD
        $this->actingAs($author)->get(route('admin.tags.index'))->assertForbidden();
        $this->actingAs($author)->get(route('admin.tags.create'))->assertForbidden();
        $this->actingAs($author)->post(route('admin.tags.store'), ['name' => 'HackTag'])->assertForbidden();
        $this->actingAs($author)->get(route('admin.tags.edit', $tag))->assertForbidden();
        $this->actingAs($author)->put(route('admin.tags.update', $tag), ['name' => 'HackTag'])->assertForbidden();
        $this->actingAs($author)->delete(route('admin.tags.destroy', $tag))->assertForbidden();

        // Viewer is forbidden
        $this->actingAs($viewer)->get(route('admin.tags.index'))->assertForbidden();

        // Admin can access and manage tags
        $this->actingAs($admin)->get(route('admin.tags.index'))->assertOk()->assertSee('TechTag');
        $this->actingAs($admin)->get(route('admin.tags.create'))->assertOk();

        // Admin creates new tag
        $createResponse = $this->actingAs($admin)->post(route('admin.tags.store'), [
            'name' => 'AdminCreatedTag',
            'slug' => 'admin-created-tag',
        ]);
        $createResponse->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['slug' => 'admin-created-tag']);

        // Admin updates tag
        $newTag = Tag::where('slug', 'admin-created-tag')->first();
        $updateResponse = $this->actingAs($admin)->put(route('admin.tags.update', $newTag), [
            'name' => 'AdminUpdatedTag',
            'slug' => 'admin-updated-tag',
        ]);
        $updateResponse->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['slug' => 'admin-updated-tag']);

        // Admin deletes tag safely
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.tags.destroy', $newTag));
        $deleteResponse->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseMissing('tags', ['id' => $newTag->id]);
    }

    // =========================================================================
    // DRAFT & SUBMISSION WORKFLOW TESTS
    // =========================================================================

    public function test_saving_post_as_draft_sets_status_to_draft(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'My Draft Post',
            'category_id' => $category->id,
            'content' => '<p>Draft body content</p>',
            'action' => 'draft',
        ]);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);
        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_editing_draft_does_not_create_new_record(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Original Draft',
            'body' => '<p>Original body</p>',
            'status' => 'draft',
        ]);

        $this->assertSame(1, Post::count());

        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Updated Draft Title',
            'category_id' => $category->id,
            'content' => '<p>Updated body content</p>',
            'action' => 'draft',
        ]);

        $response->assertRedirect(route('posts.edit', $post));

        // Must still be only 1 post record in database
        $this->assertSame(1, Post::count());
        $post->refresh();
        $this->assertSame('Updated Draft Title', $post->title);
        $this->assertSame('draft', $post->status);
    }

    public function test_saving_draft_preserves_tags_category_and_thumbnail(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $thumbnail = UploadedFile::fake()->image('cover.jpg', 600, 400);

        // 1. Create with thumbnail and tag
        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with Thumbnail and Tag',
            'category_id' => $category->id,
            'content' => '<p>Post content</p>',
            'thumbnail' => $thumbnail,
            'tags' => [$tag->id],
            'action' => 'draft',
        ]);

        $post = Post::first();
        $this->assertNotNull($post->thumbnail);
        $this->assertCount(1, $post->tags);

        // 2. Update without changing thumbnail or tags
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Updated Title Only',
            'category_id' => $category->id,
            'content' => '<p>Updated content</p>',
            'tags' => [$tag->id],
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertNotNull($post->thumbnail);
        $this->assertCount(1, $post->tags);
        $this->assertSame($tag->id, $post->tags->first()->id);
        $this->assertSame($category->id, $post->category_id);
    }

    public function test_submitting_post_for_review_transitions_status_to_pending_without_auto_publishing(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Create with submit action
        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post Submitted Directly to Admin',
            'category_id' => $category->id,
            'content' => '<p>Complete and validated content ready for editorial review.</p>',
            'action' => 'submit',
        ]);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('pending', $post->status);
        $this->assertNull($post->published_at);
        $this->assertNotSame('published', $post->status);

        $response->assertRedirect(route('posts.edit', $post));
        $response->assertSessionHas('success');
    }

    public function test_submitting_with_missing_required_fields_fails_validation_and_does_not_transition_status(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Draft Post',
            'status' => 'draft',
        ]);

        // Attempt submit without title or content
        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => '',
            'category_id' => $category->id,
            'content' => '',
            'action' => 'submit',
        ]);

        $response->assertSessionHasErrors(['title']);

        $post->refresh();
        $this->assertSame('draft', $post->status);
    }

    public function test_multiple_submit_clicks_do_not_create_duplicate_posts(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // First submit via update form
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $post->category_id,
            'content' => $post->body,
            'action' => 'submit',
        ]);

        $post->refresh();
        $this->assertSame('pending', $post->status);
        $this->assertSame(1, Post::count());

        // Subsequent update submit while pending keeps pending without creating duplicate
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $post->category_id,
            'content' => $post->body,
            'action' => 'submit',
        ]);

        $post->refresh();
        $this->assertSame('pending', $post->status);
        $this->assertSame(1, Post::count());
    }

    public function test_resubmitting_rejected_post_transitions_to_pending(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->rejected()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $this->assertSame('rejected', $post->status);

        // Author fixes and resubmits post
        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Corrected and Polished Title',
            'category_id' => $category->id,
            'content' => '<p>Content with improvements requested by reviewers.</p>',
            'action' => 'submit',
        ]);

        $response->assertRedirect(route('posts.edit', $post));

        $post->refresh();
        $this->assertSame('pending', $post->status);
        $this->assertSame('Corrected and Polished Title', $post->title);
    }

    public function test_author_cannot_edit_or_submit_another_authors_post(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();
        $category = Category::factory()->create();

        $postA = Post::factory()->create([
            'user_id' => $authorA->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Author B tries to edit
        $this->actingAs($authorB)->get(route('posts.edit', $postA))->assertForbidden();

        // Author B tries to update
        $this->actingAs($authorB)->put(route('posts.update', $postA), [
            'title' => 'Tampered Title',
            'category_id' => $category->id,
            'content' => '<p>Tampered Content</p>',
            'action' => 'submit',
        ])->assertForbidden();

        // Author B tries to submit directly
        $this->actingAs($authorB)->post(route('posts.submit', $postA))->assertForbidden();

        $postA->refresh();
        $this->assertSame('draft', $postA->status);
    }

    public function test_tag_seeder_creates_all_sample_tags_and_is_idempotent(): void
    {
        $this->seed(TagSeeder::class);

        $initialCount = Tag::count();
        $this->assertGreaterThanOrEqual(21, $initialCount);

        // Run again to ensure idempotency and no duplicates
        $this->seed(TagSeeder::class);
        $this->assertSame($initialCount, Tag::count());
    }
}
