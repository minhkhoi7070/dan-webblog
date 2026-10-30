# CloudBlog — checklist QA cuối trên môi trường testing

Ngày lập/cập nhật: 30/10/2026. Chỉ thực hiện mutation với tài khoản, mailbox, file và **MySQL database testing riêng**. Không chạy các bước ghi trên database phát triển `webblog`. Sau sửa BUG-004 và parser video, Playwright Chromium **37/37 PASS**. Bài cũ `/posts/php` còn thiếu URL video trong DB và cần tác giả chèn lại media. Các hàng bên dưới phân biệt phần đã tự động xác minh với thao tác thủ công còn thiếu; `BLOCKED` nghĩa là **toàn bộ bước mô tả** chưa đủ bằng chứng để PASS.

## Browser E2E

| ID | Bước thao tác trên môi trường testing | Expected | Actual | Status | Evidence cần lưu |
|---|---|---|---|---|---|
| B-01 | Guest mở Home → Search từ khóa có bài/rỗng → Category có bài/rỗng → mở chi tiết → Back/Forward → F5 | Feed/URL/card đúng; Back/Forward giữ trạng thái; không full reload ngoài ý muốn, không giật feed | Playwright PASS cho filter/search/detail/history và navigation AJAX; chưa đo F5, layout shift, race | PARTIAL | `guest.spec.mjs`, `navigation.spec.mjs`; cần video/layout shift |
| B-02 | Guest đăng ký bằng mailbox test → nhận OTP → verify → login → logout → login lại; thử email trùng/sai, double submit | Một user Viewer; OTP đúng mục đích, không tạo trùng, session hợp lệ | Browser registration/verify/reset/login PASS bằng mail log QA; SMTP và double submit browser chưa thử | PARTIAL | `registration.spec.mjs`, QA DB; mailbox thật còn thiếu |
| B-03 | Viewer Like/Unlike, Comment/Reply, Save/Unsave, Follow/Unfollow; thử request lỗi và reload | UI, counters, pivots và rollback đồng bộ, không handler trùng | Like/Save/Follow/Comment + DB assertion PASS; Reply, lỗi mạng/rollback và reload chưa thử | PARTIAL | `interactions.spec.mjs`; cần error/reload |
| B-04 | Viewer mở Quick Follow, confirm, Escape, click ngoài; Share/Copy link | Popover/focus đúng; chỉ confirm mới Follow; link đúng | Escape/confirm và DB PASS; click ngoài, share/copy chưa thử | PARTIAL | `interactions.spec.mjs`; cần clipboard/focus |
| B-05 | Author tạo Draft trong TipTap, dùng toolbar/shortcut, upload ảnh/video thử, lưu/reopen; xóa video tạm của mình; submit | Nội dung, media URL và Draft lưu đúng; upload/delete có quyền; submit thành Pending | WebM upload→Draft→reopen→Pending→approve→public playback PASS; upload/delete ownership PASS; toolbar/shortcut/ảnh chưa thử | PARTIAL | `workflow.spec.mjs`, `media.spec.mjs`, `video-publication.spec.mjs`; QA storage |
| B-06 | Admin approve/reject bài Pending của Author, tạo bài của mình và Direct Publish; gọi submit lại Published | Moderation đúng; Published còn trên feed; submit sai bị từ chối, counters không lệch | Approve, direct publish, submit Published 422 + DB/feed PASS; reject và counters chưa thử | PARTIAL | `workflow.spec.mjs` |
| B-07 | Viewer mở Profile Edit Modal, sửa thông tin/avatar, Escape/close, đổi password/account settings; đi Favorites/Following/Activity | Focus/scroll ổn định, dữ liệu lưu đúng, nav hoạt động sau AJAX | Favorites/Following/Activity AJAX, modal open/Escape/scroll và avatar mặc định PASS; edit/settings chưa thử | PARTIAL | `navigation.spec.mjs`, `profile.spec.mjs`, `responsive.spec.mjs` |
| B-08 | Admin CMS: dashboard, users, posts, categories, tags, comments; filter/paginate; quay lại | CRUD và quyền đúng, filter/URL còn sau Back/Forward, không handler trùng | Cả 6 trang index tải 200; dashboard/posts fit 4 viewport; CRUD/filter chưa thử | PARTIAL | `admin-cms.spec.mjs`, `responsive.spec.mjs`; cần filter/CRUD |
| B-09 | Lặp Home → category → search → detail → Back/Forward nhanh, cả trạng thái có bài/rỗng; xem Network/Console | Không reload ngoài ý muốn, request cũ không ghi đè mới, không JS error | History/AJAX test PASS; lặp nhanh/race chưa thử | PARTIAL | `guest.spec.mjs`, `navigation.spec.mjs`; cần race |

## Responsive và accessibility

