# BlogMNM — Báo Cáo Thẩm Định Hiệu Chỉnh Cuối Cùng (STEP 17.5)
## Final Report Correction & Validation Audit

**Dự án:** BlogMNM — Nền tảng Blog đa người dùng phong cách Threads trên nền tảng Laravel  
**Thời gian thẩm định:** 2026-09-29  
**Tài liệu hiệu chỉnh:**
- `docs/report/final-report.md` (1,076 dòng, 76,220 bytes)
- `artifacts/final-report-draft.md` (1,076 dòng, 76,220 bytes — đồng bộ 100%)
- `artifacts/testing-result.md` (Đã cập nhật phiên bản Laravel 13 & PHPUnit 12)  
**Căn cứ đánh giá:** `artifacts/final-report-review.md` (Kết quả rà soát độc lập STEP 17.4), `Template_BaoCao_DoAn_LTMNM.docx`, mã nguồn thực tế và kết quả chạy kiểm thử tự động.

---

## 1. Kiểm Tra Cấu Trúc Khung Báo Cáo (Structure Verification)

Báo cáo tuân thủ nghiêm ngặt khung mẫu đồ án chính thức gồm đúng **5 Chương**, không thêm bớt chương mục:

| Phần / Chương | Mục con theo chuẩn | Trạng thái | Đánh giá tuân thủ |
| :--- | :--- | :---: | :--- |
| **FRONT MATTER** | Trang bìa, Mục lục, Bảng phân công, Danh mục hình ảnh | ĐẦY ĐỦ | Đầy đủ thông tin đề tài, bảng phân công nhiệm vụ 1 thành viên (100%), danh mục 26 hình ảnh/sơ đồ. |
| **CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI** | 1.1. Lý do chọn đề tài<br/>1.2. Mục tiêu đồ án | ĐẦY ĐỦ | Không tạo thêm mục ngoài template; đã loại bỏ ký hiệu LaTeX toán học. |
| **CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG** | 2.1. Sơ đồ Use Case (UML)<br/>2.2. Thiết kế CSDL (ERD)<br/>2.3. Mô tả chi tiết các bảng dữ liệu | ĐẦY ĐỦ | 4 Actors, 25 Use Cases, ERD 9 bảng thực thể nghiệp vụ, từ điển dữ liệu 9 bảng. |
| **CHƯƠNG 3: CÔNG NGHỆ ÁP DỤNG VÀ MÔ TẢ CHỨC NĂNG** | 3.1. Công nghệ & công cụ<br/>3.2. Chức năng cốt lõi<br/>3.3. Chức năng nâng cao & sáng tạo | ĐẦY ĐỦ | Kiến trúc 3-tier, 6 nhóm chức năng cốt lõi (A-F), Monochrome design & Zero-Flash engine, Defense-in-Depth 5 tầng. |
| **CHƯƠNG 4: HƯỚNG DẪN CÀI ĐẶT VÀ VẬN HÀNH** | 4.1 đến 4.6 (Yêu cầu, Cài đặt, Tài khoản, Vận hành, Quản trị, Sự cố) | ĐẦY ĐỦ | Đã bổ sung đoạn văn mở đầu chương (S-01), 11 bước cài đặt, hướng dẫn 3 vai trò. |
| **CHƯƠNG 5: TỔNG KẾT** | 5.1. Kết quả đạt được<br/>5.2. Hạn chế và Hướng phát triển | ĐẦY ĐỦ | Minh chứng kiểm thử 142/142 tests đạt, Laravel Pint 0 lỗi, 5 hạn chế và 6 hướng phát triển. |
| **CHƯƠNG 6 / CHƯƠNG 7** | *Không có* | TUÂN THỦ | **KHÔNG** tạo thêm Chương 6 hay Chương 7. |

---

## 2. Tổng Hợp Các Điểm Đã Hiệu Chỉnh (Issues Fixed Summary)

Toàn bộ các thiếu sót và điểm chưa chuẩn xác được chỉ ra tại `final-report-review.md` đã được xử lý triệt để theo thứ tự ưu tiên:

### A. Mức độ CRITICAL (Nghiêm trọng — Sai lệch so với mã nguồn thực tế)

