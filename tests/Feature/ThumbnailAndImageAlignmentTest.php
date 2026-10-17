<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\PostContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailAndImageAlignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_create_post_with_thumbnail(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('thumbnail.webp', 800, 600)->size(500);

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Post with Thumbnail',
            'category_id' => $category->id,
            'excerpt' => 'An excerpt',
            'content' => '<p>Post body with an image.</p>',
            'thumbnail' => $file,
            'action' => 'draft',
        ]);

        $post = Post::where('title', 'Post with Thumbnail')->first();
        $this->assertNotNull($post);
        $this->assertNotNull($post->thumbnail);
        $this->assertStringStartsWith('storage/thumbnails/', $post->thumbnail);

        $storedPath = str_replace('storage/', '', $post->thumbnail);
        Storage::disk('public')->assertExists($storedPath);

        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_author_can_replace_thumbnail_with_new_image(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Initial thumbnail
        $initialFile = UploadedFile::fake()->image('old_thumb.jpg', 600, 400)->size(300);
        $oldPath = $initialFile->store('thumbnails', 'public');

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => 'storage/'.$oldPath,
        ]);
        Storage::disk('public')->assertExists($oldPath);

        // Replace with new thumbnail
        $newFile = UploadedFile::fake()->image('new_thumb.png', 800, 600)->size(400);

        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $category->id,
            'excerpt' => $post->excerpt,
            'content' => $post->body,
            'thumbnail' => $newFile,
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertNotNull($post->thumbnail);
        $newStoredPath = str_replace('storage/', '', $post->thumbnail);
        $this->assertNotSame($oldPath, $newStoredPath);

        // Old file deleted, new file exists
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newStoredPath);

        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_author_can_remove_thumbnail_completely(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $initialFile = UploadedFile::fake()->image('thumb_to_remove.jpg', 600, 400);
        $oldPath = $initialFile->store('thumbnails', 'public');

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => 'storage/'.$oldPath,
        ]);
        Storage::disk('public')->assertExists($oldPath);

        // Update with remove_thumbnail = 1
        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $category->id,
            'excerpt' => $post->excerpt,
            'content' => $post->body,
            'remove_thumbnail' => '1',
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertNull($post->thumbnail);
        Storage::disk('public')->assertMissing($oldPath);

        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_author_retains_thumbnail_when_not_changing_it(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('keep_thumb.jpg', 600, 400);
        $path = $file->store('thumbnails', 'public');

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => 'storage/'.$path,
        ]);

        // Submit form without thumbnail and remove_thumbnail = 0
        $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'category_id' => $category->id,
            'excerpt' => $post->excerpt,
            'content' => $post->body,
            'remove_thumbnail' => '0',
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertSame('storage/'.$path, $post->thumbnail);
        Storage::disk('public')->assertExists($path);
    }

    public function test_shared_thumbnail_is_protected_when_one_post_removes_or_replaces_it(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $sharedFile = UploadedFile::fake()->image('shared_thumb.jpg', 600, 400);
        $sharedPath = $sharedFile->store('thumbnails', 'public');
        $sharedUri = 'storage/'.$sharedPath;

        $postA = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => $sharedUri,
        ]);

        $postB = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => $sharedUri,
        ]);

        // Post A removes its thumbnail
        $this->actingAs($author)->put(route('posts.update', $postA), [
            'title' => $postA->title,
            'category_id' => $category->id,
            'excerpt' => $postA->excerpt,
            'content' => $postA->body,
            'remove_thumbnail' => '1',
            'action' => 'draft',
        ]);

        $postA->refresh();
        $postB->refresh();

        $this->assertNull($postA->thumbnail);
        $this->assertSame($sharedUri, $postB->thumbnail);
        // Shared file MUST NOT be deleted from disk because Post B still uses it!
        Storage::disk('public')->assertExists($sharedPath);
    }

    public function test_shared_thumbnail_is_protected_when_post_is_deleted(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $sharedFile = UploadedFile::fake()->image('shared_thumb_delete.jpg', 600, 400);
        $sharedPath = $sharedFile->store('thumbnails', 'public');
        $sharedUri = 'storage/'.$sharedPath;

        $postA = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => $sharedUri,
        ]);

        $postB = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => $sharedUri,
        ]);

        $this->actingAs($author)->delete(route('posts.destroy', $postA));

        $this->assertDatabaseMissing('posts', ['id' => $postA->id]);
        $this->assertDatabaseHas('posts', ['id' => $postB->id]);
        // Shared file MUST NOT be deleted!
        Storage::disk('public')->assertExists($sharedPath);
    }

    public function test_unauthorized_author_cannot_update_or_remove_other_authors_thumbnail(): void
    {
        $authorA = User::factory()->author()->create();
        $authorB = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $authorA->id,
            'category_id' => $category->id,
            'thumbnail' => 'storage/thumbnails/original.jpg',
        ]);

        $response = $this->actingAs($authorB)->put(route('posts.update', $post), [
            'title' => 'Hacked title',
            'category_id' => $category->id,
            'excerpt' => 'Hacked excerpt',
            'content' => 'Hacked content',
            'remove_thumbnail' => '1',
        ]);

        $response->assertForbidden();
        $post->refresh();
        $this->assertSame('storage/thumbnails/original.jpg', $post->thumbnail);
    }

    public function test_thumbnail_validation_rejects_oversized_file(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // 3MB file (> 2048KB)
        $largeFile = UploadedFile::fake()->create('large.jpg', 3072, 'image/jpeg');

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Oversized Thumbnail',
            'category_id' => $category->id,
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'thumbnail' => $largeFile,
        ]);

        $response->assertSessionHasErrors('thumbnail');
    }

    public function test_post_content_service_preserves_figure_figcaption_and_alignment(): void
    {
        $html = '<figure class="editor-image-figure align-center" data-align="center"><img src="https://example.com/photo.jpg" alt="A scenery" class="editor-content-image align-center" data-align="center"><figcaption class="editor-image-caption">Bình minh trên biển</figcaption></figure>';

        $clean = PostContentService::cleanHtml($html);

        $this->assertStringContainsString('<figure', $clean);
        $this->assertStringContainsString('editor-image-figure', $clean);
        $this->assertStringContainsString('align-center', $clean);
        $this->assertStringContainsString('<img', $clean);
        $this->assertStringContainsString('alt="A scenery"', $clean);
        $this->assertStringContainsString('<figcaption', $clean);
        $this->assertStringContainsString('Bình minh trên biển', $clean);
    }

    public function test_post_content_service_preserves_left_and_right_alignment(): void
    {
        $leftHtml = '<figure class="editor-image-figure align-left" data-align="left"><img src="https://example.com/left.jpg" class="editor-content-image align-left" data-align="left"><figcaption class="editor-image-caption">Ảnh bên trái</figcaption></figure>';
        $cleanLeft = PostContentService::cleanHtml($leftHtml);

        $this->assertStringContainsString('align-left', $cleanLeft);
        $this->assertStringContainsString('Ảnh bên trái', $cleanLeft);

        $rightHtml = '<figure class="editor-image-figure align-right" data-align="right"><img src="https://example.com/right.jpg" class="editor-content-image align-right" data-align="right"><figcaption class="editor-image-caption">Ảnh bên phải</figcaption></figure>';
        $cleanRight = PostContentService::cleanHtml($rightHtml);

        $this->assertStringContainsString('align-right', $cleanRight);
        $this->assertStringContainsString('Ảnh bên phải', $cleanRight);
    }

    public function test_post_content_service_sanitizes_malicious_scripts_in_figures_and_captions(): void
    {
        $malicious = '<figure class="editor-image-figure align-center" data-align="center" onclick="alert(1)"><img src="https://example.com/pic.jpg" onerror="alert(2)"><figcaption><script>alert(3)</script>Chú thích an toàn</figcaption></figure>';

        $clean = PostContentService::cleanHtml($malicious);

        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringContainsString('Chú thích an toàn', $clean);
    }

    public function test_create_and_edit_views_render_thumbnail_uploader_widget(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'thumbnail' => 'storage/thumbnails/existing.jpg',
        ]);

        // Create view
        $createRes = $this->actingAs($author)->get(route('posts.create'));
        $createRes->assertOk();
        $createRes->assertSee('id="thumbnail-uploader-widget"', false);
        $createRes->assertSee('id="thumbnail-drop-zone"', false);
        $createRes->assertSee('id="thumbnail-remove-confirm-modal"', false);

        // Edit view
        $editRes = $this->actingAs($author)->get(route('posts.edit', $post));
        $editRes->assertOk();
        $editRes->assertSee('id="thumbnail-uploader-widget"', false);
        $editRes->assertSee('Thay ảnh');
        $editRes->assertSee('Gỡ ảnh');
        $editRes->assertSee('id="thumbnail-remove-confirm-modal"', false);
        $editRes->assertSee('id="editor-image-modal"', false);
        $editRes->assertSee('Căn chỉnh vị trí hình ảnh');
        $editRes->assertSee('Chú thích hình ảnh (Caption)');
        $editRes->assertSee('Văn bản thay thế (ALT text)');
    }
}

