<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarDesignStandardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sidebar_renders_design_standard_card_with_login_and_register(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        // 1. Header & Primary Nav
        $response->assertSee('sidebar-container', false);
        $response->assertSee('Trang chủ');
        $response->assertSee('Tìm kiếm');

        // 2. Auth-only routes must be hidden
        $response->assertDontSee(route('posts.create'));
        $response->assertDontSee(route('following.index'));
        $response->assertDontSee(route('favorites.index'));
        $response->assertDontSee(route('activity.index'));
        $response->assertDontSee(route('posts.stats'));
        $response->assertDontSee(route('admin.dashboard'));

        // 3. Guest Footer Card matches Author card standards
        $response->assertSee('sidebar-account-card', false);
        $response->assertSee('Khám phá CloudBlog');
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
        $response->assertSee('sidebar-theme-toggle', false);
    }

    public function test_viewer_sidebar_renders_public_and_personal_items_without_author_or_admin_menus(): void
    {
        $viewer = User::factory()->viewer()->create([
            'name' => 'Nguyễn Thu Trang',
        ]);

        $response = $this->actingAs($viewer)->get(route('home'));
        $response->assertOk();

        // 1. Common Nav
        $response->assertSee('Trang chủ');
        $response->assertSee('Tìm kiếm');
        $response->assertSee('Đang theo dõi');
        $response->assertSee('Đã lưu');
        $response->assertSee('Hoạt động');
        $response->assertSee('Cài đặt');

        // 2. Author and Admin items must be hidden
        $response->assertDontSee(route('posts.create'));
        $response->assertDontSee(route('posts.stats'));
        $response->assertDontSee(route('admin.dashboard'));
        $response->assertDontSee(route('admin.users.index'));
        $response->assertDontSee(route('admin.posts.index'));

        // 3. Footer Account Card
        $response->assertSee('sidebar-account-card', false);
        $response->assertSee('Nguyễn Thu Trang');
        $response->assertSee('sidebar-account__badge--viewer', false);
        $response->assertSee('Độc giả');
        $response->assertSee('sidebar-theme-toggle', false);
        $response->assertSee('Đăng xuất');
    }

    public function test_author_sidebar_matches_standard_with_author_group_and_badge(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Lê Quốc Khánh',
        ]);

        $response = $this->actingAs($author)->get(route('home'));
        $response->assertOk();

        // 1. Navigation items
        $response->assertSee('Trang chủ');
        $response->assertSee('Tìm kiếm');
        $response->assertSee('Viết bài');
        $response->assertSee(route('posts.create'));
        $response->assertSee('Đang theo dõi');
        $response->assertSee('Đã lưu');
        $response->assertSee('Hoạt động');

        // 2. Tác giả Group
        $response->assertSee('Tác giả');
        $response->assertSee('Thống kê bài viết');
        $response->assertSee(route('posts.stats'));
        $response->assertSee('Bình luận bài viết');
        $response->assertSee(route('author.comments.index'));
        $response->assertSee('Cài đặt');

        // 3. Admin items must be hidden
        $response->assertDontSee(route('admin.dashboard'));
        $response->assertDontSee(route('admin.users.index'));

        // 4. Footer Account Card
        $response->assertSee('sidebar-account-card', false);
        $response->assertSee('Lê Quốc Khánh');
        $response->assertSee('sidebar-account__badge--author', false);
        $response->assertSee('Tác giả');
        $response->assertSee('sidebar-theme-toggle', false);
        $response->assertSee('Đăng xuất');
    }

    public function test_admin_sidebar_matches_design_standard_with_admin_group_and_badge(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Trần Hải Nam',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        // 1. Common Nav
        $response->assertSee('Trang chủ');
        $response->assertSee('Tìm kiếm');
        $response->assertSee('Đang theo dõi');
        $response->assertSee('Đã lưu');
        $response->assertSee('Hoạt động');

        // 2. QUẢN TRỊ Group
        $response->assertSee('QUẢN TRỊ');
        $response->assertSee('Dashboard');
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee('Quản lý người dùng');
        $response->assertSee(route('admin.users.index'));
        $response->assertSee('Quản lý bài viết');
        $response->assertSee(route('admin.posts.index'));
        $response->assertSee('Quản lý danh mục');
        $response->assertSee(route('admin.categories.index'));
        $response->assertSee('Quản lý Tags');
        $response->assertSee(route('admin.tags.index'));
        $response->assertSee('Quản lý bình luận');
        $response->assertSee(route('admin.comments.index'));
        $response->assertSee('Cài đặt');

        // 3. Footer Account Card
        $response->assertSee('sidebar-account-card', false);
        $response->assertSee('Trần Hải Nam');
        $response->assertSee('sidebar-account__badge--admin', false);
        $response->assertSee('Admin');
        $response->assertSee('sidebar-theme-toggle', false);
        $response->assertSee('Đăng xuất');
    }
}
