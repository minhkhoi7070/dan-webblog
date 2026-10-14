# BlogMNM — Báo Cáo Cập Nhật Authentication Flow & Chức Năng Trở Thành Tác Giả (Viewer to Author)

## Tổng Quan Thực Hiện
Theo định hướng của nhóm về trải nghiệm sản phẩm Blog/News hiện đại và loại bỏ hoàn toàn trang Dashboard mặc định của Laravel ("You're logged in!") cho người dùng thông thường, toàn bộ luồng xác thực và phân quyền tương tác đã được hoàn thiện, bảo mật và kiểm thử tự động đạt 100%.

---

## 1. Chi Tiết Thay Đổi Login & Registration Redirect

### Luồng Chuyển Hướng Mới
- **Viewer đăng nhập**: Chuyển hướng trực tiếp về trang chủ `route('home')` (hoặc URL dự định nếu có session intended).
- **Author đăng nhập**: Chuyển hướng trực tiếp về trang chủ `route('home')` (hoặc URL dự định nếu có session intended).
- **Admin đăng nhập**: Chuyển hướng trực tiếp vào trung tâm quản trị `route('admin.dashboard')`.
- **Người dùng đăng ký mới**: Mặc định nhận vai trò `viewer` và chuyển hướng thẳng về `route('home')`.
- **Đường dẫn `/dashboard` mặc định**: Đã được cấu hình tự động điều hướng:
  - Nếu là Admin: chuyển sang `admin.dashboard`.
  - Nếu là Viewer/Author: chuyển sang `home`.

---

## 2. Chức Năng Nâng Cấp: "Trở Thành Tác Giả" (Viewer to Author)

### Cơ Chế Server-Side & Bảo Mật
1. **Endpoint**: `POST /become-author` (tên route: `author.become`) bảo vệ bằng middleware `auth` và kiểm tra CSRF token.
2. **Server-side Logic strictly enforced**:
   - Chỉ người dùng có `$user->role === 'viewer'` mới được phép chuyển đổi sang `author`.
   - Mọi payload/tham số từ client (kể cả khi cố tình inject `role=admin` hay `role=author`) đều bị bỏ qua hoàn toàn.
   - Admin và Author khi gọi endpoint này sẽ được chuyển hướng an toàn mà không bị thay đổi vai trò.
3. **Phản hồi sau kích hoạt**:
   - Hệ thống cập nhật `role = 'author'` trong database.
   - Chuyển hướng ngay tới trang soạn thảo `route('posts.create')` kèm thông báo flash: *"Chúc mừng bạn đã trở thành tác giả! Hãy bắt đầu viết bài đầu tiên."*

---

## 3. Giao Diện Người Dùng (UI/UX) Theo Từng Vai Trò

### A. Sidebar Máy Tính / Tablet (`x-sidebar`)
- **Author / Admin**:
  - Hiển thị nút **"Viết bài"** với biểu tượng dấu cộng (`+`), liên kết tới `route('posts.create')`.
  - Author có thêm mục **"Thống kê bài viết"** (`/author/posts/stats`).
  - Admin có thêm bảng điều khiển quản trị (`/admin/dashboard`, bài viết, người dùng, chuyên mục, bình luận).
- **Viewer**:
  - Không hiển thị "Viết bài".
  - Hiển thị nút bấm nổi bật **"Trở thành tác giả"** kèm biểu tượng bút viết sáng tạo, kích hoạt `POST /become-author`.

### B. Header / Composer Feed Tại Trang Chủ (`posts.index`)
- **Author / Admin**:
  - Lời gợi ý: *"Có gì mới trong thế giới công nghệ hôm nay?"*
  - Nút CTA: **"Viết bài"** dẫn tới `route('posts.create')`.
- **Viewer**:
  - Lời gợi ý: *"Khám phá bài viết và nâng cấp tài khoản để bắt đầu sáng tạo nội dung..."*
  - Nút CTA: **"Trở thành tác giả"** (form POST an toàn).

### C. Khu Vực Tài Khoản / Hồ Sơ Cá Nhân (`profile.show`)
- Với Viewer, hiển thị thẻ banner riêng: *"Trở thành tác giả BlogMNM — Bắt đầu chia sẻ kiến thức, viết bài thảo luận công nghệ và tương tác với cộng đồng độc giả BlogMNM"* kèm nút bấm kích hoạt tức thì.

### D. Thanh Điều Hướng Di Động (`x-mobile-nav`)
- Nút tương tác chính tự động chuyển đổi giữa biểu tượng **"Viết bài"** (cho Author/Admin) và biểu tượng **"Trở thành tác giả"** (cho Viewer).

---

## 4. Quản Trị & Luồng Xuất Bản Bài Viết (CMS Workflow)

