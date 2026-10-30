# CloudBlog — regression và E2E

## Vòng Playwright / MySQL QA 30/10/2026 — kết quả hiện tại

| Luồng | Kết quả | Bằng chứng |
|---|---|---|
| Playwright Chromium | **37 tests: 37 PASS, 0 FAIL** sau sửa parser video | `npm run test:e2e -- --reporter=dot`; `tests/E2E/`, trace/screenshot bị Git ignore |
| Guest + AJAX | Feed/search/category, post detail, history và sidebar AJAX không document reload trong ca đã chạy: PASS | `guest.spec.mjs`, `navigation.spec.mjs` |
| Auth/OTP | Registration, verify và password reset bằng **QA mail log**, login/logout, Viewer admin 403: PASS | `registration.spec.mjs`, `auth.spec.mjs`; SMTP thật chưa chạy |
| Interaction/workflow | Like/Save/Follow/Comment, Author draft→pending, Admin approve/direct publish, ba bug cũ: PASS trên browser + QA MySQL | `interactions.spec.mjs`, `workflow.spec.mjs`, `media.spec.mjs` |
| Responsive | 16/16 role×viewport tests PASS; Viewer 4/4 PASS, không có lỗi console/network cục bộ | `responsive.spec.mjs`; chưa xác nhận mọi overlap, focus trap hoặc CLS |
| MySQL QA | Schema `webblog_qa`, user chỉ có quyền QA, 16 migrations, seed và các assertion DB E2E PASS | `/__e2e/health`, `E2ESeeder.php`; FK/unique error, rollback và concurrency chưa test trực tiếp |
| Profile/Admin CMS | SVG mặc định 200 `image/svg+xml`, render trong Chromium; Profile modal open/Escape/scroll PASS; 6 trang index Admin tải 200 | `profile.spec.mjs`, `admin-cms.spec.mjs`; chưa kiểm focus trap/CRUD |
| BUG-005 video local | RED trước sửa tại editor reopen: thẻ video biến thành iframe. GREEN sau sửa: WebM upload→Draft→reopen→Pending→approve→public player/reload PASS, ba viewport; body/URL không đổi sau approve | `video-publication.spec.mjs`; bài cũ `/posts/php` còn wrapper rỗng, cần tác giả chèn lại video |
| YouTube embed | URL hợp lệ giữ nguyên iframe qua Draft/reopen | `video-publication.spec.mjs`; playback từ YouTube bên ngoài chưa xác nhận |
| PHP/Node/format/build | 504 PHP PASS (2604 assertions), 38 Node PASS, Pint/build/diff PASS | `php artisan test --compact`, `node --test tests/Unit/*.test.mjs`, `vendor/bin/pint --test`, `npm run build`, `git diff --check` |

Console/network cục bộ sau fix: không còn lỗi 404 avatar hoặc JavaScript error trong 37 ca Playwright. Các request Google Fonts/YouTube bên ngoài có thể bị hạn chế trong môi trường test; diagnostics ghi nhận nhưng không tính là lỗi server ứng dụng. Các 403/422 trong test security/validation là response mong đợi. Không gửi SMTP thật. Bảng “Vòng xác minh cuối” bên dưới là kết quả **trước** vòng Playwright.

## Vòng xác minh cuối 30/10/2026

| Luồng | Expected | Actual | Status | Evidence |
|---|---|---|---|---|
| PHP/Node regression | Toàn bộ suite PASS | 504 PHP PASS, 2604 assertions; 38 Node PASS | PASS | `php artisan test --compact`, `node --test tests/Unit/*.test.mjs` |
| Formatter/build/diff | Không lỗi | Pint PASS, Vite build PASS (warning `/images/login-bg.jpg` cũ), diff whitespace PASS | PASS | `vendor/bin/pint --test`, `npm run build`, `git diff --check` |
| Browser Guest Home/Category smoke | Feed và lọc category hiển thị | Chrome cục bộ mở Home Feed, click `Công nghệ` → URL category và danh sách bài đúng category | PASS | Computer Use screenshot/accessibility tree |
| Browser roles, AJAX history, 4 viewport, diagnostics | Luồng và UI đúng | Chưa hoàn thành; CUA browser host lỗi, cửa sổ Chrome cục bộ bị thu nhỏ rồi có input người dùng | BLOCKED | Computer Use errors; `QA_FINAL_MANUAL_CHECKLIST.md` |
| MySQL integration | Test trên DB riêng | Chưa có MySQL testing được xác minh; 16 migration trên DB phát triển chỉ được đọc trạng thái | BLOCKED | `.env.testing` không tồn tại; `phpunit.xml` SQLite; `migrate:status` |
| SMTP thật | Gửi và nhận OTP tại mailbox test | Chưa có mailbox test được cấp; không gửi | BLOCKED | `mail.default=smtp`; `QA_FINAL_MANUAL_CHECKLIST.md` |

