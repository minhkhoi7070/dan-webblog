# CloudBlog — báo cáo QA tổng hợp

## Cập nhật Playwright + MySQL QA (30/10/2026)

- Playwright 1.64.1 và Chromium chạy được trên `http://127.0.0.1:8001` với `.env.e2e` bị Git ignore. Health endpoint xác nhận `APP_ENV=e2e`, MySQL `webblog_qa`, tài khoản `webblog_qa@localhost`, mail transport `log`, session file và `storage/e2e/app/public`. Tài khoản DB chỉ có quyền trên schema QA; truy cập `webblog.posts` bị từ chối. Không đổi `.env`, không ghi vào MySQL phát triển `webblog`.
- Schema QA mới chạy đủ 16 migrations, seeder riêng tạo 4 role, 4 post và dữ liệu tương tác. Browser thực hiện registration/verification/reset bằng mail log QA, không gửi SMTP thật.
- **37 Playwright tests: 37 PASS, 0 FAIL** sau sửa BUG-004 và parser video local. SVG avatar mặc định trả 200 `image/svg+xml` và render trong Chromium; Viewer PASS ở cả 390×844, 794×1024, 992×900, 1440×900. Video WebM QA qua Draft/reopen/approve/public playback; bài cũ `/posts/php` vẫn thiếu URL media trong DB và cần chèn lại video.
- PHP **504/504 PASS, 2604 assertions**; Node **38/38 PASS**; Pint, Vite build và `git diff --check` PASS. Vite vẫn cảnh báo `/images/login-bg.jpg` lúc build. Ba bug đã sửa trước đó cũng PASS qua browser thực: xóa video sai owner 403, submit Published 422, rich text rỗng 422.
- Tổng automated đã chạy hiện là **579 ca: 579 PASS, 0 FAIL** (504 PHP + 38 Node + 37 Playwright). Các số 542/542 và mô tả browser/MySQL BLOCKED ở phần dưới là **lịch sử trước vòng Playwright**, không còn là trạng thái hiện tại. SMTP thật, production, concurrency MySQL, accessibility/modal đầy đủ và các thao tác browser chưa được cover vẫn chưa xác minh.

Chi tiết case/giới hạn ở `QA_REGRESSION_REPORT.md`, `QA_FINAL_MANUAL_CHECKLIST.md` và lỗi mới ở `QA_BUG_REPORT.md`.

Ngày kiểm thử và sửa lỗi: 30/10/2026 (Asia/Bangkok). Working tree có thay đổi chưa commit trước QA; đợt sửa này chỉ chạm source/test liên quan ba bug và báo cáo QA. Không thay đổi schema, `.env` hoặc dữ liệu MySQL phát triển.

## Môi trường và phương pháp

