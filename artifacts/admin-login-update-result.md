# BlogMNM — Báo Cáo Triển Khai Trang Đăng Nhập Quản Trị Viên Riêng Biệt (Dedicated Admin Login)

## Tổng Quan Nhiệm Vụ
Đã tách biệt hoàn toàn cổng đăng nhập Quản trị viên (`/admin/login`) khỏi luồng đăng nhập người dùng thông thường (`/login`). Cổng đăng nhập Admin được thiết kế chuyên biệt, mang phong cách Cổng Quản trị trang trọng, đáp ứng đầy đủ nguyên tắc bảo mật, phân quyền nghiêm ngặt và không làm ảnh hưởng đến trải nghiệm của Viewer/Author.

---

## 1. Các Route Mới Được Bổ Sung

Trong file `routes/web.php`:

| HTTP Method | URI | Tên Route | Middleware | Controller & Method | Mục đích |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **GET** | `/admin/login` | `admin.login` | `web` | `Admin\AuthController@create` | Hiển thị form đăng nhập chuyên dụng cho Admin |
| **POST** | `/admin/login` | `admin.login.store` | `web` | `Admin\AuthController@store` | Xác thực đăng nhập và kiểm tra quyền Quản trị viên |

> **Lưu ý**: Nhóm route đăng nhập này nằm ngoài nhóm route bảo vệ `['auth', 'role:admin']`, cho phép người dùng truy cập để xác thực danh tính ban đầu.

---

## 2. Controller & Request Xử Lý Xác Thực

### `App\Http\Controllers\Admin\AuthController`
- **Method `create()`**:
  - Kiểm tra nếu người dùng đã đăng nhập và có quyền Admin (`$user->isAdmin()`), tự động chuyển hướng thẳng vào `admin.dashboard`.
  - Nếu chưa đăng nhập hoặc chưa phải admin, hiển thị view `admin.auth.login`.
- **Method `store(LoginRequest $request)`**:
  - Tái sử dụng cơ chế bảo mật cốt lõi qua `$request->authenticate()`:
    - Chống Brute-force qua Rate Limiter (tối đa 5 lần thử trước khi khóa tạm thời).
    - Xác thực mật khẩu Bcrypt an toàn.
    - Kiểm tra cờ tài khoản bị khóa (`is_locked`).
  - **Kiểm soát phân quyền nghiêm ngặt**:
    - Sau khi xác thực danh tính, kiểm tra trực tiếp từ database: `if (! $user || ! $user->isAdmin())`.
    - Tuyệt đối không hard-code theo email hay tên tài khoản.
    - Nếu tài khoản KHÔNG phải admin (Viewer hoặc Author cố tình đăng nhập tại cổng Admin):
      1. Ngay lập tức gọi `Auth::logout()`.
      2. Hủy session hiện tại (`$request->session()->invalidate()`).
      3. Tạo lại CSRF token (`$request->session()->regenerateToken()`).
      4. Ném `ValidationException` với thông báo: **"Tài khoản này không có quyền quản trị."**
    - Nếu tài khoản là Admin:
      1. Tái tạo session ID an toàn (`$request->session()->regenerate()`).
      2. Chuyển hướng vào `admin.dashboard`.

---

## 3. Giao Diện Đăng Nhập Quản Trị Viên (View Mới)

### File: `resources/views/admin/auth/login.blade.php`
- **Phong cách thiết kế**:
  - Tuân thủ ngôn ngữ thiết kế tối giản, hiện đại của BlogMNM: bo góc mượt mà (`rounded-3xl`), đường viền tinh tế (`border-[var(--color-border)]`), hỗ trợ Dark/Light mode chuẩn xác (không bị chớp nháy FOUC).
  - Huy hiệu bảo mật riêng: Badge `Quản trị viên` với biểu tượng Shield màu đỏ đô/rose nhẹ nhàng.
  - Tiêu đề chính: **"Đăng nhập Quản trị viên"**.
  - Mô tả: **"Khu vực dành riêng cho quản trị hệ thống BlogMNM"**.
- **Các trường biểu mẫu**:
  - Email (placeholder: `admin@blogmnm.test`, tự động autofocus).
  - Mật khẩu (placeholder: `••••••••`).
  - Checkbox ghi nhớ đăng nhập ("Ghi nhớ đăng nhập").
  - Nút bấm chính: **"Đăng nhập"** toàn chiều rộng, độ tương phản cao.
  - Liên kết điều hướng chân trang: **"← Quay lại BlogMNM"** dẫn về trang chủ `route('home')`.
