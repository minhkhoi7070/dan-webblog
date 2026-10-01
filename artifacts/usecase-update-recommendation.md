# Đề Xuất Cập Nhật & Chuẩn Hóa Use Case Hệ Thống — BlogMNM

> **Mục tiêu tài liệu:** Báo cáo kết quả rà soát (Audit) toàn bộ tài liệu Use Case hiện có của dự án BlogMNM, đối chiếu với kiến trúc thực tế và các quyết định nghiệp vụ chính thức của nhóm (Option B). Đưa ra các khuyến nghị điều chỉnh danh mục Use Case, sơ đồ quan hệ Actor và bổ sung các tính năng nâng cao đã triển khai thành công vào hồ sơ đồ án.

---

## 1. Rà Soát & Điều Chỉnh Use Case Trọng Tâm (Use Case Alignment)

### 1.1. Hiện trạng Use Case cũ
- **Tên Use Case cũ:** "Chuyển trạng thái Nháp / Xuất bản" (*Toggle Draft / Publish*).
- **Vấn đề tồn tại:** Tên gọi và mô tả cũ gây hiểu lầm rằng Tác giả (Author) có thể tự do gạt nút chuyển một bài viết từ "Bản nháp" sang "Xuất bản" công khai trên trang chủ.
- **Rủi ro:** Mô tả này xung đột trực tiếp với định hướng hệ thống Tin tức/Blog chuyên nghiệp và quy chuẩn kiểm duyệt nội dung an toàn của nhóm.

### 1.2. Khuyến nghị chuẩn hóa chính thức
Đổi tên và diễn giải lại Use Case này thành:  
👉 **"Quản lý trạng thái bài viết" (*Manage Post Lifecycle Status*)**

- **Luồng trạng thái chuẩn hóa:** `Draft` (Nháp) ➔ `Pending` (Chờ duyệt) ➔ `Published` (Xuất bản) / `Rejected` (Từ chối).
- **Phân tách trách nhiệm (Segregation of Duties):**
  1. **Tác giả (Author):**
     - Khởi tạo bài viết (`Create Post`).
     - Chỉnh sửa nội dung bài viết do mình sở hữu (`Edit Post`).
     - Lưu bài ở dạng bản nháp cá nhân (`Save Draft`).
     - Gửi bài vào hàng đợi kiểm duyệt tòa soạn (`Submit to Pending`).
     - Chỉnh sửa và nộp lại bài viết sau khi bị từ chối (`Resubmit Rejected Post`).
     - Tuyệt đối không có thẩm quyền đưa bài trực tiếp lên bảng tin công khai (`Published`).
  2. **Quản trị viên (Admin):**
     - Tiếp nhận và đánh giá bài viết tại hàng đợi kiểm duyệt (`Review Queue`).
     - Phê duyệt bài viết đạt chuẩn phát hành ra công chúng (`Approve -> Published`).
     - Từ chối bài viết vi phạm hoặc chưa đạt yêu cầu kèm lý do cụ thể (`Reject -> Rejected`).
     - Có quyền thu hồi, gỡ bỏ hoặc xóa bất kỳ bài viết nào trên toàn hệ thống khi phát hiện sai phạm.

---

## 2. Xác Nhận Nguyên Tắc Kế Thừa: Author Kế Thừa Hoàn Toàn Viewer

Trong sơ đồ phân tích nghiệp vụ hướng đối tượng (OOAD) và phân cấp vai trò (RBAC), **Tác giả (Author)** là một sự mở rộng tự nhiên của **Độc giả (Viewer)** (`Author extends Viewer`).

Hệ thống BlogMNM đã xây dựng và bảo đảm 100% tính kế thừa này cả về logic lẫn giao diện:

| Quyền hạn kế thừa từ Viewer | Tác giả (Author) có thực hiện được không? | Cơ chế kỹ thuật chứng minh |
|---|:---:|---|
| **Đọc bài viết & Tìm kiếm** |  | Truy cập mọi route công khai (`/`, `/posts`, `/posts/{slug}`). |
| **Thả tim (Like/Unlike)** |  | Gọi API AJAX `POST /posts/{id}/like` trên bài viết của tác giả khác. |
| **Lưu bài viết Yêu thích** |  | Gọi API AJAX `POST /posts/{id}/favorite` và quản lý tại `/favorites`. |
| **Theo dõi Tác giả (Follow)** |  | Theo dõi các tác giả đồng nghiệp qua `POST /authors/{id}/follow`. |
| **Bình luận bài viết (Comment)**|  | Gửi bình luận thảo luận tại `POST /posts/{id}/comments`. |
| **Trả lời phân cấp (Reply)** |  | Phản hồi bình luận lồng nhau qua tham số `parent_id`. |
| **Nhật ký tương tác (Activity)**|  | Xem dòng thời gian tương tác cá nhân tại `/activity`. |
| **Quản lý Hồ sơ & Mật khẩu** |  | Cập nhật avatar, bio, mật khẩu tại `/profile`. |
| **Đăng xuất an toàn (Logout)** |  | Đăng xuất phiên làm việc qua `POST /logout`. |

