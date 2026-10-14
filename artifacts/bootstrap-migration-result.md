# BÁO CÁO KẾT QUẢ DI CHUYỂN FRONTEND TỪ TAILWIND CSS SANG BOOTSTRAP 5
**Dự án:** BlogMNM  
**Thời gian hoàn thành:** 01/10/2026  
**Trạng thái:** Hoàn tất 100% (Build: PASS | PHPUnit: 167/167 PASS | Pint: PASS)

---

## 1. THÔNG SỐ CÀI ĐẶT & GÓI DEPENDENCY

### 1.1. Bootstrap & Core Dependencies Added
- **Bootstrap:** `^5.3.8` (được cài đặt qua `npm`, quản lý bởi `package.json` và đóng gói qua Vite).
- **Popper Core:** `@popperjs/core` `^2.11.8` (hỗ trợ native Bootstrap dropdowns, tooltips, popovers).
- **Vite Integration:** Giữ nguyên Vite 8 và plugin `@laravel/vite-plugin` để biên dịch JS & CSS cục bộ.

### 1.2. Tailwind Dependencies & Configs Removed
- **Đã gỡ bỏ khỏi `package.json`:**
  - `tailwindcss`
  - `@tailwindcss/forms`
  - `@tailwindcss/vite`
  - `alpinejs` (thay thế bằng vanilla JS + Bootstrap Modal / Dropdown / Collapse APIs).
- **Đã xóa file cấu hình:**
  - `tailwind.config.js` (xóa hoàn toàn).
- **Đã cập nhật cấu hình:**
  - `postcss.config.js`: Chỉ còn giữ `autoprefixer`, không còn plugin tailwindcss.

---

## 2. KIẾN TRÚC DESIGN SYSTEM & THEME ENGINE

### 2.1. Centralized CSS & Design Tokens (`resources/css/app.css`)
- **Bootstrap 5 Base:** Import `@import "bootstrap/dist/css/bootstrap.min.css";`.
- **CSS Variables & Monochrome Palette:**
  - `--color-bg`: Nền đen `#000000` (Dark) / Trắng `#FFFFFF` (Light).
  - `--color-surface`: Nền card `#101010` (Dark) / Trắng `#FFFFFF` (Light).
  - `--color-surface-hover`: Hover `#1A1A1A` (Dark) / Xám nhạt `#F4F4F5` (Light).
  - `--color-text`: Chữ `#FFFFFF` (Dark) / Đen `#09090B` (Light).
  - `--color-text-secondary`: Chữ phụ `#A1A1AA` (Dark) / Xám `#71717A` (Light).
  - `--color-border`: Viền `#27272A` (Dark) / `#E4E4E7` (Light).
- **Threads-inspired & Minimalist Typography:** Font Inter tiêu chuẩn cao cấp, hỗ trợ micro-transitions mượt mà.
- **Thành phần UI tùy biến trên nền Bootstrap 5:**
  - Cards, Buttons (`rounded-pill`, `.btn-dark`, `.btn-outline-secondary`), Form controls (`form-control`, `form-select`), Modals, Alerts, Badges (`bg-primary-subtle`, `bg-warning-subtle`, etc.), Tables (`table table-hover align-middle`).

### 2.2. Theme Engine & Chuyển đổi Dark/Light
- Sử dụng thuộc tính `data-theme="dark"` / `data-theme="light"` trên thẻ `<html>`.
- Tự động nhận diện và lưu trạng thái vào `localStorage` qua `BlogMNMTheme` (`resources/js/theme.js`).
- Chống FOUC (Flash of Unstyled Content) bằng inline IIFE script được nạp ngay trong `<head>` của tất cả layouts:
  ```html
  <script>
      (function() {
          var saved = localStorage.getItem('blogmnm-theme');
          var theme = saved || 'dark';
          document.documentElement.setAttribute('data-theme', theme);
          if (theme === 'dark') document.documentElement.classList.add('dark');
      })();
  </script>
  ```

---

## 3. DANH SÁCH FILE VÀ COMPONENT ĐÃ DI CHUYỂN

