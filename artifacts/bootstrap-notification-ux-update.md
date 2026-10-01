# Báo Cáo Chuyển Đổi Native Alert/Confirm Sang Bootstrap Modal & Toast UX

## 1. Tổng quan công việc
Hệ thống BlogMNM đã được nâng cấp toàn diện từ việc sử dụng các hộp thoại trình duyệt nguyên bản (`window.alert()`, `window.confirm()`, `window.prompt()`, `onsubmit="return confirm(...)"`) sang hệ thống **Bootstrap 5.3 Modal + Bootstrap Toast/Alert** chuẩn hoá, hiện đại, hỗ trợ toàn diện Dark/Light mode và Accessibility (A11y).

---

## 2. Nguyên tắc bảo toàn hệ thống (Zero Business Logic Impact)
- **Database:** Giữ nguyên 100% cấu trúc schema và quan hệ dữ liệu.
- **Routes & Controllers:** Không thay đổi bất kỳ route, controller business logic hay tham số request nào.
- **Authentication & Authorization:** Giữ nguyên toàn bộ logic phân quyền, policy, middleware, và vai trò người dùng (Admin, Author, Viewer).
- **Validation Rules:** Giữ nguyên toàn bộ cơ chế form validation của Bootstrap và server-side validation.
- **API Contracts:** Giữ nguyên toàn bộ payload JSON request và response của AJAX endpoints.

---

## 3. Danh sách các Native Dialogs đã được rà soát và chuyển đổi

| STT | File nguồn | Dòng ban đầu | Thao tác | Native Dialog trước đây | Giải pháp thay thế Bootstrap |
|---|---|---|---|---|---|
| 1 | `resources/views/admin/users/index.blade.php` | 134 | Admin khóa tài khoản người dùng | `onsubmit="return confirm('XÁC NHẬN: Bạn có chắc chắn muốn khóa tài khoản người dùng \'{{ $user->name }}\'?');"` | Chuyển sang Bootstrap Confirm Modal với target box (Tên, Email, hậu quả khóa tài khoản) |
| 2 | `resources/views/admin/users/index.blade.php` | 146 | Admin mở khóa tài khoản người dùng | `onsubmit="return confirm('Mở khóa tài khoản người dùng \'{{ $user->name }}\'?');"` | Chuyển sang Bootstrap Confirm Modal (Tên, Email, xác nhận mở khóa) |
| 3 | `resources/views/admin/users/show.blade.php` | 14 | Admin mở khóa tài khoản tại trang chi tiết | `onsubmit="return confirm('Mở khóa tài khoản người dùng \'{{ $user->name }}\'?');"` | Chuyển sang Bootstrap Confirm Modal |
| 4 | `resources/views/admin/users/show.blade.php` | 21 | Admin khóa tài khoản tại trang chi tiết | `onsubmit="return confirm('XÁC NHẬN: Bạn có chắc chắn muốn khóa tài khoản người dùng \'{{ $user->name }}\'?');"` | Chuyển sang Bootstrap Confirm Modal |
| 5 | `resources/views/admin/posts/index.blade.php` | 177 | Duyệt bài viết (Approve) | `onsubmit="return confirm('Duyệt và xuất bản ngay bài viết \'{{ $post->title }}\'?');"` | Chuyển sang Bootstrap Confirm Modal (type success, btn-success) |
| 6 | `resources/views/admin/posts/index.blade.php` | 185 | Từ chối bài viết (Reject) | `onsubmit="const reason = prompt('Nhập lý do từ chối bài viết:'); if(!reason) return false; this.rejection_reason.value = reason; return true;"` | Chuyển sang Bootstrap Confirm Modal tích hợp trường nhập `rejection_reason` (xóa bỏ `prompt()`) |
| 7 | `resources/views/admin/posts/index.blade.php` | 199 | Xóa vĩnh viễn bài viết (Admin) | `onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn bài viết \'{{ $post->title }}\'?');"` | Chuyển sang Bootstrap Confirm Modal (type danger, cảnh báo mất dữ liệu) |
| 8 | `resources/views/admin/posts/show.blade.php` | 21 | Duyệt bài viết (Approve) | `onsubmit="return confirm('Duyệt và xuất bản ngay bài viết này?');"` | Chuyển sang Bootstrap Confirm Modal |
| 9 | `resources/views/admin/posts/show.blade.php` | 31 | Từ chối bài viết (Reject) | `onsubmit="const reason = prompt('Nhập lý do từ chối bài viết:'); ..."` | Chuyển sang Bootstrap Confirm Modal tích hợp trường nhập lý do từ chối |
| 10 | `resources/views/admin/posts/show.blade.php` | 41 | Xóa bài viết (Admin) | `onsubmit="return confirm('XÁC NHẬN: Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này?');"` | Chuyển sang Bootstrap Confirm Modal |
| 11 | `resources/views/admin/comments/index.blade.php` | 147 | Admin xóa bình luận | `onsubmit="return confirm('Xóa bình luận này? Hành động không thể hoàn tác.');"` | Chuyển sang Bootstrap Confirm Modal |
| 12 | `resources/views/admin/dashboard.blade.php` | 239 | Admin xóa bình luận từ Dashboard | `onsubmit="return confirm('Xóa bình luận này?');"` | Chuyển sang Bootstrap Confirm Modal |
| 13 | `resources/views/admin/categories/index.blade.php` | 63 | Admin xóa chuyên mục | `onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa chuyên mục \'{{ $category->name }}\'? Hành động này không thể hoàn tác.');"` | Chuyển sang Bootstrap Confirm Modal |
| 14 | `resources/views/author/posts/edit.blade.php` | 219 | Tác giả xóa bài viết của mình | `onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác.');"` | Chuyển sang Bootstrap Confirm Modal |
| 15 | `resources/views/author/posts/stats.blade.php` | 243 | Tác giả xóa bài viết từ danh sách thống kê | `onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?');"` | Chuyển sang Bootstrap Confirm Modal |
| 16 | `resources/views/posts/show.blade.php` | 199 | Xóa bình luận bài viết | `onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');"` | Chuyển sang Bootstrap Confirm Modal |
| 17 | `resources/views/posts/show.blade.php` | 260 | Xóa phản hồi bình luận | `onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?');"` | Chuyển sang Bootstrap Confirm Modal |

