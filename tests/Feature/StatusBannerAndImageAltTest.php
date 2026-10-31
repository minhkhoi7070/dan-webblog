<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StatusBannerAndImageAltTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // 1. STATUS BANNER TESTS
    // =========================================================================

    public function test_pending_post_displays_redesigned_status_banner(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->pending()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        $response->assertSee('post-status-banner-pending', false);
        $response->assertSee('Bài viết đang chờ duyệt');
        $response->assertSee('Bài viết đã được gửi đến Admin. Nội dung sẽ hiển thị công khai sau khi được phê duyệt.');
        $response->assertSee('Chờ duyệt');
    }

    public function test_rejected_post_displays_redesigned_status_banner_with_reason(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->rejected()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'rejection_reason' => 'Hình ảnh đại diện vi phạm bản quyền và thiếu trích dẫn nguồn.',
        ]);

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        $response->assertSee('post-status-banner-rejected', false);
        $response->assertSee('Bài viết bị từ chối / Cần chỉnh sửa');
        $response->assertSee('Hình ảnh đại diện vi phạm bản quyền và thiếu trích dẫn nguồn.');
        $response->assertSee('Bị từ chối');
    }

    public function test_draft_post_displays_draft_status_banner(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        $response->assertSee('post-status-banner-draft', false);
        $response->assertSee('Bài viết là bản nháp');
        $response->assertSee('Bản nháp');
    }

    public function test_published_post_displays_published_status_banner_with_live_link(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'bai-viet-cong-khai',
        ]);

        $response = $this->actingAs($author)->get(route('posts.edit', $post));

        $response->assertOk();
        $response->assertSee('post-status-banner-published', false);
        $response->assertSee('Bài viết đã được xuất bản');
        $response->assertSee('Xem bài viết');
        $response->assertSee(route('posts.show', 'bai-viet-cong-khai'));
    }

    // =========================================================================
    // 2. IMAGE ALT PERSISTENCE & MULTI-IMAGE TESTS
    // =========================================================================

    public function test_image_alt_attribute_is_preserved_on_save_and_reopen(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $contentWithAlt = '<p>Giới thiệu kiến trúc hệ thống:</p><p><img src="/storage/posts/images/architecture.png" alt="Sơ đồ kiến trúc Event-Driven Microservices" title="Sơ đồ kiến trúc" class="editor-content-image" /></p>';

        // Author creates post with custom image alt
        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Kiến trúc Event-Driven Microservices',
            'category_id' => $category->id,
            'excerpt' => 'Tổng quan kiến trúc phân tán',
            'content' => $contentWithAlt,
            'action' => 'draft',
        ]);

        $post = Post::first();
        $this->assertNotNull($post);
        $response->assertRedirect(route('posts.edit', $post));

        // Verify alt is persisted in database
        $this->assertStringContainsString('alt="Sơ đồ kiến trúc Event-Driven Microservices"', $post->body);

        // Verify edit page receives alt in editor content
        $editResponse = $this->actingAs($author)->get(route('posts.edit', $post));
        $editResponse->assertOk();
        $editResponse->assertSee(e('alt="Sơ đồ kiến trúc Event-Driven Microservices"'), false);

        // Verify editor modal is present in edit page
        $editResponse->assertSee('id="editor-image-modal"', false);
        $editResponse->assertSee('id="editor-image-alt"', false);
    }

    public function test_multiple_images_maintain_distinct_alt_attributes(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $multiImageHtml = '
            <p>Phần 1: Kiến trúc tổng thể</p>
            <p><img src="/storage/posts/images/diagram1.png" alt="Biểu đồ luồng dữ liệu 1" class="editor-content-image" /></p>
            <p>Phần 2: Cơ sở dữ liệu</p>
            <p><img src="/storage/posts/images/diagram2.png" alt="Sơ đồ thực thể ERD 2" class="editor-content-image" /></p>
            <p>Phần 3: Ảnh trang trí không có alt</p>
            <p><img src="/storage/posts/images/divider.png" alt="" class="editor-content-image" /></p>
        ';

        $post = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $multiImageHtml,
            'slug' => 'kien-truc-he-thong-da-tang',
        ]);

        // Render on public post page
        $renderedBody = $post->rendered_body;
        $this->assertStringContainsString('alt="Biểu đồ luồng dữ liệu 1"', $renderedBody);
        $this->assertStringContainsString('alt="Sơ đồ thực thể ERD 2"', $renderedBody);

        // Public page request
        $response = $this->get(route('posts.show', $post->slug));
        $response->assertOk();
        $response->assertSee('alt="Biểu đồ luồng dữ liệu 1"', false);
        $response->assertSee('alt="Sơ đồ thực thể ERD 2"', false);
    }

    public function test_update_post_image_alt_does_not_affect_other_images(): void
    {
        Storage::fake('public');
        UploadedFile::fake()->image('imgA.png')->storeAs('posts/images', 'imgA.png', 'public');
        UploadedFile::fake()->image('imgB.png')->storeAs('posts/images', 'imgB.png', 'public');
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $initialHtml = '
            <p><img src="/storage/posts/images/imgA.png" alt="Ảnh ban đầu A" /></p>
            <p><img src="/storage/posts/images/imgB.png" alt="Ảnh ban đầu B" /></p>
        ';

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'body' => $initialHtml,
            'status' => 'draft',
        ]);

        // Author modifies only alt of imgA
        $updatedHtml = '
            <p><img src="/storage/posts/images/imgA.png" alt="Ảnh đã cập nhật ALT mới A" /></p>
            <p><img src="/storage/posts/images/imgB.png" alt="Ảnh ban đầu B" /></p>
        ';

        $updateResponse = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'category_id' => $category->id,
            'content' => $updatedHtml,
            'action' => 'draft',
        ]);

        $updateResponse->assertRedirect(route('posts.edit', $post));
        $post->refresh();

        $this->assertStringContainsString('alt="Ảnh đã cập nhật ALT mới A"', $post->body);
        $this->assertStringContainsString('alt="Ảnh ban đầu B"', $post->body);
    }

    // =========================================================================
    // 3. DASHBOARD SEARCH BAR, FILTERS & ACTION BUTTONS TESTS
    // =========================================================================

    public function test_author_dashboard_search_by_title_case_insensitive_and_trimmed(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post1 = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Tìm hiểu Microservices với Docker',
            'status' => 'draft',
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Lập trình frontend với VueJS',
            'status' => 'draft',
        ]);

        // Search with uppercase and extra whitespace
        $response = $this->actingAs($author)->get(route('posts.stats', [
            'q' => '  MICROSERVICES  ',
        ]));

        $response->assertOk();
        $response->assertSee('Tìm hiểu Microservices với Docker');
        $response->assertDontSee('Lập trình frontend với VueJS');
    }

    public function test_author_dashboard_search_coordinates_with_status_filter(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        // Draft matching
        $draftPost = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Laravel Queues trong thực tế',
            'status' => 'draft',
        ]);

        // Published matching
        $publishedPost = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Laravel Security Best Practices',
        ]);

        // Filter by draft + q=Laravel
        $response = $this->actingAs($author)->get(route('posts.stats', [
            'status' => 'draft',
            'q' => 'Laravel',
        ]));

        $response->assertOk();
        $response->assertSee('Laravel Queues trong thực tế');
        $response->assertDontSee('Laravel Security Best Practices');
    }

    public function test_author_dashboard_displays_friendly_empty_state_on_no_search_results(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết hiện có',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($author)->get(route('posts.stats', [
            'q' => 'Từ khóa không tồn tại xyz123',
        ]));

        $response->assertOk();
        $response->assertSee('Không tìm thấy bài viết phù hợp');
        $response->assertSee('Xóa từ khóa tìm kiếm');
        $response->assertDontSee('Bài viết hiện có');
    }

    public function test_action_buttons_displayed_appropriately_by_status(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $publishedPost = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết Live',
            'slug' => 'bai-viet-live',
        ]);

        $draftPost = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết Draft',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($author)->get(route('posts.stats'));
        $response->assertOk();

        // Both posts have Edit and Delete
        $response->assertSee(route('posts.edit', $publishedPost));
        $response->assertSee(route('posts.edit', $draftPost));
        $response->assertSee('btn-action-table', false);

        // Live button only for published
        $response->assertSee(route('posts.show', 'bai-viet-live'));

        // Submit for review button for draft
        $response->assertSee(route('posts.submit', $draftPost));
    }

    // =========================================================================
    // 4. DASHBOARD & SIDEBAR UI/UX FIX VERIFICATION TESTS
    // =========================================================================

    public function test_sidebar_user_card_renders_separated_name_and_role_badge(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Lê Quốc Khánh',
        ]);

        $response = $this->actingAs($author)->get(route('posts.stats'));
        $response->assertOk();

        // 1. Author name is on its own row with 2-line clamp support
        $response->assertSee('sidebar-account__name', false);
        $response->assertSee('Lê Quốc Khánh');

        // 2. Sub-row contains @username and semantic role badge
        $response->assertSee('sidebar-account__sub-row', false);
        $response->assertSee('sidebar-account__badge--author', false);
        $response->assertSee('Tác giả');
    }

    public function test_sidebar_admin_user_card_renders_admin_badge(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Quản trị viên Hệ thống',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertOk();

        $response->assertSee('sidebar-account__name', false);
        $response->assertSee('Quản trị viên Hệ thống');
        $response->assertSee('sidebar-account__sub-row', false);
        $response->assertSee('sidebar-account__badge--admin', false);
        $response->assertSee('Admin');
    }

    public function test_dashboard_header_and_stat_cards_render_redesigned_classes(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('posts.stats'));
        $response->assertOk();

        // Header title & create button
        $response->assertSee('dashboard-title', false);
        $response->assertSee('dashboard-create-btn', false);
        $response->assertSee('Quản lý bài viết & Thống kê', false);
        $response->assertSee('Soạn bài viết mới');

        // High contrast stat description labels
        $response->assertSee('dashboard-stat-desc', false);
        $response->assertSee('Tất cả bài');
        $response->assertSee('Đang soạn');
        $response->assertSee('Đang xét duyệt');
        $response->assertSee('Công khai');
        $response->assertSee('Cần chỉnh sửa');
        $response->assertSee('Tổng tương tác');
        $response->assertSee('Từ độc giả');
        $response->assertSee('Thảo luận');
    }

    public function test_dashboard_search_input_has_dedicated_classes_and_full_placeholder(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->get(route('posts.stats'));
        $response->assertOk();

        // Search bar with ample padding and non-overlapping icon
        $response->assertSee('dashboard-search-input', false);
        $response->assertSee('dashboard-search-icon', false);
        $response->assertSee('placeholder="Tìm theo tiêu đề bài viết..."', false);
    }

    public function test_dashboard_table_renders_timestamp_and_action_button_classes(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $draft = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết Kiểm tra UI/UX',
            'status' => 'draft',
        ]);

        $published = Post::factory()->published()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Bài viết Xuất bản Hoàn tất',
            'slug' => 'bai-viet-xuat-ban-hoan-tat',
        ]);

        $response = $this->actingAs($author)->get(route('posts.stats'));
        $response->assertOk();

        // Title and timestamp
        $response->assertSee('dashboard-post-title', false);
        $response->assertSee('dashboard-post-timestamp', false);
        $response->assertSee('Cập nhật: '.$draft->updated_at->format('d/m/Y H:i'));

        // Action button modifiers
        $response->assertSee('btn-action-submit', false);
        $response->assertSee('btn-action-live', false);
        $response->assertSee('btn-action-edit', false);
        $response->assertSee('btn-action-delete', false);
    }
}
