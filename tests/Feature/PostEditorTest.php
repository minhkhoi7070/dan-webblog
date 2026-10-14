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

class PostEditorTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // 1. IMAGE UPLOAD SECURITY & AUTHORIZATION TESTS
    // =========================================================================

    public function test_author_can_upload_image_successfully(): void
    {
        Storage::fake('public');

        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->image('custom_diagram.png', 640, 480);

        $response = $this->actingAs($author)->postJson(route('posts.upload-image'), [
            'image' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'url',
                'filename',
            ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertStringStartsWith('/storage/posts/images/', $data['url']);

        // Check file exists on disk
        $storagePath = 'posts/images/'.$data['filename'];
        Storage::disk('public')->assertExists($storagePath);

        // Security: Confirm filename was randomized, not using client filename
        $this->assertNotSame('custom_diagram.png', $data['filename']);
    }

    public function test_unauthenticated_user_cannot_upload_image(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson(route('posts.upload-image'), [
            'image' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_viewer_cannot_upload_image(): void
    {
        Storage::fake('public');
        $viewer = User::factory()->viewer()->create();
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($viewer)->postJson(route('posts.upload-image'), [
            'image' => $file,
        ]);

        $response->assertForbidden();
    }

    public function test_locked_author_cannot_upload_image(): void
    {
        Storage::fake('public');
        $lockedAuthor = User::factory()->author()->create(['is_locked' => true]);
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($lockedAuthor)->postJson(route('posts.upload-image'), [
            'image' => $file,
        ]);

        $response->assertForbidden();
    }

    public function test_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        // Attempt uploading a PHP script or text file
        $badFile = UploadedFile::fake()->create('malicious.php', 50, 'application/x-php');

        $response = $this->actingAs($author)->postJson(route('posts.upload-image'), [
            'image' => $badFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    public function test_upload_rejects_oversized_images(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        // 3MB image (limit is 2048KB)
        $largeFile = UploadedFile::fake()->image('huge.jpg')->size(3000);

        $response = $this->actingAs($author)->postJson(route('posts.upload-image'), [
            'image' => $largeFile,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    // =========================================================================
    // 2. WYSIWYG CONTENT SAVING & RENDERING TESTS
    // =========================================================================

    public function test_author_can_create_post_with_wysiwyg_content_including_images_and_links(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $contentHtml = '<p>Chào mừng đến với bài viết mới. Xem thêm tại <a href="https://laravel.com">Trang chủ Laravel</a>.</p>'
            .'<p><img src="/storage/posts/images/sample123.jpg" alt="Mô tả minh họa"></p>'
            .'<h2>Tiêu đề mục 2</h2>'
            .'<ul><li>Mục thứ nhất</li><li>Mục thứ hai</li></ul>';

        $postData = [
            'title' => 'Bài viết WYSIWYG Hoàn chỉnh',
            'category_id' => $category->id,
            'excerpt' => 'Tóm tắt bài viết định dạng trực quan.',
            'content' => $contentHtml,
            'action' => 'draft',
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::where('title', 'Bài viết WYSIWYG Hoàn chỉnh')->first();
        $this->assertNotNull($post);
        $this->assertSame('draft', $post->status);

        // Verify HTML elements were preserved in database body
        $this->assertStringContainsString('https://laravel.com', $post->body);
        $this->assertStringContainsString('/storage/posts/images/sample123.jpg', $post->body);
        $this->assertStringContainsString('<h2>Tiêu đề mục 2</h2>', $post->body);

        // Edit page loads content properly
        $editResponse = $this->actingAs($author)->get(route('posts.edit', $post));
        $editResponse->assertOk();
        $editResponse->assertSee('/storage/posts/images/sample123.jpg', false);
        $editResponse->assertSee('https://laravel.com', false);
    }

    public function test_post_content_sanitizes_malicious_xss_and_protocols(): void
    {
        $maliciousHtml = '<p>An toàn nội dung <script>alert("xss")</script>'
            .'<a href="javascript:alert(1)">Liên kết độc hại</a>'
            .'<a href="https://trusted.com">Liên kết an toàn</a>'
            .'<img src="/storage/posts/images/test.jpg" onerror="alert(2)" alt="Ảnh">'
            .'<iframe src="https://evil.com"></iframe></p>';

        $clean = PostContentService::cleanHtml($maliciousHtml);

        // Dangerous tags stripped
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('alert("xss")', $clean);
        $this->assertStringNotContainsString('<iframe>', $clean);

        // javascript: protocol stripped
        $this->assertStringNotContainsString('javascript:alert(1)', $clean);

        // onerror event handler stripped
        $this->assertStringNotContainsString('onerror', $clean);

        // Safe elements preserved
        $this->assertStringContainsString('https://trusted.com', $clean);
        $this->assertStringContainsString('/storage/posts/images/test.jpg', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_legacy_markdown_content_renders_cleanly_on_show(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $legacyMarkdown = "Đoạn văn thứ nhất.\n\nĐoạn văn thứ hai có [Blog Tuổi Trẻ](https://tuoitre.vn) và tiêu đề:\n\n## Mục quan trọng\n\n- Điểm 1\n- Điểm 2";

        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết Markdown cũ',
            'body' => $legacyMarkdown,
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();

        // Rendered HTML contains paragraphs, converted links, headings and lists
        $response->assertSee('<p>Đoạn văn thứ nhất.</p>', false);
        $response->assertSee('https://tuoitre.vn', false);
        $response->assertSee('Blog Tuổi Trẻ', false);
        $response->assertSee('<h2>Mục quan trọng</h2>', false);
        $response->assertSee('<li>Điểm 1</li>', false);
    }

    public function test_editor_content_accessor_converts_legacy_markdown_for_visual_editor(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $legacyMarkdown = "Khởi đầu bài viết.\n\nTham khảo [Tài liệu](https://docs.example.com).\n\n## Giới thiệu";

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $legacyMarkdown,
        ]);

        $editorHtml = $post->editor_content;

        // When loaded in editor, markdown is converted to HTML so author sees visual links and headings
        $this->assertStringContainsString('<p>Khởi đầu bài viết.</p>', $editorHtml);
        $this->assertStringContainsString('<a href="https://docs.example.com"', $editorHtml);
        $this->assertStringContainsString('<h2>Giới thiệu</h2>', $editorHtml);
    }

    public function test_saving_draft_and_submitting_for_review_preserves_content_and_status(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $html = '<p>Nội dung kiểm tra gửi duyệt với <a href="https://example.com">Link</a></p>';

        // 1. Submit directly on create -> pending
        $createResponse = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Bài viết Gửi Duyệt Trực Tiếp',
            'category_id' => $category->id,
            'content' => $html,
            'action' => 'submit',
        ]);

        $post = Post::where('title', 'Bài viết Gửi Duyệt Trực Tiếp')->first();
        $this->assertNotNull($post);
        $this->assertSame('pending', $post->status);
        $this->assertStringContainsString('https://example.com', $post->body);

        // 2. Author updates post as draft
        $updateResponse = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Bài viết Đã Cập Nhật Lại Nháp',
            'category_id' => $category->id,
            'content' => $html.'<p>Thêm đoạn mới</p>',
            'action' => 'draft',
        ]);

        $post->refresh();
        $this->assertStringContainsString('Thêm đoạn mới', $post->body);
    }
}
