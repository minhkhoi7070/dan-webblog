<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with baseline production/real-time structure.
     */
    public function run(): void
    {
        // 1. Ensure Admin user exists
        User::firstOrCreate(
            ['email' => 'admin@blogmnm.test'],
            [
                'name' => 'Admin User',
                'username' => 'adminuser',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Baseline Categories
        $categories = [
            'Công nghệ', 'Trí tuệ nhân tạo', 'Lập trình', 'Kinh doanh',
            'Kinh tế số', 'Học tập', 'Phát triển bản thân', 'Du lịch',
            'Đời sống', 'Sách & Văn hóa',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // 3. Baseline Tags
        $this->call(TagSeeder::class);
    }
}
