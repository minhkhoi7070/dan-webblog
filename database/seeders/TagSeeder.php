<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds for the 20+ sample tags.
     */
    public function run(): void
    {
        $tags = [
            'AI' => 'ai',
            'Bảo mật' => 'bao-mat',
            'Cân bằng sống' => 'can-bang-song',
            'ChatGPT' => 'chatgpt',
            'Công nghệ' => 'cong-nghe-tag',
            'Database' => 'database',
            'Du lịch' => 'du-lich-tag',
            'Đời sống' => 'doi-song-tag',
            'Học tập' => 'hoc-tap-tag',
            'Khởi nghiệp' => 'khoi-nghiep',
            'Kinh doanh' => 'kinh-doanh-tag',
            'Kinh tế số' => 'kinh-te-so-tag',
            'Kỹ năng' => 'ky-nang',
            'Lập trình' => 'lap-trinh-tag',
            'Laravel' => 'laravel',
            'Năng suất' => 'nang-suat',
            'Phát triển bản thân' => 'phat-trien-ban-than-tag',
            'PHP' => 'php',
            'Review sách' => 'review-sach',
            'Sách hay' => 'sach-hay',
            'Sinh viên' => 'sinh-vien',
        ];

        foreach ($tags as $name => $preferredSlug) {
            $existing = Tag::whereRaw('LOWER(name) = ?', [mb_strtolower($name, 'UTF-8')])
                ->orWhere('slug', $preferredSlug)
                ->first();

            if (! $existing) {
                // Ensure slug is unique before creating
                $slug = Tag::generateUniqueSlug($preferredSlug ?: $name);

                Tag::create([
                    'name' => $name,
                    'slug' => $slug,
                ]);
            }
        }
    }
}
