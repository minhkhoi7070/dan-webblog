<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditFindingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_cannot_delete_a_video_used_by_another_author(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/videos/other-author.mp4', 'video bytes');
        $owner = User::factory()->author()->create();
        $otherAuthor = User::factory()->author()->create();
        Post::factory()->for($owner)->create([
            'body' => '<video src="/storage/posts/videos/other-author.mp4" controls></video>',
        ]);

        $response = $this->actingAs($otherAuthor)
            ->deleteJson(route('posts.delete-video'), ['filename' => 'other-author.mp4']);

        $response->assertForbidden();
        Storage::disk('public')->assertExists('posts/videos/other-author.mp4');
    }

    public function test_admin_cannot_submit_an_already_published_post_back_to_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->published()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('posts.submit', $post));

        $response->assertUnprocessable();
        $this->assertSame('published', $post->fresh()->status);
    }

    public function test_post_with_only_empty_html_content_is_rejected(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)
            ->postJson(route('posts.store'), [
                'title' => 'Empty HTML content',
                'category_id' => $category->id,
                'body' => '<p><br></p>',
                'action' => 'draft',
            ]);

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('posts', ['title' => 'Empty HTML content', 'body' => '<p><br></p>']);
    }
}