| Mã | Hạng mục | Thực trạng trước khi sửa | Đã hiệu chỉnh thực tế |
| :---: | :--- | :--- | :--- |
| **C-01** | Thông báo tài khoản bị khóa (`AUTH-07`) | Trích dẫn tiếng Anh: *"Your account has been locked by an administrator."* (Chuỗi này không tồn tại trong mã nguồn). | Cập nhật chính xác theo tiếng Việt tại `LoginRequest.php` dòng 62: `throw ValidationException::withMessages(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);`. |
| **C-02** | Khối phòng thủ chống khóa Quản trị viên (`SEC-05`) | Báo cáo cũ mô tả một biểu thức gộp sai thực tế: `if ($user->role === 'admin' \|\| $user->id === Auth::id())`. | Cập nhật chính xác hai khối chốt chặn bảo vệ độc lập từ `Admin\UserController.php` (dòng 82–91): Chốt 1 `if ($user->id === $request->user()->id)` chống tự khóa, Chốt 2 `if ($user->isAdmin())` chống khóa đồng cấp. |

---

### B. Mức độ HIGH (Độ ưu tiên cao — Chi tiết kỹ thuật cốt lõi)

| Mã | Hạng mục | Thực trạng trước khi sửa | Đã hiệu chỉnh thực tế |
| :---: | :--- | :--- | :--- |
| **C-03** | Quy trình xử lý tài khoản khóa (`AUTH-07`) | Báo cáo cũ chỉ nói `Auth::logout()` mà bỏ qua các bước bảo mật phiên quan trọng. | Bổ sung đầy đủ 3 thao tác then chốt: vô hiệu hóa phiên (`$this->session()->invalidate()`), tái tạo CSRF token (`$this->session()->regenerateToken()`) và ghi nhận vào bộ đếm rate limiter (`RateLimiter::hit($this->throttleKey())`). |
| **C-05** | Quy tắc kiểm tra bài viết (`StorePostRequest`) | Báo cáo cũ khẳng định *"nội dung tối thiểu 10 ký tự"* (không có trong rules). | Loại bỏ khẳng định sai; xác nhận trường `content` / `body` bắt buộc có dữ liệu thông qua quy tắc `required_without` và không giới hạn độ dài tối thiểu. |
| **G-01** | Tồn tại `@tailwindcss/vite ^4.0.0` trong `package.json` | Khai báo xung đột giữa Tailwind v4 plugin và stack Tailwind v3.4.19 đang chạy. | Bổ sung ghi chú kỹ thuật rõ ràng trong Mục 3.1: Đây là tàn dư cấu hình ban đầu; pipeline đóng gói thực tế hoạt động thuần nhất trên Tailwind CSS v3 thông qua PostCSS/Vite. |
| **G-02** | Cơ chế Zero-Flash Theme Engine | Báo cáo cũ nêu script đọc cấu hình hệ điều hành `prefers-color-scheme`. | Sửa đúng thực tế mã nguồn tại `layouts/app.blade.php`: Script đọc `localStorage.getItem('blogmnm-theme')`, nếu chưa thiết lập thì mặc định ngay về chủ đề Tối (`'dark'`), loại bỏ tham chiếu `prefers-color-scheme`. |

---

### C. Mức độ MEDIUM (Độ ưu tiên trung bình — Tính nhất quán & Thẩm mỹ)

| Mã | Hạng mục | Thực trạng trước khi sửa | Đã hiệu chỉnh thực tế |
| :---: | :--- | :--- | :--- |
| **D-01** | Phiên bản PHPUnit | Báo cáo ghi `v12.5.12`. | Cập nhật chính xác theo Composer: `PHPUnit v12.5.35`. |
| **D-02** | Phiên bản Laravel Pint | Báo cáo ghi `v1.27.0`. | Cập nhật chính xác theo Composer: `Laravel Pint v1.32.1`. |
| **D-03** | Ký hiệu phiên bản kiểm thử kép | Báo cáo ghi `PHPUnit 12.5.12 / 11.5.3` gây mâu thuẫn. | Đồng nhất về một phiên bản duy nhất: `PHPUnit 12.5.35` thực thi qua `php artisan test`. |
| **D-04** | Tiêu đề artifact `testing-result.md` | Dòng 5 ghi "Laravel 12" và "PHPUnit 11.5.3". | Cập nhật thành: `Laravel 13.33.0 / PHP 8.3.30 / Tailwind CSS 3.4.19 / MySQL 8.4.3` và `PHPUnit 12.5.35`. |
| **C-06** | Giới hạn tần suất đăng nhập | Báo cáo ghi chung chung "5 lần thử/phút". | Làm rõ cơ chế khóa: Tối đa 5 lần thử thất bại cho từng cặp định danh Email kết hợp địa chỉ IP (`throttleKey`) trong cửa sổ đếm ngược của RateLimiter. |
| **G-03** | Phiên bản `@tailwindcss/forms` | Chưa nêu rõ phiên bản. | Bổ sung chính xác: `@tailwindcss/forms ^0.5.2`. |
| **H-01** | Ký hiệu toán học LaTeX | Sử dụng `$660\text{px}$, $\ge 44\text{px}$, $\to$` gây lỗi hiển thị khi chuyển đổi sang Word/PDF. | Chuyển đổi toàn bộ sang văn bản Unicode thuần: `660px`, `≥ 44px × 44px`, mũi tên `→`, `(1 - N)`, `(N - N)`, `≤ 2MB`, `375px`, `100%`. |
| **H-02** | Lặp nội dung trong phần Bảo mật 3.3.2 | Mục 3.3.2 lặp lại nguyên văn các giải thích đã có ở Mục 3.2. | Tinh giản Mục 3.3.2 thành tóm tắt kiến trúc 5 tầng kèm liên kết tham chiếu chéo ngược lại Mục 3.2. |
| **E-02** | Đồng bộ Danh mục hình ảnh | Hình 3.9 có tên trong danh mục nhưng chưa gắn tham chiếu ở phần nội dung. | Bổ sung tham chiếu `Hình 3.9: Bảng điều khiển trung tâm của Quản trị viên` tại Mục 3.2.E, đảm bảo toàn bộ 26 hình ảnh/sơ đồ đều khớp 1-1 giữa danh mục và thân bài. |

