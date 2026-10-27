<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo/mock data (only use when explicitly needed for demo or load testing).
     */
    public function run(): void
    {
        // 1. Create 5 Authors
        $authors = User::factory()->author()->count(5)->create();

        // 2. Create 18 Viewers
        $viewers = User::factory()->count(18)->create();

        // 3. Ensure Categories exist
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $categories = collect([
                'Công nghệ', 'Trí tuệ nhân tạo', 'Lập trình', 'Kinh doanh',
                'Kinh tế số', 'Học tập', 'Phát triển bản thân', 'Du lịch',
                'Đời sống', 'Sách & Văn hóa',
            ])->map(fn (string $name) => Category::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
            ]));
        }

        $tags = Tag::all();

        // 4. Create Posts
        $publishedPosts = collect();

        foreach ($authors as $author) {
            $posts = Post::factory()
                ->count(4)
                ->published()
                ->create([
                    'user_id' => $author->id,
                    'category_id' => $categories->random()->id,
                ]);

            if ($tags->isNotEmpty()) {
                $posts->each(fn (Post $post) => $post->tags()->attach(
                    $tags->random(min(3, $tags->count()))->pluck('id')
                ));
            }

            $publishedPosts = $publishedPosts->merge($posts);

            Post::factory()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
                'status' => 'draft',
            ]);

            Post::factory()->pending()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]);

            Post::factory()->rejected()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]);
        }

        // 5. Create Comments
        $allUsers = $authors->merge($viewers);

        $publishedPosts->each(function (Post $post) use ($allUsers) {
            $rootComments = Comment::factory()
                ->count(rand(2, 4))
                ->create([
                    'post_id' => $post->id,
                    'user_id' => $allUsers->random()->id,
                ]);

            $rootComments->each(function (Comment $parent) use ($post, $allUsers) {
                if (rand(0, 1) === 1) {
                    Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $allUsers->random()->id,
                        'parent_id' => $parent->id,
                    ]);
                }
            });
        });

        // 6. Create Likes
        $publishedPosts->each(function (Post $post) use ($allUsers) {
            $likerIds = $allUsers->random(rand(3, min(8, $allUsers->count())))->pluck('id');
            $post->likers()->syncWithoutDetaching($likerIds);
        });

        // 7. Create Favorites
        $publishedPosts->each(function (Post $post) use ($viewers) {
            $favoriterIds = $viewers->random(rand(2, min(5, $viewers->count())))->pluck('id');
            $post->favoritedBy()->syncWithoutDetaching($favoriterIds);
        });

        // 8. Create Follows
        $viewers->each(function (User $viewer) use ($authors) {
            $followedAuthorIds = $authors->random(rand(2, min(4, $authors->count())))->pluck('id');
            $viewer->following()->syncWithoutDetaching($followedAuthorIds);
        });
    }
}
