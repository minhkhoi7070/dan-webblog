# CloudBlog — đề xuất theo mức ưu tiên

## Cập nhật sau Playwright

1. **BUG-004/P2 đã FIXED:** thêm SVG avatar mặc định; URL 200 `image/svg+xml`, Viewer 4/4 viewport PASS và toàn Playwright 35/35 PASS. Theo dõi asset trong Git khi review/merge.
2. **Hoàn tất các bước còn thiếu:** modal/focus/accessibility, browser race và concurrency MySQL, FK/unique/rollback, Admin CMS đầy đủ. QA MySQL, storage và browser đã sẵn; không cần cấp thêm schema để tiếp tục các kiểm tra này.
3. **SMTP thật:** chỉ còn cần mailbox test được chỉ định và quyền gửi; luồng QA mail log đã PASS nhưng không chứng minh SMTP delivery.

Các mục bên dưới được viết trước vòng Playwright; tham chiếu `QA_REGRESSION_REPORT.md` cho trạng thái mới nhất.

## Đã xử lý trong đợt Fix

1. **BUG-001:** Session phía server theo dõi owner của upload tạm; endpoint xóa từ chối file người khác và file đang được post tham chiếu. Audit và 11 regression tests PASS.
2. **BUG-002:** Submit kiểm tra trạng thái sau khi khóa bản ghi trong transaction. Audit và 5 regression tests PASS.
3. **BUG-003:** Validation kiểm tra HTML sau sanitize, Unicode whitespace và media có thật. Audit và 16 regression tests PASS.

## P1 — trước khi xác nhận sẵn sàng bảo vệ đồ án

4. **Thực hiện E2E thật trên DB test riêng.** Tạo viewer/author/admin bằng factory/seed dành cho test, mailbox SMTP test và dữ liệu post disposable. Browser host hiện không khả dụng; không chạy mutation trên dữ liệu thật. Kiểm tra workflow chính, Ajax Back/Forward, modal và 4 viewport.

## P2 — trước bản demo cuối

5. **Dọn upload tạm mất session** bằng quy trình vận hành riêng nếu cần; endpoint hiện từ chối xóa sau khi session owner không còn. Kiểm tra race thực của upload/delete và submit trên MySQL test riêng.
6. **Mở rộng test boundary/security:** OTP leading zero, hết hạn, replay, cross-account; CSRF live; email case/trim; upload giả MIME/extension; status tampering; locked session.
7. **Đối soát dữ liệu MySQL bằng truy vấn read-only** trong bản sao/test DB: orphan FK, duplicate pivot, status/published_at, counts và transaction behavior. Metadata hiện có không chứng minh dữ liệu không lỗi.
8. **Đo performance thực:** query count/N+1 feed, detail, favorites, activity, admin dashboard; network requests, CLS/layout shift, TipTap init và memory khi AJAX navigation lặp.

## P3 — hoàn thiện QA vận hành

9. Kiểm tra hiển thị asset `/images/login-bg.jpg` trong trình duyệt; live HTTP đã trả 200 `image/jpeg`, warning Vite chưa chứng minh ảnh hỏng.
10. Xác nhận cấu hình production `APP_DEBUG=false`, HTTPS/cookie/session và email transport trước triển khai. Local hiện `app.debug=true` là bình thường trong phát triển.
11. Bổ sung coverage driver nếu cần phần trăm thật. Hiện không có Xdebug/PCOV, vì vậy không báo cáo tỷ lệ giả.

BUG-001/002/003/004 và luồng parser video đã được kiểm chứng lại trong QA local: 504 PHP, 38 Node và 37 Playwright tests PASS. Bài `/posts/php` có wrapper rỗng đã lưu trước sửa; tác giả cần chèn lại video gốc để khôi phục nội dung hiển thị, không tự gán một trong các MP4 orphan. MySQL QA đã chạy workflow E2E nhưng constraint/rollback/concurrency chưa kiểm đầy đủ; SMTP thật vẫn cần mailbox test. Không thay đổi `.env` hoặc schema phát triển.
