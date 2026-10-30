<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VideoDeletionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_delete_only_their_own_temporary_uploads(): void
    {
        Storage::fake('public');
        $firstAuthor = User::factory()->author()->create();
        $secondAuthor = User::factory()->author()->create();
        $firstFilename = $this->uploadVideoAs($firstAuthor);
        $secondFilename = $this->uploadVideoAs($secondAuthor);

        $this->actingAs($secondAuthor)->deleteJson(route('posts.delete-video'), ['filename' => $firstFilename])->assertForbidden();
        Storage::disk('public')->assertExists('posts/videos/'.$firstFilename);
        $this->actingAs($firstAuthor)->deleteJson(route('posts.delete-video'), ['filename' => $secondFilename])->assertForbidden();
        Storage::disk('public')->assertExists('posts/videos/'.$secondFilename);

        $this->deleteJson(route('posts.delete-video'), ['filename' => $firstFilename])->assertOk()->assertJsonPath('success', true);
        Storage::disk('public')->assertMissing('posts/videos/'.$firstFilename);
        $this->actingAs($secondAuthor)->deleteJson(route('posts.delete-video'), ['filename' => $secondFilename])->assertOk();
        Storage::disk('public')->assertMissing('posts/videos/'.$secondFilename);
    }

    public function test_video_referenced_by_a_post_cannot_be_deleted_even_by_its_uploader(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $filename = $this->uploadVideoAs($author);
        $post = Post::factory()->for($author)->create([
            'body' => '<video src="/storage/posts/videos/'.$filename.'" controls></video>',
        ]);

        $this->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertForbidden();

        Storage::disk('public')->assertExists('posts/videos/'.$filename);
        $this->assertModelExists($post);
    }

    public function test_untracked_public_video_cannot_be_deleted_by_filename(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/videos/public-video.mp4', 'video');
        $author = User::factory()->author()->create();

        $this->actingAs($author)->deleteJson(route('posts.delete-video'), ['filename' => 'public-video.mp4'])->assertForbidden();

        Storage::disk('public')->assertExists('posts/videos/public-video.mp4');
    }

    public function test_guest_and_viewer_cannot_delete_a_video(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $filename = $this->uploadVideoAs($author);
        $viewer = User::factory()->viewer()->create();

        $this->actingAs($viewer)->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertForbidden();
        auth()->logout();
        $this->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertUnauthorized();

        Storage::disk('public')->assertExists('posts/videos/'.$filename);
    }

    public function test_admin_does_not_gain_ownership_of_another_users_temporary_video(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $filename = $this->uploadVideoAs($author);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertForbidden();

        Storage::disk('public')->assertExists('posts/videos/'.$filename);
    }

    /** @return array<string, array{string}> */
    public static function invalidFilenames(): array
    {
        return [
            'traversal' => ['../public-video.mp4'],
            'nested path' => ['other/public-video.mp4'],
            'parent directory' => ['..'],
            'other file type' => ['public-video.php'],
        ];
    }

    #[DataProvider('invalidFilenames')]
    public function test_invalid_video_filename_is_rejected(string $filename): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        $this->actingAs($author)->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertUnprocessable();
    }

    public function test_missing_owned_video_returns_not_found_without_changing_post_data(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $filename = $this->uploadVideoAs($author);
        $post = Post::factory()->for($author)->create();
        Storage::disk('public')->delete('posts/videos/'.$filename);

        $this->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertNotFound();

        $this->assertModelExists($post);
        $this->assertSame('draft', $post->fresh()->status);
    }

    public function test_failed_storage_deletion_keeps_upload_ownership_for_a_retry(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();
        $filename = $this->uploadVideoAs($author);
        $path = 'posts/videos/'.$filename;
        $storageManager = Storage::getFacadeRoot();
        $failingDisk = Mockery::mock(FilesystemAdapter::class);
        $failingDisk->shouldReceive('exists')->once()->with($path)->andReturnTrue();
        $failingDisk->shouldReceive('delete')->once()->with($path)->andReturnFalse();
        Storage::shouldReceive('disk')->with('public')->andReturn($failingDisk);

        $response = $this->deleteJson(route('posts.delete-video'), ['filename' => $filename]);
        Storage::swap($storageManager);

        $response->assertInternalServerError();
        Storage::disk('public')->assertExists($path);
        $this->deleteJson(route('posts.delete-video'), ['filename' => $filename])->assertOk();
        Storage::disk('public')->assertMissing($path);
    }

    private function uploadVideoAs(User $author): string
    {
        $response = $this->actingAs($author)->postJson(route('posts.upload-video'), [
            'video' => UploadedFile::fake()->create('temporary.mp4', 1024, 'video/mp4'),
        ]);

        $response->assertCreated();

        return $response->json('filename');
    }
}
