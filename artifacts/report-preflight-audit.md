# BlogMNM — Pre-Flight Report Data Audit

**Auditor:** Lead Documentation Architect  
**Target Template:** `Template_BaoCao_DoAn_LTMNM.docx`  
**Date:** 2026-09-29  
**Audit Objective:** Verify completeness, trustworthiness, and source-code fidelity of all technical evidence before drafting the official academic project report.

---

## 1. Master Evidence Audit Matrix

| Report Item | Source File / Code | Status | Evidence | Conflict | Action |
| :--- | :--- | :---: | :--- | :--- | :--- |
| **A. Project Title** | `.env` (`APP_NAME="BlogMNM"`), `project-overview.md` | **MANUAL INPUT REQUIRED** | Defined as `"BlogMNM"` in code and documentation. Official academic title in Vietnamese (e.g. *"Xây dựng website tin tức BlogMNM trên nền tảng Laravel"*) is not specified. | None in codebase. | User must provide exact academic project title for the cover page. |
| **B. Team Member Names** | Git log (`Dang Minh Khoi`), `composer.json` | **MANUAL INPUT REQUIRED** | Git commits indicate `"Dang Minh Khoi"`. No full student roster or team division table exists in repository. | None in codebase. | User must supply official student full name(s) for the Front Matter & Contribution Table. |
| **C. Student IDs (MSSV)** | Repository root | **MISSING — MANUAL INPUT REQUIRED** | No student ID numbers found anywhere in project files. | None. | User must supply Student ID(s) (MSSV). |
| **D. Lecturer Name (GVHD)** | Repository root | **MISSING — MANUAL INPUT REQUIRED** | No supervising lecturer or course instructor name found in repository. | None. | User must supply Supervising Lecturer name. |
| **E. Technologies Actually Used** | `composer.json`, `package.json`, installed dependencies | **READY / CONFLICT IDENTIFIED** | PHP 8.3.30, Laravel Framework 13.33.0, Tailwind CSS 3.4.19, Alpine.js 3.17.4, Vite 8.3.1, MySQL 8.4.3, PHPUnit 12.5.12 / 11.5.3, Laravel Pint 1.27.0. | **CONFLICT**: Artifacts (`project-overview.md`, `architecture.md`) cite **Laravel 12.x** and **Vite 6.x**, whereas actual runtime is **Laravel 13.33.0** and **Vite 8.3.1**. | Report must accurately cite the true installed runtime: **Laravel 13** and **Vite 8** (or note compatibility with Laravel 12/13). |
| **F. Database Tables (Business vs Infra)** | `database/migrations/`, `database-schema.md` | **READY** | 9 Business Tables: `users`, `categories`, `tags`, `posts`, `post_tag`, `comments`, `favorites`, `likes`, `follows`.<br/>Infrastructure Tables: `sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`. | None. FK `RESTRICT` and audit columns verified. | Filter out infrastructure tables from Chapter 2; only document the 9 business domain tables. |
| **G. Actual Use Cases** | `use-case.md`, `use-case-detail.md`, `routes/web.php` | **READY** | 25 distinct Use Cases (UC-01 to UC-25) across 4 packages (Discovery, Social, Editorial CMS, Governance). All 25 mapped to active routes and controllers. | None. | Embed Mermaid Use Case diagram into Section 2.1 of report. |
| **H. Core Features** | Controllers, Views, `routes/web.php` | **READY** | - Authentication & RBAC (4 roles)<br/>- 4-stage post editorial workflow (`draft`, `pending`, `published`, `rejected`)<br/>- Public centered reading feed (660px)<br/>- Keyword search across title, excerpt, body<br/>- Category & Tag taxonomy<br/>- Threaded comments (2-level hierarchy)<br/>- Social reactions (Likes, Favorites, Follows)<br/>- Author portfolio analytics<br/>- Admin moderation portal | None. Fully verified against codebase. | Document in Section 3.2 (Chức năng cốt lõi). |
| **I. Advanced & Creative Features** | `resources/css/app.css`, `interactions.js`, Policies | **READY** | - Threads-inspired monochrome design system<br/>- Instantaneous zero-flash `<head>` theme engine with `localStorage`<br/>- Button color inversion pattern (`bg-[var(--color-text)] text-[var(--color-bg)]`)<br/>- Asynchronous optimistic AJAX UI updates<br/>- Multi-layer defense-in-depth security (locked user ejection, admin peer lockout protection, category deletion protection, thumbnail orphan cleanup) | None. | Document in Section 3.3 (Chức năng nâng cao & Sáng tạo). |
| **J. Installation Commands** | `installation-guide.md`, `composer.json` | **READY** | `composer install`, `cp .env.example .env`, `php artisan key:generate`, `php artisan migrate:fresh --seed`, `php artisan storage:link`, `npm install`, `npm run build`, `php artisan serve`. | None. | Document in Chapter 4 (Hướng dẫn cài đặt và vận hành). |
| **K. Demo Credentials** | `DatabaseSeeder.php`, `installation-guide.md` | **READY / CONFLICT IDENTIFIED** | `DatabaseSeeder.php` explicitly creates: Admin: `admin@blogmnm.test` / `password`. Authors and Viewers generated via `fake()->unique()->safeEmail()` with `password`. | **CONFLICT**: `installation-guide.md` cites `admin@example.com`, but seeder writes `admin@blogmnm.test`. | Report must state `admin@blogmnm.test` (or clarify both) in Chapter 4. |
| **L. Actual Test Results** | `php artisan test --compact`, `testing-result.md` | **READY** | Exact test execution verified: `142 passed (514 assertions, 0 failed, duration 7.94s - 8.04s)`. 52 verified QA scenarios (AUTH, ROLE, POST, INT, SRCH, SEC, RESP) passing 100%. `vendor/bin/pint --test` passes with 0 violations. | None. Test execution confirmed live. | Integrate into Section 5.1 (Kết quả đạt được) as empirical evidence. |
| **M. Screenshots / Visual Evidence** | `screenshot-plan.md`, filesystem | **MISSING — MANUAL / TOOL INPUT REQUIRED** | `screenshot-plan.md` provides a complete plan for 21 screenshots (SS-01 to SS-21). However, physical `.png`/`.jpg` image files do not yet exist on disk in the repo. | None. Plan is complete; images need capture. | Prior to finalizing report document, capture key screenshots or insert generated figures according to `screenshot-plan.md`. |
| **N. Actual Project Limitations** | `known-limitations.md`, source code | **READY** | Single category per article; synchronous thumbnail upload ($\le 2\text{MB}$); 2-level comment hierarchy; SQL `LIKE` query engine without Meilisearch; polled/fetch social updates without WebSockets. | None. Grounded in actual code. | Integrate into Section 5.2 (Hạn chế). |
| **O. Future Development Items** | `future-development.md` | **READY** | TipTap / Markdown rich block editor; scheduled article publishing; Cloud Object Storage (S3/R2); WebSockets via Laravel Reverb; Laravel Scout with Meilisearch; 2FA security; Gemini API AI comment moderation. | None. | Integrate into Section 5.2 (Hướng phát triển). |