- PHP 8.3.30; Laravel **13.33.0** (brief ghi Laravel 12); PHPUnit 12.5.35; MySQL 8.4.3, database phát triển `webblog`; Vite 8.3.1; Bootstrap 5.3.8; TipTap 3.32.0.
- `phpunit.xml` đặt `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `MAIL_MAILER=array`, `SESSION_DRIVER=array`. Các test viết dữ liệu chỉ chạy trong SQLite in-memory; live MySQL chỉ được xem metadata bằng `db:show`/`db:table`.
- Laravel Boost MCP không được kết nối trong phiên này; dùng Artisan đọc route/schema và source. Browser host của CUA báo `The browser access host is unavailable`. Computer Use điều khiển được một tab Chrome cục bộ cho smoke test Guest, nhưng cửa sổ bị thu nhỏ khi chuyển sang bài chi tiết và sau đó có input người dùng; dừng tương tác để tránh can thiệp. Chưa đo viewport, console/network hoặc E2E có đăng nhập.
- Không có Xdebug/PCOV trong `php -m`; **coverage chưa đo được**. Không gửi SMTP thật vì không có mailbox kiểm thử được phép.

## Kết quả hiện tại sau fix

| Hạng mục | Kết quả | Bằng chứng |
|---|---|---|
| Ba audit findings | 3 PASS, 0 FAIL | `php artisan test --compact tests/Feature/AuditFindingsTest.php` |
| Toàn bộ PHP | **504 PASS, 0 FAIL; 2604 assertions** | `php artisan test --compact`, vòng cuối 18.213 giây |
| Node | **38 PASS, 0 FAIL** | `node --test tests/Unit/*.test.mjs` |
| Pint / Vite / whitespace | PASS / PASS / PASS | `vendor/bin/pint --test`, `npm run build`, `git diff --check`; warning asset Vite cũ vẫn còn |
| Browser E2E đầy đủ, SMTP thật | BLOCKED | Chrome cục bộ chỉ xác minh Home Feed và Category Guest; không có mailbox test được cấp |

Tổng cộng **542 automated tests, 542 PASS**. Bổ sung 32 PHP regression tests: 11 cho quyền xóa video, 5 cho submit transition, 16 cho rich-text validation. Danh sách test hiện tại ở `QA_AUTOMATED_TEST_INVENTORY.md`. Ma trận nghiệp vụ 71 ca hiện là **44 PASS, 0 FAIL, 19 BLOCKED, 7 NOT TESTED, 1 N/A**. PASS tự động không thay thế E2E/browser hoặc SMTP thật.

## Vòng xác minh cuối

| Hạng mục | Expected | Actual | Status | Evidence |
|---|---|---|---|---|
| Browser Guest Home/Category smoke | Feed hiển thị và category lọc đúng | Chrome cục bộ hiển thị Home Feed; click `Công nghệ` đưa URL tới `/posts?category=cong-nghe`, danh sách có bài đúng category | PASS | Computer Use accessibility tree/screenshot, 30/10/2026; live HTTP GET `/` trả 200 |
| Browser E2E còn lại | Hoàn tất luồng Guest/Viewer/Author/Admin, UI, console/network và 4 viewport | Chưa thực thi đủ; browser host CUA không khả dụng, Chrome cục bộ bị thu nhỏ và có user input khi tiếp tục | BLOCKED | Computer Use trả `window is minimized` và `user input was detected`; checklist `QA_FINAL_MANUAL_CHECKLIST.md` |
| MySQL integration | Migration và mutation trên DB testing riêng | Không có `.env.testing`; `phpunit.xml` cố định SQLite `:memory:`. MySQL mặc định là DB phát triển; chỉ chạy `migrate:status` đọc metadata, 16 migration đã Ran | BLOCKED | `phpunit.xml`, `php artisan config:show database.default`, `php artisan migrate:status --no-interaction` |
| SMTP thật | OTP đến mailbox test được cho phép | Mailer local là SMTP nhưng không có địa chỉ mailbox test được cấp; không gửi mail | BLOCKED | `php artisan config:show mail.default`; không in credentials |
| Giới hạn upload theo cấu hình | Giới hạn PHP không thấp hơn giới hạn video ứng dụng | `upload_max_filesize` và `post_max_size` đều 2 GB; `blog.max_video_size_kb` là 102400 (100 MB). Chưa upload file sát ngưỡng | PASS cho đối chiếu cấu hình | `php -r` đọc `ini_get`, `php artisan config:show blog.max_video_size_kb` |
| Production readiness | Cấu hình production an toàn và deployable | Local `APP_ENV=local`, `APP_DEBUG=true`; production chưa có để xác minh. `.env` không tracked, storage junction và Vite manifest tồn tại, 16 migration Ran; scan mẫu private key/AWS key trong tracked files: 0 file. Quyền upload thực tế và log lỗi production chưa kiểm | BLOCKED cho production | `config:show`, `git ls-files`, `git grep -l`, `migrate:status`, kiểm tra filesystem; Vite build PASS với warning asset cũ |

Browser smoke test không xác nhận không reload, không lỗi JavaScript, HTTP network trong DevTools hoặc giao diện ở bốn viewport. Không phát hiện bug mới từ các bước đã thực thi; đây không phải bằng chứng các luồng còn lại không có lỗi.

## Lịch sử QA trước fix

| Hạng mục | Kết quả | Bằng chứng |
|---|---|---|
| Route inventory | 89 route | `php artisan route:list --json`; xem `QA_ROUTE_INVENTORY.md` |
| Suite PHP có sẵn | 469 PASS, 0 FAIL; 2477 assertions | `php artisan test --compact`, 16.972 giây, trước khi thêm test audit |
| Suite PHP trước fix | 472 tests: 469 PASS, 3 FAIL | `php artisan test --compact`, 16.933 giây; 3 FAIL chính là test audit |
| Node tests | 38 PASS, 0 FAIL | `node --test tests/Unit/*.test.mjs` |
| Test audit mới | 0 PASS, 3 FAIL | `php artisan test --compact tests/Feature/AuditFindingsTest.php`; xem `QA_BUG_REPORT.md` |
| Formatter | PASS | `vendor/bin/pint --test`; sau đó Pint chỉ áp dụng cho file test audit để giữ nguyên các PHP uncommitted khác |
| Vite build | PASS, có warning | `npm run build`; `/images/login-bg.jpg` chưa resolve lúc build; live GET asset trả 200 `image/jpeg` |
| Diff whitespace | PASS | `git diff --check` trước audit |
| Live HTTP GET | 7/7 trả 200 | `/`, `/posts`, `/posts?q=qa-audit-nonexistent`, `/login`, `/register`, `/forgot-password`, `/admin/login` qua `Invoke-WebRequest` |
| Live AJAX fragment | PASS ở mức HTTP | `GET /posts?q=qa-audit-nonexistent`, header `X-BlogMNM-Feed: 1` → 200, response header `X-BlogMNM-Feed: 1` |
| MySQL metadata | Đọc thành công | `php artisan db:show`: 18 tables; `db:table posts/email_otps/likes/comments` cho thấy FK, unique và index trọng yếu |

Trước fix đã thực thi **510 automated tests**, trong đó **507 PASS, 3 FAIL**. Ma trận nghiệp vụ lúc đó có **71 ca: 40 PASS, 3 FAIL, 19 BLOCKED, 8 NOT TESTED, 1 N/A**. Các số này là lịch sử, không phải kết quả hiện tại.

## Bản đồ kiến trúc

| Module | Route/middleware | Controller → Request/Policy | Model/DB | View/JavaScript | Test hiện có |
|---|---|---|---|---|---|
| Auth, OTP | `guest`, `auth`, `throttle` | `Auth/*`, `RegisterRequest`, `LoginRequest`, `OtpService` | `users`, `email_otps`, `password_reset_tokens`, `sessions` | `auth/*`, `resources/js/security-settings.js` | `tests/Feature/Auth/*`, `SecurityAccountSettingsTest` |
| Public feed | `web` | `PostController@index/show/byAuthor` | `posts`, `categories`, `tags`, `comments`, `likes` | `posts/*`, `authors/*`, `feed-navigation.js`, `app.js` | `GuestPostTest`, `FeedNavigationTest`, `AjaxPaginationTest` |
| Author/editor | `auth,verified,role:author,admin` | `PostController`, `StorePostRequest`, `PostPolicy`, `PostContentService` | `posts`, `post_tag`, public storage | `author/posts/*`, `editor.js` | `AuthorPostTest`, `PostEditorTest`, `AdminDirectPublishTest` |
| Interaction | `auth,verified` | `InteractionController`, `CommentController`, `StoreCommentRequest`, `CommentPolicy` | `likes`, `favorites`, `follows`, `comments` | post cards/detail, `interaction-state.js`, `app.js` | `ViewerInteractionTest`, `WorkflowIntegrityTest`, Node interaction tests |
| Personal | `auth` | `ProfileController`, `ProfileUpdateRequest`, `ActivityController`, `FavoriteController`, `FollowingController` | `users`, `posts`, pivots | `profile/*`, `activity/*`, `favorites/*`, `following/*`, `profile-settings.js` | `ProfileTest`, `AvatarUploadTest`, `FavoritesPageRedesignTest` |
| Admin CMS | `auth,verified,role:admin` | `Admin/*` | users/posts/categories/tags/comments | `admin/*`, AJAX table fragments | `AdminTest`, `WorkflowIntegrityTest`, `AdminRealtimeMetricsTest` |

`QA_ROUTE_INVENTORY.md` liệt kê từng method, URI, name, action và middleware. Những route đặc biệt như `POST /comments`, `DELETE /posts/delete-video`, `GET /posts/check-slug`, OTP và admin metrics được tính trong inventory dù không phải màn hình riêng.

## Phát hiện ban đầu và trạng thái hiện tại

- **P0 / Critical — BUG-001:** Author bất kỳ có thể xóa video của author khác bằng filename công khai; test cô lập xác nhận file bị xóa và HTTP 200.
- **P1 / High — BUG-002:** Admin gọi submit trực tiếp lên post Published của author khác làm status chuyển về Pending; HTTP 200, post rời public feed.
- **P2 / Medium — BUG-003:** Nội dung `<p><br></p>` được lưu thành post rỗng, HTTP 302 thay vì lỗi validation.
- **Cả ba bug hiện FIXED** trong SQLite test: test audit đã PASS, các regression mới đã PASS và full PHP suite đã PASS. Chi tiết code/kiểm chứng ở `QA_BUG_REPORT.md`.
- **Rủi ro triển khai:** `app.debug=true` trong môi trường phát triển hiện tại. Không suy diễn cấu hình production. Chỉ tắt debug khi triển khai production.

## Giới hạn và đánh giá

Browser chỉ mới smoke test Guest Home/Category; chưa chạy E2E đầy đủ, SMTP thật, kiểm tra 4 viewport, accessibility, console/network, race condition thực tế hoặc đối soát orphan/duplicate trên dữ liệu MySQL. Không xóa/thay đổi dữ liệu thật. Bộ test hiện có bao phủ nhiều backend và JavaScript unit behavior, nhưng ba test audit mới chứng minh các lỗ hổng ngoài coverage cũ.

**Chưa thể xác nhận toàn hệ thống sẵn sàng bảo vệ đồ án** vì browser E2E, SMTP thật, bốn viewport và MySQL integration chưa được xác minh. Ba bug đã được xử lý ở mức source và automated tests. Checklist thao tác còn lại ở `QA_FINAL_MANUAL_CHECKLIST.md`; xem thêm `QA_TEST_CASES.md`, `QA_BUG_REPORT.md`, `QA_REGRESSION_REPORT.md`, `QA_ROLE_PERMISSION_MATRIX.md` và `QA_RECOMMENDATIONS.md`.