- **Nguyên tắc Data Contract**:
  - Author tạo bài viết: Trạng thái luôn khởi tạo là `draft` (Bản nháp), không thể tự động publish.
  - Author gửi bài: Chuyển trạng thái sang `pending` (Chờ duyệt).
  - Admin duyệt bài: Chỉ Admin mới có quyền `approve` (`published`) hoặc `reject` (`rejected`).
- **Phân quyền Admin**:
  - Viewer và Author bị chặn truy cập hoàn toàn vào `/admin` và `/admin/dashboard` với mã lỗi `403 Forbidden`.

---

## 5. Kết Quả Kiểm Thử (Automated Tests)

### Bộ Test Mới: `tests/Feature/ViewerToAuthorTest.php`
Bao gồm đầy đủ 10 kịch bản yêu cầu:
1. `test_01_viewer_login_redirects_to_home`: Viewer login chuyển hướng về `home` **[PASS]**
2. `test_02_author_login_redirects_to_home`: Author login chuyển hướng về `home` **[PASS]**
3. `test_03_admin_login_redirects_to_admin_dashboard`: Admin login chuyển hướng về `admin.dashboard` **[PASS]**
4. `test_04_viewer_sees_become_author_cta`: Viewer thấy CTA "Trở thành tác giả" trên Home và Profile **[PASS]**
5. `test_05_author_sees_write_post_cta`: Author thấy CTA "Viết bài", không thấy "Trở thành tác giả" **[PASS]**
6. `test_06_viewer_after_becoming_author_can_access_create_post`: Viewer sau khi nâng cấp truy cập được `posts.create` **[PASS]**
7. `test_07_viewer_cannot_access_create_post_before_becoming_author`: Viewer bị chặn `403` khi cố vào `posts.create` trước khi nâng cấp **[PASS]**
8. `test_08_viewer_cannot_self_escalate_to_admin`: Viewer không thể tự nâng cấp thành `admin` qua request payload **[PASS]**
9. `test_09_author_cannot_self_publish_posts`: Author không thể tự ý publish bài viết (luôn lưu dưới dạng `draft`) **[PASS]**
10. `test_10_admin_dashboard_still_functions_and_blocks_viewer_and_author`: Admin Dashboard hoạt động chuẩn và chặn Viewer/Author **[PASS]**

### Toàn Bộ Test Suite Dự Án
```bash
php artisan test
```
- **Tổng số tests**: 154 tests
- **Passed**: 154 tests (100% PASS)
- **Assertions**: 566 assertions
- **Thời gian thực thi**: ~7.4 giây

### Code Style (Laravel Pint)
```bash
vendor/bin/pint --test
```
- **Kết quả**: Passed (0 linting / code formatting issues)

---

## 6. Danh Sách Các File Đã Chỉnh Sửa & Bổ Sung

| File | Thay đổi chính |
| :--- | :--- |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Phân nhánh redirect sau login: admin -> `admin.dashboard`, viewer/author -> `home` |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Cập nhật redirect sau đăng ký người dùng mới về `home` |
| `app/Models/User.php` | Bổ sung helper method `isViewer(): bool` |
| `app/Http/Controllers/ProfileController.php` | Bổ sung method `becomeAuthor(Request $request)` an toàn, chống leo thang quyền |
| `routes/web.php` | Khai báo route `POST /become-author`, điều hướng lại route `/dashboard` |
| `resources/views/components/sidebar.blade.php` | Hiển thị "Viết bài" cho Author/Admin và nút "Trở thành tác giả" cho Viewer |
| `resources/views/components/mobile-nav.blade.php` | Đồng bộ nút Viết bài / Trở thành tác giả trên giao diện mobile |
| `resources/views/posts/index.blade.php` | Cập nhật banner thông báo flash, composer CTA "Viết bài" / "Trở thành tác giả" |
| `resources/views/profile/show.blade.php` | Thêm thẻ kêu gọi nâng cấp "Trở thành tác giả BlogMNM" trong trang cá nhân |
| `tests/Feature/Auth/AuthenticationTest.php` | Cập nhật test login redirect về `home` và bổ sung test cho author/admin |
| `tests/Feature/Auth/RegistrationTest.php` | Cập nhật test registration redirect về `home` |
| `tests/Feature/QASuiteTest.php` | Cập nhật các assertion redirect tương ứng |
| `tests/Feature/ViewerToAuthorTest.php` | Tạo mới bộ test 10 ca kiểm thử bảo mật và luồng người dùng |

---

## 7. Các Điểm Đã Review & Đảm Bảo An Toàn
- [x] **Mass Assignment**: `role` không nằm trong danh sách validated của `ProfileUpdateRequest` hay `RegisteredUserController`.
- [x] **Role Escalation**: Client không thể gửi bất kỳ giá trị nào để biến tài khoản thành Admin.
- [x] **CSRF Protection**: Form "Trở thành tác giả" sử dụng `@csrf` hợp lệ.
- [x] **Zero Regression**: 154/154 bài test đều vượt qua, không phá vỡ bất kỳ API hay JSON contracts hiện có.