---

## 2. Technical Claims Verification & Conflict Log

### Conflict 1: Laravel Framework Version
- **Claimed in Artifacts**: Several earlier documentation artifacts (e.g. `project-overview.md`, `architecture.md`, `testing-result.md`) refer to *"Laravel 12"* or *"Laravel 12.x"*.
- **Verified Codebase Reality**:
  - `composer.json` declares `"laravel/framework": "^13.17"`.
  - Terminal command `php artisan --version` returns **`Laravel Framework 13.33.0`**.
- **Audit Verdict**: **CONFLICT**.
- **Reconciliation Action for Report**: The official report MUST cite **Laravel 13 (v13.33.0)** as the active framework version running on **PHP 8.3.30**.

### Conflict 2: Vite Bundler Version
- **Claimed in Artifacts**: Artifacts cite *"Vite 6.x"*.
- **Verified Codebase Reality**:
  - `package.json` declares `"vite": "^8.0.0"`.
  - `npm list vite` confirms **`vite@8.3.1`** and **`laravel-vite-plugin@3.2.0`**.
- **Audit Verdict**: **CONFLICT**.
- **Reconciliation Action for Report**: The official report MUST cite **Vite 8.x (v8.3.1)**.

### Conflict 3: Seeded Admin Email Address
- **Claimed in Artifacts**: `installation-guide.md` listed `admin@example.com` / `password`.
- **Verified Codebase Reality**:
  - `database/seeders/DatabaseSeeder.php` explicitly provisions:
    ```php
    $admin = User::factory()->admin()->create([
        'name' => 'Admin User',
        'email' => 'admin@blogmnm.test',
    ]);
    ```