Trên **mỗi viewport** dưới đây, đăng nhập lần lượt Guest, Viewer, Author, Admin bằng tài khoản testing. Kiểm tra Home Feed, post card/detail, sidebar desktop/mobile, mobile header, bottom navigation, Profile/Edit Modal, Activity, Favorites, Post Editor, Admin CMS (khi role có quyền). Chỉ đánh dấu PASS cho từng tổ hợp đã thao tác. Kiểm tra tràn chữ, avatar/metadata lệch, hai header trùng, khoảng trắng, feed giật khi đổi category, nền giật khi mở modal, nav hỏng sau AJAX và desktop bị ảnh hưởng bởi mobile. Dùng keyboard để kiểm focus, Escape, labels và modal trap.

| Viewport | Expected | Actual | Status | Evidence cần lưu |
|---|---|---|---|---|
| 390 × 844 | Không overflow/overlap; mobile nav/modal hoạt động | 4/4 role PASS về width và local diagnostics; overlap/modal toàn diện chưa đo | PARTIAL | `responsive.spec.mjs`; screenshot `test-results/` |
| 794 × 1024 | Breakpoint tablet đúng; không hai header | 4/4 role PASS về width và local diagnostics; breakpoint toàn diện chưa đo | PARTIAL | `responsive.spec.mjs`; screenshot `test-results/` |
| 992 × 900 | Chuyển desktop/mobile đúng; sidebar không lệch | 4/4 role PASS về width và local diagnostics; sidebar toàn diện chưa đo | PARTIAL | `responsive.spec.mjs`; screenshot `test-results/` |
| 1440 × 900 | Desktop layout ổn định, không khoảng trắng/tràn | 4/4 role PASS về width và local diagnostics; layout toàn diện chưa đo | PARTIAL | `responsive.spec.mjs`; screenshot `test-results/` |

## MySQL testing riêng

`phpunit.xml` vẫn dùng SQLite `:memory:`. Riêng Playwright dùng `.env.e2e` bị Git ignore, schema `webblog_qa`, user `webblog_qa@localhost` chỉ có quyền trên schema đó, QA storage riêng. Health check xác nhận process testing trỏ đúng schema/storage trước mọi E2E; QA server đã chạy 16 migrations và seed. Không đổi `.env` phát triển.

| Bước | Expected | Actual | Status | Evidence cần lưu |
|---|---|---|---|---|
| M-01: Migration + FK/unique | Migration chạy trên schema test; FK/unique từ chối dữ liệu sai | 16 migrations PASS; constraint errors chưa thử | PARTIAL | QA health, migration output; cần constraint tests |
| M-02: Relationships/transactions | Eloquent pivots/cascade và rollback đúng trên MySQL | Browser DB assertions cho like/favorite/follow/comment PASS; rollback chưa thử | PARTIAL | `interactions.spec.mjs`; cần rollback/cascade |
| M-03: Auth/OTP/post/video/rich text | Workflow và quyền giống SQLite, OTP lưu/hash/expire đúng | Auth/OTP/post/video/rich-text và quyền PASS trong ca đã chạy; expire chưa thử | PARTIAL | `registration`, `workflow`, `media` specs |
| M-04: Concurrency | Double submit và upload/delete race không sai trạng thái/mất file | Chưa chạy | BLOCKED | Hai request đồng thời, status/counters/file state |

## SMTP thật và production readiness

Chỉ gửi một vài email tới mailbox testing do người dùng chỉ định; không ghi OTP hay credentials vào báo cáo. Dùng DB testing riêng cho user/OTP. Không thay đổi `.env` hiện tại hoặc cấu hình production trong vòng QA này.

| Bước | Expected | Actual | Status | Evidence cần lưu |
|---|---|---|---|---|
| S-01: Verification OTP | Mail đến inbox/spam; OTP đúng user, đúng mục đích; expire/replay bị từ chối | Chưa có mailbox testing được cấp | BLOCKED | Thời gian gửi/nhận, sender/subject, response và DB test state; che mã OTP |
| S-02: Reset OTP | Reset mail đến; mã đúng đổi password một lần; mã cũ/hết hạn không dùng lại | Chưa chạy | BLOCKED | Mailbox test, response, DB test token/hash; che mã OTP |
| P-01: Production config | `APP_ENV=production`, `APP_DEBUG=false`, secrets ngoài Git, SMTP/storage/HTTPS/cookie/logs đúng | Chưa có production để xác minh; local là `APP_ENV=local`, `APP_DEBUG=true` | BLOCKED | Giá trị không nhạy cảm trong production, secret scan, storage link/permissions, log summary |
| P-02: Assets/migrations/errors | Vite assets tải 200, upload hoạt động, migrations sẵn sàng, lỗi hiển thị an toàn | Local build PASS với warning login background; 16 migration local Ran; production chưa kiểm | BLOCKED | Network/console, deploy logs, migration status production (read-only) |
