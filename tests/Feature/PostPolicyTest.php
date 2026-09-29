<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_their_own_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->assertTrue(Gate::forUser($author)->allows('update', $post));
    }

    public function test_owner_can_delete_their_own_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->assertTrue(Gate::forUser($author)->allows('delete', $post));
    }

    public function test_admin_can_manage_all_posts(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $post));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $post));
        $this->assertTrue(Gate::forUser($admin)->allows('approve', $post));
        $this->assertTrue(Gate::forUser($admin)->allows('reject', $post));
    }

    public function test_other_author_cannot_update_or_delete_post(): void
    {
        $author1 = User::factory()->author()->create();
        $author2 = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $author1->id]);

        $this->assertFalse(Gate::forUser($author2)->allows('update', $post));
        $this->assertFalse(Gate::forUser($author2)->allows('delete', $post));
    }

    public function test_viewer_cannot_update_or_delete_post(): void
    {
        $author = User::factory()->author()->create();
        $viewer = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->assertFalse(Gate::forUser($viewer)->allows('update', $post));
        $this->assertFalse(Gate::forUser($viewer)->allows('delete', $post));
        $this->assertFalse(Gate::forUser($viewer)->allows('create', Post::class));
    }

    public function test_author_can_create_post(): void
    {
        $author = User::factory()->author()->create();

        $this->assertTrue(Gate::forUser($author)->allows('create', Post::class));
    }
}
