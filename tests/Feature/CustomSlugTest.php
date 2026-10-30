<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CustomSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_create_post_with_custom_slug(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $postData = [
            'title' => 'Hướng dẫn xây dựng website thương mại điện tử',
            'slug' => 'huong-dan-xay-dung-website-thuong-mai-dien-tu',
            'category_id' => $category->id,
            'excerpt' => 'Mô tả bài viết',
            'content' => '<p>Nội dung bài viết chi tiết.</p>',
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('huong-dan-xay-dung-website-thuong-mai-dien-tu', $post->slug);
        $response->assertRedirect(route('posts.edit', $post));
    }

    public function test_empty_slug_is_automatically_generated_from_title(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $postData = [
            'title' => 'Khám phá Trí tuệ Nhân tạo & Cloud Computing 2026',
            'slug' => '',
            'category_id' => $category->id,
            'excerpt' => 'Mô tả tóm tắt',
            'content' => '<p>Nội dung bài viết mẫu.</p>',
        ];

        $this->actingAs($author)->post(route('posts.store'), $postData);

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame('kham-pha-tri-tue-nhan-tao-cloud-computing-2026', $post->slug);
    }

    public function test_generated_slug_stays_within_column_limit_after_a_long_title_collision(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $longTitle = str_repeat('a', 255);
        Post::factory()->create(['slug' => $longTitle]);

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => $longTitle,
            'slug' => '',
            'category_id' => $category->id,
            'content' => '<p>Nội dung bài viết.</p>',
        ]);

        $post = Post::where('user_id', $author->id)->firstOrFail();
        $response->assertRedirect(route('posts.edit', $post));
        $this->assertSame(str_repeat('a', 253).'-2', $post->slug);
    }

    public function test_generated_slug_stays_within_column_limit_when_transliteration_expands_title(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => str_repeat('@', 255),
            'slug' => '',
            'category_id' => $category->id,
            'content' => '<p>Nội dung bài viết.</p>',
        ]);

        $post = Post::where('user_id', $author->id)->firstOrFail();
        $response->assertRedirect(route('posts.edit', $post));
        $this->assertLessThanOrEqual(255, mb_strlen($post->slug));
    }

    public function test_duplicate_slug_is_rejected_on_create(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        Post::factory()->create([
            'slug' => 'duong-dan-da-ton-tai',
        ]);

        $postData = [
            'title' => 'Bài viết mới nhưng trùng slug',
            'slug' => 'duong-dan-da-ton-tai',
            'category_id' => $category->id,
            'content' => '<p>Nội dung...</p>',
        ];

        $response = $this->actingAs($author)->post(route('posts.store'), $postData);
        $response->assertSessionHasErrors(['slug']);
    }

    #[DataProvider('reservedSlugs')]
    public function test_reserved_custom_slug_is_rejected_on_create(string $slug): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Bài viết mới',
            'slug' => $slug,
            'category_id' => $category->id,
            'content' => '<p>Nội dung bài viết.</p>',
        ]);

        $response->assertSessionHasErrors([
            'slug' => 'Đường dẫn này được dành riêng cho chức năng hệ thống. Vui lòng chọn đường dẫn khác.',
        ]);
        $this->assertDatabaseCount('posts', 0);
    }

    #[DataProvider('reservedSlugs')]
    public function test_generated_slug_skips_reserved_post_paths(string $slug): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => $slug,
            'slug' => '',
            'category_id' => $category->id,
            'content' => '<p>Nội dung bài viết.</p>',
        ]);

        $post = Post::where('user_id', $author->id)->firstOrFail();
        $response->assertRedirect(route('posts.edit', $post));
        $this->assertSame($slug.'-2', $post->slug);
    }

    public function test_author_can_update_post_with_new_custom_slug(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'slug-cu-ban-dau',
        ]);

        $updateData = [
            'title' => $post->title,
            'slug' => 'slug-moi-duoc-cap-nhat',
            'category_id' => $category->id,
            'content' => '<p>Nội dung mới.</p>',
        ];

        $response = $this->actingAs($author)->put(route('posts.update', $post), $updateData);

        $response->assertRedirect(route('posts.edit', $post));
        $this->assertSame('slug-moi-duoc-cap-nhat', $post->fresh()->slug);
    }

    public function test_author_can_update_post_retaining_same_slug(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'giu-nguyen-slug-nay',
        ]);

        $updateData = [
            'title' => 'Tiêu đề đã được chỉnh sửa nhẹ',
            'slug' => 'giu-nguyen-slug-nay',
            'category_id' => $category->id,
            'content' => '<p>Nội dung mới.</p>',
        ];

        $response = $this->actingAs($author)->put(route('posts.update', $post), $updateData);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame('giu-nguyen-slug-nay', $post->fresh()->slug);
    }

    public function test_author_cannot_update_post_to_colliding_slug(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $post1 = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'bai-viet-so-mot',
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'bai-viet-so-hai',
        ]);

        $updateData = [
            'title' => 'Sửa post 2 thành trùng post 1',
            'slug' => 'bai-viet-so-mot',
            'category_id' => $category->id,
            'content' => '<p>Nội dung.</p>',
        ];

        $response = $this->actingAs($author)->put(route('posts.update', $post2), $updateData);
        $response->assertSessionHasErrors(['slug']);
    }

    public function test_author_cannot_update_post_to_reserved_slug(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'permalink-hien-tai',
        ]);

        $response = $this->actingAs($author)->put(route('posts.update', $post), [
            'title' => $post->title,
            'slug' => 'check-slug',
            'category_id' => $category->id,
            'content' => '<p>Nội dung bài viết.</p>',
        ]);

        $response->assertSessionHasErrors(['slug']);
        $this->assertSame('permalink-hien-tai', $post->fresh()->slug);
    }

    public function test_empty_slug_on_update_regenerates_from_title(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Tiêu đề gốc',
            'slug' => 'tieu-de-goc',
        ]);

        $updateData = [
            'title' => 'Tiêu đề mới được cập nhật',
            'slug' => '',
            'category_id' => $category->id,
            'content' => '<p>Nội dung.</p>',
        ];

        $response = $this->actingAs($author)->put(route('posts.update', $post), $updateData);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame('tieu-de-moi-duoc-cap-nhat', $post->fresh()->slug);
    }

    public function test_check_slug_api_returns_availability(): void
    {
        $author = User::factory()->author()->create();
        Post::factory()->create([
            'user_id' => $author->id,
            'slug' => 'da-su-dung',
        ]);

        // Available slug
        $resAvailable = $this->actingAs($author)->getJson(route('posts.check-slug', ['slug' => 'chua-su-dung']));
        $resAvailable->assertOk();
        $resAvailable->assertJson(['available' => true, 'slug' => 'chua-su-dung']);

        // Taken slug
        $resTaken = $this->actingAs($author)->getJson(route('posts.check-slug', ['slug' => 'da-su-dung']));
        $resTaken->assertOk();
        $resTaken->assertJson(['available' => false, 'slug' => 'da-su-dung']);

        // Taken slug but ignored by post_id
        $post = Post::where('slug', 'da-su-dung')->first();
        $resIgnored = $this->actingAs($author)->getJson(route('posts.check-slug', [
            'slug' => 'da-su-dung',
            'post_id' => $post->id,
        ]));
        $resIgnored->assertOk();
        $resIgnored->assertJson(['available' => true]);
    }

    public function test_check_slug_api_forbids_ignoring_another_authors_post(): void
    {
        $author = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        $post = Post::factory()->create([
            'user_id' => $otherAuthor->id,
            'slug' => 'duong-dan-cua-nguoi-khac',
        ]);

        $response = $this->actingAs($author)->getJson(route('posts.check-slug', [
            'slug' => $post->slug,
            'post_id' => $post->id,
        ]));

        $response->assertForbidden();
    }

    #[DataProvider('reservedSlugs')]
    public function test_check_slug_api_rejects_reserved_post_paths(string $slug): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->getJson(route('posts.check-slug', ['slug' => $slug]));

        $response->assertOk()->assertJson([
            'valid' => false,
            'available' => false,
            'slug' => $slug,
        ]);
    }

    public function test_legacy_numeric_id_url_redirects_301_to_canonical_slug(): void
    {
        $post = Post::factory()->create([
            'status' => 'published',
            'slug' => 'bai-viet-chuan-seo',
        ]);

        // Access via numeric ID: /posts/{id}
        $response = $this->get('/posts/'.$post->id);

        $response->assertStatus(301);
        $response->assertRedirect(route('posts.show', 'bai-viet-chuan-seo'));

        // Access via canonical slug: /posts/{slug}
        $slugResponse = $this->get(route('posts.show', 'bai-viet-chuan-seo'));
        $slugResponse->assertOk();
    }

    public function test_create_and_edit_views_render_slug_controls(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'slug' => 'bai-viet-thu-nghiem-giao-dien',
        ]);

        // Check create view
        $createRes = $this->actingAs($author)->get(route('posts.create'));
        $createRes->assertOk();
        $createRes->assertSee('Đường dẫn bài viết');
        $createRes->assertSee('sidebar-slug-card');
        $createRes->assertSee('Tạo lại từ tiêu đề');
        $createRes->assertSee('id="slug"', false);
        $createRes->assertSee('id="slug-preview-url"', false);
        $createRes->assertSee('id="btn-edit-slug"', false);
        $createRes->assertSee('id="btn-copy-slug-url"', false);
        $createRes->assertSee('id="btn-regenerate-slug"', false);
        $createRes->assertSee('id="btn-save-slug"', false);
        $createRes->assertSee('id="btn-cancel-slug"', false);
        $createRes->assertSee('/posts/');

        // Check edit view
        $editRes = $this->actingAs($author)->get(route('posts.edit', $post));
        $editRes->assertOk();
        $editRes->assertSee('Đường dẫn bài viết');
        $editRes->assertSee('sidebar-slug-card');
        $editRes->assertSee('Tạo lại từ tiêu đề');
        $editRes->assertSee('bai-viet-thu-nghiem-giao-dien');
        $editRes->assertSee('id="slug"', false);
        $editRes->assertSee('id="slug-preview-url"', false);
        $editRes->assertSee('id="btn-edit-slug"', false);
        $editRes->assertSee('id="btn-copy-slug-url"', false);
        $editRes->assertSee('id="btn-regenerate-slug"', false);
        $editRes->assertSee('id="btn-save-slug"', false);
        $editRes->assertSee('id="btn-cancel-slug"', false);
        $editRes->assertSee('/posts/');
    }

    public static function reservedSlugs(): array
    {
        return [
            'create' => ['create'],
            'stats' => ['stats'],
            'check-slug' => ['check-slug'],
        ];
    }
}
