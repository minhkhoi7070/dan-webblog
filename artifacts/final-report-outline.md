# BlogMNM — Final Academic Report Outline
**Template Authority:** `Template_BaoCao_DoAn_LTMNM.docx`  
**Status:** Approved Outline for Step 17.1 (Strict Template Compliance)  
**Verification Audit:** Validated against [`/artifacts/report-preflight-audit.md`](file:///c:/laragon/www/webblog/artifacts/report-preflight-audit.md)

---

## Template Structural Verification & Compliance Checklist

- [x] **NO extra chapters** (Strictly Chapters 1 through 5, exactly matching template).
- [x] **NO extra top-level sections** (Strictly 1.1, 1.2, 2.1, 2.2, 2.3, 3.1, 3.2, 3.3, Chapter 4, 5.1, 5.2).
- [x] **NO duplicated sections**.
- [x] **NO missing template sections**.
- [x] **NO standalone Testing Chapter** (Integrated into Section 5.1 & empirical test results).
- [x] **NO standalone Security Chapter** (Integrated into Sections 3.1, 3.2, and 3.3).
- [x] **NO standalone UI/UX Chapter** (Integrated into Sections 3.2 and 3.3).
- [x] **NO standalone Architecture Chapter** (Integrated into Sections 2.1, 2.2, 2.3, and 3.1).
- [x] **Installation & Operations strictly in Chapter 4**.
- [x] **Results, Limitations & Future Roadmap strictly in Chapter 5**.

---

## Detailed Section-by-Section Content Mapping

```
============================================================
FRONT MATTER
============================================================
```

### 0.1. Trang Bìa (Cover Page)
1. **Purpose**: Formal identification of university, faculty, course, project title, and academic year.
2. **Evidence Sources**: `.env` (`APP_NAME="BlogMNM"`), Pre-flight Audit Item A.
3. **Features to Describe**: Course: Lập trình Mã Nguồn Mở (LTMNM); Project Title: *"Xây dựng Nền tảng Web Blog đa người dùng BlogMNM trên nền tảng Laravel"*.
4. **Screenshots**: None.
5. **Diagrams**: University Logo.
6. **Source Files**: `.env`.
7. **Relevant Artifacts**: `report-preflight-audit.md`.

### 0.2. Thông tin Giảng viên hướng dẫn & Sinh viên thực hiện (Supervisor / Student Information)
1. **Purpose**: Document student authorship and supervising faculty.
2. **Evidence Sources**: Git authorship (`Dang Minh Khoi`), Pre-flight Audit Items B, C, D.
3. **Features to Describe**: Student details (Name, Student ID / MSSV), Lecturer name (GVHD), Department.
4. **Screenshots**: None.
5. **Diagrams**: None.
6. **Source Files**: Git commit log.
7. **Relevant Artifacts**: `report-preflight-audit.md`.

### 0.3. MỤC LỤC (Table of Contents)
1. **Purpose**: Hierarchical navigation index strictly mirroring the 5 official chapters.
2. **Evidence Sources**: Official template structural index.
3. **Features to Describe**: Complete list of sections with page numbering.
4. **Screenshots**: None.
5. **Diagrams**: None.
6. **Source Files**: None.
7. **Relevant Artifacts**: `final-report-outline.md`.

### 0.4. BẢNG PHÂN CÔNG (Task & Contribution Assignment Table)
1. **Purpose**: Document workload distribution, assigned system modules, and completion percentage for each team member.
2. **Evidence Sources**: Git commit distribution, module ownership breakdown.
3. **Features to Describe**: Module assignments: Backend & Architecture (Dang Minh Khoi - 100%), Database & Migrations (100%), Frontend UI/UX (100%), QA & Testing (100%).
4. **Screenshots**: None.
5. **Diagrams**: Structured Contribution Table.
6. **Source Files**: Git log.
7. **Relevant Artifacts**: `rubric-evidence-matrix.md`.

### 0.5. DANH MỤC HÌNH ẢNH (List of Figures)
1. **Purpose**: Catalogue all figures, UML diagrams, flowcharts, and system screenshots used across the report.
2. **Evidence Sources**: `screenshot-plan.md`, UML diagram catalog in `/artifacts/`.
3. **Features to Describe**: List of Figures: Use Case Diagram, ERD Diagram, Component Diagram, Class Diagram, State Machine Diagram, Sequence Diagrams, Activity Diagrams, and UI Screenshots.
4. **Screenshots**: Figure index reference.
5. **Diagrams**: None.
6. **Source Files**: None.
7. **Relevant Artifacts**: `screenshot-plan.md`.

---

```
============================================================
CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI
============================================================
```

### 1.1. Lý do chọn đề tài
1. **Purpose**: Establish real-world context, technical problem statement, and justification for building BlogMNM.
2. **Evidence Sources**: `project-overview.md` (Sections 1 & 2), `requirements.md` (Section 1).
3. **Features to Describe**:
   - Modern shift from bloated CMS platforms (WordPress) toward fast, minimalist, social-content-inspired platforms (e.g. Meta Threads, Substack).
   - Demand for high-performance reading experiences with optimized typography (centered 660px reading column).
   - Necessity of structured editorial governance (authoring vs administrative moderation) paired with engaging community mechanics (Likes, Favorites, Follows, Threaded Discussions).
   - Practical demonstration of enterprise PHP 8.3 & Laravel framework capabilities in building secure, scalable web systems.
4. **Screenshots**: None.
5. **Diagrams**: None.
6. **Source Files**: `README.md`.
7. **Relevant Artifacts**: `project-overview.md`, `requirements.md`.

### 1.2. Mục tiêu đồ án
1. **Purpose**: Define clear functional, technical, security, and quality goals achieved by the project.
2. **Evidence Sources**: `requirements.md` (FR-01 to FR-15, NFR-01 to NFR-10), `project-overview.md` (Section 5).
3. **Features to Describe**:
   - **Functional Scope**: Multi-tier RBAC (Guest, Viewer, Author, Admin), 4-state post publishing workflow (`draft`, `pending`, `published`, `rejected`), two-level threaded discussions with anti-cross-posting validation, and asynchronous social reactions.
   - **Technical & Architectural Scope**: Clean MVC layered architecture with FormRequest validation and Model Policies; relational database with InnoDB foreign key constraints (`RESTRICT` on delete for categories); full-text search with query persistence.
   - **Presentation & UX Scope**: Threads-inspired monochrome design tokens (`resources/css/app.css`), zero-flash `<head>` theme switching engine, responsive layout across Desktop, Tablet, and Mobile.
   - **Quality Assurance Scope**: 100% automated test pass rate (142 tests, 514 assertions) with zero style violations (Laravel Pint).
4. **Screenshots**: None.
5. **Diagrams**: None.
6. **Source Files**: `composer.json`, `phpunit.xml`.
7. **Relevant Artifacts**: `requirements.md`, `rubric-evidence-matrix.md`.

---

```
============================================================
CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG
============================================================
```

### 2.1. Sơ đồ Use Case (UML Use Case)
1. **Purpose**: Present actor boundary analysis, system use case diagram, and detailed use case specifications.
2. **Evidence Sources**: `use-case.md`, `actor-description.md`, `use-case-detail.md`, `routes/web.php`.
3. **Features to Describe**:
   - **Actor Hierarchy & Capabilities**: 4 actors (**Guest**, **Viewer**, **Author**, **Administrator**) and the locked account state (`is_locked = true`).
   - **Use Case Directory**: 25 distinct Use Cases categorized across 4 packages:
     - *Public Discovery*: UC-01 (Browse Feed), UC-02 (Search), UC-03 (Filter Taxonomy), UC-04 (Read Post & Track Views), UC-05 (Author Profile), UC-06 (Theme Toggle), UC-07 (Auth).
     - *Social Engagement*: UC-08 (Like), UC-09 (Favorite), UC-10 (Favorites Library), UC-11 (Follow Author), UC-12 (Root Comment), UC-13 (Threaded Reply), UC-14 (Profile Management).
     - *Author Editorial CMS*: UC-15 (Create Draft), UC-16 (Upload/Replace Thumbnail), UC-17 (Update Post), UC-18 (Submit for Review), UC-19 (Delete Post), UC-20 (Portfolio Stats).
     - *Admin Governance*: UC-21a/b (Approve/Reject Post), UC-22a/b/c (Approve/Spam/Delete Comment), UC-23 (Manage Categories), UC-24 (Lock/Unlock User), UC-25 (Platform Analytics).
   - **Detailed Workflow Specifications**: Step-by-step main flow, alternate flows, and pre/post conditions for core use cases (Auth with lockout, Like toggle, Follow author, Post submission, Admin review, Category deletion safety).
4. **Screenshots**: None.
5. **Diagrams**:
   - **Hình 2.1**: Sơ đồ tổng thể Use Case hệ thống BlogMNM (System Use Case Diagram).
   - **Hình 2.2**: Sơ đồ phân quyền các Actor trong hệ thống (Actor Hierarchy & Role Boundary).
6. **Source Files**: `routes/web.php`, `routes/auth.php`, `app/Http/Middleware/RoleMiddleware.php`.
7. **Relevant Artifacts**: `use-case.md`, `use-case-detail.md`, `actor-description.md`.

### 2.2. Thiết kế Cơ sở dữ liệu (ERD)
1. **Purpose**: Present the entity-relationship data model, relational cardinality, foreign key constraints, and indexing strategy.
2. **Evidence Sources**: `database-schema.md`, `database/migrations/`.
3. **Features to Describe**:
   - **ERD Architecture**: Relational design strictly limited to the **9 business domain tables** (`users`, `categories`, `tags`, `posts`, `post_tag`, `comments`, `favorites`, `likes`, `follows`). Framework infrastructure tables (`sessions`, `cache`, `jobs`) excluded from domain model.
   - **Relational Integrity Rules**:
     - `posts.user_id` $\to$ `users(id)` ON DELETE CASCADE.
     - `posts.category_id` $\to$ `categories(id)` **ON DELETE RESTRICT** (guarantees category cannot be deleted if active articles exist).
     - `posts.reviewed_by` $\to$ `users(id)` ON DELETE SET NULL.
     - `post_tag` $\to$ Many-to-Many composite unique constraint `(post_id, tag_id)`.
     - `comments.parent_id` $\to$ `comments(id)` self-referencing foreign key for threaded replies.
     - Composite unique keys preventing duplicate reactions: `likes(user_id, post_id)`, `favorites(user_id, post_id)`, `follows(follower_id, following_id)`.
   - **Database Indexing Strategy**: Indexes on `posts.status`, `posts.published_at`, `posts.views`, and `comments.status`.
4. **Screenshots**: None.
5. **Diagrams**:
   - **Hình 2.3**: Sơ đồ quan hệ thực thể (Entity Relationship Diagram - ERD) hệ thống BlogMNM.
6. **Source Files**: `database/migrations/2024_01_01_000001_create_categories_table.php` through `2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php`.
7. **Relevant Artifacts**: `database-schema.md`, `report-preflight-audit.md` (Section 3).

### 2.3. Mô tả chi tiết các bảng dữ liệu
1. **Purpose**: Document comprehensive Data Dictionary for each of the 9 business domain tables.
2. **Evidence Sources**: `database-dictionary.md`, `database/migrations/`.
3. **Features to Describe**:
   - Detailed schema tables containing Column Name, Data Type, Nullability, Default Value, Key/Index, and Business Description:
     1. Bảng `users`: Account identity, role enum (`admin`, `author`, `viewer`), password hash, lockout flag (`is_locked`).
     2. Bảng `categories`: Taxonomy classifications with unique slug.
     3. Bảng `tags`: Cross-cutting topic keywords with unique slug.
     4. Bảng `posts`: Core articles with 4-state status (`draft`, `pending`, `published`, `rejected`), view counter, thumbnail, and review audit trail (`reviewed_by`, `reviewed_at`, `rejection_reason`).
     5. Bảng `post_tag`: Many-to-many pivot associating articles with tags.
     6. Bảng `comments`: Discussion records with `parent_id` for nested replies and `status` moderation enum (`pending`, `approved`, `spam`).
     7. Bảng `favorites`: Bookmark records.
     8. Bảng `likes`: Reaction records.
     9. Bảng `follows`: Author subscription graph.
4. **Screenshots**: None.
5. **Diagrams**: Schema data dictionary tables.
6. **Source Files**: `app/Models/User.php`, `Post.php`, `Category.php`, `Tag.php`, `Comment.php`, `Like.php`, `Favorite.php`, `Follow.php`.
7. **Relevant Artifacts**: `database-dictionary.md`.

---

```
============================================================
CHƯƠNG 3: CÔNG NGHỆ ÁP DỤNG VÀ MÔ TẢ CHỨC NĂNG
============================================================
```

### 3.1. Công nghệ và Công cụ sử dụng
1. **Purpose**: Document the concrete technology stack, runtime versions, engineering tools, and system architectural layers.
2. **Evidence Sources**: `composer.json`, `package.json`, installed CLI outputs, `architecture.md`, `component-diagram.md`, `class-diagram.md`.
3. **Features to Describe**:
   - **Backend Stack**: PHP 8.3.30 (Strict typing, constructor property promotion, match expressions) and **Laravel Framework 13.33.0** (verified runtime).
   - **Frontend Stack**: Tailwind CSS 3.4.19 (configured with custom CSS variables), Alpine.js 3.17.4 (lightweight reactivity), **Vite 8.3.1** & `laravel-vite-plugin 3.2.0` (asset bundler), Blade Templating Engine.
   - **Database & Storage**: MySQL 8.4.3 (InnoDB, foreign key constraints), Laravel Local Public Filesystem (`storage/app/public/thumbnails`).
   - **Development & QA Tools**: PHPUnit 12.5.12 / 11.5.3 (Feature & Unit testing), Laravel Pint 1.27.0 (PSR-12 style enforcement), Laragon / Composer / Git.
   - **System Architecture & MVC Integration**:
     - 3-tier MVC architecture: Presentation (Blade components) $\to$ Controllers & Routing $\to$ Eloquent ORM & MySQL.
     - Integration of FormRequest validation layer (`StorePostRequest`, `StoreCommentRequest`, `LoginRequest`).
     - Integration of Authorization Policy layer (`PostPolicy`, `CommentPolicy`) and Middleware pipeline (`web`, `auth`, `RoleMiddleware`).
     - File storage lifecycle: automated image thumbnail unlinking via model observer (`Post::booted()`).
4. **Screenshots**: None.
5. **Diagrams**:
   - **Hình 3.1**: Sơ đồ kiến trúc phân tầng hệ thống BlogMNM (System 3-Tier Layered Architecture).
   - **Hình 3.2**: Sơ đồ thành phần phần mềm (UML Component Diagram).
   - **Hình 3.3**: Sơ đồ lớp miền nghiệp vụ (UML Domain Model Class Diagram).
6. **Source Files**: `composer.json`, `package.json`, `bootstrap/app.php`, `app/Http/Middleware/RoleMiddleware.php`.
7. **Relevant Artifacts**: `architecture.md`, `component-diagram.md`, `class-diagram.md`, `report-preflight-audit.md` (Section 2).

### 3.2. Mô tả các chức năng cốt lõi
1. **Purpose**: Provide comprehensive functional descriptions, workflows, security rules, and UI presentations for all baseline platform modules.
2. **Evidence Sources**: `route-map.md`, `post-workflow.md`, `search-design.md`, `pagination-design.md`, `authentication-flow.md`, `authorization-flow.md`, UI views in `resources/views/`.
3. **Features to Describe**:
   - **1. Xác thực & Quản lý phiên (Authentication & Session Management)**:
     - Breeze session-based authentication, CSRF protection, Bcrypt password hashing.
     - Rate-limiting (5 attempts/min per IP/email) preventing brute-force attacks.
     - Session fixation prevention via session ID regeneration upon login.
   - **2. Phân quyền người dùng (Role-Based Access Control - RBAC)**:
     - 4 roles: Guest, Viewer, Author, Admin.
     - Guarded by `RoleMiddleware` and policy gates. Unauthorized requests return HTTP 403.
   - **3. Quy trình xuất bản bài viết (Editorial Publishing Lifecycle)**:
     - 4-stage state machine: `draft` $\to$ `pending` $\to$ `published` or `rejected`.
     - Author post creation with title, slug, excerpt, body, category, tags, and featured thumbnail dropzone.
     - Parameter tampering defense: `StorePostRequest` strips untrusted `status` parameters (`SEC-02`). Newly created posts strictly default to `draft`.
     - Author submits draft/rejected post for review $\to$ `pending`.
     - Atomic article view tracking (`views` incremented on post detail view).
   - **4. Tìm kiếm, Phân loại & Phân trang (Search, Taxonomy & Pagination)**:
     - Keyword search across `title`, `excerpt`, and `body` using grouped boolean SQL clauses (`Post::scopeSearch`).
     - Taxonomy filtering by Category and Tag with active filter badges and reset controls.
     - LengthAwarePaginator (10 items/page) with automatic query string preservation (`withQueryString()`).
     - Boundary safety redirection redirecting invalid page parameters (`?page=0`, `?page=999`) safely (`SRCH-04`).
   - **5. Tương tác cộng đồng & Bình luận phân cấp (Social Interactions & Threaded Comments)**:
     - Optimistic AJAX Like toggle (`POST /posts/{id}/like`) returning live like counts.
     - Personal bookmarks library (`/favorites`).
     - Author following with self-follow prevention (`INT-06`).
     - Two-level threaded discussions with anti-cross-posting parent validation (`INT-09`).
     - Public view strictly suppresses unapproved/pending/spam comments (`INT-10`).
   - **6. Trung tâm Quản trị & Điều duyệt (Admin Governance Portal)**:
     - Admin dashboard overview with real-time metric cards.
     - Post review queue: Approve (sets `published_at`, `reviewed_by`, `reviewed_at`) or Reject with mandatory feedback notes.
     - Comment moderation: approve, mark spam, or delete.
     - User directory: inspection, lock/unlock accounts.
     - Category management with deletion safety.
   - **7. Bố cục giao diện & Điều hướng (UI Layout & Responsive Navigation)**:
     - Centered 660px reading column maximizing readability.
     - Responsive navigation: Desktop full sidebar (`w-64`), Tablet compact icon-only rail (`w-20`), Mobile minimal top bar and fixed bottom navigation (`h-16`) with $\ge 44\text{px}$ touch targets.
4. **Screenshots**:
   - **Hình 3.4**: Giao diện Trang chủ và Dòng tin chính (Home Feed & Centered Reading View).
   - **Hình 3.5**: Giao diện Chi tiết bài viết và Bình luận phân cấp (Post Detail & Discussion Tree).
   - **Hình 3.6**: Giao diện Tìm kiếm và Bộ lọc danh mục (Search Results & Active Filter Banner).
   - **Hình 3.7**: Giao diện Soạn thảo bài viết dành cho Tác giả (Author Post Creation CMS).
   - **Hình 3.8**: Bảng thống kê portfolio của Tác giả (Author Analytics Dashboard).
   - **Hình 3.9**: Bảng điều khiển Quản trị viên (Admin Dashboard Overview).
   - **Hình 3.10**: Hàng đợi duyệt bài viết của Quản trị viên (Admin Post Moderation Queue).
5. **Diagrams**:
   - **Hình 3.11**: Sơ đồ máy trạng thái vòng đời bài viết (Post Editorial State Machine Diagram).
   - **Hình 3.12**: Sơ đồ tuần tự quy trình duyệt bài viết (Post Moderation Sequence Diagram).
   - **Hình 3.13**: Sơ đồ tuần tự tương tác Like bất đồng bộ qua AJAX (AJAX Like Interaction Sequence Diagram).
   - **Hình 3.14**: Sơ đồ tuần tự gửi bình luận phân cấp và kiểm tra cross-post (Threaded Comment Sequence Diagram).
6. **Source Files**:
   - `app/Http/Controllers/PostController.php`, `InteractionController.php`, `CommentController.php`.
   - `app/Http/Controllers/Admin/PostController.php`, `CommentController.php`, `UserController.php`, `CategoryController.php`.
   - `app/Http/Requests/StorePostRequest.php`, `StoreCommentRequest.php`, `Auth/LoginRequest.php`.
   - `resources/views/posts/index.blade.php`, `posts/show.blade.php`, `author/posts/create.blade.php`, `admin/dashboard.blade.php`.
7. **Relevant Artifacts**: `route-map.md`, `post-workflow.md`, `search-design.md`, `pagination-design.md`, `sequence-diagrams.md`.

### 3.3. Chức năng nâng cao & Sáng tạo (Nếu có)
1. **Purpose**: Highlight standout technical innovations, unique design implementations, and deep security safeguards distinguishing BlogMNM.
2. **Evidence Sources**: `theme-design.md`, `resources/css/app.css`, `security-business-fix-result.md`, `testing-result.md` (Critical fixes), `QASuiteTest.php`.
3. **Features to Describe**:
   - **1. Hệ thống giao diện Monochrome lấy cảm hứng từ Threads (Threads-Inspired Design System)**:
     - Pure black (`#000000`) and pure white (`#ffffff`) high-contrast aesthetic.
     - Unified CSS variables (`--color-bg`, `--color-surface`, `--color-border`, `--color-text`) in `resources/css/app.css`.
     - Button Color Inversion Pattern (`bg-[var(--color-text)] text-[var(--color-bg)]`), automatically reversing contrast between themes without conditional Blade logic.
   - **2. Cơ chế chuyển đổi giao diện không giật hình (Zero-Flash Theme Engine)**:
     - Inline synchronous execution script embedded directly in `<head>` before HTML DOM rendering.
     - Reads user preference from `localStorage` with fallback to `prefers-color-scheme`.
     - Completely eliminates visual Flash of Unstyled Content (FOUC).
   - **3. Kiến trúc Bảo mật Đa lớp & Chống leo thang quyền (Defense-in-Depth Security)**:
     - *Locked User Ejection (`AUTH-07` & `ROLE-11`)*: Locked accounts (`is_locked = true`) are intercepted across 5 layers: login authentication request, `RoleMiddleware`, `PostPolicy::before()`, `StorePostRequest`, and `InteractionController`. Active sessions are invalidated immediately.
     - *Admin Peer Lockout Protection (`SEC-05`)*: Administrators cannot lock other administrator accounts or lock themselves.
     - *Anti-Cross-Posting Comment Validation (`INT-09`)*: Validates that `parent_id` strictly belongs to the current post, preventing cross-article comment tree corruption.
     - *Category Deletion Protection (`SEC-06`)*: Enforces relational integrity via `ON DELETE RESTRICT` foreign key, blocking category deletion if active articles exist.
     - *Automated Media Orphan Cleanup (`POST-04`)*: Model lifecycle deleting hook (`Post::booted()`) automatically unlinks orphaned image files from disk upon post deletion or thumbnail replacement.
   - **4. Tương tác lạc quan trên giao diện (Optimistic UI AJAX Updates)**:
     - Heart animation and live like count updates executed immediately on the client before network roundtrip resolves, reconciling gracefully on completion.
4. **Screenshots**:
   - **Hình 3.15**: So sánh trực quan giao diện Dark Mode và Light Mode (Zero-Flash Dark vs Light Theme Comparison).
   - **Hình 3.16**: Giao diện Responsive trên Tablet và Mobile với thanh điều hướng cố định (Tablet & Mobile Responsive Views).
   - **Hình 3.17**: Minh chứng xử lý tài khoản bị khóa và ngăn chặn khóa tài khoản Quản trị viên (Locked Account & Admin Safeguard UI).
5. **Diagrams**:
   - **Hình 3.18**: Sơ đồ luồng khóa tài khoản và phòng thủ đa tầng (Account Locking & Defense-in-Depth Activity Diagram).
   - **Hình 3.19**: Sơ đồ tuần tự bảo vệ xóa danh mục bài viết (Category Deletion Protection Sequence Diagram).
6. **Source Files**:
   - `resources/css/app.css`, `resources/views/layouts/app.blade.php`, `resources/js/interactions.js`.
   - `app/Policies/PostPolicy.php`, `app/Http/Controllers/Admin/UserController.php`, `database/migrations/2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php`.
7. **Relevant Artifacts**: `theme-design.md`, `security-business-fix-result.md`, `activity-diagrams.md`.

---

```
============================================================
CHƯƠNG 4: HƯỚNG DẪN CÀI ĐẶT VÀ VẬN HÀNH
============================================================
```

### 4. Hướng dẫn cài đặt và vận hành
1. **Purpose**: Provide complete, reproducible setup instructions, environment configuration, database seeding, test credentials, and end-to-end operation walkthroughs.
2. **Evidence Sources**: `installation-guide.md`, `user-manual.md`, `admin-manual.md`, `database/seeders/DatabaseSeeder.php`, `demo-script.md`.
3. **Features to Describe**:
   - **1. Yêu cầu môi trường hệ thống (System Prerequisites)**:
     - PHP 8.3+, Composer 2.x, Node.js 18+/20+, MySQL 8.4+, Laragon / XAMPP web server.
   - **2. Quy trình cài đặt từng bước (Step-by-Step Installation)**:
     - Clone repository: `git clone <repo_url> && cd webblog`
     - Cấu hình tệp môi trường: `cp .env.example .env` (thiết lập database credentials).
     - Cài đặt thư viện phụ thuộc PHP: `composer install`
     - Khởi tạo khóa mã hóa ứng dụng: `php artisan key:generate`
     - Thực thi di chuyển cơ sở dữ liệu và nạp dữ liệu mẫu: `php artisan migrate:fresh --seed`
     - Tạo liên kết symbolic link lưu trữ tệp tin: `php artisan storage:link`
     - Cài đặt thư viện frontend và biên dịch assets: `npm install && npm run build`
     - Khởi chạy máy chủ nội bộ: `php artisan serve --port=8000`
   - **3. Tài khoản thử nghiệm mặc định (Seeded Demo Accounts)**:
     - *Quản trị viên (Administrator)*: `admin@blogmnm.test` / Mật khẩu: `password`
     - *Tác giả (Author)*: Tài khoản tác giả tạo từ seeder / Mật khẩu: `password`
     - *Độc giả (Viewer)*: Tài khoản độc giả tạo từ seeder / Mật khẩu: `password`
   - **4. Hướng dẫn vận hành người dùng & tác giả (User & Author Operation Walkthrough)**:
     - Đọc bài viết, chuyển đổi giao diện Sáng/Tối.
     - Tìm kiếm bài viết, lọc theo danh mục, lọc theo thẻ tag.
     - Thả tim (Like), đánh dấu yêu thích (Bookmark), theo dõi tác giả (Follow).
     - Gửi bình luận gốc và gửi câu trả lời phân cấp (Reply).
     - Tác giả soạn thảo bài viết mới (`/posts/create`), tải ảnh thumbnail, lưu bản nháp (Draft), gửi duyệt bài viết (Submit for Review).
     - Tác giả xem phản hồi lý do từ chối (Rejection Reason), chỉnh sửa và gửi duyệt lại.
     - Tác giả theo dõi biểu đồ thống kê portfolio tại `/author/posts/stats`.
   - **5. Hướng dẫn vận hành quản trị viên (Administrator Operation Walkthrough)**:
     - Truy cập Trung tâm Quản trị tại `/admin`.
     - Theo dõi các chỉ số hệ thống trên Admin Dashboard.
     - Hàng đợi duyệt bài viết: xem chi tiết, Phê duyệt (Approve) hoặc Từ chối kèm lý do (Reject).
     - Điều duyệt bình luận: duyệt bình luận, đánh dấu Spam, xóa bình luận.
     - Quản lý tài khoản: khóa tài khoản vi phạm, mở khóa tài khoản.
     - Quản lý danh mục: tạo danh mục mới, chỉnh sửa, xóa danh mục an toàn.
   - **6. Xử lý các sự cố thường gặp (Troubleshooting Guide)**:
     - Lỗi thiếu manifest Vite $\to$ chạy `npm run build`.
     - Lỗi ảnh thumbnail không hiển thị (404) $\to$ chạy `php artisan storage:link`.
     - Lỗi phân quyền thư mục `storage` và `bootstrap/cache`.
4. **Screenshots**:
   - **Hình 4.1**: Giao diện đăng nhập tài khoản Quản trị viên (Administrator Login Screen).
   - **Hình 4.2**: Minh chứng thực thi lệnh cài đặt và khởi chạy hệ thống trên terminal (Terminal Setup & Artisan Serve Commands).
5. **Diagrams**: Sơ đồ luồng triển khai và cấu hình hệ thống.
6. **Source Files**: `.env.example`, `database/seeders/DatabaseSeeder.php`, `package.json`, `composer.json`.
7. **Relevant Artifacts**: `installation-guide.md`, `user-manual.md`, `admin-manual.md`, `demo-script.md`.

---

```
============================================================
CHƯƠNG 5: TỔNG KẾT
============================================================
```

### 5.1. Kết quả đạt được
1. **Purpose**: Summarize the tangible accomplishments, project deliverables, empirical testing data, and rubric compliance.
2. **Evidence Sources**: `testing-result.md`, `final-audit.md`, `rubric-evidence-matrix.md`, automated test suite outputs.
3. **Features to Describe**:
   - **Hoàn thành 100% mục tiêu chức năng**: 25 Use Cases, 4-tier RBAC, 4-state editorial pipeline, threaded discussions, social interactions, and full-text search.
   - **Số liệu kiểm thử tự động toàn diện (Empirical Automated Testing Results)**:
     - Tổng số test tự động: **142/142 tests đạt (Pass Rate: 100.0%)**.
     - Tổng số assertions: **514 assertions**.
     - Thời gian thực thi toàn bộ test suite: **7.94 giây** (hiệu năng cao).
     - 18 Feature và Unit test suites bao phủ toàn diện Auth, Role, Post, Comment, Interaction, Pagination, Admin, Security, QA.
     - 52 kịch bản QA chi tiết (AUTH-01..09, ROLE-01..12, POST-01..10, INT-01..10, SRCH-01..04, SEC-01..06, RESP-01..03) đạt 100%.
   - **Minh chứng khắc phục triệt để 5 lỗi trọng yếu**:
     - *AUTH-07*: Chặn hoàn toàn tài khoản bị khóa đăng nhập.
     - *ROLE-11*: Chặn tác giả bị khóa tạo/sửa/xóa/gửi bài viết.
     - *INT-10*: Ẩn bình luận pending/spam khỏi giao diện công khai.
     - *SEC-05*: Ngăn chặn Admin khóa tài khoản Admin đồng cấp.
     - *SEC-06*: Khóa ngoại `RESTRICT` chặn xóa danh mục khi có bài viết, tự động dọn dẹp ảnh thumbnail mồ côi trên ổ đĩa.
   - **Tiêu chuẩn mã nguồn & Định dạng (Code Quality)**:
     - 100% tuân thủ chuẩn PSR-12 thông qua Laravel Pint (0 lỗi vi phạm).
   - **Trải nghiệm giao diện & Responsive**:
     - Hoàn thiện hệ thống giao diện monochrome Threads-inspired với chế độ Dark/Light không giật hình, đáp ứng hoàn hảo trên Desktop, Tablet và Mobile.
4. **Screenshots**:
   - **Hình 5.1**: Minh chứng kết quả chạy kiểm thử tự động toàn diện trên Terminal (PHPUnit 142/142 Passed Test Suite Terminal Output).
   - **Hình 5.2**: Minh chứng kiểm tra định dạng mã nguồn đạt chuẩn Laravel Pint (Laravel Pint Code Style Pass Output).
5. **Diagrams**: Bảng tổng hợp số liệu kiểm thử và ma trận đáp ứng yêu cầu (Rubric Evidence Matrix).
6. **Source Files**: `tests/Feature/QASuiteTest.php`, `tests/Feature/AdminTest.php`, `tests/Feature/AuthorPostTest.php`.
7. **Relevant Artifacts**: `testing-result.md`, `final-audit.md`, `rubric-evidence-matrix.md`.

### 5.2. Hạn chế và Hướng phát triển
1. **Purpose**: Provide honest technical appraisal of current architectural boundaries and outline a realistic roadmap for future versions.
2. **Evidence Sources**: `known-limitations.md`, `future-development.md`.
3. **Features to Describe**:
   - **1. Các hạn chế hiện tại của hệ thống (Known System Limitations)**:
     - *Phân loại danh mục đơn*: Mỗi bài viết hiện tại chỉ gắn với đúng một Category (phân loại đa chiều phải thông qua thẻ Tag).
     - *Giới hạn cấp bình luận*: Hệ thống giới hạn ở 2 cấp đàm thoại (bình luận gốc và trả lời trực tiếp) để bảo vệ trải nghiệm đọc trên màn hình điện thoại hẹp.
     - *Xử lý tệp tin đồng bộ*: Ảnh thumbnail được tải lên và lưu trữ đồng bộ trong chu kỳ HTTP request ($\le 2\text{MB}$), chưa đưa vào hàng đợi background queue.
     - *Tìm kiếm bằng SQL LIKE*: Sử dụng truy vấn SQL pattern matching nhóm, phù hợp với quy mô blog hiện tại nhưng chưa có khả năng sửa lỗi chính tả hay tính điểm ngữ nghĩa chuyên sâu.
     - *Cập nhật thời gian thực dựa trên fetch AJAX*: Tương tác mạng xã hội cập nhật tức thời qua fetch, chưa tích hợp kết nối liên tục WebSocket 2 chiều.
   - **2. Hướng phát triển trong tương lai (Future Roadmap v2.0+)**:
     - *Trình soạn thảo khối trực quan (Block Editor)*: Tích hợp TipTap hoặc Editor.js hỗ trợ xem trước Markdown trực tiếp và nhúng code syntax highlighting.
     - *Lên lịch xuất bản tự động*: Cho phép tác giả hẹn giờ xuất bản (`published_at` trong tương lai) với lệnh Laravel Task Scheduling (`posts:publish-scheduled`).
     - *Lưu trữ đám mây & Tối ưu đa phương tiện*: Tích hợp AWS S3 / Cloudflare R2 và hàng đợi xử lý ảnh nền chuyển đổi sang định dạng WebP/AVIF.
     - *Thông báo thời gian thực qua Laravel Reverb*: Triển khai WebSockets để thông báo ngay lập tức khi bài viết được Like, có bình luận mới hoặc có người theo dõi.
     - *Tích hợp công cụ tìm kiếm Meilisearch*: Kết hợp Laravel Scout và Meilisearch để hỗ trợ tìm kiếm mờ (fuzzy search), tự động hoàn thành từ khóa và lọc đa chiều siêu tốc.
     - *Bảo mật xác thực 2 yếu tố (2FA) & Điều duyệt AI*: Xác thực OTP qua Google Authenticator cho Quản trị viên và tích hợp Google Gemini API để tự động quét, phân loại bình luận độc hại.
4. **Screenshots**: None.
5. **Diagrams**: Sơ đồ lộ trình phát triển hệ thống (Future Development Roadmap Timeline).
6. **Source Files**: `known-limitations.md`, `future-development.md`.
7. **Relevant Artifacts**: `known-limitations.md`, `future-development.md`.

---

## Structural Compliance Audit Certificate

```
================================================================================
STRUCTURE COMPLIANCE VERIFICATION SUMMARY
================================================================================
Template Source: Template_BaoCao_DoAn_LTMNM.docx
Required Chapters: 5 (Exactly Five Chapters)
Generated Outline Chapters: 5 (Exactly Five Chapters)

Chapter Verification:
  [OK] FRONT MATTER (Cover, Supervisors/Students, TOC, Task Assignment, Figures)
  [OK] CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI (1.1, 1.2)
  [OK] CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG (2.1, 2.2, 2.3)
  [OK] CHƯƠNG 3: CÔNG NGHỆ ÁP DỤNG VÀ MÔ TẢ CHỨC NĂNG (3.1, 3.2, 3.3)
  [OK] CHƯƠNG 4: HƯỚNG DẪN CÀI ĐẶT VÀ VẬN HÀNH (Full Implementation Guide)
  [OK] CHƯƠNG 5: TỔNG KẾT (5.1, 5.2)

Disallowed Content Check:
  [OK] No Chapter 6 / Chapter 7
  [OK] No standalone Testing Chapter (Integrated in 5.1 & 3.2)
  [OK] No standalone Security Chapter (Integrated in 3.1, 3.2, 3.3)
  [OK] No standalone UI/UX Chapter (Integrated in 3.2, 3.3)
  [OK] No standalone Architecture Chapter (Integrated in 2.1, 2.2, 2.3, 3.1)

Status: PASSED — 100% TEMPLATE COMPLIANT
================================================================================
```
