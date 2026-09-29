<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateVietnameseDemoContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCategories();
            $this->seedTags();
            $this->updateAuthors();
            $this->updateViewers();
            $this->seedPosts();
            $this->refreshComments();
            $this->seedInteractionsForAuthorVy();
        });
    }

    /**
     * Seed or update categories with Vietnamese names and slug.
     */
    protected function seedCategories(): void
    {
        $categories = [
            1 => ['name' => 'Công nghệ', 'slug' => 'cong-nghe'],
            2 => ['name' => 'Trí tuệ nhân tạo', 'slug' => 'tri-tue-nhan-tao'],
            3 => ['name' => 'Lập trình', 'slug' => 'lap-trinh'],
            4 => ['name' => 'Kinh doanh', 'slug' => 'kinh-doanh'],
            5 => ['name' => 'Kinh tế số', 'slug' => 'kinh-te-so'],
            6 => ['name' => 'Học tập', 'slug' => 'hoc-tap'],
            7 => ['name' => 'Phát triển bản thân', 'slug' => 'phat-trien-ban-than'],
            8 => ['name' => 'Du lịch', 'slug' => 'du-lich'],
            9 => ['name' => 'Đời sống', 'slug' => 'doi-song'],
            10 => ['name' => 'Sách & Văn hóa', 'slug' => 'sach-van-hoa'],
        ];

        foreach ($categories as $id => $data) {
            $cat = Category::find($id);
            if ($cat) {
                $cat->update([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                ]);
            } else {
                Category::firstOrCreate(
                    ['slug' => $data['slug']],
                    ['name' => $data['name']]
                );
            }
        }
    }

    /**
     * Seed or update tags with Vietnamese and tech tags.
     */
    protected function seedTags(): void
    {
        $tags = [
            'AI' => 'ai',
            'ChatGPT' => 'chatgpt',
            'Công nghệ' => 'cong-nghe-tag',
            'Lập trình' => 'lap-trinh-tag',
            'Web' => 'web',
            'Laravel' => 'laravel',
            'PHP' => 'php',
            'Database' => 'database',
            'Bảo mật' => 'bao-mat',
            'Kinh doanh' => 'kinh-doanh-tag',
            'Khởi nghiệp' => 'khoi-nghiep',
            'Kinh tế số' => 'kinh-te-so-tag',
            'Thương mại điện tử' => 'thuong-mai-dien-tu',
            'Học tập' => 'hoc-tap-tag',
            'Tự học' => 'tu-hoc',
            'Sinh viên' => 'sinh-vien',
            'Kỹ năng' => 'ky-nang',
            'Năng suất' => 'nang-suat',
            'Phát triển bản thân' => 'phat-trien-ban-than-tag',
            'Du lịch' => 'du-lich-tag',
            'Trải nghiệm' => 'trai-nghiem',
            'Đời sống' => 'doi-song-tag',
            'Cân bằng sống' => 'can-bang-song',
            'Sách hay' => 'sach-hay',
            'Văn hóa đọc' => 'van-hoa-doc',
            'Review sách' => 'review-sach',
        ];

        foreach ($tags as $name => $slug) {
            Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }

    /**
     * Update the 6 Author accounts with Vietnamese identities.
     */
    protected function updateAuthors(): void
    {
        $authors = [
            2 => [
                'name' => 'Nguyễn Minh Anh',
                'email' => 'minhanh@blogmnm.test',
                'bio' => 'Viết về AI, công nghệ và đời sống số.',
            ],
            3 => [
                'name' => 'Trần Gia Hân',
                'email' => 'giahan@blogmnm.test',
                'bio' => 'Chia sẻ về học tập, kỹ năng và phát triển bản thân.',
            ],
            4 => [
                'name' => 'Lê Quốc Bảo',
                'email' => 'quocbao@blogmnm.test',
                'bio' => 'Lập trình viên yêu thích Web, Laravel và công nghệ mã nguồn mở.',
            ],
            5 => [
                'name' => 'Phạm Ngọc Mai',
                'email' => 'ngocmai@blogmnm.test',
                'bio' => 'Viết về du lịch, đời sống và những trải nghiệm thường ngày.',
            ],
            6 => [
                'name' => 'Võ Hoàng Nam',
                'email' => 'hoangnam@blogmnm.test',
                'bio' => 'Quan tâm đến kinh doanh, thương mại điện tử và kinh tế số.',
            ],
            26 => [
                'name' => 'Đặng Thảo Vy',
                'email' => 'thaovy@blogmnm.test',
                'bio' => 'Chia sẻ về sách, văn hóa và phong cách sống.',
            ],
        ];

        foreach ($authors as $id => $data) {
            $user = User::find($id) ?? User::where('email', $data['email'])->first();

            if ($user) {
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'bio' => $data['bio'],
                    'role' => 'author',
                    'is_locked' => false,
                    'password' => Hash::make('password'),
                ]);
            } else {
                User::create([
                    'id' => $id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'bio' => $data['bio'],
                    'role' => 'author',
                    'is_locked' => false,
                    'password' => Hash::make('password'),
                ]);
            }
        }
    }

    /**
     * Update existing Viewers' display names to Vietnamese.
     */
    protected function updateViewers(): void
    {
        $viewerNames = [
            7 => 'Vũ Minh Tuấn',
            8 => 'Đỗ Thu Trang',
            9 => 'Hoàng Anh Dũng',
            10 => 'Bùi Phương Linh',
            11 => 'Ngô Quốc Huy',
            12 => 'Dương Thùy Chi',
            13 => 'Lý Gia Bảo',
            14 => 'Mai Thanh Hằng',
            15 => 'Trịnh Công Sơn',
            16 => 'Lưu Bích Ngọc',
            17 => 'Hồ Quang Hiếu',
            18 => 'Tô Ngọc Vân',
            19 => 'Đinh Tiến Dũng',
            20 => 'Phan Nhật Minh',
            21 => 'Nguyễn Khánh Linh',
            22 => 'Trần Quang Khải',
            23 => 'Lê Hải Đăng',
            24 => 'Phạm Thùy Dương',
            25 => 'Nguyễn Việt Anh',
        ];

        foreach ($viewerNames as $id => $name) {
            $viewer = User::find($id);
            if ($viewer && $viewer->role === 'viewer') {
                $viewer->update(['name' => $name]);
            }
        }
    }

    /**
     * Seed and refresh posts content.
     */
    protected function seedPosts(): void
    {
        $categories = Category::all()->keyBy('slug');
        $tagMap = Tag::all()->keyBy('name');

        $authorsPosts = $this->getPostsDefinitions();

        foreach ($authorsPosts as $authorId => $posts) {
            $existingPosts = Post::where('user_id', $authorId)->orderBy('id')->get();

            foreach ($posts as $index => $postData) {
                $category = $categories->get($postData['category_slug']);
                if (! $category) {
                    continue;
                }

                $tagIds = collect($postData['tags'])
                    ->map(fn (string $name) => $tagMap->get($name)?->id)
                    ->filter()
                    ->values()
                    ->all();

                $attributes = [
                    'user_id' => $authorId,
                    'category_id' => $category->id,
                    'title' => $postData['title'],
                    'slug' => $postData['slug'],
                    'excerpt' => $postData['excerpt'],
                    'body' => $postData['body'],
                    'status' => $postData['status'],
                    'views' => $postData['views'] ?? 0,
                    'published_at' => $postData['published_at'] ?? null,
                    'reviewed_by' => $postData['reviewed_by'] ?? null,
                    'reviewed_at' => $postData['reviewed_at'] ?? null,
                    'rejection_reason' => $postData['rejection_reason'] ?? null,
                ];

                if ($existingPosts->has($index)) {
                    $post = $existingPosts->get($index);
                    $post->update($attributes);
                } else {
                    $post = Post::updateOrCreate(
                        ['slug' => $postData['slug']],
                        $attributes
                    );
                }

                $post->tags()->sync($tagIds);
            }
        }
    }

    /**
     * Refresh existing comments with natural Vietnamese discussions.
     */
    protected function refreshComments(): void
    {
        $rootPhrases = [
            'Bài viết rất chất lượng và có chiều sâu, cảm ơn tác giả đã chia sẻ!',
            'Mình thấy góc nhìn này rất thực tế và dễ áp dụng vào việc học tập hàng ngày.',
            'Những chia sẻ rất hữu ích cho sinh viên và người mới bắt đầu.',
            'Bài viết phân tích đúng những trăn trở của mình bấy lâu nay.',
            'Mong tác giả tiếp tục ra thêm các bài viết cùng chủ đề này nhé!',
            'Nội dung rất hay, mình đã lưu lại bài viết để đọc lại sau.',
            'Cảm ơn tác giả, cách hành văn mạch lạc và rất dễ hiểu.',
            'Quan điểm rất thú vị, mở ra thêm một hướng tiếp cận mới mẻ.',
            'Đọc xong thấy có thêm nhiều động lực để thay đổi và hoàn thiện bản thân.',
            'Một bài viết đáng đọc cho những ai quan tâm đến lĩnh vực này.',
            'Phần chia sẻ kinh nghiệm thực tế rất bổ ích, mình học hỏi được rất nhiều.',
            'Cảm ơn bạn, bài viết ngắn gọn súc tích nhưng đầy đủ ý nghĩa.',
        ];

        $replyPhrases = [
            'Cảm ơn bạn đã đọc và để lại phản hồi tích cực nhé!',
            'Hoàn toàn đồng ý với bạn, chúc bạn thực hiện thành công!',
            'Cảm ơn bạn nhiều, mình sẽ cố gắng chia sẻ thêm nhiều bài viết nữa!',
            'Đúng vậy bạn ạ, từng bước nhỏ mỗi ngày sẽ tạo nên thay đổi lớn.',
            'Rất vui vì bài viết mang lại giá trị hữu ích đối với bạn!',
            'Nếu có thắc mắc gì trong quá trình áp dụng, bạn cứ để lại bình luận nhé!',
        ];

        $comments = Comment::all();
        foreach ($comments as $comment) {
            if ($comment->parent_id) {
                $newBody = $replyPhrases[$comment->id % count($replyPhrases)];
            } else {
                $newBody = $rootPhrases[$comment->id % count($rootPhrases)];
            }

            $comment->update(['body' => $newBody]);
        }
    }

    /**
     * Ensure Author Dang Thao Vy has realistic interactions.
     */
    protected function seedInteractionsForAuthorVy(): void
    {
        $authorVy = User::find(26);
        if (! $authorVy) {
            return;
        }

        $viewers = User::where('role', 'viewer')->get();
        if ($viewers->isEmpty()) {
            return;
        }

        // 1. Follows
        if ($authorVy->followers()->count() === 0) {
            $followerIds = $viewers->random(min(4, $viewers->count()))->pluck('id');
            $authorVy->followers()->syncWithoutDetaching($followerIds);
        }

        // 2. Published posts interactions
        $publishedPosts = Post::where('user_id', 26)->where('status', 'published')->get();

        foreach ($publishedPosts as $post) {
            // Likes
            if ($post->likers()->count() === 0) {
                $likerIds = $viewers->random(min(rand(4, 7), $viewers->count()))->pluck('id');
                $post->likers()->syncWithoutDetaching($likerIds);
            }

            // Favorites
            if ($post->favoritedBy()->count() === 0) {
                $favIds = $viewers->random(min(rand(2, 4), $viewers->count()))->pluck('id');
                $post->favoritedBy()->syncWithoutDetaching($favIds);
            }

            // Comments
            if ($post->comments()->count() === 0) {
                $sampleComments = [
                    'Những tựa sách này mình cũng đã đọc qua và thấy rất tâm đắc!',
                    'Đọc sách mỗi ngày quả thực là một thói quen tuyệt vời giúp tĩnh tâm.',
                    'Bài viết phân tích rất sâu sắc về văn hóa đọc trong thời đại số.',
                ];

                foreach ($sampleComments as $cText) {
                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $viewers->random()->id,
                        'body' => $cText,
                        'status' => 'approved',
                    ]);
                }
            }
        }
    }

    /**
     * Get array of post definitions for all 6 authors.
     *
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function getPostsDefinitions(): array
    {
        $now = Carbon::now();

        return [
            // =================================================================
            // 1. NGUYỄN MINH ANH (ID: 2) - AI & Công nghệ
            // =================================================================
            2 => [
                [
                    'title' => 'AI đang thay đổi cách sinh viên học tập như thế nào?',
                    'slug' => 'ai-dang-thay-doi-cach-sinh-vien-hoc-tap-nhu-the-nao',
                    'category_slug' => 'tri-tue-nhan-tao',
                    'status' => 'published',
                    'views' => 1540,
                    'published_at' => $now->copy()->subDays(6),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(6),
                    'excerpt' => 'Khám phá cách trí tuệ nhân tạo đang định hình lại phương pháp học tập, nghiên cứu tài liệu và tư duy giải quyết vấn đề của sinh viên hiện đại.',
                    'body' => "Trí tuệ nhân tạo không còn là một khái niệm xa vời trong các phòng thí nghiệm, mà đã trở thành người bạn đồng hành quen thuộc trong giảng đường đại học. Từ việc tóm tắt tài liệu học thuật hàng trăm trang đến việc gợi ý dàn ý nghiên cứu, các công cụ AI tạo sinh như ChatGPT, Claude hay Gemini đang thay đổi căn bản cách sinh viên tiếp cận tri thức.\n\nThay vì mất nhiều giờ đồng hồ tra cứu tài liệu rời rạc, sinh viên giờ đây có thể trò chuyện với tài liệu, đặt câu hỏi phản biện và nhận về những phản hồi gần như tức thì. Điều này giúp tối ưu hóa thời gian đọc hiểu và mở ra không gian cho những thảo luận sâu sắc hơn về mặt bản chất vấn đề.\n\nTuy nhiên, sự tiện lợi này cũng đặt ra thách thức không nhỏ về tư duy độc lập. Nếu quá phụ thuộc vào câu trả lời có sẵn từ AI, người học rất dễ rơi vào bẫy thụ động, mất dần năng lực phân tích phản biện và khả năng xác thực nguồn tin gốc. Một sinh viên thông minh là người biết dùng AI như một trợ lý nghiên cứu, chứ không phải người làm thay tư duy.\n\nĐể tận dụng AI hiệu quả, hãy bắt đầu bằng việc học kỹ năng đặt câu hỏi chính xác (prompting), luôn đối chiếu chéo các kết quả do mô hình đưa ra với giáo trình chính thống, và giữ vững tinh thần hoài nghi khoa học trước mọi thông tin tổng hợp.",
                    'tags' => ['AI', 'ChatGPT', 'Sinh viên', 'Tự học'],
                ],
                [
                    'title' => '5 công cụ AI hữu ích cho sinh viên công nghệ',
                    'slug' => '5-cong-cu-ai-huu-ich-cho-sinh-vien-cong-nghe',
                    'category_slug' => 'cong-nghe',
                    'status' => 'published',
                    'views' => 2180,
                    'published_at' => $now->copy()->subDays(5),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(5),
                    'excerpt' => 'Điểm qua 5 công cụ trí tuệ nhân tạo đắc lực giúp sinh viên ngành CNTT nâng cao năng suất viết code, gỡ lỗi và nghiên cứu thuật toán.',
                    'body' => "Đối với sinh viên ngành công nghệ thông tin, việc nắm bắt và làm chủ các công cụ hỗ trợ thông minh là chìa khóa để rút ngắn khoảng cách giữa lý thuyết giảng đường và yêu cầu thực tế của doanh nghiệp. Dưới đây là 5 công cụ AI nổi bật đang được cộng đồng lập trình viên trẻ đánh giá rất cao.\n\nĐầu tiên phải kể đến GitHub Copilot, trợ lý lập trình thông minh giúp tự động hoàn thiện dòng lệnh, gợi ý cú pháp và viết unit test nhanh chóng. Tiếp theo là Claude 3.5 Sonnet, mô hình có khả năng giải thích logic thuật toán phức tạp và hỗ trợ refactor code với độ chính xác ấn tượng.\n\nBên cạnh đó, Cursor IDE mang lại trải nghiệm biên tập mã nguồn tích hợp AI mạnh mẽ, cho phép lập trình viên tương tác trực tiếp với toàn bộ codebase. Perplexity AI là lựa chọn số một cho việc tìm kiếm giải pháp kỹ thuật có kèm nguồn tài liệu dẫn chứng rõ ràng. Cuối cùng, v0 by Vercel hỗ trợ tạo giao diện UI nhanh chóng từ mô tả ngôn ngữ tự nhiên.\n\nViệc tích hợp những công cụ này vào quy trình học tập không chỉ giúp bạn hoàn thành bài tập lớn nhanh hơn, mà còn rèn luyện tư duy phối hợp nhịp nhàng giữa con người và máy móc trong kỷ nguyên số.",
                    'tags' => ['AI', 'Công nghệ', 'Lập trình', 'Sinh viên'],
                ],
                [
                    'title' => 'Những điều cần biết trước khi sử dụng AI để viết nội dung',
                    'slug' => 'nhung-dieu-can-biet-truoc-khi-su-dung-ai-de-viet-noi-dung',
                    'category_slug' => 'tri-tue-nhan-tao',
                    'status' => 'published',
                    'views' => 980,
                    'published_at' => $now->copy()->subDays(4),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(4),
                    'excerpt' => 'Hiểu rõ giới hạn, tính xác thực và đạo đức nội dung khi ứng dụng các mô hình ngôn ngữ lớn vào sáng tạo văn bản hàng ngày.',
                    'body' => "Sự bùng nổ của các mô hình ngôn ngữ lớn đã mở ra kỷ nguyên mới cho việc sáng tạo nội dung. Chỉ với vài dòng mô tả, bất kỳ ai cũng có thể tạo ra một bài blog, một bức thư chào hàng hay một bài phát biểu dài mạch lạc. Tuy nhiên, sự dễ dàng này thường đi kèm với những rủi ro tiềm ẩn mà người viết cần hết sức tỉnh táo.\n\nMột trong những nhược điểm lớn nhất của AI là hiện tượng ảo giác (hallucination). Các mô hình có thể tự tin đưa ra những số liệu, trích dẫn hoặc sự kiện hoàn toàn không có thật nhưng được hành văn vô cùng trôi chảy. Nếu người viết không kiểm chứng cẩn thận trước khi xuất bản, uy tín cá nhân và tổ chức sẽ bị ảnh hưởng nghiêm trọng.\n\nBên cạnh tính chính xác, văn phong của AI thường thiếu đi cảm xúc cá nhân, trải nghiệm thực tế và dấu ấn riêng biệt của người viết. Một bài viết chạm đến trái tim người đọc luôn đòi hỏi những góc nhìn chân thực, những câu chuyện đời thường mà máy móc không thể tự trải nghiệm.\n\nDo đó, cách tiếp cận chuẩn mực nhất là coi AI như một người cộng sự hỗ trợ lên dàn ý và tìm kiếm ý tưởng sơ khởi. Phần hồn của bài viết, từ quan điểm, giọng điệu cho đến kiểm chứng thông tin, nhất định phải do chính bạn làm chủ.",
                    'tags' => ['AI', 'Kỹ năng', 'Đời sống'],
                ],
                [
                    'title' => 'AI tạo sinh và xu hướng phát triển của ứng dụng web',
                    'slug' => 'ai-tao-sinh-va-xu-huong-phat-trien-cua-ung-dung-web',
                    'category_slug' => 'cong-nghe',
                    'status' => 'published',
                    'views' => 1840,
                    'published_at' => $now->copy()->subDays(2),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'excerpt' => 'Xu hướng tích hợp Generative AI vào các sản phẩm web hiện đại: từ cá nhân hóa trải nghiệm đến tự động hóa quy trình nghiệp vụ.',
                    'body' => "Ứng dụng web hiện đại đang bước qua giai đoạn chuyển giao mạnh mẽ khi GenAI trở thành một thành phần cốt lõi thay vì chỉ là tính năng bổ trợ. Người dùng ngày nay kỳ vọng các nền tảng web không chỉ hiển thị dữ liệu tĩnh mà phải có khả năng hiểu ngữ cảnh, phản hồi thông minh và cá nhân hóa trải nghiệm theo thời gian thực.\n\nMột xu hướng rõ nét là sự phổ biến của các giao diện đàm thoại (chat-driven UI) và trợ lý ảo nhúng sâu vào nghiệp vụ. Thay vì phải nhấp chuột qua nhiều tầng menu phức tạp, người dùng có thể ra lệnh bằng văn bản tự nhiên để lọc dữ liệu, xuất báo cáo hoặc thực hiện các tác vụ tài chính chỉ trong vài giây.\n\nỞ phía backend, các kiến trúc sư phần mềm đang dần chuyển dịch sang các mô hình tích hợp API linh hoạt, kết hợp cơ chế Retrieval-Augmented Generation (RAG) và cơ sở dữ liệu vector. Điều này cho phép hệ thống tra cứu kho tài liệu nội bộ của doanh nghiệp và cung cấp câu trả lời có tính bảo mật cao, không lo rò rỉ dữ liệu.\n\nĐối với các nhà phát triển web, việc trang bị kỹ năng làm việc với LLM API, kỹ thuật prompt engineering và hiểu biết về an toàn dữ liệu sẽ là lợi thế cạnh tranh vô cùng lớn trong thị trường tuyển dụng những năm tới.",
                    'tags' => ['AI', 'Công nghệ', 'Web', 'Lập trình'],
                ],
                [
                    'title' => 'Đạo đức AI và bài toán bản quyền dữ liệu huấn luyện',
                    'slug' => 'dao-duc-ai-va-bai-toan-ban-quyen-du-lieu-huan-luyen',
                    'category_slug' => 'tri-tue-nhan-tao',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Phân tích tranh cãi xoay quanh việc sử dụng tác phẩm nghệ thuật và bài viết của con người để huấn luyện các mô hình AI thương mại.',
                    'body' => "Bản thảo nghiên cứu về các vụ kiện bản quyền gần đây giữa các hiệp hội tác giả và các tập đoàn công nghệ lớn trên thế giới.\n\nNội dung đang tiếp tục được thu thập thêm số liệu pháp lý và góc nhìn từ các luật sư sở hữu trí tuệ tại Việt Nam.",
                    'tags' => ['AI', 'Công nghệ'],
                ],
                [
                    'title' => 'Tương lai của giao diện người dùng dưới kỷ nguyên trợ lý AI',
                    'slug' => 'tuong-lai-cua-giao-dien-nguoi-dung-duoi-ky-nguyen-tro-ly-ai',
                    'category_slug' => 'cong-nghe',
                    'status' => 'pending',
                    'views' => 12,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Liệu các nút bấm và form truyền thống có biến mất hoàn toàn khi giao diện giọng nói và ngữ cảnh AI trở nên hoàn thiện?',
                    'body' => "Bài viết đang chờ duyệt về thiết kế UI/UX thích ứng trong giai đoạn mới.\n\nCác tương tác vi mô và micro-animations sẽ chuyển mình ra sao khi giao diện người dùng trở nên tối giản và linh hoạt hơn.",
                    'tags' => ['Công nghệ', 'Web', 'AI'],
                ],
                [
                    'title' => 'Sử dụng AI để làm bài hộ trong các kỳ thi đại học',
                    'slug' => 'su-dung-ai-de-lam-bai-ho-trong-cac-ky-thi-dai-hoc',
                    'category_slug' => 'tri-tue-nhan-tao',
                    'status' => 'rejected',
                    'views' => 8,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(1),
                    'rejection_reason' => 'Vi phạm tiêu chuẩn cộng đồng về tính trung thực trong học thuật và đạo đức nghề nghiệp.',
                    'excerpt' => 'Hướng dẫn vượt qua phần mềm kiểm tra đạo văn và gian lận bài thi bằng AI.',
                    'body' => 'Nội dung bài viết hướng dẫn hành vi không trung thực trong môi trường học thuật, đã bị ban quản trị từ chối kiểm duyệt.',
                    'tags' => ['AI', 'Sinh viên'],
                ],
            ],

            // =================================================================
            // 2. TRẦN GIA HÂN (ID: 3) - Học tập & Phát triển bản thân
            // =================================================================
            3 => [
                [
                    'title' => 'Cách lập kế hoạch học tập hiệu quả trong một học kỳ',
                    'slug' => 'cach-lap-ke-hoach-hoc-tap-hieu-qua-trong-mot-hoc-ky',
                    'category_slug' => 'hoc-tap',
                    'status' => 'published',
                    'views' => 1920,
                    'published_at' => $now->copy()->subDays(7),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(7),
                    'excerpt' => 'Phương pháp phân bổ thời gian môn học, quản lý deadline và duy trì điểm số ổn định mà không bị quá tải vào mùa thi.',
                    'body' => "Bước vào mỗi học kỳ mới, sinh viên thường ấp ủ quyết tâm đạt kết quả cao với hàng loạt mục tiêu to lớn. Thế nhưng chỉ sau vài tuần, sự hào hứng ban đầu dễ dàng bị thay thế bởi sự choáng ngợp trước bài tập lớn, thuyết trình nhóm và các bài kiểm tra dồn dập. Chìa khóa để giữ vững phong độ nằm ở một bản kế hoạch thực tế và linh hoạt.\n\nTrước tiên, hãy chuyển đổi đề cương môn học (syllabus) thành một lịch trình trực quan ngay trong tuần đầu tiên. Hãy đánh dấu rõ ràng các mốc nộp bài tập lớn và tuần thi giữa kỳ vào Google Calendar hoặc Notion. Khi nhìn thấy bức tranh tổng thể, bạn sẽ không bao giờ bị rơi vào thế bị động khi các deadline trùng nhau.\n\nTiếp theo, hãy chia nhỏ các dự án lớn thành những nhiệm vụ hàng tuần có thể hoàn thành trong 1 đến 2 giờ. Việc chia nhỏ này giúp giảm bớt sức ỳ tâm lý và tạo cảm giác tiến bộ liên tục. Thay vì để dành cả bài tiểu luận 15 trang đến đêm trước ngày nộp, mỗi ngày viết một trang sẽ nhẹ nhàng hơn rất nhiều.\n\nCuối cùng, đừng quên dành ra những khoảng nghỉ cố định trong tuần để tái tạo năng lượng. Kế hoạch học tập tốt nhất không phải là kế hoạch ép bạn ngồi vào bàn học 14 tiếng mỗi ngày, mà là bản kế hoạch giúp bạn cân bằng hài hòa giữa việc học, sức khỏe thể chất và đời sống tinh thần.",
                    'tags' => ['Học tập', 'Kỹ năng', 'Sinh viên', 'Năng suất'],
                ],
                [
                    'title' => 'Làm thế nào để duy trì thói quen tự học bền bỉ?',
                    'slug' => 'lam-the-nao-de-duy-tri-thoi-quen-tu-hoc-ben-bi',
                    'category_slug' => 'phat-trien-ban-than',
                    'status' => 'published',
                    'views' => 2450,
                    'published_at' => $now->copy()->subDays(5),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(5),
                    'excerpt' => 'Vượt qua động lực nhất thời để xây dựng kỷ luật tự giác và biến việc tiếp thu kiến thức mới thành niềm vui mỗi ngày.',
                    'body' => "Hầu hết chúng ta đều từng trải qua cảm giác hừng hực khí thế mua một khóa học trực tuyến hay một chồng sách chuyên ngành, nhưng rồi bỏ dở chỉ sau vài buổi đầu. Động lực chỉ là mồi lửa châm ngòi, còn thứ giúp chúng ta đi được đường dài chính là hệ thống thói quen và kỷ luật tự giác.\n\nĐể việc tự học không trở thành gánh nặng, hãy áp dụng nguyên lý hành động vi mô (micro-habits). Thay vì đặt mục tiêu học 2 tiếng mỗi ngày – một con số rất dễ gây nản lòng khi bận rộn – hãy cam kết chỉ dành 15 đến 20 phút mỗi ngày vào một khung giờ cố định. Tính đều đặn quan trọng hơn rất nhiều so với thời lượng ngắt quãng.\n\nMột yếu tố quan trọng khác là tạo dựng môi trường thuận lợi. Hãy dọn dẹp bàn học ngăn nắp, tắt thông báo điện thoại hoặc sử dụng các ứng dụng chặn mạng xã hội trong lúc học. Khi giảm thiểu tối đa các tác nhân gây xao nhãng, não bộ sẽ dễ dàng bước vào trạng thái tập trung sâu (deep work).\n\nHãy nhớ rằng tự học là một cuộc chạy marathon chứ không phải chạy nước rút. Đừng tự trách mình nếu có một ngày lỡ nhịp; điều quan trọng nhất là quay lại bàn học vào ngày hôm sau với tâm thế sẵn sàng tiếp tục.",
                    'tags' => ['Tự học', 'Phát triển bản thân', 'Kỹ năng'],
                ],
                [
                    'title' => '5 phương pháp ghi chú phù hợp với sinh viên đại học',
                    'slug' => '5-phuong-phap-ghi-chu-phu-hop-voi-sinh-vien-dai-hoc',
                    'category_slug' => 'hoc-tap',
                    'status' => 'published',
                    'views' => 1670,
                    'published_at' => $now->copy()->subDays(3),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(3),
                    'excerpt' => 'So sánh và hướng dẫn ứng dụng các hệ thống ghi chú Cornell, Mindmap, Outline, Boxing và Zettelkasten trong học tập.',
                    'body' => "Ghi chép bài giảng trên giảng đường đại học không đơn thuần là chép lại từng lời thầy cô nói, mà là quá trình xử lý, chọn lọc và tái cấu trúc thông tin vào bộ nhớ dài hạn. Lựa chọn đúng phương pháp ghi chú phù hợp với môn học sẽ giúp bạn tiết kiệm hàng chục giờ ôn tập trước kỳ thi.\n\nPhương pháp Cornell là giải pháp kinh điển cho các môn lý luận và khoa học xã hội. Bằng cách chia trang giấy thành cột gợi ý, cột ghi chép và phần tóm tắt ở cuối trang, bạn có thể biến trang vở thành tài liệu tự kiểm tra kiến thức vô cùng tiện lợi.\n\nĐối với các môn kỹ thuật hoặc cần liên kết nhiều khái niệm, sơ đồ tư duy (Mindmap) và phương pháp đóng hộp (Boxing method) mang lại hiệu quả thị giác vượt trội. Chúng giúp bạn nhanh chóng nhận diện mối liên hệ nhân quả và cấu trúc phân tầng của các chủ đề phức tạp.\n\nDù bạn chọn ghi chép bằng sổ tay truyền thống hay các công cụ số như Obsidian, Notion, hãy ghi nhớ nguyên tắc vàng: luôn diễn đạt lại ý niệm bằng chính ngôn từ của bạn thay vì sao chép nguyên văn.",
                    'tags' => ['Học tập', 'Kỹ năng', 'Sinh viên'],
                ],
                [
                    'title' => 'Học lập trình từ đâu khi bạn chưa có nền tảng?',
                    'slug' => 'hoc-lap-trinh-tu-dau-khi-ban-chua-co-nen-tang',
                    'category_slug' => 'phat-trien-ban-than',
                    'status' => 'published',
                    'views' => 3100,
                    'published_at' => $now->copy()->subDays(1),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(1),
                    'excerpt' => 'Lộ trình và lời khuyên thực tế dành cho người mới bắt đầu hoặc sinh viên trái ngành muốn bước chân vào thế giới lập trình.',
                    'body' => "Nhìn vào những dòng mã nguồn dày đặc trên màn hình đen, rất nhiều người mới bắt đầu thường cảm thấy hoang mang và tự hỏi liệu mình có đủ thông minh để học lập trình hay không. Sự thật là lập trình không đòi hỏi bạn phải là thiên tài toán học, mà cần tư duy giải quyết vấn đề có phương pháp và tính kiên nhẫn.\n\nBước đầu tiên không phải là vội vã học ngay một ngôn ngữ phức tạp, mà là làm quen với tư duy máy tính (computational thinking). Hãy hiểu cách máy tính xử lý điều kiện, vòng lặp và lưu trữ dữ liệu thông qua những bài toán đơn giản đời thường. Python hoặc JavaScript là hai ngôn ngữ khởi đầu tuyệt vời nhờ cú pháp thân thiện và cộng đồng hỗ trợ khổng lồ.\n\nThay vì chỉ ngồi xem video bài giảng một cách thụ động, hãy bắt tay vào gõ từng dòng lệnh ngay từ ngày đầu. Xây dựng các dự án nhỏ như một chiếc máy tính bỏ túi, một trang web cá nhân hay một ứng dụng quản lý chi tiêu sẽ giúp bạn hiểu sâu sắc lý thuyết hơn bất kỳ giáo trình nào.\n\nĐừng ngại gặp lỗi (bugs). Mỗi lần chương trình báo lỗi đỏ rực là một cơ hội để bạn rèn luyện kỹ năng tra cứu tài liệu và tư duy phản biện – những kỹ năng làm nên một kỹ sư phần mềm thực thụ.",
                    'tags' => ['Tự học', 'Kỹ năng', 'Lập trình'],
                ],
                [
                    'title' => 'Xây dựng kỷ luật tự thân khi học tập trực tuyến',
                    'slug' => 'xay-dung-ky-luat-tu-than-khi-hoc-tap-truc-tuyen',
                    'category_slug' => 'hoc-tap',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Những nguyên tắc giúp sinh viên duy trì sự tập trung khi học qua Zoom và các nền tảng e-learning tại nhà.',
                    'body' => "Bản nháp chia sẻ về các mẹo vượt qua cảm giác cô đơn và phân tán chú ý khi học online.\n\nPhương pháp thiết lập ranh giới rõ ràng giữa không gian nghỉ ngơi và không gian học tập trong phòng ngủ.",
                    'tags' => ['Học tập', 'Kỹ năng'],
                ],
                [
                    'title' => 'Bí quyết vượt qua hội chứng trì hoãn bài tập lớn',
                    'slug' => 'bi-quyet-vuot-qua-hoi-chung-tri-hoan-bai-tap-lon',
                    'category_slug' => 'phat-trien-ban-than',
                    'status' => 'pending',
                    'views' => 15,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Phân tích tâm lý sợ thất bại và các bước hành động cụ thể để bắt tay vào làm việc ngay lập tức.',
                    'body' => 'Bài viết đang chờ ban biên tập duyệt về kỹ thuật Pomodoro và quy tắc 5 giây giúp vượt qua sức ỳ tâm lý.',
                    'tags' => ['Phát triển bản thân', 'Năng suất'],
                ],
                [
                    'title' => 'Dịch vụ làm thuê đồ án tốt nghiệp và tiểu luận uy tín',
                    'slug' => 'dich-vu-lam-thue-do-an-tot-nghiep-va-tieu-luan-uy-tin',
                    'category_slug' => 'hoc-tap',
                    'status' => 'rejected',
                    'views' => 5,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'rejection_reason' => 'Quảng bá dịch vụ thi hộ, làm hộ bài tập trái với quy chế đào tạo và chuẩn mực học đường.',
                    'excerpt' => 'Cung cấp giải pháp nhận làm hộ bài tập và khóa luận cho sinh viên bận rộn.',
                    'body' => 'Nội dung quảng cáo dịch vụ vi phạm nghiêm trọng quy chế thi cử và chuẩn mực đạo đức sinh viên.',
                    'tags' => ['Học tập', 'Sinh viên'],
                ],
            ],

            // =================================================================
            // 3. LÊ QUỐC BẢO (ID: 4) - Lập trình & Laravel
            // =================================================================
            4 => [
                [
                    'title' => 'Laravel 12 có gì đáng chú ý trong phát triển ứng dụng web?',
                    'slug' => 'laravel-12-co-gi-dang-chu-y-trong-phat-trien-ung-dung-web',
                    'category_slug' => 'lap-trinh',
                    'status' => 'published',
                    'views' => 2890,
                    'published_at' => $now->copy()->subDays(8),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(8),
                    'excerpt' => 'Tổng hợp những cải tiến nổi bật về hiệu năng, cú pháp tinh gọn và hệ sinh thái tooling hiện đại trong phiên bản Laravel 12.',
                    'body' => "Laravel tiếp tục khẳng định vị thế là một trong những framework PHP được yêu thích nhất toàn cầu với sự ra mắt của phiên bản Laravel 12. Không chỉ tập trung vào việc tối ưu hóa tốc độ thực thi, bản phát hành này còn mang lại trải nghiệm phát triển (developer experience) mượt mà hơn bao giờ hết cho các lập trình viên web.\n\nĐiểm đáng chú ý đầu tiên là sự hoàn thiện của kiến trúc ứng dụng tinh giản. Các file cấu hình được gom gọn, việc đăng ký middleware và route provider trở nên trực quan hơn mà không làm mất đi tính linh hoạt vốn có. Điều này giúp các dự án mới khởi động nhanh chóng và giảm thiểu boilerplate code đáng kể.\n\nBên cạnh đó, hệ thống hỗ trợ concurrency và các tác vụ bất đồng bộ được nâng cấp mạnh mẽ, cho phép xử lý song song các truy vấn I/O nặng mà không làm tắc nghẽn tiến trình chính. Khả năng tương thích sâu với PHP 8.3 và 8.4 giúp tận dụng triệt để các tính năng ngôn ngữ mới nhất.\n\nĐối với các đội ngũ đang xây dựng sản phẩm trên nền tảng Laravel, việc nâng cấp lên phiên bản mới không chỉ giúp tăng cường bảo mật mà còn mở ra nhiều cơ hội ứng dụng các công nghệ hiện đại trong hệ sinh thái Laravel.",
                    'tags' => ['Laravel', 'PHP', 'Lập trình', 'Web'],
                ],
                [
                    'title' => 'Những lỗi thường gặp khi xây dựng CRUD với Laravel',
                    'slug' => 'nhung-loi-thuong-gap-khi-xay-dung-crud-voi-laravel',
                    'category_slug' => 'lap-trinh',
                    'status' => 'published',
                    'views' => 3410,
                    'published_at' => $now->copy()->subDays(6),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(6),
                    'excerpt' => 'Phân tích các sai lầm phổ biến từ vấn đề N+1 query, validation lỏng lẻo cho đến việc nhồi nhét business logic vào Controller.',
                    'body' => "Xây dựng các chức năng thêm, xem, sửa, xóa (CRUD) là bài toán vỡ lòng mà bất kỳ ai học Laravel cũng phải trải qua. Tuy nhiên, việc làm cho tính năng chạy được và viết mã nguồn theo đúng chuẩn mực sạch (clean code) là hai câu chuyện hoàn toàn khác nhau.\n\nSai lầm phổ biến nhất mà các bạn mới làm quen thường mắc phải là để xảy ra lỗi N+1 Query khi truy vấn dữ liệu quan hệ trong Blade view. Nếu không sử dụng eager loading (with()), hệ thống sẽ gửi hàng trăm câu truy vấn dư thừa về cơ sở dữ liệu, khiến trang web trở nên ì ạch khi lượng bản ghi tăng lên.\n\nMột vấn đề khác là thói quen viết logic nghiệp vụ trực tiếp trong Controller, biến controller thành các \"fat controller\" khổng lồ khó kiểm thử và bảo trì. Hãy tận dụng Form Request để xử lý xác thực dữ liệu và chuyển các tính năng phức tạp sang Service class hoặc Action pattern.\n\nCuối cùng, việc bỏ qua các chính sách phân quyền (Policies và Gates) khiến hệ thống dễ bị tấn công IDOR, cho phép người dùng sửa đổi dữ liệu của người khác chỉ bằng cách thay đổi ID trên thanh địa chỉ URL.",
                    'tags' => ['Laravel', 'PHP', 'Lập trình'],
                ],
                [
                    'title' => 'Eloquent ORM và cách tổ chức quan hệ giữa các bảng',
                    'slug' => 'eloquent-orm-va-cach-to-chuc-quan-he-giua-cac-bang',
                    'category_slug' => 'lap-trinh',
                    'status' => 'published',
                    'views' => 2150,
                    'published_at' => $now->copy()->subDays(4),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(4),
                    'excerpt' => 'Hướng dẫn thiết kế mô hình cơ sở dữ liệu chuẩn mực và sử dụng thành thạo các mối quan hệ One-to-Many, Many-to-Many trong Eloquent.',
                    'body' => "Eloquent ORM là một trong những viên ngọc quý giá nhất của Laravel, biến các thao tác với cơ sở dữ liệu quan hệ phức tạp trở nên thanh lịch và biểu cảm như đọc văn xuôi tiếng Anh. Để khai thác tối đa sức mạnh của Eloquent, việc nắm vững cách thiết kế và ánh xạ các mối quan hệ giữa các bảng là điều tiên quyết.\n\nBắt đầu với các quan hệ cơ bản như One-to-Many (hasMany, belongsTo), lập trình viên cần chú ý đến việc đặt tên khóa ngoại theo quy ước chuẩn của framework để tránh phải truyền tham số thủ công rườm rà. Khi thiết kế quan hệ Many-to-Many (belongsToMany), việc định nghĩa bảng trung gian kèm các trường timestamps là bài học kinh nghiệm không nên bỏ qua.\n\nNgoài ra, Eloquent còn cung cấp các quan hệ nâng cao như Has-Many-Through và Polymorphic Relations. Những quan hệ đa hình này đặc biệt hữu dụng khi xây dựng các tính năng dùng chung như hệ thống bình luận (Comments), lượt thích (Likes) hay gắn thẻ (Tags) áp dụng cho nhiều loại thực thể khác nhau trong cùng dự án.\n\nTận dụng các tính năng quan hệ của Eloquent một cách khéo léo sẽ giúp codebase của bạn luôn ngắn gọn, dễ hiểu và dễ dàng mở rộng khi nghiệp vụ sản phẩm phát triển trong tương lai.",
                    'tags' => ['Laravel', 'Lập trình', 'Database'],
                ],
                [
                    'title' => 'Kinh nghiệm xây dựng hệ thống phân quyền trong Laravel',
                    'slug' => 'kinh-nghiem-xay-dung-he-thong-phan-quyen-trong-laravel',
                    'category_slug' => 'lap-trinh',
                    'status' => 'published',
                    'views' => 1980,
                    'published_at' => $now->copy()->subDays(2),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'excerpt' => 'So sánh giải pháp phân quyền dựa trên vai trò (RBAC) với Laravel Gates, Policies và các package chuyên dụng như Spatie Permission.',
                    'body' => "Bảo mật và phân quyền luôn là bài toán trọng yếu trong bất kỳ hệ thống quản lý thông tin nào. Một lỗ hổng phân quyền nhỏ cũng có thể dẫn đến việc rò rỉ dữ liệu nhạy cảm hoặc tạo điều kiện cho người dùng trái phép can thiệp vào tài nguyên của hệ thống.\n\nTrong các ứng dụng quy mô vừa và nhỏ, việc sử dụng các tính năng có sẵn của Laravel như Gates và Model Policies thường là lựa chọn tối ưu nhất. Policies giúp bạn tách biệt toàn bộ luật phân quyền cho một Model cụ thể ra khỏi Controller và View, giúp code trở nên mạch lạc và dễ viết test tự động.\n\nKhi bài toán mở rộng sang các hệ thống phân quyền nhiều cấp độ với vai trò linh hoạt và hàng trăm quyền hạn chi tiết, các giải pháp như Spatie Laravel Permission sẽ phát huy tác dụng. Nó cung cấp sẵn cấu trúc bảng cơ sở dữ liệu và các hàm tiện ích kiểm tra quyền gọn gàng như \$user->can('edit-post').\n\nNguyên tắc quan trọng nhất khi làm phân quyền là luôn kiểm tra quyền ở phía server (backend) trong từng API endpoint hoặc controller action, tuyệt đối không chỉ dựa vào việc ẩn hiện nút bấm ở giao diện người dùng.",
                    'tags' => ['Laravel', 'Bảo mật', 'Lập trình'],
                ],
                [
                    'title' => 'Tối ưu hiệu năng truy vấn database với Eloquent và MySQL',
                    'slug' => 'toi-uu-hieu-nang-truy-van-database-voi-eloquent-va-mysql',
                    'category_slug' => 'lap-trinh',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Bí quyết đánh chỉ mục index thông minh và sử dụng query caching để giảm tải cho database server.',
                    'body' => "Bản thảo kỹ thuật chia sẻ về việc debug câu lệnh SQL bằng Laravel Debugbar và phân tích Explain query trong MySQL.\n\nCách lựa chọn giữa composite index và single index khi ứng dụng có nhiều tiêu chí lọc bài viết.",
                    'tags' => ['Laravel', 'Database'],
                ],
                [
                    'title' => 'Hướng dẫn tích hợp cổng thanh toán trực tuyến trong Laravel',
                    'slug' => 'huong-dan-tich-hop-cong-thanh-toan-truc-tuyen-trong-laravel',
                    'category_slug' => 'lap-trinh',
                    'status' => 'pending',
                    'views' => 20,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Quy trình kết nối webhook và xử lý giao dịch an toàn với các cổng thanh toán phổ biến tại Việt Nam.',
                    'body' => 'Hướng dẫn kỹ thuật đang chờ duyệt về bảo mật chữ ký điện tử HMAC và cơ chế IPN đảm bảo tính toàn vẹn của đơn hàng thanh toán.',
                    'tags' => ['Laravel', 'Web', 'Bảo mật'],
                ],
                [
                    'title' => 'Chia sẻ script bẻ khóa phần mềm và tải phim bản quyền',
                    'slug' => 'chia-se-script-be-khoa-phan-mem-va-tai-phim-ban-quyen',
                    'category_slug' => 'lap-trinh',
                    'status' => 'rejected',
                    'views' => 7,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(3),
                    'rejection_reason' => 'Nội dung vi phạm bản quyền phần mềm và hướng dẫn hành vi phá hoại an ninh mạng.',
                    'excerpt' => 'Hướng dẫn sử dụng các script crawler để lấy nội dung từ các website chiếu phim trả phí.',
                    'body' => 'Hướng dẫn kỹ thuật xâm nhập và bẻ khóa tài nguyên số bất hợp pháp, bị ban kiểm duyệt từ chối.',
                    'tags' => ['Lập trình', 'Bảo mật'],
                ],
            ],

            // =================================================================
            // 4. PHẠM NGỌC MAI (ID: 5) - Du lịch & Đời sống
            // =================================================================
            5 => [
                [
                    'title' => 'Một ngày khám phá những góc xanh giữa lòng thành phố',
                    'slug' => 'mot-ngay-kham-pha-nhung-goc-xanh-giua-long-thanh-pho',
                    'category_slug' => 'du-lich',
                    'status' => 'published',
                    'views' => 1420,
                    'published_at' => $now->copy()->subDays(7),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(7),
                    'excerpt' => 'Tạm rời xa nhịp sống hối hả để tìm về những khoảng không gian xanh mát, những quán cà phê ẩn mình yên bình ngay giữa đô thị.',
                    'body' => "Sống giữa một thành phố năng động với tiếng còi xe inh ỏi và những tòa nhà chọc trời san sát, đôi khi chúng ta khao khát một khoảng lặng để hít thở bầu không khí trong lành. Bạn không nhất thiết phải bắt một chuyến bay xa xôi để tìm thấy sự bình yên; ngay trong lòng đô thị vẫn có những góc xanh bình dị đang chờ được khám phá.\n\nBuổi sáng sớm bắt đầu tại một công viên rợp bóng cây cổ thụ. Dạo bước dưới những tán lá còn vương sương sớm, ngắm nhìn các cụ già tập dưỡng sinh và lắng nghe tiếng chim hót ríu rít là cách tuyệt vời nhất để đánh thức các giác quan sau những ngày dài cắm mặt vào màn hình máy tính.\n\nĐến trưa, hãy ghé thăm một quán cà phê nhỏ nép mình trong con ngõ nhỏ yên tĩnh. Với những chậu cây xanh mướt xếp quanh hiên nhà và tiếng nhạc acoustic êm dịu, đây là không gian lý tưởng để đọc vài trang sách yêu thích hoặc nhâm nhi tách trà thơm mát.\n\nHành trình khám phá góc xanh thành phố nhắc nhở chúng ta rằng: thiên nhiên và sự tĩnh lặng luôn ở rất gần, chỉ cần chúng ta chịu chậm lại một nhịp để cảm nhận và trân trọng những điều giản dị quanh mình.",
                    'tags' => ['Du lịch', 'Đời sống', 'Trải nghiệm'],
                ],
                [
                    'title' => 'Kinh nghiệm chuẩn bị hành lý cho chuyến đi ngắn ngày',
                    'slug' => 'kinh-nghiem-chuan-bi-hanh-ly-cho-chuyen-di-ngan-ngay',
                    'category_slug' => 'du-lich',
                    'status' => 'published',
                    'views' => 1780,
                    'published_at' => $now->copy()->subDays(5),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(5),
                    'excerpt' => 'Nghệ thuật sắp xếp hành lý tối giản và thông minh giúp bạn vi vu nhẹ nhàng trong các chuyến du lịch cuối tuần 2 ngày 1 đêm.',
                    'body' => "Một trong những sai lầm phổ biến nhất của các bạn trẻ khi đi du lịch là mang theo quá nhiều đồ đạc dự phòng không cần thiết. Chiếc balo nặng trĩu vai không chỉ khiến bạn nhanh mệt mỏi trong suốt chuyến đi mà còn làm giảm đi niềm vui tự do di chuyển.\n\nQuy tắc đầu tiên của việc đóng gói hành lý tối giản là chọn trang phục đa năng theo tông màu trung tính. Những món đồ dễ phối chéo sẽ giúp bạn biến hóa nhiều phong cách khác nhau mà chỉ cần mang theo 2 đến 3 bộ quần áo. Hãy cuộn tròn quần áo thay vì gấp phẳng để tiết kiệm diện tích và hạn chế nếp nhăn.\n\nVề đồ dùng cá nhân, hãy chiết các loại sữa tắm, dầu gội và mỹ phẩm vào các lọ nhỏ mini dung tích dưới 50ml. Đừng quên chuẩn bị một túi sơ cứu nhỏ với các loại thuốc thông dụng như thuốc cảm, băng gạt cá nhân và thuốc chống côn trùng đốt.\n\nHãy nhớ rằng mục đích của chuyến đi là trải nghiệm và thư giãn. Khi gánh nặng trên vai được trút bỏ, bạn sẽ có nhiều không gian và tâm trí hơn để mở lòng đón nhận vẻ đẹp của những vùng đất mới.",
                    'tags' => ['Du lịch', 'Trải nghiệm', 'Kỹ năng'],
                ],
                [
                    'title' => 'Những cách đơn giản để có một góc học tập dễ chịu',
                    'slug' => 'nhung-cach-don-gian-de-co-mot-goc-hoc-tap-de-chiu',
                    'category_slug' => 'doi-song',
                    'status' => 'published',
                    'views' => 2120,
                    'published_at' => $now->copy()->subDays(3),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(3),
                    'excerpt' => 'Biến góc bàn làm việc quen thuộc thành không gian tràn đầy cảm hứng sáng tạo với chi phí tiết kiệm và bài trí khoa học.',
                    'body' => "Không gian xung quanh có tác động trực tiếp và sâu sắc đến tâm trạng cũng như khả năng tập trung của chúng ta. Một chiếc bàn bừa bộn với giấy tờ vương vãi và dây sạc rối rắm thường vô thức tạo ra cảm giác căng thẳng, trì trệ trước khi bạn kịp bắt đầu công việc.\n\nĐể F5 lại góc học tập, bước đầu tiên luôn là dọn dẹp triệt để (declutter). Hãy cất bớt những vật dụng không dùng thường xuyên vào ngăn kéo, sắp xếp lại dây cáp bằng các kẹp giữ dây chuyên dụng và chỉ để lại trên mặt bàn những thứ thực sự cần thiết cho công việc hiện tại.\n\nÁnh sáng là yếu tố quan trọng tiếp theo. Nếu có thể, hãy kê bàn làm việc gần cửa sổ để tận dụng tối đa ánh sáng tự nhiên. Một chiếc đèn bàn có ánh sáng vàng ấm và nhiệt độ màu dịu mắt sẽ giúp bảo vệ thị lực trong những buổi học tập ban đêm.\n\nCuối cùng, hãy cá nhân hóa góc nhỏ của bạn bằng một chậu sen đá xanh xinh xắn, một khung ảnh kỷ niệm hoặc một ngọn nến thơm với mùi hương gỗ dịu nhẹ. Không gian làm việc ấm cúng sẽ biến việc ngồi vào bàn học mỗi ngày thành một niềm vui thay vì nghĩa vụ.",
                    'tags' => ['Đời sống', 'Phát triển bản thân', 'Cân bằng sống'],
                ],
                [
                    'title' => 'Cuối tuần nên làm gì để cân bằng giữa học tập và nghỉ ngơi?',
                    'slug' => 'cuoi-tuan-nen-lam-gi-de-can-bang-giua-hoc-tap-va-nghi-ngoi',
                    'category_slug' => 'doi-song',
                    'status' => 'published',
                    'views' => 1560,
                    'published_at' => $now->copy()->subDays(1),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(1),
                    'excerpt' => 'Gợi ý các hoạt động nạp lại năng lượng tinh thần hiệu quả, tránh rơi vào bẫy lướt điện thoại cả ngày trong những ngày nghỉ.',
                    'body' => "Sau một tuần làm việc và học tập căng thẳng, nhiều người trong chúng ta thường có xu hướng nằm lì trên giường và lướt mạng xã hội suốt cả ngày thứ Bảy. Thế nhưng điều kỳ lạ là sau hàng giờ nhìn vào màn hình điện thoại, bạn thường cảm thấy kiệt sức và uể oải hơn so với lúc chưa nghỉ ngơi.\n\nNghỉ ngơi thực thụ đòi hỏi sự chuyển dịch trạng thái có chủ đích. Thay vì tiêu thụ nội dung số thụ động, hãy thử tham gia các hoạt động thể chất nhẹ nhàng như đạp xe quanh bờ hồ, đi bộ dưới tán cây hoặc tham gia một buổi tập yoga giãn cơ. Vận động giúp giải phóng endorphin và xua tan mệt mỏi tích tụ trong cơ thể.\n\nDành thời gian nấu một bữa ăn ngon tại nhà cùng người thân hoặc hẹn hò cà phê trò chuyện chân thành với một vài người bạn thân cũng là phương thuốc xoa dịu tâm trí vô cùng diệu kỳ. Những kết nối xã hội ấm áp ngoài đời thực luôn có giá trị vượt trội so với những tương tác trên mạng ảo.\n\nCuối tuần là khoảng thời gian để bạn nạp đầy năng lượng cho tâm hồn. Hãy đặt điện thoại xuống, bước ra ngoài và tận hưởng từng khoảnh khắc tươi đẹp của cuộc sống thực tế.",
                    'tags' => ['Đời sống', 'Cân bằng sống', 'Sinh viên'],
                ],
                [
                    'title' => 'Cẩm nang du lịch tự túc tiết kiệm cho sinh viên',
                    'slug' => 'cam-nang-du-lich-tu-tuc-tiet-kiem-cho-sinh-vien',
                    'category_slug' => 'du-lich',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Cách săn vé xe giá rẻ, chọn homestay an toàn và thưởng thức ẩm thực địa phương trọn vẹn với ngân sách sinh viên.',
                    'body' => "Bản nháp tổng hợp kinh nghiệm phượt các tỉnh miền Trung dành cho nhóm bạn trẻ với lịch trình 3 ngày 2 đêm tối ưu chi phí.\n\nDanh sách các ứng dụng đặt phòng và mã giảm giá vận chuyển dành riêng cho học sinh sinh viên.",
                    'tags' => ['Du lịch', 'Sinh viên'],
                ],
                [
                    'title' => 'Hành trình tìm lại cảm xúc qua những bức ảnh phim',
                    'slug' => 'hanh-trinh-tim-lai-cam-xuc-qua-nhung-buc-anh-phim',
                    'category_slug' => 'doi-song',
                    'status' => 'pending',
                    'views' => 18,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Sức hút kỳ lạ của nhiếp ảnh analog và bài học về tính kiên nhẫn khi chờ đợi từng cuộn phim được tráng rửa.',
                    'body' => 'Tản văn nghệ thuật đang chờ duyệt về góc nhìn cuộc sống qua ống kính máy ảnh cơ cổ điển và vẻ đẹp của những hạt grain hoài niệm.',
                    'tags' => ['Đời sống', 'Trải nghiệm'],
                ],
                [
                    'title' => 'Kinh nghiệm trốn vé và đi chui khi tham quan các danh lam',
                    'slug' => 'kinh-nghiem-tron-ve-va-di-chui-khi-tham-quan-cac-danh-lam',
                    'category_slug' => 'du-lich',
                    'status' => 'rejected',
                    'views' => 9,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(4),
                    'rejection_reason' => 'Hướng dẫn hành vi vi phạm pháp luật và quy định tại các khu danh lam thắng cảnh công cộng.',
                    'excerpt' => 'Chia sẻ các lối đi tắt và thời điểm không có bảo vệ gác cổng để vào khu di tích miễn phí.',
                    'body' => 'Bài viết hướng dẫn các hành vi vi phạm quy định ban quản lý di tích và du lịch thiếu văn minh, không đủ điều kiện phê duyệt.',
                    'tags' => ['Du lịch'],
                ],
            ],

            // =================================================================
            // 5. VÕ HOÀNG NAM (ID: 6) - Kinh doanh & Kinh tế số
            // =================================================================
            6 => [
                [
                    'title' => 'Thương mại điện tử đang thay đổi hành vi mua sắm như thế nào?',
                    'slug' => 'thuong-mai-dien-tu-dang-thay-doi-hanh-vi-mua-sam-nhu-the-nao',
                    'category_slug' => 'kinh-te-so',
                    'status' => 'published',
                    'views' => 2310,
                    'published_at' => $now->copy()->subDays(8),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(8),
                    'excerpt' => 'Phân tích sự chuyển dịch từ mua sắm truyền thống sang thương mại xã hội (Social Commerce) và livestream bán hàng tại thị trường Việt Nam.',
                    'body' => "Trong vòng nửa thập kỷ qua, thị trường bán lẻ tại Việt Nam đã chứng kiến sự chuyển mình ngoạn mục dưới tác động của làn sóng số hóa. Từ việc người tiêu dùng ngần ngại thanh toán online vì sợ lừa đảo, giờ đây việc đặt mua từ mớ rau, ly cà phê cho đến những thiết bị điện tử đắt tiền qua ứng dụng đã trở thành thói quen thường nhật của hàng chục triệu người.\n\nXu hướng nổi bật nhất hiện nay là sự bùng nổ của mô hình Social Commerce (thương mại mạng xã hội) và Shoppertainment (mua sắm kết hợp giải trí). Khách hàng không còn tìm kiếm sản phẩm theo cách truyền thống qua thanh tìm kiếm, mà ra quyết định mua hàng ngay khi đang theo dõi các buổi livestream vui nhộn hoặc xem các video review chân thực của các nhà sáng tạo nội dung.\n\nTốc độ giao hàng thần tốc và sự tối ưu hóa chuỗi cung ứng cũng đặt ra tiêu chuẩn mới cho trải nghiệm khách hàng. Người mua ngày càng thiếu kiên nhẫn với thời gian giao hàng kéo dài, buộc các sàn thương mại điện tử và nhà bán lẻ phải đầu tư mạnh mẽ vào hệ thống kho vận thông minh và công nghệ fulfillment tự động.\n\nĐối với các doanh nghiệp, việc thích ứng với hành vi mua sắm mới không chỉ đơn thuần là mở một gian hàng trực tuyến, mà đòi hỏi tư duy xây dựng thương hiệu đa kênh (Omnichannel) liền mạch và cá nhân hóa trải nghiệm cho từng phân khúc khách hàng.",
                    'tags' => ['Kinh tế số', 'Thương mại điện tử', 'Kinh doanh'],
                ],
                [
                    'title' => 'Doanh nghiệp nhỏ có thể bắt đầu chuyển đổi số từ đâu?',
                    'slug' => 'doanh-nghiep-nho-co-the-bat-dau-chuyen-doi-so-tu-dau',
                    'category_slug' => 'kinh-doanh',
                    'status' => 'published',
                    'views' => 2780,
                    'published_at' => $now->copy()->subDays(6),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(6),
                    'excerpt' => 'Lộ trình chuyển đổi số thực tế, tối ưu chi phí dành cho các hộ kinh doanh và doanh nghiệp SME mà không cần ngân sách khổng lồ.',
                    'body' => "Nhắc đến \"chuyển đổi số\", nhiều chủ doanh nghiệp vừa và nhỏ (SME) thường nghĩ ngay đến những dự án phần mềm đắt đỏ hàng tỷ đồng hay những hệ thống ERP cồng kềnh. Quan niệm này vô tình tạo ra rào cản tâm lý lớn, khiến nhiều cơ sở kinh doanh bỏ lỡ những cơ hội số hóa thiết thực ngay trong tầm tay.\n\nChuyển đổi số thực chất bắt đầu từ việc giải quyết các điểm nghẽn cụ thể trong hoạt động kinh doanh hàng ngày. Thay vì quản lý đơn hàng và tồn kho bằng sổ sách ghi tay hay file Excel rời rạc dễ nhầm lẫn, hãy bắt đầu bằng một phần mềm bán hàng trên nền tảng đám mây (SaaS) với chi phí chỉ vài trăm nghìn đồng mỗi tháng.\n\nKhâu tiếp theo là số hóa quy trình tương tác với khách hàng. Thiết lập một trang Fanpage chuyên nghiệp, tích hợp chatbot tự động trả lời các câu hỏi phổ biến và sử dụng phần mềm CRM cơ bản sẽ giúp doanh nghiệp nâng cao tỷ lệ chốt đơn và chăm sóc khách hàng chu đáo hơn.\n\nĐiều cốt lõi của chuyển đổi số không nằm ở công nghệ, mà nằm ở con người và tư duy dám thay đổi cách làm cũ. Hãy bắt đầu từ những bước nhỏ nhất, đo lường kết quả rõ ràng rồi mới từng bước mở rộng quy mô.",
                    'tags' => ['Kinh doanh', 'Kinh tế số', 'Khởi nghiệp'],
                ],
                [
                    'title' => 'Marketing nội dung có vai trò gì trong kinh doanh trực tuyến?',
                    'slug' => 'marketing-noi-dung-co-vai-tro-gi-trong-kinh-doanh-truc-tuyen',
                    'category_slug' => 'kinh-doanh',
                    'status' => 'published',
                    'views' => 1690,
                    'published_at' => $now->copy()->subDays(4),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(4),
                    'excerpt' => 'Xây dựng lòng tin thương hiệu và thu hút khách hàng tiềm năng bền vững thông qua chiến lược sáng tạo nội dung giá trị.',
                    'body' => "Trong bối cảnh chi phí quảng cáo trực tuyến trên các nền tảng như Facebook hay Google ngày càng đắt đỏ, các doanh nghiệp kinh doanh trực tuyến đang nhận ra rằng việc đốt tiền vào quảng cáo tràn lan không còn là con đường bền vững. Marketing nội dung (Content Marketing) nổi lên như một chiến lược tối ưu giúp xây dựng tài sản thương hiệu lâu dài.\n\nKhác với quảng cáo bán hàng lộ liễu khiến người dùng muốn bấm nút bỏ qua ngay lập tức, content marketing tập trung vào việc cung cấp giá trị hữu ích trước khi đề cập đến sản phẩm. Bằng cách chia sẻ kiến thức chuyên môn, giải đáp thắc mắc và tháo gỡ khó khăn của khách hàng, thương hiệu sẽ dần chiếm được sự tin tưởng tự nhiên.\n\nMột chiến lược nội dung bài bản cần kết hợp hài hòa giữa các định dạng: bài viết chuyên sâu trên blog website để tối ưu hóa công cụ tìm kiếm (SEO), video ngắn sinh động trên TikTok để tiếp cận tệp khách hàng trẻ, và các cẩm nang hướng dẫn chi tiết để thu thập thông tin khách hàng tiềm năng (leads).\n\nKhi khách hàng nhìn nhận bạn như một chuyên gia đáng tin cậy trong ngành, việc họ lựa chọn sử dụng sản phẩm hoặc dịch vụ của bạn khi có nhu cầu phát sinh chỉ là kết quả tất yếu.",
                    'tags' => ['Kinh doanh', 'Kỹ năng', 'Web'],
                ],
                [
                    'title' => 'Xu hướng thanh toán không dùng tiền mặt tại Việt Nam',
                    'slug' => 'xu-huong-thanh-toan-khong-dung-tien-mat-tai-viet-nam',
                    'category_slug' => 'kinh-te-so',
                    'status' => 'published',
                    'views' => 3250,
                    'published_at' => $now->copy()->subDays(2),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'excerpt' => 'Sự bùng nổ của mã QR thanh toán và ví điện tử đang đưa Việt Nam tiến nhanh vào nhóm các quốc gia thanh toán số hàng đầu khu vực.',
                    'body' => "Chưa bao giờ việc thanh toán tại Việt Nam lại trở nên tiện lợi và nhanh chóng như hiện nay. Từ những siêu thị sang trọng, trung tâm thương mại cho đến các quán trà đá vỉa hè hay quầy bán hàng rong trong chợ truyền thống, mã QR thanh toán đã xuất hiện ở khắp mọi ngõ ngách đời sống.\n\nSự phát triển vượt bậc của hạ tầng ngân hàng số kết hợp cùng chuẩn kết nối VietQR đã tạo nên một cuộc cách mạng trong phương thức thanh toán. Người dân không còn cần phải mang theo chiếc ví dày cộm tiền lẻ hay lo lắng về việc trả lại tiền thừa; chỉ với một thao tác quét mã trên điện thoại, giao dịch hoàn tất trong tích tắc.\n\nBên cạnh mã QR, các phương thức thanh toán không tiếp xúc (contactless) qua Apple Pay, Google Pay hay thẻ chip thông minh cũng đang nhanh chóng chinh phục thế hệ người tiêu dùng trẻ. Sự tiện lợi, an toàn và các chương trình hoàn tiền hấp dẫn là những động lực mạnh mẽ thúc đẩy thói quen không dùng tiền mặt.\n\nLàn sóng thanh toán số không chỉ mang lại sự tiện ích cho người tiêu dùng mà còn giúp minh bạch hóa dòng tiền, giảm thiểu chi phí in ấn lưu thông tiền mặt và tạo đòn bẩy vững chắc cho nền kinh tế số quốc gia phát triển vượt bậc.",
                    'tags' => ['Kinh tế số', 'Thương mại điện tử', 'Công nghệ'],
                ],
                [
                    'title' => 'Chiến lược giữ chân khách hàng trực tuyến trung thành',
                    'slug' => 'chien-luoc-giu-chan-khach-hang-truc-tuyen-trung-thanh',
                    'category_slug' => 'kinh-doanh',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Bí quyết xây dựng chương trình hội viên thân thiết và tối ưu hóa giá trị vòng đời khách hàng (LTV).',
                    'body' => "Bản thảo nghiên cứu về chi phí thu hút khách hàng mới so với việc giữ chân khách hàng cũ trong e-commerce.\n\nCác mô hình tích điểm và chăm sóc hậu mãi tự động qua ứng dụng Zalo ZNS.",
                    'tags' => ['Kinh doanh', 'Thương mại điện tử'],
                ],
                [
                    'title' => 'Phân tích cơ hội kinh doanh trên các sàn thương mại điện tử 2026',
                    'slug' => 'phan-tich-co-hoi-kinh-doanh-tren-cac-san-thuong-mai-dien-tu-2026',
                    'category_slug' => 'kinh-te-so',
                    'status' => 'pending',
                    'views' => 25,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Báo cáo xu hướng ngành hàng tiềm năng và dự báo chính sách thuế, phí sàn thương mại điện tử mới nhất.',
                    'body' => 'Báo cáo phân tích thị trường đang chờ duyệt từ ban biên tập kinh tế về sự thay đổi cơ cấu chi phí vận hành gian hàng trực tuyến.',
                    'tags' => ['Kinh tế số', 'Kinh doanh'],
                ],
                [
                    'title' => 'Khóa học đầu tư tài chính siêu tốc cam kết lãi suất 50% mỗi tháng',
                    'slug' => 'khoa-hoc-dau-tu-tai-chinh-sieu-toc-cam-ket-lai-suat-50-moi-thang',
                    'category_slug' => 'kinh-doanh',
                    'status' => 'rejected',
                    'views' => 11,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'rejection_reason' => 'Chứa nội dung quảng bá mô hình huy động vốn đa cấp trái phép và cam kết lợi nhuận phi thực tế.',
                    'excerpt' => 'Phương pháp giao dịch tiền số bí mật giúp bạn tự do tài chính chỉ sau 3 tháng tham gia.',
                    'body' => 'Nội dung chào mời đầu tư tài chính đa cấp biến tướng có dấu hiệu lừa đảo tài chính, đã bị chặn xuất bản.',
                    'tags' => ['Kinh doanh'],
                ],
            ],

            // =================================================================
            // 6. ĐẶNG THẢO VY (ID: 26) - Sách & Văn hóa
            // =================================================================
            26 => [
                [
                    'title' => '5 cuốn sách phù hợp cho những ngày bạn cần tìm lại cảm hứng',
                    'slug' => '5-cuon-sach-phu-hop-cho-nhung-ngay-ban-can-tim-lai-cam-hung',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'published',
                    'views' => 2190,
                    'published_at' => $now->copy()->subDays(7),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(7),
                    'excerpt' => 'Giới thiệu những tựa sách nhẹ nhàng, sâu lắng giúp chữa lành tâm hồn và tiếp thêm động lực vượt qua những giai đoạn chông chênh.',
                    'body' => "Trong cuộc sống bận rộn, ai trong chúng ta cũng có những ngày thức dậy với cảm giác kiệt sức, mất phương hướng và không còn chút cảm hứng nào cho công việc. Những lúc như vậy, một cuốn sách hay chính là liều thuốc xoa dịu dịu dàng nhất, đưa tâm trí bạn về với vùng an trú yên bình.\n\nCuốn sách đầu tiên bạn nên mở ra là \"Nhà Giả Kim\" của Paulo Coelho. Câu chuyện ngụ ngôn về chàng chăn cừu Santiago đi tìm kho báu nhắc nhở mỗi chúng ta về việc lắng nghe tiếng gọi từ trái tim và kiên định theo đuổi ước mơ chân chính của cuộc đời mình.\n\nNếu bạn đang cảm thấy cuộc sống quá nhiều áp lực, \"Mèo Dạy Tôi Sống Chậm Lại\" hay \"Đại Dương Đen\" sẽ mang lại những góc nhìn thấu cảm về sức khỏe tinh thần. Chúng giúp bạn nhận ra rằng việc thừa nhận sự yếu đuối và cho phép bản thân nghỉ ngơi không phải là thất bại, mà là một phần tự nhiên của hành trình trưởng thành.\n\nHãy pha cho mình một tách trà ấm, chọn một góc ngồi thật thoải mái và để những trang sách dẫn lối bạn đi qua những khoảng lặng dịu êm của tâm hồn.",
                    'tags' => ['Sách hay', 'Văn hóa đọc', 'Review sách', 'Phát triển bản thân'],
                ],
                [
                    'title' => 'Đọc sách giấy hay sách điện tử hiệu quả hơn?',
                    'slug' => 'doc-sach-giay-hay-sach-dien-tu-hieu-qua-hon',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'published',
                    'views' => 1870,
                    'published_at' => $now->copy()->subDays(5),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(5),
                    'excerpt' => 'So sánh trải nghiệm xúc giác của sách giấy truyền thống với tính tiện lợi, gọn nhẹ của máy đọc sách Kindle hiện đại.',
                    'body' => "Tranh luận giữa việc đọc sách giấy truyền thống hay chuyển sang sử dụng máy đọc sách điện tử (e-reader) luôn là chủ đề sôi nổi trong các hội nhóm yêu sách. Mỗi phương thức đều sở hữu những ưu thế riêng biệt phù hợp với những hoàn cảnh và phong cách sống khác nhau.\n\nSách giấy mang lại một trải nghiệm xúc giác tuyệt vời mà không thiết bị công nghệ nào có thể thay thế được. Mùi thơm của mực in trên trang giấy mới, cảm giác lật từng trang sách và niềm vui ngắm nhìn giá sách gia đình đầy ắp tạo nên một sự gắn kết cảm xúc sâu sắc giữa người đọc và tác phẩm.\n\nỞ chiều ngược lại, máy đọc sách như Kindle hay Kobo lại thể hiện ưu thế vượt trội về tính tiện dụng khi di chuyển. Bạn có thể mang theo cả một thư viện hàng nghìn cuốn sách trong một thiết bị nhỏ gọn, dễ dàng đọc sách trong bóng tối và tra cứu từ điển tức thì chỉ bằng một cú chạm ngón tay.\n\nThay vì đặt hai hình thức này vào thế đối lập, giải pháp tối ưu của người đọc thông minh là kết hợp linh hoạt: sử dụng máy đọc sách cho những chuyến công tác, du lịch xa và dành những cuốn sách giấy bìa cứng trang trọng cho những buổi chiều thảnh thơi tại nhà.",
                    'tags' => ['Sách hay', 'Văn hóa đọc', 'Công nghệ'],
                ],
                [
                    'title' => 'Vì sao văn hóa đọc đang thay đổi trong thời đại số?',
                    'slug' => 'vi-sao-van-hoa-doc-dang-thay-doi-trong-thoi-dai-so',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'published',
                    'views' => 1650,
                    'published_at' => $now->copy()->subDays(3),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(3),
                    'excerpt' => 'Tìm hiểu sự chuyển dịch từ đọc sâu sang đọc lướt và cách thế hệ trẻ duy trì thói quen tiếp cận tri thức giữa bão video ngắn.',
                    'body' => "Sự thống trị của các nền tảng mạng xã hội và định dạng video ngắn đã và đang tái cấu trúc sâu sắc cách bộ não con người tiếp nhận thông tin. Khả năng tập trung chú ý của độc giả có xu hướng suy giảm, khiến việc ngồi yên tĩnh đọc một cuốn sách dày hàng trăm trang trở thành một thử thách không nhỏ đối với nhiều người trẻ.\n\nTuy nhiên, văn hóa đọc không hề biến mất mà đang chuyển mình sang những hình thức biểu đạt mới mẻ hơn. Cộng đồng BookTok trên TikTok hay Bookstagram trên Instagram đã tạo nên một làn sóng lan tỏa tình yêu sách vô cùng mạnh mẽ, đưa hàng loạt tác phẩm văn học kinh điển tiếp cận gần gũi hơn với thế hệ Gen Z.\n\nBên cạnh đó, thị trường sách nói (Audiobook) và các ứng dụng tóm tắt sách cũng phát triển bùng nổ, phục vụ nhu cầu tiếp thu kiến thức của những người bận rộn trong lúc lái xe hoặc tập thể dục thể thao.\n\nĐiều quan trọng nhất của văn hóa đọc không nằm ở hình thức vật lý của trang sách, mà nằm ở tinh thần khao khát học hỏi, sự rung cảm trước cái đẹp và khả năng thấu hiểu chiều sâu thế giới nội tâm của con người.",
                    'tags' => ['Văn hóa đọc', 'Sách hay', 'Đời sống'],
                ],
                [
                    'title' => 'Những thói quen nhỏ giúp xây dựng văn hóa đọc mỗi ngày',
                    'slug' => 'nhung-thoi-quen-nho-giup-xay-dung-van-hoa-doc-moi-ngay',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'published',
                    'views' => 2430,
                    'published_at' => $now->copy()->subDays(1),
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(1),
                    'excerpt' => 'Bí quyết duy trì việc đọc đều đặn mỗi ngày mà không cảm thấy áp lực hay tốn quá nhiều thời gian bận rộn.',
                    'body' => "Rất nhiều người thường chia sẻ rằng họ rất muốn đọc sách nhưng luôn cảm thấy mình không có đủ thời gian giữa bộn bề công việc và học tập. Thế nhưng, nếu bạn cộng dồn thời gian lướt mạng xã hội vô định trong ngày, con số đó thường lên đến 2 đến 3 tiếng đồng hồ.\n\nBí quyết để xây dựng thói quen đọc sách bền vững là quy tắc 15 trang mỗi ngày. Hãy bắt đầu buổi sáng sớm hoặc kết thúc một ngày trước khi đi ngủ bằng việc đọc đúng 15 trang sách. Với tốc độ khiêm tốn này, bạn đã có thể hoàn thành từ 15 đến 20 cuốn sách mỗi năm một cách nhẹ nhàng.\n\nHãy luôn mang theo một cuốn sách nhỏ hoặc cài sẵn ứng dụng đọc sách trong điện thoại. Những khoảng thời gian chờ đợi như ngồi trên xe buýt, xếp hàng mua cà phê hay chờ bạn bè đến điểm hẹn sẽ trở thành những phút giây thưởng thức văn học quý giá thay vì lướt màn hình vô thức.\n\nĐừng ép bản thân phải đọc những cuốn sách mà mọi người ca ngợi nếu bạn không cảm thấy hứng thú. Hãy bắt đầu bằng những thể loại bạn thực sự yêu thích, bởi niềm vui thích chân thành chính là động lực mạnh mẽ nhất giữ bạn ở lại bên trang sách.",
                    'tags' => ['Văn hóa đọc', 'Sách hay', 'Tự học', 'Kỹ năng'],
                ],
                [
                    'title' => 'Review sách Atomic Habits và sức mạnh của thói quen nhỏ',
                    'slug' => 'review-sach-atomic-habits-va-suc-manh-cua-thoi-quen-nho',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'draft',
                    'views' => 0,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Phân tích các định luật thay đổi hành vi của James Clear và cách áp dụng vào cuộc sống thực tế.',
                    'body' => "Bản nháp đánh giá chi tiết cấu trúc cuốn sách Atomic Habits cùng các ghi chú thực hành thiết lập vòng lặp thói quen.\n\nPhân tích 4 định luật: Làm cho rõ ràng, Làm cho hấp dẫn, Làm cho dễ dàng và Làm cho thỏa mãn.",
                    'tags' => ['Review sách', 'Sách hay'],
                ],
                [
                    'title' => 'Cách chọn sách hay giữa một biển sách tràn ngập hiện nay',
                    'slug' => 'cach-chon-sach-hay-giua-mot-bien-sach-tran-ngap-hien-nay',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'pending',
                    'views' => 14,
                    'published_at' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                    'excerpt' => 'Tiêu chí thẩm định giá trị nội dung, chọn nhà xuất bản và dịch giả uy tín cho tủ sách gia đình.',
                    'body' => 'Bài viết chia sẻ kinh nghiệm chọn lọc sách đang chờ biên tập viên duyệt xuất bản với các tiêu chí đánh giá uy tín bản dịch.',
                    'tags' => ['Sách hay', 'Văn hóa đọc'],
                ],
                [
                    'title' => 'Tải ebook lậu không có bản quyền từ các nguồn không chính thống',
                    'slug' => 'tai-ebook-lau-khong-co-ban-quyen-tu-cac-nguon-khong-chinh-thong',
                    'category_slug' => 'sach-van-hoa',
                    'status' => 'rejected',
                    'views' => 6,
                    'published_at' => null,
                    'reviewed_by' => 1,
                    'reviewed_at' => $now->copy()->subDays(2),
                    'rejection_reason' => 'Vi phạm quyền tác giả và quyền sở hữu trí tuệ đối với các ấn phẩm xuất bản.',
                    'excerpt' => 'Danh sách các trang web chia sẻ file PDF scan sách bản quyền miễn phí không cần trả tiền.',
                    'body' => 'Bài viết hướng dẫn phát tán và sử dụng tác phẩm số vi phạm quyền tác giả, bị ban quản trị từ chối kiểm duyệt.',
                    'tags' => ['Sách hay'],
                ],
            ],
        ];
    }
}
