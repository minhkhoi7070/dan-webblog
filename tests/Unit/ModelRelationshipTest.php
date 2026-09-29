<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_posts_and_comments(): void
    {
        $user = User::factory()->author()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->posts->contains($post));
        $this->assertTrue($user->comments->contains($comment));
        $this->assertInstanceOf(User::class, $post->user);
        $this->assertInstanceOf(User::class, $comment->user);
    }

    public function test_category_and_tag_relationships(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->create(['category_id' => $category->id]);
        $post->tags()->attach($tag);

        $this->assertTrue($category->posts->contains($post));
        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertTrue($post->tags->contains($tag));
        $this->assertTrue($tag->posts->contains($post));
    }

    public function test_comment_replies_relationship(): void
    {
        $parent = Comment::factory()->create();
        $reply = Comment::factory()->create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
        ]);

        $this->assertTrue($parent->replies->contains($reply));
        $this->assertEquals($parent->id, $reply->parent->id);
    }

    public function test_likes_and_favorites_relationships(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        // Like
        $like = Like::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertInstanceOf(User::class, $like->user);
        $this->assertInstanceOf(Post::class, $like->post);
        $this->assertTrue($post->likers->contains($user));
        $this->assertTrue($user->likedPosts->contains($post));
        $this->assertTrue($post->isLikedBy($user));

        // Favorite
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertInstanceOf(User::class, $favorite->user);
        $this->assertInstanceOf(Post::class, $favorite->post);
        $this->assertTrue($post->favoritedBy->contains($user));
        $this->assertTrue($user->favoritePosts->contains($post));
        $this->assertTrue($post->isSavedBy($user));
    }

    public function test_follow_relationships(): void
    {
        $follower = User::factory()->create();
        $following = User::factory()->author()->create();

        $follow = Follow::factory()->create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->assertInstanceOf(User::class, $follow->follower);
        $this->assertInstanceOf(User::class, $follow->following);
        $this->assertTrue($follower->following->contains($following));
        $this->assertTrue($following->followers->contains($follower));
        $this->assertTrue($follower->isFollowing($following));
    }
}