### 3.1. Master Layouts (`resources/views/layouts/`)
1. [layouts/app.blade.php](file:///c:/laragon/www/webblog/resources/views/layouts/app.blade.php): Layout người dùng đăng nhập (sidebar trái, mobile bottom-bar, main content trung tâm).
2. [layouts/public.blade.php](file:///c:/laragon/www/webblog/resources/views/layouts/public.blade.php): Layout công khai khách/độc giả BlogMNM.
3. [layouts/admin.blade.php](file:///c:/laragon/www/webblog/resources/views/layouts/admin.blade.php): Layout quản trị viên riêng biệt (topbar điều hướng, sub-nav tabs, container quản trị).
4. [layouts/guest.blade.php](file:///c:/laragon/www/webblog/resources/views/layouts/guest.blade.php): Layout trang xác thực (login, register, forgot/reset password).
5. [layouts/navigation.blade.php](file:///c:/laragon/www/webblog/resources/views/layouts/navigation.blade.php): Navbar Breeze mặc định chuyển sang Bootstrap 5 Navbar.

### 3.2. Toàn bộ 25 Shared Blade Components (`resources/views/components/`)
1. `sidebar.blade.php`: Thanh bên cố định bên trái trên desktop, dùng Bootstrap flexbox, icons và action links.
2. `mobile-nav.blade.php`: Thanh điều hướng cố định dưới đáy màn hình trên mobile (`fixed-bottom`).
3. `dropdown.blade.php`: Dropdown native Bootstrap 5 (`data-bs-toggle="dropdown"`).
4. `dropdown-link.blade.php`: Item dropdown với class `.dropdown-item`.
5. `modal.blade.php`: Modal native Bootstrap 5 (`modal`, `modal-dialog`, `modal-content`), hỗ trợ sự kiện `open-modal`.
6. `primary-button.blade.php`: Button phong cách monochrome chuẩn (`btn btn-dark rounded-pill fw-bold`).
7. `secondary-button.blade.php`: Button viền (`btn btn-outline-secondary rounded-pill`).
8. `danger-button.blade.php`: Button xóa/hủy hiểm (`btn btn-outline-danger rounded-pill`).
9. `text-input.blade.php`: Ô nhập liệu Bootstrap (`form-control`, `is-invalid`).
10. `input-label.blade.php`: Nhãn trường (`form-label fw-bold small`).
11. `input-error.blade.php`: Thông báo lỗi validation (`invalid-feedback` / `text-danger small`).
12. `badge.blade.php`: Thẻ tag/trạng thái (`badge rounded-pill`).
13. `stat-card.blade.php`: Card chỉ số thống kê (`card border shadow-sm rounded-3 p-3`).
14. `avatar.blade.php`: Ảnh đại diện tròn (`rounded-circle`).
15. `user-meta.blade.php`: Khối thông tin tác giả và thời gian bài viết.
16. `like-button.blade.php`: Nút Like bài viết, tương thích tương tác AJAX.
17. `favorite-button.blade.php`: Nút Lưu / Bookmark bài viết.
18. `follow-button.blade.php`: Nút Theo dõi tác giả.
19. `post-action-bar.blade.php`: Thanh tương tác Like, Favorite, Comment count dưới mỗi bài viết.
20. `post-card.blade.php`: Thẻ bài viết dạng feed Threads-like với layout 2 cột (avatar cột trái, nội dung cột phải).
21. `empty-state.blade.php`: Trạng thái rỗng chuẩn Bootstrap (`card text-center p-5 border-dashed`).
22. `auth-session-status.blade.php`: Alert trạng thái phiên đăng nhập (`alert alert-success`).
23. `nav-link.blade.php`: Link điều hướng với active state.
24. `responsive-nav-link.blade.php`: Link điều hướng responsive.
25. `application-logo.blade.php`: SVG Logo BlogMNM.

### 3.3. Public Guest & Blog Views (`resources/views/posts/`, `authors/`)
1. [posts/index.blade.php](file:///c:/laragon/www/webblog/resources/views/posts/index.blade.php): Trang chủ Blog/News với thanh tìm kiếm, danh sách chuyên mục ngang cuộn mượt, feed bài viết và phân trang.
2. [posts/show.blade.php](file:///c:/laragon/www/webblog/resources/views/posts/show.blade.php): Chi tiết bài viết, thẻ bài viết, box tác giả, bài viết liên quan, cây bình luận đa tầng (nested comments) và form gửi bình luận/phản hồi.
3. [authors/show.blade.php](file:///c:/laragon/www/webblog/resources/views/authors/show.blade.php): Trang tác giả, thông tin tiểu sử, số liệu thống kê và danh sách bài viết đã xuất bản.

### 3.4. Authentication Views (`resources/views/auth/`, `admin/auth/`)
1. [auth/login.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/login.blade.php): Đăng nhập độc giả / tác giả.
2. [auth/register.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/register.blade.php): Đăng ký tài khoản độc giả mới.
3. [auth/forgot-password.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/forgot-password.blade.php): Quên mật khẩu.
4. [auth/reset-password.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/reset-password.blade.php): Đặt lại mật khẩu.
5. [auth/verify-email.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/verify-email.blade.php): Xác thực email.
6. [auth/confirm-password.blade.php](file:///c:/laragon/www/webblog/resources/views/auth/confirm-password.blade.php): Xác nhận mật khẩu trước khi thao tác bảo mật.
7. [admin/auth/login.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/auth/login.blade.php): Đăng nhập trang Quản trị viên tại `/admin/login`.

### 3.5. Author CMS Views (`resources/views/author/posts/`)
1. [author/posts/create.blade.php](file:///c:/laragon/www/webblog/resources/views/author/posts/create.blade.php): Tạo bài viết mới (chọn chuyên mục, tags, upload thumbnail, lưu draft hoặc gửi pending).
2. [author/posts/edit.blade.php](file:///c:/laragon/www/webblog/resources/views/author/posts/edit.blade.php): Chỉnh sửa bài viết, banner hiển thị lý do từ chối nếu bị rejected, nút gửi lại thẩm định (`resubmit`).
3. [author/posts/stats.blade.php](file:///c:/laragon/www/webblog/resources/views/author/posts/stats.blade.php): Bảng điều khiển tác giả với danh sách bài viết, lọc trạng thái, chỉ số tương tác (views, likes, comments).

### 3.6. Viewer Interaction & Profile Views (`resources/views/favorites/`, `activity/`, `profile/`)
1. [favorites/index.blade.php](file:///c:/laragon/www/webblog/resources/views/favorites/index.blade.php): Danh sách bài viết đã lưu kèm phân trang Bootstrap.
2. [activity/index.blade.php](file:///c:/laragon/www/webblog/resources/views/activity/index.blade.php): Nhật ký hoạt động tương tác (bình luận, like).
3. [profile/show.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/show.blade.php): Trang cá nhân, nút nâng cấp vai trò "Trở thành tác giả" cho Viewer.
4. [profile/edit.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/edit.blade.php): Trang cài đặt tài khoản.
5. [profile/partials/appearance-settings.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/partials/appearance-settings.blade.php): Thiết lập giao diện Sáng / Tối trực quan với radio cards Bootstrap.
6. [profile/partials/update-profile-information-form.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/partials/update-profile-information-form.blade.php): Đổi tên, email.
7. [profile/partials/update-password-form.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/partials/update-password-form.blade.php): Đổi mật khẩu bảo mật.
8. [profile/partials/delete-user-form.blade.php](file:///c:/laragon/www/webblog/resources/views/profile/partials/delete-user-form.blade.php): Xóa tài khoản với Bootstrap modal xác nhận.

### 3.7. Admin Dashboard & Moderation Views (`resources/views/admin/`)
1. [admin/dashboard.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/dashboard.blade.php): Thống kê tổng thể (người dùng, lượt xem, bình luận, tiến trình xuất bản bài viết, danh sách chờ duyệt nhanh).
2. [admin/categories/index.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/categories/index.blade.php): Danh sách chuyên mục, tìm kiếm và thao tác xóa/sửa.
3. [admin/categories/create.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/categories/create.blade.php): Thêm chuyên mục mới với validation Bootstrap.
4. [admin/categories/edit.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/categories/edit.blade.php): Chỉnh sửa tên, slug chuyên mục.
5. [admin/comments/index.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/comments/index.blade.php): Bảng kiểm duyệt bình luận (phân loại Tất cả / Chờ duyệt / Đã duyệt / Spam), duyệt nhanh, báo spam, xóa.
6. [admin/posts/index.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/posts/index.blade.php): Quản lý và duyệt bài viết toàn trang (lọc theo trạng thái và chuyên mục).
7. [admin/posts/show.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/posts/show.blade.php): Chi tiết bài viết thẩm định, nút Duyệt bài hoặc Từ chối với form nhập lý do phản hồi cho tác giả.
8. [admin/users/index.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/users/index.blade.php): Quản lý danh sách người dùng (lọc vai trò, trạng thái, khóa/mở khóa tài khoản).
9. [admin/users/show.blade.php](file:///c:/laragon/www/webblog/resources/views/admin/users/show.blade.php): Xem hồ sơ chi tiết, lịch sử bài viết và bình luận gần đây của người dùng.

---

## 4. XỬ LÝ PHÂN TRANG (PAGINATION)

- **Cấu hình Laravel Paginator:**
  Đã cấu hình trong [app/Providers/AppServiceProvider.php](file:///c:/laragon/www/webblog/app/Providers/AppServiceProvider.php):
  ```php
  use Illuminate\Pagination\Paginator;

  public function boot(): void
  {
      Paginator::useBootstrapFive();
  }
  ```
- **Xóa các view phân trang Tailwind:**
  - Đã xóa `resources/views/vendor/pagination/tailwind.blade.php`.
  - Đã xóa `resources/views/vendor/pagination/simple-tailwind.blade.php`.
- **Kiểm tra hoạt động:**
  - Tất cả các view gọi `{{ $posts->links() }}`, `{{ $categories->links() }}`, `{{ $comments->links() }}`, `{{ $users->links() }}` đều hiển thị chuẩn thẻ `<nav><ul class="pagination">...</ul></nav>` của Bootstrap 5, không gây lệch trang hay sinh thêm trang thừa.

---

## 5. THIẾT KẾ ĐÁP ỨNG (RESPONSIVE BEHAVIOR)

Đã kiểm tra và tối ưu hiển thị trên 4 mốc màn hình chuẩn:
- **Mobile (< 576px / 375px):**
  - Sidebar ẩn (`d-none d-sm-flex`), chuyển sang thanh điều hướng đáy `mobile-nav` (`fixed-bottom`).
  - Feed bài viết hiển thị full-width, không có overflow ngang (`overflow-x: hidden`).
  - Các bảng admin và form dài co giãn linh hoạt với wrapper `table-responsive`.
- **Tablet (576px - 991px / 768px):**
  - Cột bên trái thu gọn hiển thị icon (`px-2 px-lg-3`).
  - Nội dung chính căn giữa cân đối.
- **Desktop (992px - 1199px / 1024px):**
  - Sidebar cố định, hiển thị đầy đủ icon + nhãn chức năng.
  - Feed tối ưu bề rộng đọc bài (~680px) tập trung trải nghiệm thị giác.
- **Large Desktop (>= 1200px / 1440px):**
  - Giao diện cân đối, sắc nét, không bị kéo dãn bất hợp lý.

---

## 6. KẾT QUẢ KIỂM THỬ & CHẤT LƯỢNG MÃ NGUỒN

### 6.1. Build Frontend (`npm run build`)
```
vite v8.3.1 building client environment for production...
transforming...
✓ 63 modules transformed.
rendering chunks...
computing gzip size...
public/build/manifest.json              0.33 kB │ gzip:  0.16 kB
public/build/assets/app-BcuMcGfa.css  235.93 kB │ gzip: 32.55 kB
public/build/assets/app-DAWNhJeS.js    90.00 kB │ gzip: 26.56 kB
✓ built in 299ms
Status: EXIT 0 (Success)
```

### 6.2. Kiểm thử Tự động Backend (`php artisan test`)
```
{"tool":"phpunit","result":"passed","tests":167,"passed":167,"assertions":607,"duration_ms":6782}
Status: 167 passed (100% PASS), 0 failures, 0 errors
```

### 6.3. Tiêu chuẩn Mã nguồn (`vendor/bin/pint --dirty --format agent`)
```
{"tool":"pint","result":"passed"}
Status: All modified PHP files pass Pint code styling.
```

---

## 7. BẢO TOÀN DỮ LIỆU & QUY TẮC NGHIỆP VỤ

1. **Database & Migrations:** Giữ nguyên 100%, không chạy `migrate:fresh`, không làm mất mát dữ liệu hiện có trong Laragon MySQL.
2. **Models & Relationships:** Không thay đổi bất kỳ relationship nào (Post, User, Category, Comment, Tag, Interaction).
3. **Route Contracts & Middleware:** Tất cả tên route, URL endpoints, controller methods và middleware (`auth`, `role:admin`, `role:author,admin`) hoạt động ổn định và chính xác.
4. **Post Workflow:**
   - Author: Draft $\rightarrow$ Pending $\rightarrow$ (Admin duyệt) $\rightarrow$ Published.
   - Author: Rejected $\rightarrow$ Author chỉnh sửa $\rightarrow$ Resubmit $\rightarrow$ Pending.
   - Author tuyệt đối không tự publish; chỉ Admin có quyền Approve và Reject (kèm nhập lý do).
5. **JSON Contract cho Tương tác:**
   - Các hành động AJAX Like, Favorite, Follow, Comment trả về đúng cấu trúc JSON (`success`, `liked`, `likes_count`, `favorited`, `following`, v.v.) và được xử lý trơn tru bằng `resources/js/interactions.js`.

---

## 8. CÁC ĐIỂM TỒN ĐỌNG (REMAINING ISSUES)

- **Không còn điểm tồn đọng:** Toàn bộ views của ứng dụng đã được rà soát bằng công cụ tìm kiếm tĩnh, xác nhận không còn bất kỳ utility class Tailwind nào (`dark:`, `space-y-`, `space-x-`, `min-h-screen`, `max-w-`, v.v.).
- Tất cả các trang đã sẵn sàng trình chiếu theo đúng tiêu chuẩn đồ án: **Laravel + Blade Template + Bootstrap 5 + JavaScript + Vite**.