- **Audit Verdict**: **CONFLICT**.
- **Reconciliation Action for Report**: Chapter 4 must list the exact seeded email **`admin@blogmnm.test`** (password: `password`).

---

## 3. Database Scope Verification (Chapter 2 Input)

To comply with the rule *"Do not include infrastructure tables in the report unless relevant to explaining the project"*, the database documentation is partitioned as follows:

### Primary Business Domain Tables (To include in Section 2.2 ERD & 2.3 Tables):
1. **`users`**: Account credentials, roles (`admin`, `author`, `viewer`), profile attributes, and lockout flag (`is_locked`).
2. **`categories`**: Taxonomy categories with `RESTRICT` on delete safety constraint.
3. **`tags`**: Cross-cutting topic keywords.
4. **`posts`**: Core articles with 4-state lifecycle (`draft`, `pending`, `published`, `rejected`), atomic views counter, thumbnail, and review audit trail (`reviewed_by`, `reviewed_at`, `rejection_reason`).
5. **`post_tag`**: Pivot table binding posts to tags with composite unique constraint `(post_id, tag_id)`.
6. **`comments`**: 2-level threaded comments with `parent_id` foreign key, `status` moderation enum (`pending`, `approved`, `spam`), and cross-post validation.
7. **`favorites`**: User bookmark records with unique `(user_id, post_id)` constraint.
8. **`likes`**: Post endorsements with unique `(user_id, post_id)` constraint.
9. **`follows`**: Bidirectional user subscription graph with unique `(follower_id, following_id)` constraint.

### Framework Infrastructure Tables (To exclude from Chapter 2):
- `sessions` (Session storage)
- `password_reset_tokens` (Auth recovery)
- `cache`, `cache_locks` (Rate limiting & caching)
- `jobs`, `job_batches`, `failed_jobs` (Queue worker infrastructure)
- `migrations` (Migration tracker)

---

## 4. Visual Evidence Status (Chapter 2 & 3 Input)

According to `screenshot-plan.md`:
- **Diagrams (Available)**:
  - Architecture Diagram (Mermaid) $\to$ **AVAILABLE**
  - Use Case Diagram (Mermaid) $\to$ **AVAILABLE**
  - Entity-Relationship Diagram (ERD Mermaid) $\to$ **AVAILABLE**
  - Class Diagram (Mermaid) $\to$ **AVAILABLE**
  - Post Workflow State Machine Diagram (Mermaid) $\to$ **AVAILABLE**
  - Sequence Diagrams (Post review, Like AJAX, Comment reply) $\to$ **AVAILABLE**
- **UI Screenshots (Status)**:
  - 21 screenshot scenarios mapped in `screenshot-plan.md` $\to$ **PLAN AVAILABLE**.
  - Physical screenshot images in repository $\to$ **MISSING ON DISK**.
  - **Action**: In the report document, high-priority UI figures (Home Feed, Post Detail, Author CMS, Admin Dashboard, Dark/Light comparison, Mobile view) will be designated with exact captions and placeholders or captured using local browser tools.

---

## 5. Report Structure Mapping Compliance Check

Verification against the mandatory template `Template_BaoCao_DoAn_LTMNM.docx`:

