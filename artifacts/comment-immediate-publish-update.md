# Báo Cáo Cập Nhật Quy Trình Xuất Bản Bình Luận Trực Tiếp (Immediate Publish Comment Workflow) — BlogMNM

> **Ngày thực hiện:** 01/10/2026  
> **Dự án:** BlogMNM (Laravel 11, PHP 8.3)  
> **Trạng thái:** Hoàn tất, 100% tests passed (179/179), Pint pass, Vite build pass.

---

## 1. Business Rules Chính Thức

Hệ thống bình luận trên BlogMNM đã được chuyển đổi hoàn toàn sang mô hình **Xuất bản Trực tiếp (Immediate Publish)** thay vì mô hình tiền kiểm duyệt (pre-moderation):

1. **Độc giả (Viewer) đã đăng nhập và không bị khóa:**
   - Được tạo bình luận và câu trả lời (reply).
   - Bình luận mới luôn được tạo với `status = approved`.
   - Hiển thị ngay lập tức trên giao diện bài viết mà không cần chờ Admin duyệt trước.
2. **Tác giả (Author) đã đăng nhập và không bị khóa:**
   - Được tạo bình luận và câu trả lời.
   - Bình luận mới luôn có `status = approved` và hiển thị ngay.
3. **Phản hồi phân cấp (Nested Replies):**
   - Phản hồi mới kế thừa cùng cơ chế: `status = approved`, xuất hiện ngay dưới bình luận cha.
4. **Khách vãng lai (Guest):**
   - Không được phép bình luận hay phản hồi.
   - Yêu cầu đăng nhập trước (chặn 401 với AJAX request, redirect đến trang đăng nhập với web request).
5. **Tài khoản bị khóa (Locked User):**
   - Bị chặn toàn bộ khả năng tạo bình luận, phản hồi và thao tác quản lý (HTTP 403 Forbidden).
6. **Quản trị viên (Admin):**
   - Không còn phải duyệt bình luận thủ công trước khi xuất bản.
   - Giữ nguyên toàn quyền quản lý hậu kiểm (post-moderation) toàn hệ thống: đánh dấu Spam, gỡ bỏ vi phạm, thay đổi trạng thái theo contract hiện tại.
7. **Tác giả bài viết (Author):**
   - Có quyền quản trị, xóa và điều duyệt các bình luận xuất hiện trên bài viết của chính mình.
   - Không có quyền can thiệp vào bình luận trên bài viết của tác giả khác.

---

## 2. Chi Tiết Thay Đổi Mã Nguồn

### 2.1. Model [`app/Models/Comment.php`](file:///c:/laragon/www/webblog/app/Models/Comment.php)
- Thiết lập `$attributes = ['status' => 'approved']` để đảm bảo bất kỳ thực thể `Comment` mới nào khởi tạo đều mặc định mang trạng thái đã duyệt.
- Bổ sung các query scopes: `scopeApproved()`, `scopeSpam()`, `scopePending()`.
- Bổ sung các helper methods: `isApproved()`, `isSpam()`, `isPending()`.

### 2.2. Controller [`app/Http/Controllers/CommentController.php`](file:///c:/laragon/www/webblog/app/Http/Controllers/CommentController.php)
- Cập nhật phương thức `store()`: Khởi tạo comment với `'status' => 'approved'` được gán cứng từ backend.
- Tuyệt đối không nhận hoặc ghi đè `status` từ client request.
- Giữ nguyên JSON contract và định dạng dữ liệu phản hồi, bổ sung trường `status` trong đối tượng `comment` trả về để client dễ đồng bộ trạng thái.

### 2.3. Form Request [`app/Http/Requests/StoreCommentRequest.php`](file:///c:/laragon/www/webblog/app/Http/Requests/StoreCommentRequest.php)
- `authorize()`: Ngăn chặn triệt để người dùng chưa đăng nhập hoặc bị khóa (`$this->user() !== null && ! $this->user()->is_locked`).
- `rules()`: Chỉ cho phép `post_id`, `body`, `parent_id`. Trường `status` không nằm trong danh sách validation và bị loại bỏ tự động.

