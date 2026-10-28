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

    public function test_content_image_includes_editor_content_image_class_for_unified_styling(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $content = '<p>Văn bản trước ảnh</p><p><img src="/storage/posts/images/test1234.png" alt="Ảnh minh họa"></p>';

        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $content,
        ]);

        // Accessing rendered_body applies editor-content-image and loading="lazy"
        $rendered = $post->rendered_body;
        $this->assertStringContainsString('editor-content-image', $rendered);
        $this->assertStringContainsString('loading="lazy"', $rendered);
        $this->assertStringContainsString('/storage/posts/images/test1234.png', $rendered);

        // Public show page displays the sanitized image within editorial-content container
        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('editorial-content', false);
        $response->assertSee('editor-content-image', false);
        $response->assertSee('/storage/posts/images/test1234.png', false);
    }

    // =========================================================================
    // 3. FULL TOOLBAR & ADVANCED FORMATTING TESTS
    // =========================================================================

    public function test_post_editor_toolbar_contains_full_block_dropdown_and_formatting_controls(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
        ]);

        foreach ([route('posts.create'), route('posts.edit', $post)] as $url) {
            $response = $this->actingAs($author)->get($url);
            $response->assertOk();

            // Block format dropdown
            $response->assertSee('editor-block-dropdown', false);
            $response->assertSee('data-editor-block="p"', false);
            $response->assertSee('data-editor-block="h1"', false);
            $response->assertSee('data-editor-block="h2"', false);
            $response->assertSee('data-editor-block="h3"', false);
            $response->assertSee('data-editor-block="h4"', false);
            $response->assertSee('data-editor-block="h5"', false);
            $response->assertSee('data-editor-block="h6"', false);
            $response->assertSee('data-editor-block="quote"', false);

            // Character formatting actions
            $response->assertSee('data-editor-action="bold"', false);
            $response->assertSee('data-editor-action="italic"', false);
            $response->assertSee('data-editor-action="underline"', false);
            $response->assertSee('data-editor-action="strike"', false);
            $response->assertSee('data-editor-action="clear-format"', false);

            // Alignment actions
            $response->assertSee('data-editor-action="align-left"', false);
            $response->assertSee('data-editor-action="align-center"', false);
            $response->assertSee('data-editor-action="align-right"', false);
            $response->assertSee('data-editor-action="align-justify"', false);

            // Lists & Indentation group
            $response->assertSee('data-editor-action="bullet-list"', false);
            $response->assertSee('data-editor-action="ordered-list"', false);
            $response->assertSee('data-editor-action="indent"', false);
            $response->assertSee('data-editor-action="outdent"', false);

            // Text color & Highlight controls
            $response->assertSee('id="editor-color-dropdown"', false);
            $response->assertSee('id="editor-highlight-dropdown"', false);
            $response->assertSee('data-editor-color="reset"', false);
            $response->assertSee('data-editor-highlight="reset"', false);
            $response->assertSee('Xóa màu chữ', false);
            $response->assertSee('Xóa màu nền', false);

            // History
            $response->assertSee('data-editor-action="undo"', false);
            $response->assertSee('data-editor-action="redo"', false);

            // Utilities & Modals
            $response->assertSee('data-editor-action="paste-text"', false);
            $response->assertSee('data-editor-action="shortcuts-help"', false);
            $response->assertSee('id="editor-shortcuts-modal"', false);
            $response->assertSee('id="editor-link-modal"', false);
        }
    }

    public function test_post_content_preserves_all_heading_levels_h1_through_h6_and_rich_marks(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $richHtml = '<h1>Tiêu đề Cấp 1</h1>'
            .'<h2>Tiêu đề Cấp 2</h2>'
            .'<h3>Tiêu đề Cấp 3</h3>'
            .'<h4>Tiêu đề Cấp 4</h4>'
            .'<h5>Tiêu đề Cấp 5</h5>'
            .'<h6>Tiêu đề Cấp 6</h6>'
            .'<p>Đoạn văn có <strong>đậm</strong>, <em>nghiêng</em>, <u>gạch chân</u>, <s>gạch ngang</s>, <del>xóa</del>, <strike>strike</strike>, và <mark data-color="#fef08a" style="background-color: #fef08a; color: #0f172a;">tô màu nền</mark>.</p>'
            .'<p><span style="color: #ef4444">Chữ màu đỏ</span> và <span style="color: #3b82f6">chữ màu xanh</span>.</p>'
            .'<blockquote>Trích dẫn ý nghĩa</blockquote>'
            .'<hr>'
            .'<ul><li>Mục chấm 1</li><li>Mục chấm 2</li></ul>'
            .'<ol><li>Mục số 1</li><li>Mục số 2</li></ol>';

        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $richHtml,
        ]);

        $rendered = $post->rendered_body;

        // Verify all 6 headings are preserved
        $this->assertStringContainsString('<h1>Tiêu đề Cấp 1</h1>', $rendered);
        $this->assertStringContainsString('<h2>Tiêu đề Cấp 2</h2>', $rendered);
        $this->assertStringContainsString('<h3>Tiêu đề Cấp 3</h3>', $rendered);
        $this->assertStringContainsString('<h4>Tiêu đề Cấp 4</h4>', $rendered);
        $this->assertStringContainsString('<h5>Tiêu đề Cấp 5</h5>', $rendered);
        $this->assertStringContainsString('<h6>Tiêu đề Cấp 6</h6>', $rendered);

        // Verify marks
        $this->assertStringContainsString('<strong>đậm</strong>', $rendered);
        $this->assertStringContainsString('<em>nghiêng</em>', $rendered);
        $this->assertStringContainsString('<u>gạch chân</u>', $rendered);
        $this->assertStringContainsString('<s>gạch ngang</s>', $rendered);
        $this->assertStringContainsString('<del>xóa</del>', $rendered);
        $this->assertStringContainsString('<mark', $rendered);
        $this->assertStringContainsString('color: #ef4444', $rendered);
        $this->assertStringContainsString('color: #3b82f6', $rendered);
        $this->assertStringContainsString('<blockquote>Trích dẫn ý nghĩa</blockquote>', $rendered);
        $this->assertStringContainsString('<hr>', $rendered);

        // Verify public view
        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('<h1>Tiêu đề Cấp 1</h1>', false);
        $response->assertSee('<h6>Tiêu đề Cấp 6</h6>', false);
        $response->assertSee('color: #ef4444', false);
    }

    public function test_post_content_preserves_safe_css_styles_and_strips_malicious_styles(): void
    {
        $safeHtml = '<p style="text-align: center; color: #3b82f6; background-color: #fef08a; padding-left: 2rem">Văn bản căn giữa có màu và thụt lề</p>'
            .'<h2 style="text-align: right">Tiêu đề căn phải</h2>'
            .'<p style="text-align: justify">Văn bản căn đều hai bên</p>';

        $clean = PostContentService::cleanHtml($safeHtml);

        $this->assertStringContainsString('text-align: center', $clean);
        $this->assertStringContainsString('color: #3b82f6', $clean);
        $this->assertStringContainsString('background-color: #fef08a', $clean);
        $this->assertStringContainsString('padding-left: 2rem', $clean);
        $this->assertStringContainsString('text-align: right', $clean);
        $this->assertStringContainsString('text-align: justify', $clean);

        // Test stripping dangerous style injections
        $dangerousHtml = '<p style="color: red; background: url(\'javascript:alert(1)\'); expression(alert(2)); -moz-binding: url(test)">Đoạn văn độc hại</p>';
        $cleanDangerous = PostContentService::cleanHtml($dangerousHtml);

        $this->assertStringNotContainsString('javascript', $cleanDangerous);
        $this->assertStringNotContainsString('expression', $cleanDangerous);
        $this->assertStringNotContainsString('url(', $cleanDangerous);
        $this->assertStringNotContainsString('-moz-binding', $cleanDangerous);
    }

    public function test_legacy_markdown_with_h1_to_h6_and_blockquotes_converts_properly(): void
    {
        $markdown = "# Tiêu đề 1\n\n## Tiêu đề 2\n\n### Tiêu đề 3\n\n#### Tiêu đề 4\n\n##### Tiêu đề 5\n\n###### Tiêu đề 6\n\n> Trích dẫn Markdown\n\n---\n\n- Gạch đầu dòng\n1. Số thứ tự";

        $html = PostContentService::renderHtml($markdown);

        $this->assertStringContainsString('<h1>Tiêu đề 1</h1>', $html);
        $this->assertStringContainsString('<h2>Tiêu đề 2</h2>', $html);
        $this->assertStringContainsString('<h3>Tiêu đề 3</h3>', $html);
        $this->assertStringContainsString('<h4>Tiêu đề 4</h4>', $html);
        $this->assertStringContainsString('<h5>Tiêu đề 5</h5>', $html);
        $this->assertStringContainsString('<h6>Tiêu đề 6</h6>', $html);
        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringContainsString('<hr>', $html);
        $this->assertStringContainsString('<li>Gạch đầu dòng</li>', $html);
        $this->assertStringContainsString('<li>Số thứ tự</li>', $html);
    }

    public function test_author_and_admin_role_permissions_remain_intact(): void
    {
        $author1 = User::factory()->author()->create();
        $author2 = User::factory()->author()->create();
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $author1->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Author 1 can view edit page of their own post
        $response1 = $this->actingAs($author1)->get(route('posts.edit', $post));
        $response1->assertOk();

        // Author 2 cannot edit Author 1's post
        $response2 = $this->actingAs($author2)->get(route('posts.edit', $post));
        $response2->assertForbidden();

        // Admin can access admin post review page
        $adminResponse = $this->actingAs($admin)->get(route('admin.posts.show', $post));
        $adminResponse->assertOk();
    }

    public function test_nested_ordered_and_bulleted_lists_are_preserved_in_content_service_and_public_views(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $nestedListsHtml = '<ol>'
            .'<li>Mục 1 chính'
                .'<ol>'
                    .'<li>Mục con 1.1'
                        .'<ol>'
                            .'<li>Mục cháu 1.1.1</li>'
                        .'</ol>'
                    .'</li>'
                    .'<li>Mục con 1.2</li>'
                .'</ol>'
            .'</li>'
            .'<li>Mục 2 chính</li>'
        .'</ol>'
        .'<ul>'
            .'<li>Chấm 1'
                .'<ul>'
                    .'<li>Chấm con 1.1</li>'
                .'</ul'
                .'></li>'
        .'</ul>';

        $clean = PostContentService::cleanHtml($nestedListsHtml);

        // Verify structural nested HTML is preserved without corruption
        $this->assertStringContainsString('<ol>', $clean);
        $this->assertStringContainsString('<li>Mục con 1.1', $clean);
        $this->assertStringContainsString('<li>Mục cháu 1.1.1</li>', $clean);
        $this->assertStringContainsString('<ul>', $clean);
        $this->assertStringContainsString('<li>Chấm con 1.1</li>', $clean);

        // Verify through post creation and public show
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $nestedListsHtml,
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('Mục cháu 1.1.1', false);
        $response->assertSee('Chấm con 1.1', false);
    }

    public function test_text_color_and_text_highlight_are_independent_and_can_coexist_or_be_cleared_separately(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // 1. Text color and highlight coexisting
        $coexistingHtml = '<p><mark data-color="#fef08a" style="background-color: #fef08a"><span style="color: #ef4444">Chữ đỏ nền vàng</span></mark></p>';
        $cleanCoexisting = PostContentService::cleanHtml($coexistingHtml);

        $this->assertStringContainsString('data-color="#fef08a"', $cleanCoexisting);
        $this->assertStringContainsString('background-color: #fef08a', $cleanCoexisting);
        $this->assertStringContainsString('color: #ef4444', $cleanCoexisting);
        $this->assertStringContainsString('Chữ đỏ nền vàng', $cleanCoexisting);

        // 2. Removing highlight keeps text color
        $onlyColorHtml = '<p><span style="color: #3b82f6">Chữ xanh không nền</span></p>';
        $cleanOnlyColor = PostContentService::cleanHtml($onlyColorHtml);

        $this->assertStringContainsString('color: #3b82f6', $cleanOnlyColor);
        $this->assertStringNotContainsString('background-color', $cleanOnlyColor);
        $this->assertStringNotContainsString('<mark', $cleanOnlyColor);

        // 3. Removing text color keeps highlight
        $onlyHighlightHtml = '<p><mark data-color="#bbf7d0" style="background-color: #bbf7d0">Chữ nền xanh lá không màu chữ</mark></p>';
        $cleanOnlyHighlight = PostContentService::cleanHtml($onlyHighlightHtml);

        $this->assertStringContainsString('background-color: #bbf7d0', $cleanOnlyHighlight);
        $this->assertDoesNotMatchRegularExpression('/(?<!background-)color\s*:/', $cleanOnlyHighlight);

        // 4. Combined on single element style attribute
        $combinedSpanHtml = '<p><span style="color: #8b5cf6; background-color: #e9d5ff">Chữ tím nền tím</span></p>';
        $cleanCombined = PostContentService::cleanHtml($combinedSpanHtml);

        $this->assertStringContainsString('color: #8b5cf6', $cleanCombined);
        $this->assertStringContainsString('background-color: #e9d5ff', $cleanCombined);

        // 5. Test persistence and display
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $coexistingHtml,
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('color: #ef4444', false);
        $response->assertSee('background-color: #fef08a', false);
    }

    public function test_shortcuts_modal_is_independent_and_contains_complete_categorized_shortcuts(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
        ]);

        foreach ([route('posts.create'), route('posts.edit', $post)] as $url) {
            $response = $this->actingAs($author)->get($url);
            $response->assertOk();

            // 1. Verify modal exists and is a distinct sibling, NOT nested in editor-link-modal
            $content = $response->getContent();
            $linkModalPos = strpos($content, 'id="editor-link-modal"');
            $shortcutsModalPos = strpos($content, 'id="editor-shortcuts-modal"');
            $this->assertNotFalse($linkModalPos);
            $this->assertNotFalse($shortcutsModalPos);

            // Find closing tag of editor-link-modal and ensure it occurs BEFORE editor-shortcuts-modal
            $linkModalSubstr = substr($content, $linkModalPos, $shortcutsModalPos - $linkModalPos);
            $this->assertStringContainsString('</div>', $linkModalSubstr);

            // 2. Title & Close controls
            $response->assertSee('id="editorShortcutsModalLabel"', false);
            $response->assertSee('Phím tắt', false);
            $response->assertSee('data-bs-dismiss="modal"', false);

            // 3. Supported Categories
            $response->assertSee('Định dạng văn bản', false);
            $response->assertSee('Tiêu đề và đoạn văn', false);
            $response->assertSee('Danh sách & Thụt lề', false);
            $response->assertSee('Căn lề và trích dẫn', false);
            $response->assertSee('Liên kết', false);
            $response->assertSee('Lịch sử thao tác', false);
            $response->assertSee('Quy tắc gõ tắt tự động', false);

            // 4. Platform distinction (macOS vs Windows/Linux)
            $response->assertSee('macOS', false);
            $response->assertSee('Windows / Linux', false);
            $response->assertSee('⌘ + B', false);
            $response->assertSee('Ctrl + B', false);
            $response->assertSee('⌘ + K', false);
            $response->assertSee('Ctrl + K', false);
            $response->assertSee('⌘ + Shift + 8', false);
            $response->assertSee('Ctrl + Shift + 8', false);
            $response->assertSee('Tab', false);
            $response->assertSee('Shift + Tab', false);
        }
    }

    public function test_post_content_service_allows_youtube_iframes_and_wraps_in_responsive_container(): void
    {
        $rawHtml = '<p>Nội dung giới thiệu trước video</p>'
            .'<iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" width="560" height="315"></iframe>'
            .'<p>Nội dung kết luận sau video</p>';

        $clean = PostContentService::cleanHtml($rawHtml);

        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $clean);
        $this->assertStringContainsString('video-embed-wrapper', $clean);
        $this->assertStringContainsString('ratio-16x9', $clean);
        $this->assertStringContainsString('allowfullscreen="true"', $clean);
        $this->assertStringContainsString('loading="lazy"', $clean);

        // Test helper methods
        $this->assertEquals('dQw4w9WgXcQ', PostContentService::parseYoutubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
        $this->assertEquals('dQw4w9WgXcQ', PostContentService::parseYoutubeId('https://youtu.be/dQw4w9WgXcQ?si=abc'));
        $this->assertEquals('dQw4w9WgXcQ', PostContentService::parseYoutubeId('https://www.youtube.com/shorts/dQw4w9WgXcQ'));
        $this->assertEquals('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', PostContentService::buildYoutubeEmbedUrl('https://youtu.be/dQw4w9WgXcQ'));
    }

    public function test_post_content_service_strips_malicious_and_non_youtube_iframes(): void
    {
        $maliciousHtml = '<p>Thử nghiệm chèn iframe độc hại</p>'
            .'<iframe src="javascript:alert(1)"></iframe>'
            .'<iframe src="https://evil-site.com/malware.html" onload="stealCookies()"></iframe>'
            .'<iframe src="http://youtube.com.evil.com/fake"></iframe>';

        $clean = PostContentService::cleanHtml($maliciousHtml);

        $this->assertStringNotContainsString('evil-site.com', $clean);
        $this->assertStringNotContainsString('javascript:alert', $clean);
        $this->assertStringNotContainsString('stealCookies', $clean);
        $this->assertStringNotContainsString('evil.com', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
    }

    public function test_post_creation_and_update_preserves_youtube_video(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $videoEmbed = '<div class="video-embed-wrapper ratio ratio-16x9 my-3" data-youtube-video="" data-video-id="dQw4w9WgXcQ">'
            .'<iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ" allowfullscreen="true" loading="lazy"></iframe>'
            .'</div>';
        $postBody = '<p>Đoạn văn mở đầu</p>'.$videoEmbed.'<p>Đoạn văn kết thúc</p>';

        // 1. Create draft with video
        $createResponse = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Bài viết có video nhúng',
            'category_id' => $category->id,
            'body' => $postBody,
            'action' => 'draft',
        ]);

        $createResponse->assertRedirect();
        $post = Post::where('title', 'Bài viết có video nhúng')->first();
        $this->assertNotNull($post);
        $this->assertTrue($post->isDraft());
        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $post->body);
        $this->assertStringContainsString('video-embed-wrapper', $post->rendered_body);

        // 2. Open edit form and verify video is preserved in editor content
        $editFormResponse = $this->actingAs($author)->get(route('posts.edit', $post));
        $editFormResponse->assertOk();
        $editFormResponse->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false);

        // 3. Update post and submit for review
        $updateResponse = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Bài viết có video nhúng (Đã cập nhật)',
            'category_id' => $category->id,
            'body' => $postBody,
            'action' => 'submit',
        ]);

        $updateResponse->assertRedirect();
        $post->refresh();
        $this->assertTrue($post->isPending());
        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $post->body);

        // 4. Admin reviews and approves post
        $admin = User::factory()->admin()->create();
        $approveResponse = $this->actingAs($admin)->post(route('admin.posts.approve', $post));
        $approveResponse->assertRedirect();

        $post->refresh();
        $this->assertTrue($post->isPublished());
        $this->assertStringContainsString('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $post->body);

        // 5. View published post
        $showResponse = $this->get(route('posts.show', $post->slug));
        $showResponse->assertOk();
        $showResponse->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
        $showResponse->assertSee('video-embed-wrapper', false);
    }

    public function test_editor_ui_renders_video_toolbar_button_and_modal(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        foreach ([route('posts.create'), route('posts.edit', $post)] as $url) {
            $response = $this->actingAs($author)->get($url);
            $response->assertOk();

            // Verify video toolbar button
            $response->assertSee('data-editor-action="video"', false);
            $response->assertSee('title="Chèn video YouTube"', false);

            // Verify video modal
            $response->assertSee('id="editor-video-modal"', false);
            $response->assertSee('id="editor-video-url"', false);
            $response->assertSee('id="editor-video-file-input"', false);
            $response->assertSee('id="editor-video-dropzone"', false);
            $response->assertSee('id="editor-video-save-btn"', false);
            $response->assertSee('Tải video lên từ máy', false);
            $response->assertSee('Chèn video YouTube', false);
        }
    }

    // =========================================================================
    // 8. PERSONAL VIDEO UPLOAD SECURITY & FUNCTIONAL TESTS
    // =========================================================================

    public function test_author_can_upload_video_successfully(): void
    {
        Storage::fake('public');

        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('sample_walkthrough.mp4', 5120, 'video/mp4');

        $response = $this->actingAs($author)->postJson(route('posts.upload-video'), [
            'video' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'url',
                'filename',
                'original_name',
                'size',
                'type',
            ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertStringStartsWith('/storage/posts/videos/', $data['url']);
        $this->assertSame('sample_walkthrough.mp4', $data['original_name']);

        // Check file exists on public disk
        $storagePath = 'posts/videos/'.$data['filename'];
        Storage::disk('public')->assertExists($storagePath);

        // Security: Confirm filename was randomized
        $this->assertNotSame('sample_walkthrough.mp4', $data['filename']);
        $this->assertStringEndsWith('.mp4', $data['filename']);
    }

    public function test_author_can_upload_webm_video_successfully(): void
    {
        Storage::fake('public');

        $author = User::factory()->author()->create();
        $file = UploadedFile::fake()->create('screencast.webm', 4096, 'video/webm');

        $response = $this->actingAs($author)->postJson(route('posts.upload-video'), [
            'video' => $file,
        ]);

        $response->assertStatus(201);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertStringEndsWith('.webm', $data['filename']);
        Storage::disk('public')->assertExists('posts/videos/'.$data['filename']);
    }

    public function test_unauthenticated_and_unauthorized_users_cannot_upload_video(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('test.mp4', 1024, 'video/mp4');

        // Guest
        $this->postJson(route('posts.upload-video'), ['video' => $file])
            ->assertUnauthorized();

        // Viewer role
        $viewer = User::factory()->viewer()->create();
        $this->actingAs($viewer)->postJson(route('posts.upload-video'), ['video' => $file])
            ->assertForbidden();

        // Locked author
        $lockedAuthor = User::factory()->author()->create(['is_locked' => true]);
        $this->actingAs($lockedAuthor)->postJson(route('posts.upload-video'), ['video' => $file])
            ->assertForbidden();
    }

    public function test_video_upload_rejects_non_video_and_disallowed_formats(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        // Malicious executable file disguised as script
        $badScript = UploadedFile::fake()->create('exploit.php', 100, 'application/x-php');
        $this->actingAs($author)->postJson(route('posts.upload-video'), ['video' => $badScript])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video']);

        // Unsupported format (e.g., avi or mkv)
        $aviFile = UploadedFile::fake()->create('movie.avi', 2048, 'video/x-msvideo');
        $this->actingAs($author)->postJson(route('posts.upload-video'), ['video' => $aviFile])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video']);
    }

    public function test_author_can_delete_uploaded_video(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        $file = UploadedFile::fake()->create('temp_clip.mp4', 2048, 'video/mp4');
        $uploadRes = $this->actingAs($author)->postJson(route('posts.upload-video'), ['video' => $file]);
        $data = $uploadRes->json();
        $filename = $data['filename'];

        Storage::disk('public')->assertExists('posts/videos/'.$filename);

        // Delete video
        $deleteRes = $this->actingAs($author)->deleteJson(route('posts.delete-video'), [
            'filename' => $filename,
        ]);

        $deleteRes->assertOk()->assertJson(['success' => true]);
        Storage::disk('public')->assertMissing('posts/videos/'.$filename);
    }

    public function test_post_creation_and_lifecycle_preserves_html5_video(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $videoHtml = '<div class="video-embed-wrapper ratio ratio-16x9 my-3" data-html5-video="">'
            .'<video controls playsinline preload="metadata" class="rounded-3 shadow-xs w-100 h-100">'
            .'<source src="/storage/posts/videos/demo_presentation.mp4" type="video/mp4">'
            .'</video>'
            .'</div>';
        $postBody = '<p>Giới thiệu video</p>'.$videoHtml.'<p>Kết thúc bài viết</p>';

        // 1. Create draft with HTML5 video
        $createRes = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Bài viết tích hợp video cá nhân',
            'category_id' => $category->id,
            'body' => $postBody,
            'action' => 'draft',
        ]);

        $createRes->assertRedirect();
        $post = Post::where('title', 'Bài viết tích hợp video cá nhân')->first();
        $this->assertNotNull($post);
        $this->assertTrue($post->isDraft());
        $this->assertStringContainsString('/storage/posts/videos/demo_presentation.mp4', $post->body);
        $this->assertStringContainsString('video-embed-wrapper', $post->rendered_body);

        // 2. Open edit view and ensure video is preserved
        $editRes = $this->actingAs($author)->get(route('posts.edit', $post));
        $editRes->assertOk();
        $editRes->assertSee('/storage/posts/videos/demo_presentation.mp4', false);

        // 3. Update post and submit for review
        $updateRes = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => 'Bài viết tích hợp video cá nhân (Cập nhật)',
            'category_id' => $category->id,
            'body' => $postBody,
            'action' => 'submit',
        ]);

        $updateRes->assertRedirect();
        $post->refresh();
        $this->assertTrue($post->isPending());
        $this->assertStringContainsString('/storage/posts/videos/demo_presentation.mp4', $post->body);

        // 4. Admin approves post
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post(route('admin.posts.approve', $post))->assertRedirect();

        $post->refresh();
        $this->assertTrue($post->isPublished());
        $this->assertStringContainsString('/storage/posts/videos/demo_presentation.mp4', $post->body);

        // 5. Public show view renders responsive HTML5 player
        $showRes = $this->get(route('posts.show', $post->slug));
        $showRes->assertOk();
        $showRes->assertSee('/storage/posts/videos/demo_presentation.mp4', false);
        $showRes->assertSee('video-embed-wrapper', false);
    }
}