> *Ghi chú bảo mật:* Hệ thống đã được kiểm thử tự động với test case `test_author_inherits_viewer_social_capabilities()` trong `tests/Feature/AuthorPostTest.php` để đảm bảo tác giả không bao giờ bị tước bỏ các quyền của một độc giả thông thường.

---

## 3. Bổ Sung Các Chức Năng Nâng Cao Vào Danh Mục Use Case

Trong quá trình phát triển hoàn thiện sản phẩm, nhóm đã phát triển thêm 5 tính năng cốt lõi giúp nâng cao đáng kể trải nghiệm người dùng và tính bảo mật. Các tính năng này cần được ghi nhận chính thức vào hồ sơ Use Case của đồ án:

### 3.1. UC-Like: Tương tác Thả Tim Thời Gian Thực (Like / Unlike Post)
- **Mã Use Case:** `UC-08`
- **Tác nhân:** Độc giả (Viewer), Tác giả (Author).
- **Mô tả:** Người dùng đã đăng nhập có thể nhấn vào biểu tượng trái tim để bày tỏ sự yêu thích đối với bài viết. Ứng dụng công nghệ AJAX cập nhật ngay số lượt thích mà không làm tải lại trang (Optimistic UI update).
- **Ràng buộc:** Xử lý an toàn trường hợp gửi request liên tục (idempotent), không gây lỗi duplicate key nhờ cơ chế `syncWithoutDetaching()`.

### 3.2. UC-Follow: Mạng Lưới Theo Dõi Tác Giả (Follow / Unfollow Author)
- **Mã Use Case:** `UC-11`
- **Tác nhân:** Độc giả (Viewer), Tác giả (Author).
- **Mô tả:** Cho phép người dùng theo dõi các tác giả yêu thích để cập nhật các bài viết mới.
- **Ràng buộc nghiệp vụ (`INT-06`):** Tác giả **không được tự theo dõi chính mình** (hệ thống bắt lỗi HTTP 422 và ẩn nút follow trên trang cá nhân của chính tác giả).

### 3.3. UC-Activity: Nhật Ký Tương Tác Cá Nhân (Activity History)
- **Mã Use Case:** `UC-26`
- **Tác nhân:** Độc giả (Viewer), Tác giả (Author).
- **Mô tả:** Cung cấp trang `/activity` hiển thị dòng thời gian trực quan về các hoạt động gần đây của người dùng: các bài viết đã thả tim, các bình luận đã đăng tải và danh sách các tác giả đang theo dõi.

### 3.4. UC-BecomeAuthor: Tự Nâng Cấp Từ Độc Giả Lên Tác Giả (Viewer to Author Upgrade)
- **Mã Use Case:** `UC-27`
- **Tác nhân:** Độc giả (Viewer).
- **Mô tả:** Độc giả có nhu cầu viết bài có thể tự kích hoạt tính năng "Trở thành tác giả" ngay trong khu vực tài khoản cá nhân.
- **Ràng buộc an toàn:** Hệ thống xử lý hoàn toàn ở backend (`POST /become-author`), chuyển trạng thái `role = 'author'` an toàn; tuyệt đối không chấp nhận các tham số role gửi từ client để ngăn ngừa leo thang đặc quyền (Privilege Escalation) lên vai trò Admin.

### 3.5. UC-AdminLogin: Cổng Đăng Nhập Quản Trị Viên Chuyên Biệt (Dedicated Admin Authentication)
- **Mã Use Case:** `UC-28`
- **Tác nhân:** Quản trị viên (Admin).
- **Mô tả:** Tách biệt hoàn toàn giao diện đăng nhập của ban quản trị (`/admin/login`) khỏi trang đăng nhập độc giả (`/login`).
- **Đặc điểm giao diện & an toàn:** Thiết kế giao diện chuyên dụng tối giản phong cách quản trị, loại bỏ các nút đăng ký hay điều hướng không liên quan. Nếu tài khoản không phải Admin (như Viewer hoặc Author) cố gắng đăng nhập tại đây, hệ thống từ chối đăng nhập và thông báo lỗi rõ ràng.

---

## 4. Bảng Tổng Hợp Danh Mục Use Case Hoàn Chỉnh Sau Cập Nhật