---

## 4. Kiến trúc Thành phần (Reusable Components & Interactions)

### 4.1. Blade Component: `resources/views/components/confirm-modal.blade.php`
- Cấu trúc chuẩn Bootstrap 5.3:
  - **Header:** Tiêu đề "Xác nhận thao tác", icon động theo phân loại (`danger`, `warning`, `success`), nút đóng X.
  - **Body:**
    - Tiêu đề thao tác (`#confirm-modal-title`).
    - Lời nhắc xác nhận (`#confirm-modal-message`).
    - Khối chi tiết đối tượng (`#confirm-modal-target-box`): Tên người dùng / tên bài viết / nội dung bình luận và metadata (email, chuyên mục, tác giả...).
    - Khối giải thích hậu quả (`#confirm-modal-description-box`).
    - Khối nhập thông tin bổ sung (`#confirm-modal-prompt-box`): Tự động hiển thị khi form có yêu cầu nhập liệu (ví dụ lý do từ chối bài viết), thay thế hoàn toàn cho hàm `prompt()`.
  - **Footer:** Nút "Hủy" và nút Action tùy biến (màu sắc, nhãn text) có tích hợp spinner loading (`.spinner-border-sm`).

### 4.2. Blade Component: `resources/views/components/toast.blade.php`
- Container cố định góc phải bên dưới màn hình: `#global-toast-container` (`position-fixed bottom-0 end-0 p-3`, `z-index: 1090`).
- Tự động bắt và hiển thị flash message từ backend Laravel session:
  - `session('success')` -> Toast thành công (xanh lá).
  - `session('error')` -> Toast lỗi (đỏ).
  - `session('warning')` -> Toast cảnh báo (vàng).
  - `session('status')` -> Toast trạng thái.
- Hỗ trợ auto-dismiss sau 4 giây, nút đóng thủ công và responsive trên mobile.

### 4.3. JavaScript Hub: `resources/js/interactions.js`
- Quản lý global confirmation flow qua cơ chế declarative `data-confirm="true"`:
  - Chặn sự kiện submit form: `form[data-confirm="true"]`.
  - Trích xuất cấu hình: `data-confirm-title`, `data-confirm-message`, `data-confirm-target-name`, `data-confirm-target-meta`, `data-confirm-description`, `data-confirm-btn-text`, `data-confirm-btn-class`, `data-confirm-type`, `data-confirm-prompt`.
  - Hiển thị Modal qua Bootstrap Modal API.
  - Khi người dùng xác nhận: Chuyển nút sang loading state `"Đang xử lý..."`, hiển thị spinner, disable toàn bộ nút (Hủy, Submit, Close X), ngăn đóng nhầm modal trong lúc submit (`hide.bs.modal`), gửi request.
