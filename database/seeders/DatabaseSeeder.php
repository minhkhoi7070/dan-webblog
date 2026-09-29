<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create 1 Admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@blogmnm.test',
        ]);

        // 2. Create 5 Authors (4-6 required)
        $authors = User::factory()->author()->count(5)->create();

        // 3. Create 18 Viewers (15-20 required)
        $viewers = User::factory()->count(18)->create();

        // 4. Create Categories
        $categories = collect([
            'Technology', 'Lifestyle', 'Travel', 'Food & Culinary',
            'Health & Fitness', 'Education', 'Entertainment', 'Business & Finance',
        ])->map(fn (string $name) => Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));

        // 5. Create Tags
        $tags = collect([
            'Laravel', 'PHP', 'JavaScript', 'CSS', 'Vue.js',
            'React', 'Database', 'API', 'Architecture', 'Security',
            'Tutorial', 'Tips', 'DevOps', 'Mobile',
        ])->map(fn (string $name) => Tag::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]));

        // 6. Create Posts with all required statuses: published, draft, pending, rejected
        $publishedPosts = collect();

        foreach ($authors as $author) {
            // Published posts (3-4 per author)
            $posts = Post::factory()
                ->count(4)
                ->published()
                ->create([
                    'user_id' => $author->id,
                    'category_id' => $categories->random()->id,
                ]);

            $posts->each(fn (Post $post) => $post->tags()->attach(
                $tags->random(rand(2, 4))->pluck('id')
            ));

            $publishedPosts = $publishedPosts->merge($posts);

            // Draft posts (1 per author)
            Post::factory()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
                'status' => 'draft',
            ]);

            // Pending posts (1 per author)
            Post::factory()->pending()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]);

            // Rejected posts (1 per author)
            Post::factory()->rejected()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]);
        }

        // 7. Create Comments (including nested replies) on published posts
        $allUsers = $authors->merge($viewers)->push($admin);

        $publishedPosts->each(function (Post $post) use ($allUsers) {
            $rootComments = Comment::factory()
                ->count(rand(2, 4))
                ->create([
                    'post_id' => $post->id,
                    'user_id' => $allUsers->random()->id,
                ]);

            // Replies
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

        // 8. Create Likes
        $publishedPosts->each(function (Post $post) use ($allUsers) {
            $likerIds = $allUsers->random(rand(3, 8))->pluck('id');
            $post->likers()->attach($likerIds);
        });

        // 9. Create Favorites
        $publishedPosts->each(function (Post $post) use ($viewers) {
            $favoriterIds = $viewers->random(rand(2, 5))->pluck('id');
            $post->favoritedBy()->attach($favoriterIds);
        });

        // 10. Create Follows
        $viewers->each(function (User $viewer) use ($authors) {
            $followedAuthorIds = $authors->random(rand(2, 4))->pluck('id');
            $viewer->following()->attach($followedAuthorIds);
        });

        $authors->each(function (User $author) use ($authors) {
            $otherAuthors = $authors->where('id', '!==', $author->id);
            $author->following()->attach($otherAuthors->random(rand(1, 2))->pluck('id'));
        });
    }
}