| Nhóm chức năng | Mã UC | Tên Use Case | Tác nhân chính | Trạng thái hiện tại |
|---|---|---|---|:---:|
| **Khám phá & Công khai** | UC-01 | Duyệt dòng tin bảng tin (Browse Feed) | Guest | Đã hoàn thành |
| | UC-02 | Tìm kiếm bài viết (Search Articles) | Guest | Đã hoàn thành |
| | UC-03 | Lọc theo Chuyên mục & Thẻ (Category / Tag) | Guest | Đã hoàn thành |
| | UC-04 | Đọc bài viết & Tăng lượt xem (Read Post) | Guest | Đã hoàn thành |
| | UC-05 | Xem hồ sơ tác giả (View Author Profile) | Guest | Đã hoàn thành |
| | UC-06 | Chuyển đổi giao diện Sáng / Tối (Theme Toggle)| Guest | Đã hoàn thành |
| | UC-07 | Đăng ký & Đăng nhập người dùng (Auth) | Guest | Đã hoàn thành |
| **Tương tác Độc giả** | UC-08 | Thả tim / Bỏ tim bài viết (Like/Unlike) | Viewer | Đã hoàn thành |
| | UC-09 | Lưu bài viết vào mục Yêu thích (Bookmark) | Viewer | Đã hoàn thành |
| | UC-10 | Xem thư viện bài viết yêu thích (Favorites) | Viewer | Đã hoàn thành |
| | UC-11 | Theo dõi / Hủy theo dõi tác giả (Follow) | Viewer | Đã hoàn thành |
| | UC-12 | Gửi bình luận gốc (Post Root Comment) | Viewer | Đã hoàn thành |
| | UC-13 | Trả lời bình luận lồng nhau (Reply Comment) | Viewer | Đã hoàn thành |
| | UC-14 | Quản lý thông tin hồ sơ & mật khẩu (Profile) | Viewer | Đã hoàn thành |
| | **UC-26** | **Xem nhật ký hoạt động cá nhân (Activity)** | **Viewer** | **Đã hoàn thành (Mới)** |
| | **UC-27** | **Nâng cấp tài khoản lên Tác giả (Become Author)**| **Viewer** | **Đã hoàn thành (Mới)** |
| **Không gian Tác giả** | UC-15 | Khởi tạo bài viết mới & Tải ảnh đại diện | Author | Đã hoàn thành |
| | UC-16 | Quản lý trạng thái bài viết (Lưu nháp / Nộp duyệt)| Author | **Chuẩn hóa (Option B)** |
| | UC-17 | Chỉnh sửa nội dung & Thẻ bài viết của mình | Author | Đã hoàn thành |
| | UC-18 | Xóa bài viết của chính mình | Author | Đã hoàn thành |
| | UC-19 | Xem bảng chỉ số hiệu suất bài viết (Stats) | Author | Đã hoàn thành |
| | **UC-20** | **Xóa bình luận spam trên bài viết của mình** | **Author** | **Đã khắc phục hoàn chỉnh** |
| **Hệ thống Quản trị** | **UC-28** | **Đăng nhập Quản trị viên riêng biệt (Admin Login)**| **Admin** | **Đã hoàn thành (Mới)** |
| | UC-21 | Phê duyệt bài viết chờ duyệt (Approve Post) | Admin | Đã hoàn thành |
| | UC-22 | Từ chối bài viết kèm lý do phản hồi (Reject) | Admin | Đã hoàn thành |
| | UC-23 | Điều duyệt bình luận toàn hệ thống (Approve/Spam/Delete)| Admin | Đã hoàn thành |
| | UC-24 | Quản lý Chuyên mục bài viết (Category CRUD) | Admin | Đã hoàn thành |
| | UC-25 | Khóa / Mở khóa tài khoản người dùng vi phạm | Admin | Đã hoàn thành |

---

## 5. Kết Luận Khuyến Nghị Cho Báo Cáo Đồ Án

1. **Về hình vẽ biểu đồ Use Case trong báo cáo:** Cập nhật lại sơ đồ Use Case Diagram tổng quát để phản ánh đúng quan hệ kế thừa `Author extends Viewer`, đồng thời phân rã Use Case quản lý bài viết thành các trường hợp cụ thể: *Lưu nháp*, *Gửi duyệt*, *Phê duyệt* và *Từ chối*.
2. **Về bảng đặc tả Use Case (Use Case Specification):** Cập nhật kịch bản chính (Main Flow) và kịch bản ngoại lệ (Alternative Flow) của Use Case kiểm duyệt bài viết để thể hiện rõ điều kiện tiên quyết: Admin là người duy nhất chuyển đổi bài viết sang trạng thái `Published`.
3. **Về tính năng tương tác:** Bổ sung đầy đủ 5 tính năng đã phát triển (Like, Follow, Activity, Become Author, Dedicated Admin Login) vào bảng phân tích yêu cầu để làm nổi bật sự hoàn thiện vượt bậc của sản phẩm so với đề cương ban đầu.