### 2.4. Policy [`app/Policies/CommentPolicy.php`](file:///c:/laragon/www/webblog/app/Policies/CommentPolicy.php)
- `before()`:
  - Nếu `$user->is_locked === true` &rarr; lập tức trả về `false` (chặn toàn bộ quyền).
  - Nếu `$user->isAdmin() === true` &rarr; trả về `true` (Admin có quyền quản trị hậu kiểm toàn hệ thống).
- `create()`: Chỉ cho phép khi người dùng không bị khóa (`! $user->is_locked`).
- `delete()`: Cho phép người viết comment, tác giả sở hữu bài viết (`$user->id === $comment->post?->user_id`), hoặc Admin.
- `moderate()`: Cho phép tác giả sở hữu bài viết hoặc Admin điều duyệt bình luận. Ngăn chặn tác giả khác can thiệp.

### 2.5. Admin Dashboard [`app/Http/Controllers/Admin/DashboardController.php`](file:///c:/laragon/www/webblog/app/Http/Controllers/Admin/DashboardController.php) & View [`resources/views/admin/dashboard.blade.php`](file:///c:/laragon/www/webblog/resources/views/admin/dashboard.blade.php)
- Điều chỉnh khái niệm từ "Bình luận chờ kiểm duyệt" sang **"Bình luận cần xử lý"**.
- Hàng đợi hậu kiểm trên dashboard tải các bình luận thuộc diện `whereIn('status', ['pending', 'spam'])` cần Admin xử lý (bình luận Spam hoặc bình luận treo từ dữ liệu cũ).
- Cập nhật card thống kê: Hiển thị chỉ số "Cần xử lý (Spam/Treo)" thay cho quy trình kiểm duyệt bắt buộc.

### 2.6. Admin Comments Index [`resources/views/admin/comments/index.blade.php`](file:///c:/laragon/www/webblog/resources/views/admin/comments/index.blade.php)
- Loại bỏ quy trình cũ "User comment -> Admin approve -> public".
- Giao diện trực quan, tinh gọn: không chứa các banner giải thích thừa thải, đảm bảo trải nghiệm quản trị gọn gàng.
- Sắp xếp và phân loại rõ ràng các bộ lọc:
  - Tất cả
  - Đã duyệt (Công khai)
  - Spam
  - Chờ xử lý (Thủ công / Treo)

### 2.7. Tương tác Frontend [`resources/js/interactions.js`](file:///c:/laragon/www/webblog/resources/js/interactions.js)
- Xử lý mượt mà khi gửi bình luận qua AJAX:
  - Tự động xóa thông báo trống (empty placeholder) khi bình luận đầu tiên xuất hiện.
  - Hiển thị ngay lập tức bình luận / câu trả lời mới tại đầu danh sách.
  - Cập nhật huy hiệu đếm số lượng bình luận (`.comment-count-badge`).
  - Reset form nhập liệu và thông báo toast thành công "Đã đăng bình luận thành công!".