- Hàm toàn cục `window.showToast(message, type)` phục vụ AJAX interactions (Like, Bookmark, Follow, Comment).

### 4.4. Tương thích Dark Mode & Light Mode (`resources/css/app.css`)
- Tokenized styling đảm bảo:
  - `.modal-content` sử dụng biến `--color-surface` và viền `--color-border`.
  - `.btn-close` tự động đổi màu hiển thị trong Dark Mode (`filter: invert(...)`).
  - Màu nền Toast sắc nét, tương phản chuẩn WCAG trong cả Dark Mode và Light Mode.

---

## 5. Danh sách Files đã thay đổi

1. `resources/views/components/confirm-modal.blade.php` (Mới: Component Bootstrap Modal dùng chung)
2. `resources/views/components/toast.blade.php` (Mới: Component Bootstrap Toast dùng chung)
3. `resources/views/layouts/public.blade.php` (Tích hợp Confirm Modal và Toast container)
4. `resources/views/layouts/app.blade.php` (Tích hợp Confirm Modal và Toast container)
5. `resources/views/layouts/guest.blade.php` (Tích hợp Toast container cho auth flash messages)
6. `resources/js/app.js` (Export `window.bootstrap = bootstrap`)
7. `resources/js/interactions.js` (Engine điều khiển modal confirmation, accessibility, loading state, showToast)
8. `resources/css/app.css` (Cập nhật theme rules cho Modal và Toast)
9. `resources/views/admin/users/index.blade.php` (Thay thế confirm khóa/mở khóa)
10. `resources/views/admin/users/show.blade.php` (Thay thế confirm khóa/mở khóa)
11. `resources/views/admin/posts/index.blade.php` (Thay thế confirm duyệt, xóa và prompt từ chối bài viết)
12. `resources/views/admin/posts/show.blade.php` (Thay thế confirm duyệt, xóa và prompt từ chối bài viết)
13. `resources/views/admin/comments/index.blade.php` (Thay thế confirm xóa bình luận)
14. `resources/views/admin/dashboard.blade.php` (Thay thế confirm xóa bình luận)
15. `resources/views/admin/categories/index.blade.php` (Thay thế confirm xóa chuyên mục)
16. `resources/views/author/posts/edit.blade.php` (Thay thế confirm xóa bài viết)
17. `resources/views/author/posts/stats.blade.php` (Thay thế confirm xóa bài viết)
18. `resources/views/posts/show.blade.php` (Thay thế confirm xóa bình luận và phản hồi)
19. `tests/Feature/BootstrapModalConfirmationTest.php` (Mới: Bộ test tự động kiểm tra modal attributes và loại bỏ hoàn toàn native dialogs)

---

## 6. Kết quả Kiểm thử & Build

### 6.1. Kiểm tra sạch mã nguồn (Grep Search for Native Dialogs)
- Lệnh: `grep_search` với pattern `\b(alert|confirm|prompt)\s*\(` trên toàn bộ thư mục `resources/` và `app/`.
- Kết quả: **0 kết quả tìm thấy**. Toàn bộ native dialogs đã được dọn sạch.

### 6.2. Kiểm thử tự động (PHPUnit Tests)
- Lệnh: `php artisan test`
- Kết quả: **187 / 187 tests PASSED (720 assertions)**, thời gian thực thi: ~6.9s.
  - Toàn bộ 8 test ca chuyên sâu trong `BootstrapModalConfirmationTest` pass 100%.
  - Toàn bộ test suites hiện hữu (Comment, Post, Auth, Roles, Admin, Author, Viewer) đều pass 100%.

### 6.3. Kiểm tra định dạng mã nguồn (Laravel Pint)
- Lệnh: `vendor/bin/pint --test`
- Kết quả: **Passed (0 issues)**.

### 6.4. Biên dịch Frontend (Vite Build)
- Lệnh: `npm run build`
- Kết quả:
  - `public/build/manifest.json`: 0.33 kB
  - `public/build/assets/app-IGXJlD6h.css`: 236.73 kB
  - `public/build/assets/app-BuUcBuE5.js`: 96.31 kB
  - **Build thành công 100% trong 281ms mà không có bất kỳ warning/lỗi nào**.
