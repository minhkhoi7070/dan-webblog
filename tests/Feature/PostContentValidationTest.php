<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PostContentValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function emptyEditorContent(): array
    {
        return [
            'empty paragraph' => ['<p></p>'],
            'line break paragraph' => ['<p><br></p>'],
            'spaces' => ['<div>   </div>'],
            'nonbreaking space' => ['<p>&nbsp;</p>'],
            'empty bold' => ['<p><strong></strong></p>'],
            'empty italic' => ['<p><em> </em></p>'],
            'empty span' => ['<p><span></span></p>'],
            'zero width unicode' => ["<p>\u{200B}\u{00A0}</p>"],
            'zero width entity' => ['<p>&ZeroWidthSpace;</p>'],
            'script only' => ['<script>alert(1)</script>'],
        ];
    }

    #[DataProvider('emptyEditorContent')]
    public function test_empty_or_invisible_editor_content_cannot_create_a_draft(string $body): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $this->actingAs($author)->postJson(route('posts.store'), [
            'title' => 'Invalid editor content',
            'category_id' => $category->id,
            'body' => $body,
            'action' => 'draft',
        ])->assertUnprocessable()->assertJsonValidationErrors('body');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_vietnamese_rich_text_remains_valid_and_scripts_are_removed(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Nội dung hợp lệ',
            'category_id' => $category->id,
            'content' => '<p><strong>Xin chào</strong> <em>thế giới</em></p><script>alert(1)</script>',
            'action' => 'submit',
        ])->assertRedirect();

        $post = Post::query()->firstOrFail();
        $this->assertSame('pending', $post->status);
        $this->assertStringContainsString('<strong>Xin chào</strong>', $post->body);
        $this->assertStringNotContainsString('<script>', $post->body);
    }

    public function test_existing_local_image_alone_is_valid_editor_content(): void
    {
        Storage::fake('public');
        UploadedFile::fake()->image('photo.jpg')->storeAs('posts/images', 'photo.jpg', 'public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Image only',
            'category_id' => $category->id,
            'body' => '<p><img src="/storage/posts/images/photo.jpg" alt="Ảnh"></p>',
            'action' => 'draft',
        ])->assertRedirect();

        $this->assertSame('draft', Post::query()->firstOrFail()->status);
    }

    public function test_existing_local_video_alone_is_valid_editor_content(): void
    {
        Storage::fake('public');
        UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4')->storeAs('posts/videos', 'clip.mp4', 'public');
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('posts.store'), [
            'title' => 'Video only',
            'category_id' => $category->id,
            'body' => '<video src="/storage/posts/videos/clip.mp4" controls></video>',
            'action' => 'publish',
        ])->assertRedirect();

        $this->assertSame('published', Post::query()->firstOrFail()->status);
    }

    public function test_youtube_embed_alone_remains_valid(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'YouTube only',
            'category_id' => $category->id,
            'body' => '<iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"></iframe>',
            'action' => 'submit',
        ])->assertRedirect();

        $this->assertSame('pending', Post::query()->firstOrFail()->status);
    }

    public function test_nonexistent_or_unsafe_media_alone_does_not_create_a_post(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        foreach (['<img src="/storage/posts/images/missing.jpg">', '<img src="javascript:alert(1)">'] as $body) {
            $this->actingAs($author)->postJson(route('posts.store'), [
                'title' => 'Invalid media',
                'category_id' => $category->id,
                'body' => $body,
                'action' => 'submit',
            ])->assertUnprocessable()->assertJsonValidationErrors('body');
        }

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_invalid_update_keeps_existing_content_and_status(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->published()->create(['body' => '<p>Original visible content</p>']);

        $this->actingAs($author)->putJson(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $post->category_id,
            'body' => '<p><br></p>',
            'action' => 'draft',
        ])->assertUnprocessable()->assertJsonValidationErrors('body');

        $this->assertSame('<p>Original visible content</p>', $post->fresh()->body);
        $this->assertSame('published', $post->fresh()->status);
    }
}
