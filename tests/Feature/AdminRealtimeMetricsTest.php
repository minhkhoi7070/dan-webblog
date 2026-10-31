<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRealtimeMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_realtime_metrics(): void
    {
        $response = $this->getJson(route('admin.metrics.realtime'));

        $response->assertUnauthorized();
    }

    public function test_viewer_or_author_cannot_access_realtime_metrics(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $author = User::factory()->author()->create();

        $this->actingAs($viewer, 'admin')->getJson(route('admin.metrics.realtime'))->assertForbidden();
        $this->actingAs($author, 'admin')->getJson(route('admin.metrics.realtime'))->assertForbidden();
    }

    public function test_admin_can_retrieve_realtime_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        Post::factory()->create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'status' => 'published',
            'views' => 15,
        ]);

        $response = $this->actingAs($admin, 'admin')->getJson(route('admin.metrics.realtime'));

        $response->assertOk()
            ->assertJsonStructure([
                'metrics' => [
                    'users',
                    'authors',
                    'viewers',
                    'posts',
                    'draft',
                    'pending',
                    'published',
                    'rejected',
                    'comments',
                    'pending_comments',
                    'spam_comments',
                    'moderation_comments',
                    'views',
                ],
                'pending_posts_count',
                'pending_comments_count',
                'timestamp',
            ])
            ->assertJsonPath('metrics.views', 15)
            ->assertJsonPath('metrics.published', 1);
    }

    public function test_clean_sample_data_command_removes_sample_records(): void
    {
        $admin = User::factory()->admin()->create(['id' => 1, 'email' => 'admin@blogmnm.test']);
        $realAuthor = User::factory()->author()->create(['email' => 'realauthor@gmail.com']);
        $demoUser = User::factory()->author()->create(['email' => 'minhanh@blogmnm.test']);
        $category = Category::factory()->create();

        Post::factory()->create([
            'user_id' => $demoUser->id,
            'category_id' => $category->id,
            'title' => 'Demo post',
            'views' => 2000,
        ]);

        Post::factory()->create([
            'user_id' => $realAuthor->id,
            'category_id' => $category->id,
            'title' => 'Real post',
            'views' => 5,
        ]);

        $this->artisan('app:clean-sample-data')
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', ['email' => 'minhanh@blogmnm.test']);
        $this->assertDatabaseMissing('posts', ['title' => 'Demo post']);
        $this->assertDatabaseHas('users', ['email' => 'realauthor@gmail.com']);
        $this->assertDatabaseHas('posts', ['title' => 'Real post']);
    }
}
