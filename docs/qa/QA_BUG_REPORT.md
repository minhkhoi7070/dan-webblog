# CloudBlog — lỗi tái hiện được

## BUG-005 — Video local thành khung rỗng sau khi mở lại editor (code đã sửa, bài cũ cần khôi phục media)

- **Bài được báo:** `/posts/php` (post 56, Published). Kiểm tra MySQL `webblog` **chỉ đọc** cho thấy `body` chứa `<div class="video-embed-wrapper ratio ratio-16x9 my-3" data-youtube-video=""></div>`; không còn `<video>`, `<source>`, `<iframe>` hoặc URL video. CSS vẫn tạo khung tỷ lệ 16:9, vì vậy trình duyệt hiện khung xám trống. Bước Admin approve chỉ đổi status/review/published metadata, không sửa `body`.
- **Root cause tái hiện được:** `CustomYoutube.parseHTML()` trong `resources/js/editor.js` có rule quá rộng `div.video-embed-wrapper`, chạy trước `CustomHtml5Video`. Khi Author lưu Draft chứa video local rồi mở lại editor, wrapper HTML5 bị nhận thành YouTube node; URL `/storage/posts/videos/*.webm` bị render vào iframe. `PostContentService` cho phép iframe YouTube hợp lệ, nên khi lưu lại nó bỏ iframe chứa URL local, để lại wrapper rỗng. Không nới whitelist HTML hoặc tắt sanitizer.
- **Bằng chứng RED/GREEN:** Playwright test `video-publication.spec.mjs` tạo video WebM thật trên QA storage. Trước sửa, test FAIL tại `#tiptap-editor video source` sau reopen; DOM chỉ có iframe. Sau sửa rule YouTube bỏ qua wrapper chứa `video/source` và chỉ nhận video ID YouTube hợp lệ, test PASS qua Draft → reopen → Pending → Admin approve → public playback/reload. DB body và URL video giữ nguyên sau approve; player `readyState > 0`, `error=null`, media URL 200 `video/webm`, ba viewport PASS. Test YouTube embed reopen cũng PASS. Full Playwright 37/37, PHP 504/504, Node 38/38.
- **Dữ liệu lịch sử:** Không ghi/xóa dữ liệu `webblog`. Bài `/posts/php` vẫn thiếu URL video trong DB nên **chưa thể phát video cho bài cũ** chỉ bằng sửa code. Có hai MP4 không được post nào tham chiếu trên disk, nhưng không có metadata đủ để gắn an toàn một file với bài 56. Tác giả cần mở bài và chèn lại video gốc; sau đó kiểm tra public page. Vì vậy trạng thái lỗi nhìn thấy ở bài cụ thể là **CHƯA KHÉP**, dù regression của code path đã PASS.
- **Giới hạn:** Local PHP server trả HTTP 200 cho Range request thay vì 206; Chromium vẫn phát được MP4 hiện có khi kiểm tra trực tiếp. Chưa xác nhận playback trên production hoặc Safari/Firefox. YouTube chỉ kiểm tra embed DOM vì network ngoài môi trường QA bị hạn chế.

## BUG-004 — Avatar mặc định ở Profile trả 404 (FIXED)

- **Severity/Priority:** P2 / Medium. **Môi trường:** Playwright Chromium 1.64.1, QA server `127.0.0.1:8001`, Viewer với avatar mặc định, cả bốn viewport 390×844, 794×1024, 992×900, 1440×900.
- **Steps:** Đăng nhập Viewer QA, mở `/profile/overview`. `resources/views/profile/show.blade.php` dòng 358 render `asset('images/default-avatar.svg')` khi user chưa có avatar.
- **Expected:** Ảnh mặc định tải thành công, không có lỗi console/network cục bộ. **Actual trước fix:** `GET /images/default-avatar.svg` trả 404; `public/images/default-avatar.svg` không tồn tại. Cả bốn Playwright responsive tests của Viewer FAIL ở assertion browser diagnostics.
- **Root cause:** Modal Profile tham chiếu SVG cố định nhưng repository chưa có asset đó; component avatar dùng chung tự hiển thị chữ cái đầu và không cung cấp SVG dùng lại. **Fix:** thêm `public/images/default-avatar.svg` trung tính, cùng bảng màu xám đậm với avatar dùng chung. Không đổi Blade hoặc logic BUG-001/002/003.
- **Kiểm chứng sau fix:** URL trả **200**, `Content-Type: image/svg+xml`; Chromium tải và render ảnh (`complete` và `naturalWidth > 0`). Sáu ca Viewer liên quan, gồm bốn viewport và regression test asset, **6/6 PASS**; toàn Playwright **35/35 PASS**. Console/network cục bộ của các ca Viewer sạch. `git check-ignore` xác nhận file không bị ignore và `git status` hiển thị asset đã được thêm vào Git index. **Trạng thái: FIXED trong QA local.**
- **Giới hạn:** Các phép đo `document/body.scrollWidth` đạt ở 16 tổ hợp role/viewport; chưa suy ra mọi overlap, focus trap, layout shift hoặc production đều đạt.