- **Loại bỏ hoàn toàn**:
  - "Đăng ký ngay".
  - Các CTA chuyển đổi vai trò của Viewer/Author.
  - Các nội dung thừa không phục vụ mục đích quản trị.
- **Xử lý trạng thái lỗi trực quan**:
  - Hiển thị banner cảnh báo riêng biệt khi tài khoản không có quyền quản trị.
  - Hiển thị lỗi form khi sai email, sai mật khẩu hoặc tài khoản bị khóa.

---

## 4. Bảo Toàn Trải Nghiệm Người Dùng Thông Thường (`/login`)
- Route `/login` tiếp tục phục vụ Viewer và Author.
- Luồng chuyển hướng sau khi đăng nhập qua `/login`:
  - Viewer -> Trang chủ (`/`)
  - Author -> Trang chủ (`/`)
  - Admin (nếu đăng nhập tại `/login`) -> Tự động chuyển hướng vào `admin.dashboard`
- Luồng đăng ký tài khoản mới (`/register`) được giữ nguyên vẹn 100%.

---

## 5. Kết Quả Kiểm Thử (Automated Test Suite)

### Bộ Test Chuyên Biệt Mới: `tests/Feature/AdminLoginTest.php` (10/10 PASS)
1. `test_01_admin_login_screen_can_be_rendered`: GET `/admin/login` trả về HTTP 200, hiển thị đúng các tiêu đề và không có link đăng ký **[PASS]**
2. `test_02_admin_login_successful_redirects_to_admin_dashboard`: Admin đăng nhập thành công được chuyển hướng vào `admin.dashboard` **[PASS]**
3. `test_03_viewer_login_via_admin_login_is_rejected`: Viewer đăng nhập qua `/admin/login` bị từ chối với thông báo "Tài khoản này không có quyền quản trị." **[PASS]**
4. `test_04_author_login_via_admin_login_is_rejected`: Author đăng nhập qua `/admin/login` bị từ chối với thông báo "Tài khoản này không có quyền quản trị." **[PASS]**
5. `test_05_admin_can_access_admin_dashboard`: Admin truy cập `/admin/dashboard` thành công (HTTP 200) **[PASS]**
6. `test_06_viewer_accessing_admin_dashboard_is_forbidden`: Viewer truy cập `/admin/dashboard` bị chặn với lỗi `403 Forbidden` **[PASS]**
7. `test_07_author_accessing_admin_dashboard_is_forbidden`: Author truy cập `/admin/dashboard` bị chặn với lỗi `403 Forbidden` **[PASS]**
8. `test_08_admin_login_fails_with_wrong_password`: Đăng nhập với mật khẩu sai bị từ chối xác thực **[PASS]**
9. `test_09_locked_admin_cannot_login`: Tài khoản admin bị khóa (`is_locked = true`) không thể đăng nhập **[PASS]**
10. `test_10_csrf_protection_is_active_on_admin_login_routes`: Route POST `/admin/login` áp dụng đầy đủ nhóm middleware `web` (bao gồm CSRF verification) **[PASS]**

### Toàn Bộ Test Suite Dự Án
```bash
php artisan test
```
- **Tổng số tests**: 164 tests
- **Passed**: 164 tests (100% PASS)
- **Assertions**: 596 assertions
- **Tình trạng**: Không có lỗi hồi quy (Zero Regressions)

### Tiêu Chuẩn Mã Nguồn (Laravel Pint)
```bash
vendor/bin/pint --test
```
- **Kết quả**: Passed (0 lỗi định dạng)

---

## 6. Danh Sách Các File Đã Thay Đổi & Tạo Mới

| File | Hành động | Nội dung thay đổi |
| :--- | :--- | :--- |
| `app/Http/Controllers/Admin/AuthController.php` | Tạo mới | Controller xử lý hiển thị và xác thực đăng nhập Admin |
| `resources/views/admin/auth/login.blade.php` | Tạo mới | Giao diện cổng đăng nhập chuyên biệt cho Quản trị viên |
| `app/View/Components/GuestLayout.php` | Chỉnh sửa | Hỗ trợ thuộc tính `$title` cho layout khách |
| `resources/views/layouts/guest.blade.php` | Chỉnh sửa | Động hóa tiêu đề tab trình duyệt và chuẩn hóa liên kết "Quay lại BlogMNM" |
| `routes/web.php` | Chỉnh sửa | Khai báo các route `GET/POST /admin/login` |
| `tests/Feature/AdminLoginTest.php` | Tạo mới | Bộ 10 bài test kiểm thử tự động toàn diện cho cổng Admin Login |
