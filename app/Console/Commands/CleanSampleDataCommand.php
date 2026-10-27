<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:clean-sample-data {--dry-run : Chỉ kiểm tra số lượng dữ liệu mẫu sẽ xóa mà không thực thi}')]
#[Description('Xóa bỏ toàn bộ dữ liệu mẫu (demo users, demo posts, fake views, fake comments) và chỉ giữ lại dữ liệu thực tế')]
class CleanSampleDataCommand extends Command
{
    /**
     * Danh sách email của tác giả demo được sinh từ seeder.
     *
     * @var array<int, string>
     */
    protected array $demoAuthorEmails = [
        'minhanh@blogmnm.test',
        'giahan@blogmnm.test',
        'quocbao@blogmnm.test',
        'ngocmai@blogmnm.test',
        'hoangnam@blogmnm.test',
        'thaovy@blogmnm.test',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info($isDryRun ? '=== KIỂM TRA DỮ LIỆU MẪU (DRY RUN) ===' : '=== BẮT ĐẦU XÓA BỎ DỮ LIỆU MẪU ===');

        // 1. Xác định tài khoản demo cần xóa
        $demoUsersQuery = User::where(function ($query) {
            $query->whereIn('email', $this->demoAuthorEmails)
                ->orWhere('email', 'like', '%@example.org')
                ->orWhere('email', 'like', '%@example.com')
                ->orWhere('email', 'like', '%@example.net');
        })->where('id', '!=', 1); // Không bao giờ xóa Admin chính (ID 1)

        $demoUserIds = $demoUsersQuery->pluck('id');
        $demoUsersCount = $demoUserIds->count();

        // 2. Xác định các bài viết demo
        $demoPostsQuery = Post::whereIn('user_id', $demoUserIds);
        $demoPostsCount = $demoPostsQuery->count();
        $demoViewsSum = (int) $demoPostsQuery->sum('views');

        // 3. Người dùng thật được giữ lại
        $preservedUsers = User::whereNotIn('id', $demoUserIds)->get(['id', 'name', 'email', 'role']);
        $preservedUserIds = $preservedUsers->pluck('id');

        // 4. Bài viết thật được giữ lại
        $preservedPosts = Post::whereIn('user_id', $preservedUserIds)->get(['id', 'title', 'views', 'status']);
        $preservedPostIds = $preservedPosts->pluck('id');

        $this->table(
            ['ID', 'Tên người dùng', 'Email', 'Vai trò', 'Trạng thái bảo tồn'],
            $preservedUsers->map(fn ($u) => [
                $u->id,
                $u->name,
                $u->email,
                $u->role,
                'ĐƯỢC GIỮ LẠI (Dữ liệu thật)',
            ])
        );

        $this->newLine();
        $this->line("• Số người dùng mẫu sẽ xóa: <comment>{$demoUsersCount}</comment>");
        $this->line("• Số bài viết mẫu sẽ xóa: <comment>{$demoPostsCount}</comment>");
        $this->line('• Số lượt xem ảo (fake views) sẽ loại bỏ: <comment>'.number_format($demoViewsSum).'</comment>');
        $this->line("• Số bài viết thật được giữ lại: <info>{$preservedPosts->count()}</info>");
        $this->line('• Số lượt xem thật (real-time views) được giữ lại: <info>'.number_format($preservedPosts->sum('views')).'</info>');

        if ($isDryRun) {
            $this->warn('Chế độ Dry Run kết thúc. Không có dữ liệu nào bị thay đổi.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($demoUserIds, $preservedPostIds, $preservedUserIds) {
            // Xóa người dùng demo (cascade sẽ tự động xóa posts, comments, likes, favorites, follows của họ)
            if ($demoUserIds->isNotEmpty()) {
                User::whereIn('id', $demoUserIds)->delete();
            }

            // Dọn dẹp bất kỳ bình luận nào không thuộc bài viết thật
            Comment::whereNotIn('post_id', $preservedPostIds)->delete();

            // Dọn dẹp likes và favorites không thuộc bài viết thật hoặc người dùng thật
            DB::table('likes')
                ->whereNotIn('post_id', $preservedPostIds)
                ->orWhereNotIn('user_id', $preservedUserIds)
                ->delete();

            DB::table('favorites')
                ->whereNotIn('post_id', $preservedPostIds)
                ->orWhereNotIn('user_id', $preservedUserIds)
                ->delete();

            // Dọn dẹp follows của người dùng demo
            DB::table('follows')
                ->whereNotIn('follower_id', $preservedUserIds)
                ->orWhereNotIn('following_id', $preservedUserIds)
                ->delete();

            // Dọn dẹp pivot post_tag không còn bài viết
            DB::table('post_tag')
                ->whereNotIn('post_id', $preservedPostIds)
                ->delete();
        });

        $this->newLine();
        $this->info('ĐÃ XÓA TOÀN BỘ DỮ LIỆU MẪU THÀNH CÔNG!');
        $this->line('Hệ thống hiện tại chỉ vận hành với 100% dữ liệu thực tế và thời gian thực.');

        $this->table(
            ['Chỉ số vận hành', 'Giá trị sau khi dọn dẹp'],
            [
                ['Tổng người dùng thật', User::count()],
                ['Tác giả thật', User::where('role', 'author')->count()],
                ['Độc giả thật', User::where('role', 'viewer')->count()],
                ['Tổng bài viết thật', Post::count()],
                ['Tổng lượt xem thật', number_format((int) Post::sum('views'))],
                ['Tổng bình luận thật', Comment::count()],
                ['Lượt thích thật', DB::table('likes')->count()],
                ['Lượt lưu yêu thích thật', DB::table('favorites')->count()],
            ]
        );

        return self::SUCCESS;
    }
}