---

### D. Mức độ LOW (Tiểu tiết, Văn phong & Chuẩn hóa)

| Mã | Hạng mục | Thực trạng trước khi sửa | Đã hiệu chỉnh thực tế |
| :---: | :--- | :--- | :--- |
| **S-01** | Thiếu câu mở đầu Chương 4 | Chương 4 mở đầu ngay bằng `## 4.1` mà không có lời dẫn. | Đã bổ sung 1 đoạn văn dẫn nhập toàn diện cho Chương 4. |
| **D-05** | Thời gian chạy kiểm thử | Ghi cứng `7.94s` (dao động theo cấu hình máy tính). | Cập nhật thành: `Khoảng 7–8 giây trên cấu hình máy chuẩn (dao động nhẹ theo phần cứng máy chủ)`. |
| **E-03** | Khớp chú thích Hình 4.2 | Thân bài ghi thiếu chữ "hệ thống" so với Danh mục. | Chuẩn hóa thống nhất: `Minh chứng thực thi lệnh cài đặt và khởi chạy hệ thống trên terminal`. |
| **F-04** | Tên tệp migration 000010 | Chỉ ghi mã vắn tắt `2024_01_01_000010`. | Nêu đầy đủ tên tệp: `2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php`. |
| **H-03** | Tính nhất quán thuật ngữ Like | Dùng đan xen "thả tim", "Like", "thích", "tim". | Thống nhất chuẩn hóa thuật ngữ `Thả tim (Like)` tại điểm xuất hiện đầu tiên và sử dụng nhất quán. |

---

## 3. Các Vấn Đề Chưa Xử Lý / Giữ Nguyên Có Chủ Đích (Unresolved Items)

1. **Sự hiện diện của gói `@tailwindcss/vite` trong `package.json`**:
   - *Nguyên nhân giữ nguyên*: Quy tắc dự án (AGENTS.md) nghiêm cấm thay đổi các phụ thuộc (dependencies) của ứng dụng mà chưa có sự chấp thuận trực tiếp từ người dùng.
   - *Biện pháp đã áp dụng*: Trong báo cáo (Mục 3.1), đã nêu rõ bản chất đây là tàn dư cấu hình (vestigial dependency), hoàn toàn không ảnh hưởng đến pipeline đóng gói v3 đang vận hành của ứng dụng.

2. **Tiêu đề trang trong layout (`<title>`)**:
   - `resources/views/layouts/app.blade.php` có chuỗi `{{ config('app.name', 'BlogMNM') }} - Quản lý tài khoản`. Đây là mã nguồn ứng dụng (Frontend), không nằm trong phạm vi chỉnh sửa của bước hiệu chỉnh tài liệu (Documentation Mode). Được ghi nhận để xử lý khi bảo trì giao diện tiếp theo.

---

## 4. Các Hạng Mục Cần Thao Tác Thủ Công Trước Khi Nộp Bài (Missing Manual Input)

Đây là các hạng mục kỹ thuật/hành chính mang tính bắt buộc mà một công cụ AI tự động không thể tự ý điền hoặc tự tạo:

### 1. [CRITICAL] Chụp màn hình thực tế và nhúng vào tệp Microsoft Word (Mã E-01)
Hiện tại, báo cáo định dạng Markdown chứa 23 thẻ giữ chỗ `[SCREENSHOT REQUIRED - Hình X.Y: ...]`. Khi sao chép báo cáo vào tệp mẫu `Template_BaoCao_DoAn_LTMNM.docx`, sinh viên thực hiện các thao tác sau:
1. Đảm bảo máy chủ cục bộ đang chạy: `php artisan serve --port=8000` (đang chạy nền sẵn sàng).
2. Mở trình duyệt tại `http://127.0.0.1:8000` và làm theo thứ tự chụp ảnh đã lập sẵn tại `artifacts/screenshot-plan.md`:
   - **Hình 3.4**: Trang chủ feed 660px (Dark Mode).
   - **Hình 3.5**: Trang chi tiết bài viết & cây thảo luận phân cấp.
   - **Hình 3.6**: Tìm kiếm từ khóa và click chọn chip lọc danh mục.
   - **Hình 3.7**: Trang soạn thảo `/posts/create` có kéo thả thumbnail.
   - **Hình 3.8**: Trang thống kê tác giả `/author/posts/stats`.
   - **Hình 3.9**: Bảng điều khiển quản trị `/admin`.
   - **Hình 3.10**: Hàng đợi duyệt bài `/admin/posts` với nút Duyệt/Từ chối.
   - **Hình 3.15**: Ghép ảnh đối chiếu chế độ Tối (Dark) và Sáng (Light).
   - **Hình 3.16**: Ảnh Responsive trên DevTools mô phỏng iPad (768px) và iPhone (375px).
   - **Hình 3.17**: Modal khóa tài khoản và thông báo lỗi chặn tự khóa / khóa Admin khác.
   - **Hình 4.1**: Giao diện đăng nhập `/login`.
   - **Hình 4.2**: Ảnh chụp cửa sổ Terminal chạy `git clone`, `composer install`, `migrate --seed`.
   - **Hình 5.1**: Ảnh chụp Terminal chạy `php artisan test` với kết quả `142 passed`.
   - **Hình 5.2**: Ảnh chụp Terminal chạy `vendor/bin/pint --test` đạt `passed`.
3. Chèn ảnh chụp trực tiếp vào đúng vị trí tương ứng trong tệp Word và xóa dòng thẻ giữ chỗ `[SCREENSHOT REQUIRED...]`.
4. Đối với các sơ đồ Mermaid (Hình 2.1, 2.2, 2.3, 3.1, 3.2, 3.3, 3.11, 3.12, 3.13, 3.14, 3.18, 3.19): Sử dụng công cụ [Mermaid Live Editor](https://mermaid.live) hoặc tính năng Preview Markdown để xuất ảnh PNG/SVG và dán vào Word.

### 2. Điền thông tin cá nhân và giảng viên tại Trang bìa
Tại các dòng 19–22 của báo cáo, cần điền chính xác thông tin thực tế:
- **Giảng viên hướng dẫn:** Thay thế `[Cần điền thông tin Giảng viên hướng dẫn]` bằng học hàm, học vị và họ tên thầy/cô hướng dẫn môn học.
- **Mã số sinh viên (MSSV):** Thay thế `[Cần điền MSSV]` bằng mã sinh viên chính thức của Đặng Minh Khởi (cả ở trang bìa và Bảng phân công nhiệm vụ).
- **Lớp / Khóa:** Thay thế `[Cần điền Lớp / Khóa]` bằng tên lớp học phần chính thức.

---

## 5. Kết Luận Thẩm Định

- **Tính toàn vẹn kỹ thuật:** 100% các tuyên bố kỹ thuật, số liệu kiểm thử (142 tests, 514 assertions), cấu trúc quan hệ CSDL (9 bảng thực thể), phiên bản công nghệ (PHP 8.3, Laravel 13, MySQL 8.4, Tailwind v3, Vite 8, Alpine.js, PHPUnit 12.5.35, Pint v1.32.1) đều đã được đối soát khớp hoàn hảo với mã nguồn sống.
- **Tính tuân thủ mẫu báo cáo:** Đúng 100% cấu trúc 5 chương của trường, không phát sinh chương dư thừa.
- **Trạng thái tài liệu:** Đã hoàn thiện sẵn sàng cho giai đoạn xuất bản Word và bảo vệ đồ án sau khi hoàn tất việc chèn ảnh chụp màn hình minh chứng thực tế.
