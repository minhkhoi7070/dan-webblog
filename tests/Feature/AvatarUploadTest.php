<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_valid_jpeg_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar' => null]);
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringContainsString('/storage/'.$user->avatar, $user->avatar_url);
    }

    public function test_user_can_upload_valid_png_and_webp_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // PNG
        $png = UploadedFile::fake()->image('photo.png', 150, 150);
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $png,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringEndsWith('.png', $user->avatar);

        // WebP
        $webp = UploadedFile::fake()->image('photo.webp', 150, 150);
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $webp,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
        $this->assertStringEndsWith('.webp', $user->avatar);
    }

    public function test_upload_rejects_invalid_file_types(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar' => null]);
        $textDoc = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $textDoc,
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_upload_rejects_files_larger_than_2mb(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar' => null]);
        $largeImage = UploadedFile::fake()->image('huge.jpg')->size(2049); // 2049 KB > 2048 KB limit

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $largeImage,
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_previous_custom_avatar_is_deleted_when_new_avatar_uploaded(): void
    {
        Storage::fake('public');

        // Store an initial avatar
        $oldFile = UploadedFile::fake()->image('first.jpg');
        $oldPath = $oldFile->store('avatars', 'public');
        $user = User::factory()->create(['avatar' => $oldPath]);

        Storage::disk('public')->assertExists($oldPath);

        // Upload a new avatar
        $newFile = UploadedFile::fake()->image('second.png');
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $newFile,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_default_or_external_avatar_is_not_deleted(): void
    {
        Storage::fake('public');

        // External avatar URL
        $externalUrl = 'https://example.com/avatar.jpg';
        $user = User::factory()->create(['avatar' => $externalUrl]);

        $newFile = UploadedFile::fake()->image('custom.jpg');
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $newFile,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_avatar_url_accessor_handles_null_storage_and_external_paths(): void
    {
        $userWithoutAvatar = User::factory()->make(['avatar' => null]);
        $this->assertNull($userWithoutAvatar->avatar_url);

        $userWithStorageAvatar = User::factory()->make(['avatar' => 'avatars/profile123.jpg']);
        $this->assertStringContainsString('/storage/avatars/profile123.jpg', $userWithStorageAvatar->avatar_url);

        $userWithExternalAvatar = User::factory()->make(['avatar' => 'https://cdn.example.com/photo.png']);
        $this->assertSame('https://cdn.example.com/photo.png', $userWithExternalAvatar->avatar_url);
    }

    public function test_avatar_component_handles_vietnamese_utf8_initials(): void
    {
        $authorDang = User::factory()->make(['name' => 'Đặng Thảo Vy', 'avatar' => null]);
        $html = Blade::render('<x-avatar :user="$user" size="md" />', ['user' => $authorDang]);

        $this->assertStringContainsString('Đ', $html);
        $this->assertStringContainsString('aspect-ratio: 1 / 1', $html);
        $this->assertStringContainsString('border-radius: 50%', $html);
    }

    public function test_quick_follow_badge_rendered_for_viewer_on_other_authors_posts(): void
    {
        $author = User::factory()->create(['name' => 'Quỳnh Anh']);
        $viewer = User::factory()->create(['name' => 'Người xem']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->actingAs($viewer)->view('components.post-card', ['post' => $post]);
        $view->assertSee('threads-avatar-follow-btn');
        $view->assertSee('data-action="quick-follow-open"', false);
        $view->assertSee('follow-icon-plus');
        $view->assertSee('follow-icon-check');
    }

    public function test_quick_follow_badge_not_rendered_for_author_on_own_posts(): void
    {
        $author = User::factory()->create(['name' => 'Tác giả']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->actingAs($author)->view('components.post-card', ['post' => $post]);
        $view->assertDontSee('data-action="quick-follow-open"', false);
        $view->assertDontSee('threads-avatar-follow-btn');
    }

    public function test_quick_follow_badge_links_to_login_for_guest(): void
    {
        $author = User::factory()->create(['name' => 'Quỳnh Anh']);
        $post = Post::factory()->published()->create(['user_id' => $author->id]);

        $view = $this->view('components.post-card', ['post' => $post]);
        $view->assertSee('threads-avatar-follow-btn');
        $view->assertSee('data-action="quick-follow-open"', false);
        $view->assertSee('data-guest="true"', false);
        $view->assertSee(route('login'));
    }

    public function test_quick_follow_toggle_does_not_create_duplicate_records(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->create();

        // 1. First follow request
        $res1 = $this->actingAs($viewer)->postJson(route('authors.follow', $author->id), ['following' => true]);
        $res1->assertOk()->assertJson(['following' => true]);
        $this->assertEquals(1, $viewer->following()->where('following_id', $author->id)->count());

        // 2. Idempotent follow request
        $res2 = $this->actingAs($viewer)->postJson(route('authors.follow', $author->id), ['following' => true]);
        $res2->assertOk()->assertJson(['following' => true]);
        $this->assertEquals(1, $viewer->following()->where('following_id', $author->id)->count());

        // 3. Unfollow request
        $res3 = $this->actingAs($viewer)->postJson(route('authors.follow', $author->id), ['following' => false]);
        $res3->assertOk()->assertJson(['following' => false]);
        $this->assertEquals(0, $viewer->following()->where('following_id', $author->id)->count());
    }
}