Phần còn lại dưới đây là lịch sử và mô tả ba bug đã FIXED. Dòng “không có phát hiện mới” chỉ phản ánh vòng trước khi cài Playwright.

Môi trường: Laravel 13.33.0/PHP 8.3.30, `APP_ENV=testing`, SQLite `:memory:`, `Storage::fake('public')`. Trước fix: **3 audit tests FAIL**. Sau fix: **3 audit tests PASS**, full PHP suite **504 PASS**. Các mô tả Expected/Actual bên dưới giữ nguyên lịch sử tái hiện lỗi ban đầu.

Vòng QA cuối ngày 30/10/2026 chạy lại 504 PHP tests (2604 assertions), 38 Node tests, Pint và Vite build: tất cả PASS. Không có phát hiện mới được xác nhận từ các bước đã thực thi. Browser có đăng nhập, MySQL testing riêng và SMTP thật vẫn chưa kiểm; trạng thái FIXED của ba bug dựa trên automated tests SQLite, không mở rộng thành xác nhận E2E production.

| Bug ID | Severity | Root cause | File đã sửa | Kiểm chứng | Trạng thái |
|---|---|---|---|---|---|
| BUG-001 | P0 | Endpoint xóa chỉ dựa vào filename, không có owner metadata | `PostController.php` | Audit PASS; `VideoDeletionAuthorizationTest.php` 11 PASS | **FIXED** |
| BUG-002 | P1 | Admin được policy cho phép submit mọi post; controller thiếu kiểm tra status | `PostController.php` | Audit PASS; `PostSubmitTransitionTest.php` 5 PASS | **FIXED** |
| BUG-003 | P2 | Validation chỉ kiểm tra chuỗi HTML trước khi đánh giá nội dung hiển thị | `StorePostRequest.php`, `PostContentService.php` | Audit PASS; `PostContentValidationTest.php` 16 PASS | **FIXED** |

## Cách sửa và giới hạn

- **BUG-001:** Upload video ghi `filename → user_id` vào session phía server. Delete yêu cầu đúng user/session, chỉ chấp nhận basename `.mp4`/`.webm`, từ chối file đã được bất kỳ post nào tham chiếu, và chỉ xóa trong `posts/videos` trên public disk. File không được theo dõi trả 403; file do chính user upload nhưng không còn tồn tại trả 404. Admin không tự được cấp ownership upload của người khác. Không có migration. Session hết hạn làm upload tạm không còn xóa được qua endpoint; cần dọn orphan bằng tác vụ riêng nếu vận hành dài hạn.
- **BUG-002:** `submit` khóa bản ghi trong transaction rồi authorize và chỉ chuyển trạng thái Draft/Rejected. Admin tự submit Draft/Rejected → Published; author hoặc admin submit post Author hợp lệ → Pending. Submit Published/Pending không thay đổi DB; admin nhận 422, author nhận 403 theo policy hiện có. `published_at` và metadata review giữ nguyên khi request bị từ chối.
- **BUG-003:** `StorePostRequest` kiểm tra nội dung **sau** `PostContentService::cleanHtml()`, loại whitespace Unicode/zero-width và HTML entities vô hình. Text thật hợp lệ; ảnh/video local cần file tồn tại trên public disk đúng thư mục và extension; YouTube embed đúng định dạng được chấp nhận. Áp dụng cho create/update và cả Draft theo audit test gốc. Media-only từ URL ngoài không được coi là hợp lệ nếu không có text, vì hệ thống không xác minh được file ngoài.
- Full suite lần đầu sau fix có 1 failure ở test ALT ảnh dùng hai URL media không có file. Fixture test được bổ sung hai ảnh giả hợp lệ; assertion ALT giữ nguyên. Sau khi thêm kiểm tra lỗi storage, full suite 504/504 PASS.
- Browser E2E, SMTP thật, MySQL integration và concurrency thực vẫn **BLOCKED/NOT TESTED**, không được đánh dấu PASS.

## BUG-001 — Xóa video của author khác