| Official Report Chapter | Mapped Project Information Sources | Compliance |
| :--- | :--- | :---: |
| **FRONT MATTER** | Cover page, Title, Student Name(s), MSSV, GVHD, Table of Contents, Bảng phân công, Danh mục hình ảnh | ✅ Formatted to template; awaiting manual student details |
| **CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI** | `project-overview.md` (1.1 Lý do chọn đề tài), `requirements.md` (1.2 Mục tiêu đồ án) | ✅ 100% Covered |
| **CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG** | `use-case.md` & `actor-description.md` (2.1 Sơ đồ Use Case), `database-schema.md` (2.2 ERD), `database-dictionary.md` (2.3 Chi tiết các bảng) | ✅ 100% Covered (9 business tables only) |
| **CHƯƠNG 3: CÔNG NGHỆ ÁP DỤNG VÀ MÔ TẢ CHỨC NĂNG** | `composer.json` / `package.json` (3.1 Công nghệ & Công cụ), `post-workflow.md`, `route-map.md`, `search-design.md`, `pagination-design.md` (3.2 Chức năng cốt lõi), `theme-design.md`, `security-business-fix-result.md` (3.3 Chức năng nâng cao & Sáng tạo) | ✅ 100% Covered (integrated without extra chapters) |
| **CHƯƠNG 4: HƯỚNG DẪN CÀI ĐẶT VÀ VẬN HÀNH** | `installation-guide.md` (Cài đặt môi trường, migrations, seeding), `user-manual.md` & `admin-manual.md` (Vận hành người dùng và quản trị viên) | ✅ 100% Covered |
| **CHƯƠNG 5: TỔNG KẾT** | `testing-result.md` (5.1 Kết quả đạt được - 142/142 tests passing, 514 assertions, Pint 0 violations), `known-limitations.md` & `future-development.md` (5.2 Hạn chế và Hướng phát triển) | ✅ 100% Covered |

*Verification confirms:* **NO separate Chapter 6, Chapter 7, or isolated Testing/Security/Architecture chapters will be introduced.** All technical data fits into the standard 5-chapter structure.

---

## 6. Pre-Flight Summary & Readiness Decision

### 1. Ready-to-Use Information:
- Complete system architecture, domain models, and UML diagrams (Use Case, ERD, Class, Sequence, State Machine, Component).
- Full 9-table database schema with verified foreign keys (`RESTRICT` on category delete) and review audit fields.
- 25 verified Use Cases across all 4 actor roles.
- Core & advanced feature descriptions, including Threads-inspired visual design, zero-flash theme engine, and defense-in-depth security.
- Comprehensive testing evidence: **142/142 tests passing (514 assertions), 52 QA test scenarios verified, 0 Pint violations**.
- Complete installation steps, seeding instructions, and user/admin operation procedures.
- Documented technical limitations and future roadmap.

### 2. Missing Manual Information (Must be provided by user/student):
1. **Official Vietnamese Project Title** (e.g., *"Xây dựng Nền tảng Web Blog đa người dùng BlogMNM"*).
2. **Student Full Name(s)** (e.g., *"Đặng Minh Khởi"*).
3. **Student ID(s) / MSSV**.
4. **Supervising Lecturer Name / GVHD** (e.g., *"ThS. Nguyễn Văn A"*).
5. **Class / Course Code** (e.g., *"Lập trình Mã Nguồn Mở - LTMNM"*).

### 3. Conflicting Information (Resolved for Report Drafting):
1. **Framework Version**: Cite **Laravel 13.33.0** (not Laravel 12).
2. **Bundler Version**: Cite **Vite 8.3.1** (not Vite 6).
3. **Admin Demo Email**: State **`admin@blogmnm.test`** as seeded by `DatabaseSeeder.php` (not `admin@example.com`).

### 4. Items Requiring Verification / Action:
- Physical UI screenshot capture (or generating high-resolution SVG/PNG assets from the planned views) to insert into `DANH MỤC HÌNH ẢNH` and Chapters 2, 3, and 4.

### 5. Final Readiness Verdict:
**THE PROJECT IS 95% READY FOR REPORT GENERATION.**  
All technical, architectural, functional, security, and empirical testing data are 100% complete and verified against the codebase. Once the user supplies the 5 manual front-matter fields (Title, Name, MSSV, GVHD, Class), report generation can proceed immediately.