Không có bug mới được xác nhận. Kết quả Browser Guest chỉ chứng minh các thao tác đã quan sát; không khẳng định console/network sạch, không reload hoặc giao diện bốn viewport.

## Kết quả sau fix

| Luồng | Kết quả | Bằng chứng |
|---|---|---|
| Audit BUG-001/002/003 | 3/3 PASS | `AuditFindingsTest.php` |
| Video ownership/path/session/attached media/storage failure | 11/11 PASS | `VideoDeletionAuthorizationTest.php`; upload, xóa hợp lệ và retry sau lỗi storage PASS |
| Submit state, metadata, public feed, counters | 5/5 PASS | `PostSubmitTransitionTest.php`; `AdminDirectPublishTest.php`, `WorkflowIntegrityTest.php` cũng PASS |
| HTML rỗng, Unicode, media-only, update | 16/16 PASS | `PostContentValidationTest.php`; `PostEditorTest.php` PASS |
| Full PHP regression | 504/504 PASS | `php artisan test --compact`; 2604 assertions |
| Node unit regression | 38/38 PASS | `node --test tests/Unit/*.test.mjs` |
| Vite, Pint, diff | PASS | `npm run build`, `vendor/bin/pint --test`, `git diff --check` |

## Lịch sử kiểm thử trước fix

| Luồng | Kết quả | Bằng chứng và giới hạn |
|---|---|---|
| AJAX feed request/fragment | PASS ở HTTP | Live `GET /posts?q=qa-audit-nonexistent` và header `X-BlogMNM-Feed: 1` → 200, response header `1`; chưa click UI |
| AJAX pagination/history/race logic | PASS ở Node unit | `FeedNavigation.test.mjs`, `AppNavigation.test.mjs` 38/38 Node tests tổng; chưa đo browser |
| Optimistic like/favorite rollback | PASS ở Node unit | `InteractionState.test.mjs`; backend idempotence trong `WorkflowIntegrityTest.php` |
| Quick Follow markup/handlers | PASS ở test hiện có mức source/HTTP | `QuickFollowPopoverTest.php`; click, Escape, click ngoài chưa chạy |
| Bookmark backend/favorites | PASS ở PHP test | `ViewerInteractionTest.php`, `FavoritesPageRedesignTest.php`; đồng bộ đa trang browser chưa chạy |
| Avatar upload backend | PASS ở PHP test | `AvatarUploadTest.php`, `ProfileTest.php`; preview/modal browser chưa chạy |
| OTP verification/reset | PASS ở PHP test | `Auth/EmailVerificationTest.php`, `Auth/PasswordResetTest.php`; SMTP thật chưa chạy |
| Post moderation happy path | PASS ở PHP test | `AdminDirectPublishTest.php`, `WorkflowIntegrityTest.php`; BUG-002 là đường đi trực tiếp ngoài happy path |
| Media deletion ownership | FAIL trước fix | BUG-001/P0, test audit tái hiện file của người khác bị xóa |
| Empty editor content | FAIL trước fix | BUG-003/P2, test audit tái hiện post rỗng được lưu |

## Browser E2E đầy đủ chưa thực hiện

CUA browser host trả `The browser access host is unavailable`; không có Playwright/Dusk được cài trong dự án. Computer Use chỉ hoàn tất Guest Home/Category smoke test. Các kiểm tra sau là **BLOCKED**, không suy ra từ unit/feature tests:

- Register → nhận OTP SMTP → verify → login; reset password OTP tới mailbox test.
- Author draft → pending → admin approve → public feed; admin publish trực tiếp qua editor.
- Viewer Like → Comment → Save → Follow; optimistic UI và rollback khi network lỗi.
- Search → Category → pagination → Back/Forward → F5, kể cả đổi nhanh có bài↔rỗng và request chậm.
- Favorites unsave → counter update; Activity → post detail; Quick Follow popover confirm/Escape/click ngoài.
- Profile modal open/close: đo tọa độ main content trước/sau, focus trap, Escape, avatar preview/update/reload.
- Responsive 390×844, 794×1024, 992×900, 1440×900; bổ sung 320px và hai phía breakpoint; kiểm tra horizontal overflow, ảnh, modal, header, sidebar/drawer, nav và dark/light.
- Browser console/network, layout shifts, asset loading và thời gian đáp ứng. Không có số đo hiệu năng browser nào được tạo.

## Kịch bản kiểm thử đã chạy sau Fix

1. BUG-001: audit test và 11 test video ownership/path/session/attached media/storage failure PASS.
2. BUG-002: audit test, 5 test transition, `AdminDirectPublishTest`, `WorkflowIntegrityTest` PASS.
3. BUG-003: audit test, 16 test validation, `AuthorPostTest`, `PostEditorTest` PASS.
4. Toàn suite PHP/Node, build, Pint và diff check PASS. Browser E2E với tài khoản/mailbox test vẫn BLOCKED.