### 2.8. Dữ liệu hiện có & Lệnh bảo toàn dữ liệu
- Tạo Artisan Command [`app/Console/Commands/PublishPendingCommentsCommand.php`](file:///c:/laragon/www/webblog/app/Console/Commands/PublishPendingCommentsCommand.php): `php artisan comments:publish-pending`.
- Tạo Seeder [`database/seeders/PublishPendingCommentsSeeder.php`](file:///c:/laragon/www/webblog/database/seeders/PublishPendingCommentsSeeder.php).
- Đã chạy thành công chuyển đổi an toàn 88 bình luận pending demo cũ sang `approved` mà **hoàn toàn giữ nguyên các bình luận spam** (ID 86).
- Tuyệt đối không thay đổi cấu trúc bảng (schema) và không xóa database.

---

## 3. Danh Sách Kiểm Thử Tự Động (12 Test Cases Bắt Buộc)

Đã tạo bộ kiểm thử tính năng hoàn chỉnh tại [`tests/Feature/CommentImmediatePublishTest.php`](file:///c:/laragon/www/webblog/tests/Feature/CommentImmediatePublishTest.php):

| STT | Tên Test Case | Mục đích kiểm thử | Kết quả |
|:---:|:---|:---|:---:|
| 1 | `test_viewer_can_create_comment_and_it_is_approved_immediately` | Viewer đăng bình luận & nhận ngay status = approved | **PASSED** |
| 2 | `test_author_can_create_comment_and_it_is_approved_immediately` | Author đăng bình luận & nhận ngay status = approved | **PASSED** |
| 3 | `test_viewer_can_reply_and_reply_is_approved_immediately` | Viewer trả lời (reply) & reply được duyệt ngay | **PASSED** |
| 4 | `test_guest_cannot_create_comment` | Khách vãng lai bị chặn (401 JSON / Redirect Login) | **PASSED** |
| 5 | `test_locked_user_cannot_create_comment` | Tài khoản bị khóa bị chặn 403 Forbidden | **PASSED** |
| 6 | `test_newly_created_comment_appears_immediately_in_public_post_detail` | Bình luận vừa đăng xuất hiện ngay trên trang công khai | **PASSED** |
| 7 | `test_newly_created_reply_appears_immediately` | Phản hồi vừa đăng xuất hiện ngay trên trang công khai | **PASSED** |
| 8 | `test_spam_comment_remains_hidden_from_public` | Bình luận spam bị ẩn hoàn toàn khỏi trang công khai | **PASSED** |
| 9 | `test_admin_can_still_manage_comment_after_publication` | Admin vẫn có toàn quyền đánh dấu spam, xóa sau khi đăng | **PASSED** |
| 10 | `test_author_can_moderate_comments_on_own_post` | Tác giả được quyền xóa/điều duyệt bình luận trên bài của mình | **PASSED** |
| 11 | `test_author_cannot_moderate_comments_on_another_authors_post` | Tác giả không thể xóa/điều duyệt bình luận bài tác giả khác | **PASSED** |
| 12 | `test_creating_a_normal_comment_does_not_create_a_pending_moderation_item` | Đăng bình luận bình thường không làm tăng mục pending | **PASSED** |

---

## 4. Kết Quả Chạy Lệnh Kiểm Định Toàn Hệ Thống

### 4.1. Test Suite (`php artisan test`)
```text
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Unit\ModelRelationshipTest
   PASS  Tests\Feature\ActivityTest
   PASS  Tests\Feature\AdminArchitectureAuditTest
   PASS  Tests\Feature\AdminCategoryTest
   PASS  Tests\Feature\AdminDashboardTest
   PASS  Tests\Feature\AdminLoginUpdateTest
   PASS  Tests\Feature\AdminPostTest
   PASS  Tests\Feature\AdminTest
   PASS  Tests\Feature\AdminUserTest
   PASS  Tests\Feature\AuthorPostStatsTest
   PASS  Tests\Feature\AuthorPostTest
   PASS  Tests\Feature\CommentImmediatePublishTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\FavoriteTest
   PASS  Tests\Feature\GuestAccessTest
   PASS  Tests\Feature\InteractionTest
   PASS  Tests\Feature\PaginationAuditTest
   PASS  Tests\Feature\PostPolicyTest
   PASS  Tests\Feature\PublicPostDisplayTest
   PASS  Tests\Feature\QASuiteTest
   PASS  Tests\Feature\RoleMiddlewareTest
   PASS  Tests\Feature\SecurityAuditTest
   PASS  Tests\Feature\ThemeSwitchTest
   PASS  Tests\Feature\UserManagementSecurityAuditTest
   PASS  Tests\Feature\ViewerInteractionTest
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\EmailVerificationTest
   PASS  Tests\Feature\Auth\PasswordConfirmationTest
   PASS  Tests\Feature\Auth\PasswordResetTest
   PASS  Tests\Feature\Auth\PasswordUpdateTest
   PASS  Tests\Feature\Auth\RegistrationTest

Tests:    179 passed (659 assertions)
Duration: ~7.05s
```

### 4.2. Code Style Formatter (`vendor/bin/pint --test`)
```text
{"tool":"pint","result":"passed"}
```
*(100% tệp tin PHP tuân thủ tiêu chuẩn mã nguồn Laravel Pint)*

### 4.3. Frontend Asset Build (`npm run build`)
```text
> vite build
vite v8.3.1 building client environment for production...
transforming...
✓ 63 modules transformed.
rendering chunks...
computing gzip size...
public/build/manifest.json              0.33 kB │ gzip:  0.16 kB
public/build/assets/app-BcuMcGfa.css  235.93 kB │ gzip: 32.55 kB
public/build/assets/app-xPGVvH8e.js    90.06 kB │ gzip: 26.59 kB
✓ built in 298ms
```

---

## 5. Danh Sách Các Tệp Tin Đã Chỉnh Sửa & Bổ Sung

1. **[`app/Models/Comment.php`](file:///c:/laragon/www/webblog/app/Models/Comment.php)**: Thiết lập `$attributes = ['status' => 'approved']`, bổ sung status scopes & methods.
2. **[`app/Http/Controllers/CommentController.php`](file:///c:/laragon/www/webblog/app/Http/Controllers/CommentController.php)**: Tạo bình luận và phản hồi với `status = approved`.
3. **[`app/Policies/CommentPolicy.php`](file:///c:/laragon/www/webblog/app/Policies/CommentPolicy.php)**: Bổ sung `before()`, `create()`, `moderate()`, bảo vệ RBAC cho tác giả và chặn tài khoản bị khóa.
4. **[`database/factories/CommentFactory.php`](file:///c:/laragon/www/webblog/database/factories/CommentFactory.php)**: Cập nhật mặc định factory với `status = approved`.
5. **[`app/Http/Controllers/Admin/DashboardController.php`](file:///c:/laragon/www/webblog/app/Http/Controllers/Admin/DashboardController.php)**: Đổi truy vấn hàng đợi sang `whereIn('status', ['pending', 'spam'])`.
6. **[`resources/views/admin/dashboard.blade.php`](file:///c:/laragon/www/webblog/resources/views/admin/dashboard.blade.php)**: Đổi "Bình luận chờ kiểm duyệt" thành "Bình luận cần xử lý", hiển thị huy hiệu và thao tác chuẩn.
7. **[`resources/views/admin/comments/index.blade.php`](file:///c:/laragon/www/webblog/resources/views/admin/comments/index.blade.php)**: Cập nhật tiêu đề, bộ lọc tabs, banner giải thích luồng Immediate Publish.
8. **[`resources/js/interactions.js`](file:///c:/laragon/www/webblog/resources/js/interactions.js)**: Tối ưu tương tác DOM AJAX cho bình luận tức thời.
9. **[`app/Console/Commands/PublishPendingCommentsCommand.php`](file:///c:/laragon/www/webblog/app/Console/Commands/PublishPendingCommentsCommand.php)**: Lệnh Artisan chuyển đổi dữ liệu pending an toàn.
10. **[`database/seeders/PublishPendingCommentsSeeder.php`](file:///c:/laragon/www/webblog/database/seeders/PublishPendingCommentsSeeder.php)**: Seeder hỗ trợ xuất bản bình luận pending.
11. **[`tests/Feature/CommentImmediatePublishTest.php`](file:///c:/laragon/www/webblog/tests/Feature/CommentImmediatePublishTest.php)**: Bộ test chuyên biệt với 12 test cases đáp ứng trọn vẹn yêu cầu nghiệp vụ.
