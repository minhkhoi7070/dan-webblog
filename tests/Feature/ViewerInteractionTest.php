<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewerInteractionTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // 1. LIKE TESTS
    // ==========================================

    public function test_viewer_can_like_a_post(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('posts.like', $post->id));

        $response->assertOk()
            ->assertExactJson([
                'liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
        ]);
        $this->assertTrue($post->isLikedBy($viewer));
    }

    public function test_viewer_can_unlike_a_post(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();
        $post->likers()->attach($viewer->id);

        $response = $this->actingAs($viewer)
            ->postJson(route('posts.like', $post->id));

        $response->assertOk()
            ->assertExactJson([
                'liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
        ]);
        $this->assertFalse($post->isLikedBy($viewer));
    }

    public function test_duplicate_like_is_handled_safely(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        // Attach like directly
        $post->likers()->attach($viewer->id);

        // Call syncWithoutDetaching in app logic - verify safe handling without unique violation crash
        $post->likers()->syncWithoutDetaching([$viewer->id]);

        $this->assertEquals(1, $post->likers()->count());
    }

    public function test_guest_cannot_like_a_post(): void
    {
        $post = Post::factory()->published()->create();

        $response = $this->postJson(route('posts.like', $post->id));
        $response->assertUnauthorized();
    }

    // ==========================================
    // 2. FAVORITE TESTS
    // ==========================================

    public function test_viewer_can_favorite_a_post(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('posts.favorite', $post->id));

        $response->assertOk()
            ->assertExactJson([
                'saved' => true,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
        ]);
        $this->assertTrue($post->isSavedBy($viewer));
    }

    public function test_viewer_can_unfavorite_a_post(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();
        $post->favoritedBy()->attach($viewer->id);

        $response = $this->actingAs($viewer)
            ->postJson(route('posts.favorite', $post->id));

        $response->assertOk()
            ->assertExactJson([
                'saved' => false,
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
        ]);
        $this->assertFalse($post->isSavedBy($viewer));
    }

    public function test_viewer_can_view_favorites_page(): void
    {
        $viewer = User::factory()->create();
        $post1 = Post::factory()->published()->create(['title' => 'Favorite Post One']);
        $post2 = Post::factory()->published()->create(['title' => 'Favorite Post Two']);
        $otherPost = Post::factory()->published()->create(['title' => 'Other Regular Post']);

        $post1->favoritedBy()->attach($viewer->id);
        $post2->favoritedBy()->attach($viewer->id);

        $response = $this->actingAs($viewer)->get(route('favorites.index'));

        $response->assertOk()
            ->assertSee('Favorite Post One')
            ->assertSee('Favorite Post Two')
            ->assertDontSee('Other Regular Post');
    }

    // ==========================================
    // 3. FOLLOW TESTS
    // ==========================================

    public function test_viewer_can_follow_an_author(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->author()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('authors.follow', $author->id));

        $response->assertOk()
            ->assertExactJson([
                'following' => true,
            ]);

        $this->assertDatabaseHas('follows', [
            'follower_id' => $viewer->id,
            'following_id' => $author->id,
        ]);
        $this->assertTrue($viewer->isFollowing($author));
    }

    public function test_viewer_can_unfollow_an_author(): void
    {
        $viewer = User::factory()->create();
        $author = User::factory()->author()->create();
        $viewer->following()->attach($author->id);

        $response = $this->actingAs($viewer)
            ->postJson(route('authors.follow', $author->id));

        $response->assertOk()
            ->assertExactJson([
                'following' => false,
            ]);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $viewer->id,
            'following_id' => $author->id,
        ]);
        $this->assertFalse($viewer->isFollowing($author));
    }

    public function test_user_cannot_follow_self(): void
    {
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)
            ->postJson(route('authors.follow', $author->id));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot follow self.',
            ]);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $author->id,
            'following_id' => $author->id,
        ]);
    }

    // ==========================================
    // 4. COMMENT & REPLY TESTS
    // ==========================================

    public function test_viewer_can_post_comment_on_published_post(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'This is a genuine comment about Laravel architecture.',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.body', 'This is a genuine comment about Laravel architecture.');

        $this->assertDatabaseHas('comments', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
            'parent_id' => null,
            'body' => 'This is a genuine comment about Laravel architecture.',
        ]);
    }

    public function test_viewer_can_reply_to_existing_comment(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();
        $parentComment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $viewer->id,
            'body' => 'Root parent comment',
        ]);

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'This is an inline reply to the parent comment.',
                'parent_id' => $parentComment->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('comment.parent_id', $parentComment->id);

        $this->assertDatabaseHas('comments', [
            'user_id' => $viewer->id,
            'post_id' => $post->id,
            'parent_id' => $parentComment->id,
            'body' => 'This is an inline reply to the parent comment.',
        ]);
    }

    public function test_comment_validation_fails_for_invalid_parent_id(): void
    {
        $viewer = User::factory()->create();
        $post1 = Post::factory()->published()->create();
        $post2 = Post::factory()->published()->create();

        // Comment belongs to post 2
        $commentOnOtherPost = Comment::factory()->create([
            'post_id' => $post2->id,
            'body' => 'Comment on other post',
        ]);

        // Attempt to reply on post 1 with parent_id belonging to post 2
        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post1->id), [
                'body' => 'Malicious cross-post reply attempt.',
                'parent_id' => $commentOnOtherPost->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['parent_id']);
    }

    public function test_comment_validation_fails_for_empty_body(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($viewer)
            ->postJson(route('comments.store', $post->id), [
                'body' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    public function test_locked_user_cannot_interact(): void
    {
        $lockedUser = User::factory()->locked()->create();
        $post = Post::factory()->published()->create();

        // Like attempt
        $this->actingAs($lockedUser)
            ->postJson(route('posts.like', $post->id))
            ->assertForbidden();

        // Favorite attempt
        $this->actingAs($lockedUser)
            ->postJson(route('posts.favorite', $post->id))
            ->assertForbidden();

        // Comment attempt
        $this->actingAs($lockedUser)
            ->postJson(route('comments.store', $post->id), [
                'body' => 'Spam comment from locked user',
            ])
            ->assertForbidden();
    }

    public function test_viewer_can_view_activity_page(): void
    {
        $viewer = User::factory()->create();
        $post = Post::factory()->published()->create(['title' => 'Recent Activity Post']);
        $post->likers()->attach($viewer->id);

        $response = $this->actingAs($viewer)->get(route('activity.index'));

        $response->assertOk()
            ->assertSee('Nhật ký hoạt động của bạn')
            ->assertSee('Recent Activity Post');
    }

    public function test_viewer_can_view_profile_overview(): void
    {
        $viewer = User::factory()->create([
            'name' => 'John Viewer',
            'bio' => 'Web development enthusiast',
        ]);

        $response = $this->actingAs($viewer)->get(route('profile.show'));

        $response->assertOk()
            ->assertSee('John Viewer')
            ->assertSee('Web development enthusiast');
    }
}
