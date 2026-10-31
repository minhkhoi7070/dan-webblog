<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileResponsiveDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $author;

    protected User $viewer;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);

        $this->author = User::factory()->create([
            'role' => 'author',
            'name' => 'Author User',
            'email' => 'author@test.com',
        ]);

        $this->viewer = User::factory()->create([
            'role' => 'viewer',
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
        ]);

        $this->category = Category::factory()->create([
            'name' => 'Công nghệ',
            'slug' => 'cong-nghe',
        ]);
    }

    public function test_guest_mobile_shell_has_header_drawer_and_three_tab_bottom_nav(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('mobile-top-header', false);
        $response->assertSee('mobile-bottom-nav', false);
        $response->assertSee('id="mobileAppDrawer"', false);

        // Guest Bottom Nav should have exactly 3 items: Home, Search, Login
        $response->assertSee('Trang chủ');
        $response->assertSee('Tìm kiếm');
        $response->assertSee('Đăng nhập');
        $response->assertDontSee('mobile-bottom-nav-label">Viết bài<', false);
        $response->assertDontSee('mobile-bottom-nav-label">Quản trị<', false);
    }

    public function test_viewer_mobile_shell_has_five_tab_bottom_nav(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('home'));

        $response->assertOk();
        $response->assertSee('mobile-bottom-nav', false);
        $response->assertSee('id="mobileAppDrawer"', false);

        // Viewer Bottom Nav: Trang chủ, Tìm kiếm, Hoạt động, Đã lưu, Tài khoản
        $response->assertSee('mobile-bottom-nav-label">Trang chủ<', false);
        $response->assertSee('mobile-bottom-nav-label">Tìm kiếm<', false);
        $response->assertSee('mobile-bottom-nav-label">Hoạt động<', false);
        $response->assertSee('mobile-bottom-nav-label">Đã lưu<', false);
        $response->assertSee('mobile-bottom-nav-label">Tài khoản<', false);
        $response->assertDontSee('mobile-bottom-nav-label">Viết bài<', false);
        $response->assertDontSee('mobile-bottom-nav-label">Quản trị<', false);
    }

    public function test_author_mobile_shell_has_five_tab_bottom_nav_with_create_action(): void
    {
        $response = $this->actingAs($this->author)->get(route('home'));

        $response->assertOk();
        $response->assertSee('mobile-bottom-nav', false);

        // Author Bottom Nav: Trang chủ, Tìm kiếm, Viết bài, Đã lưu, Tài khoản
        $response->assertSee('mobile-bottom-nav-label">Trang chủ<', false);
        $response->assertSee('mobile-bottom-nav-label">Tìm kiếm<', false);
        $response->assertSee('mobile-bottom-nav-label">Viết bài<', false);
        $response->assertSee('mobile-bottom-nav-label">Đã lưu<', false);
        $response->assertSee('mobile-bottom-nav-label">Tài khoản<', false);
        $response->assertDontSee('mobile-bottom-nav-label">Quản trị<', false);
    }

    public function test_admin_mobile_shell_has_five_tab_bottom_nav_with_admin_hub(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('mobile-bottom-nav', false);

        // Admin Bottom Nav: Trang chủ, Tìm kiếm, Quản trị, Hoạt động, Tài khoản
        $response->assertSee('mobile-bottom-nav-label">Trang chủ<', false);
        $response->assertSee('mobile-bottom-nav-label">Tìm kiếm<', false);
        $response->assertSee('mobile-bottom-nav-label">Quản trị<', false);
        $response->assertSee('mobile-bottom-nav-label">Hoạt động<', false);
        $response->assertSee('mobile-bottom-nav-label">Tài khoản<', false);
    }

    public function test_author_stats_view_renders_both_desktop_table_and_mobile_cards(): void
    {
        Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Bài viết thử nghiệm Mobile',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->author)->get(route('posts.stats'));

        $response->assertOk();
        // Desktop table container: d-none d-md-block
        $response->assertSee('table-responsive d-none d-md-block', false);
        // Mobile cards container: d-block d-md-none
        $response->assertSee('d-block d-md-none divide-y divide-theme', false);
        $response->assertSee('Bài viết thử nghiệm Mobile');
    }

    public function test_admin_users_view_renders_both_desktop_table_and_mobile_cards(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('table-responsive d-none d-md-block', false);
        $response->assertSee('d-block d-md-none divide-y divide-theme', false);
    }

    public function test_subpage_mobile_header_shows_back_button_and_title(): void
    {
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'category_id' => $this->category->id,
            'title' => 'Chi tiết bài viết trải nghiệm',
            'slug' => 'chi-tiet-bai-viet-trai-nghiem',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee('id="mobile-header-back-btn"', false);
        $response->assertSee('id="mobile-header-subpage-title"', false);
        $response->assertSee('Chi tiết bài viết');
    }

    public function test_mobile_header_logo_is_constrained_and_body_has_flex_md_row(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        // Body should use flex-md-row to prevent horizontal flex layout at sm (576px-767px)
        $response->assertSee('flex-column flex-md-row', false);
        // Logo in mobile header should have explicit 32px max dimensions
        $response->assertSee('max-width: 32px', false);
        // Mobile bottom nav should use balanced nav icon structure
        $response->assertSee('mobile-bottom-nav-icon', false);
    }

    public function test_admin_control_bar_is_hidden_on_mobile_to_prevent_duplicate_headers(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        // Admin control bar must be hidden on mobile (d-none d-md-block)
        $response->assertSee('admin-control-bar user-select-none d-none d-md-block', false);
    }

    public function test_settings_page_renders_clean_without_verbose_instruction_blocks(): void
    {
        $response = $this->actingAs($this->viewer)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Giao diện hiển thị');
        $response->assertSee('Trải nghiệm đọc');
        $response->assertSee('Thông tin hồ sơ');
        // Redundant instruction texts must not be rendered
        $response->assertDontSee('Lựa chọn chế độ màu sắc phù hợp với bạn.');
        $response->assertDontSee('Đổi mật khẩu định kỳ và sử dụng chuỗi ký tự phức tạp');
        $response->assertDontSee('Mỗi trang viết chất lượng không chỉ trao gửi tri thức');
    }

    public function test_author_post_editor_renders_responsive_action_buttons(): void
    {
        $response = $this->actingAs($this->author)->get(route('posts.create'));

        $response->assertOk();
        // Both responsive variants must exist in HTML so CSS can toggle them smoothly
        $response->assertSee('Lưu bản nháp');
        $response->assertSee('Lưu nháp');
        $response->assertSee('Gửi bài viết để Admin duyệt');
        $response->assertSee('Gửi duyệt');
    }
}