- **Module:** editor/media. **Severity:** Critical. **Priority:** P0.
- **Precondition:** Hai author đã xác minh; author A có post dùng `posts/videos/other-author.mp4`; file tồn tại trên public disk.
- **Steps:** Đăng nhập author B, gửi `DELETE /posts/delete-video` với JSON `{"filename":"other-author.mp4"}`. Test thực hiện bằng `deleteJson(route('posts.delete-video'), ...)` và `Storage::fake`.
- **Expected:** 403; file vẫn tồn tại. **Actual:** 200 và file bị xóa. Một lượt test cô lập đã xác nhận `assertMissing`; test audit cuối cùng giữ assertion 403 + file tồn tại để dùng làm regression gate.
- **Root cause:** `PostController::deleteVideo()` chỉ kiểm tra role `create` và tên file hợp lệ rồi xóa `posts/videos/{filename}`; không kiểm tra uploader, post owner hay file đang được tham chiếu. Đường dẫn file nằm trong HTML/URL công khai nên tên file có thể biết được.
- **Code:** `app/Http/Controllers/PostController.php` dòng 484–507; `routes/web.php` route `posts.delete-video`; bằng chứng test dòng 14–30.
- **Suggested fix:** Lưu metadata owner/temporary upload token hoặc gắn media với post; chỉ cho phép xóa file do chính actor upload và chưa được post sử dụng. Kiểm tra ownership trong transaction trước khi xóa. Với nội dung đã xuất bản, dùng quy trình thay media có kiểm tra quyền post.
- **Regression risk:** Sửa quyền media có thể ảnh hưởng nút xóa video tạm của editor; test cả upload mới, xóa của mình, edit post cũ và cleanup upload thất bại.

## BUG-002 — Admin submit làm post Published quay về Pending

- **Module:** publishing/moderation. **Severity:** High. **Priority:** P1.
- **Precondition:** Admin và author; post của author có `status=published`.
- **Steps:** Admin gửi `POST /posts/{post}/submit` trực tiếp. Test dùng `postJson(route('posts.submit', $post))`.
- **Expected:** 422 hoặc từ chối workflow; post vẫn Published. **Actual:** 200; DB status thành Pending. Một lượt test cô lập đã xác nhận status Pending; test audit cuối cùng giữ assertion 422 + Published.
- **Root cause:** `PostPolicy::before()` cho admin qua mọi ability, kể cả `submit`; `PostController::submit()` chuyển mọi post không thuộc admin sang Pending mà không kiểm tra trạng thái ban đầu.
- **Code:** `app/Policies/PostPolicy.php` dòng 16–27; `app/Http/Controllers/PostController.php` dòng 629–655; test audit dòng 33–43.
- **Suggested fix:** Chặn status ngoài Draft/Rejected ở controller hoặc giới hạn ability `submit` cho admin trước khi policy `before` cho phép; giữ invariant `published_at`/status. Dùng transaction/lock khi chuyển trạng thái.
- **Regression risk:** Admin publish draft của chính mình và author submit draft/rejected vẫn phải hoạt động; approve/reject pending không thay đổi.

## BUG-003 — Lưu post chỉ chứa HTML rỗng

- **Module:** author post validation. **Severity:** Medium. **Priority:** P2.
- **Precondition:** Author, category hợp lệ.
- **Steps:** `POST /posts` với title, category, `body=<p><br></p>`, `action=draft`.
- **Expected:** 422 validation error; không tạo post. **Actual:** 302 redirect; DB có post với `body=<p><br></p>`. Một lượt test cô lập đã xác nhận record; test audit cuối cùng giữ assertion 422 + record không tồn tại.
- **Root cause:** `StorePostRequest` chỉ yêu cầu chuỗi `body`/`content`; `PostContentService::cleanHtml()` giữ cấu trúc rỗng; không kiểm tra nội dung hiển thị sau sanitize.
- **Code:** `app/Http/Requests/StorePostRequest.php` dòng 76–86; `app/Http/Controllers/PostController.php` dòng 243–275; test audit dòng 46–60.
- **Suggested fix:** Sau sanitize, kiểm tra text có nghĩa hoặc media hợp lệ theo nghiệp vụ; trả validation error trước khi lưu/upload. Quy định riêng nếu draft được phép rỗng.
- **Regression risk:** Bài chỉ chứa ảnh/video cần được xử lý theo yêu cầu nghiệp vụ; không loại nhầm các post legacy hợp lệ.

## Quan sát chưa đủ điều kiện lập bug

- `npm run build` cảnh báo `/images/login-bg.jpg` không resolve lúc build, nhưng file có tại `public/images/login-bg.jpg` và live GET trả 200 `image/jpeg`; chỉ phần hiển thị browser chưa kiểm.
- `app.debug=true` là cấu hình môi trường local; chưa xác minh deployment production. Đây là điểm kiểm tra triển khai, chưa phải lỗ hổng production đã chứng minh.
- Không có screenshot hoặc browser log vì browser host không khả dụng. Không có bằng chứng SMTP thật vì không gửi thư tới người dùng thật.
