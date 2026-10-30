<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class E2ESeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('e2e')
            || DB::connection()->getDatabaseName() !== 'webblog_qa'
            || ! str_contains(storage_path(), DIRECTORY_SEPARATOR.'e2e')) {
            throw new RuntimeException('E2E seeding requires the isolated webblog_qa database and storage.');
        }

        $password = config('e2e.test_password');
        if (! is_string($password) || $password === '') {
            throw new RuntimeException('E2E test password is missing.');
        }

        $users = [];
        foreach ([
            'viewer' => ['email' => 'viewer@e2e.test', 'name' => 'QA Viewer'],
            'author_a' => ['email' => 'author-a@e2e.test', 'name' => 'QA Author A'],
            'author_b' => ['email' => 'author-b@e2e.test', 'name' => 'QA Author B'],
            'admin' => ['email' => 'admin@e2e.test', 'name' => 'QA Admin'],
        ] as $key => $attributes) {
            $role = str_starts_with($key, 'author') ? 'author' : $key;
            $users[$key] = User::query()->where('email', $attributes['email'])->first()
                ?? User::factory()->create([
                    ...$attributes,
                    'role' => $role,
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
        }

        $category = Category::query()->where('slug', 'qa-technology')->first()
            ?? Category::factory()->create(['name' => 'QA Technology', 'slug' => 'qa-technology']);
        Category::query()->firstOrCreate(['slug' => 'qa-empty'], ['name' => 'QA Empty']);
        $tag = Tag::query()->where('slug', 'qa-tag')->first()
            ?? Tag::factory()->create(['name' => 'QA Tag', 'slug' => 'qa-tag']);

        $posts = [];
        foreach (['draft', 'pending', 'published', 'rejected'] as $status) {
            $posts[$status] = Post::query()->where('slug', 'qa-'.$status.'-post')->first()
                ?? Post::factory()->for($users['author_a'])->for($category)->create([
                    'title' => 'QA '.ucfirst($status).' Post',
                    'slug' => 'qa-'.$status.'-post',
                    'body' => '<p>CloudBlog QA '.$status.' content.</p>',
                    'status' => $status,
                    'published_at' => $status === 'published' ? now() : null,
                ]);
        }

        $posts['published']->tags()->syncWithoutDetaching([$tag->id]);

        if (! Comment::query()->where('post_id', $posts['published']->id)->where('user_id', $users['viewer']->id)->exists()) {
            Comment::factory()->for($posts['published'])->for($users['viewer'])->create(['body' => 'QA seed comment']);
        }

        if (! Like::query()->where('post_id', $posts['published']->id)->where('user_id', $users['author_b']->id)->exists()) {
            Like::factory()->for($posts['published'])->for($users['author_b'])->create();
        }

        if (! Favorite::query()->where('post_id', $posts['published']->id)->where('user_id', $users['author_b']->id)->exists()) {
            Favorite::factory()->for($posts['published'])->for($users['author_b'])->create();
        }

        if (! Follow::query()->where('follower_id', $users['viewer']->id)->where('following_id', $users['author_b']->id)->exists()) {
            Follow::factory()->create([
                'follower_id' => $users['viewer']->id,
                'following_id' => $users['author_b']->id,
            ]);
        }
    }
}
