# BlogMNM — System Requirements Specification

## 1. Introduction
This document defines the functional and non-functional requirements for the **BlogMNM** platform. All requirements documented herein are verified against the actual Laravel 12 codebase and test suite.

---

## 2. Functional Requirements (FR)

### FR-01: User Registration & Initial Role Assignment
- **Description**: The system must allow new users to register with `name`, `email`, and a confirmed `password`.
- **Behavior**: Newly registered users are strictly assigned the `viewer` role by default. Direct parameter tampering (e.g. passing `role=admin` in the payload) must be ignored.
- **Code Reference**: `RegisteredUserController.php`, `User.php` (`$fillable` whitelist).
- **Verification**: `tests/Feature/Auth/RegistrationTest.php`, `AUTH-01`, `AUTH-03`.

### FR-02: Authentication, Session Management & Throttling
- **Description**: Users must authenticate using valid email and password credentials.
- **Behavior**: Successful login regenerates the session ID to prevent session fixation. Failed logins are throttled to 5 attempts per minute per IP/email combination via `LoginRequest`.
- **Code Reference**: `AuthenticatedSessionController.php`, `LoginRequest.php`.
- **Verification**: `tests/Feature/Auth/AuthenticationTest.php`, `AUTH-04`, `AUTH-06`.

### FR-03: User Account Locking & Session Invalidation
- **Description**: Administrators can lock or unlock user accounts.
- **Behavior**: When an account is locked (`is_locked = true`), any active session is ejected upon authentication attempt. Locked users are blocked from creating/updating posts, submitting posts, commenting, liking, favoriting, or following. Self-locking by an administrator is explicitly forbidden.
- **Code Reference**: `LoginRequest.php`, `RoleMiddleware.php`, `PostPolicy.php`, `InteractionController.php`, `Admin\UserController.php`.
- **Verification**: `QASuiteTest::test_auth_07_locked_user_login_attempt`, `AUTH-07`, `ROLE-11`, `SEC-05`.

### FR-04: Author Post Creation, Editing & Tagging
- **Description**: Authors and Administrators can create, edit, update, and delete their own blog posts.
- **Behavior**: Creating a post requires `title`, `category_id`, and `body`. Post slugs are automatically generated. Posts can be tagged with multiple existing tags via a many-to-many pivot (`post_tag`). Newly created posts default to `draft` status with `0` views.
- **Code Reference**: `PostController.php`, `StorePostRequest.php`, `Post.php`.
- **Verification**: `AuthorPostTest.php`, `POST-01`, `POST-03`, `POST-05`.

### FR-05: Featured Thumbnail Storage & Orphan File Cleanup
- **Description**: Authors can upload an image thumbnail (`jpg`, `jpeg`, `png`, `webp` $\le$ 2MB) stored on the public storage disk.
- **Behavior**: When updating a post with a new thumbnail, the previous image file must be deleted from disk. When a post is deleted, its associated thumbnail must be unlinked.
- **Code Reference**: `PostController.php` (`store`, `update`, `destroy`), `Post::booted()` deletion hook.
- **Verification**: `AuthorPostTest::test_thumbnail_upload_works_through_public_filesystem`, `POST-03`, `POST-04`.

### FR-06: Editorial Review & Moderation Workflow
- **Description**: Posts follow a rigid editorial pipeline: `draft` $\to$ `pending` $\to$ `published` or `rejected`.
- **Behavior**: Authors submit draft or rejected posts for review (`POST /posts/{id}/submit`). Administrators approve (`POST /admin/posts/{id}/approve`) setting `published_at`, `reviewed_by`, and `reviewed_at`. Alternatively, administrators reject posts with a mandatory or optional reason note (`POST /admin/posts/{id}/reject`). Authors can view rejection feedback and resubmit.
- **Code Reference**: `PostController.php`, `Admin\PostController.php`, `PostPolicy.php`.
- **Verification**: `AuthorPostTest.php`, `AdminTest.php`, `POST-06`, `POST-07`, `POST-08`, `POST-09`.

### FR-07: Public Feed, Article Detail & View Counter
- **Description**: Guests and authenticated users can view published posts in a reverse-chronological feed and read individual post details.
- **Behavior**: Only posts with `status = 'published'` are visible to the public. Visiting `/posts/{slug}` increments the `views` column atomically. Draft or pending posts return 404 for unprivileged visitors.
- **Code Reference**: `PostController.php` (`index`, `show`).
- **Verification**: `GuestPostTest.php`, `ROLE-01`, `ROLE-02`, `POST-02`.

### FR-08: Full-Text Keyword Search
- **Description**: Users can search published articles by query keyword (`?q=keyword`).
- **Behavior**: `Post::scopeSearch()` matches query against `title`, `excerpt`, and `body` using grouped SQL OR clauses within the `published` scope. Search parameters are preserved across pagination and filters.
- **Code Reference**: `Post.php` (`scopeSearch`), `PostController.php`.
- **Verification**: `GuestPostTest::test_search_works`, `SRCH-01`, `SRCH-03`.

### FR-09: Taxonomy Classification & Filtering
- **Description**: Posts are categorized under a single Category and tagged with multiple Tags.
- **Behavior**: Clicking category chips filters the feed via `?category={slug}`. Tag filtering works via `?tag={slug}`. Active filter chips display count and provide an easy reset button.
- **Code Reference**: `PostController.php`, `Post.php` (`scopeCategory`, `scopeTag`).
- **Verification**: `GuestPostTest::test_category_filter_works`, `SRCH-02`.

### FR-10: Social Interactions (Like, Favorite, Follow)
- **Description**: Authenticated users can like posts, bookmark favorites, and follow authors.
- **Behavior**: Endpoints return JSON responses. Likes toggle state and return `{liked: bool, likes_count: int}`. Favorites toggle state and return `{saved: bool}`. Follows toggle author following and return `{following: bool}`. Self-following is rejected with HTTP 422. Unauthenticated attempts prompt or redirect to login.
- **Code Reference**: `InteractionController.php`, `FavoriteController.php`.
- **Verification**: `ViewerInteractionTest.php`, `INT-01`, `INT-02`, `INT-03`, `INT-04`, `INT-05`, `INT-06`.

### FR-11: Threaded Comments & Comment Moderation
- **Description**: Authenticated users can comment on published posts and reply to existing comments.
- **Behavior**: Comments support two-level threading (`parent_id`). The system strictly validates that `parent_id` belongs to the same post (anti-cross-posting). Public views only render approved comments (`status = 'approved'`). Administrators can approve, mark spam, or delete comments.
- **Code Reference**: `CommentController.php`, `StoreCommentRequest.php`, `Admin\CommentController.php`.
- **Verification**: `ViewerInteractionTest.php`, `QASuiteTest.php`, `INT-07`, `INT-08`, `INT-09`, `INT-10`.

### FR-12: Author Performance Dashboard & Metrics
- **Description**: Authors can access `/author/posts/stats` to monitor their portfolio performance.
- **Behavior**: Displays total post counts by status (draft, pending, published, rejected), total views, total likes, total comments, and total bookmarks across their authored posts. Includes an editorial management table.
- **Code Reference**: `PostController.php` (`stats`), `author/posts/stats.blade.php`.
- **Verification**: `AuthorPostTest.php`, `ROLE-08`.

### FR-13: Administrative Governance & Metric Overview
- **Description**: Administrators access `/admin` to monitor global platform statistics.
- **Behavior**: Metrics include total users (by role), total posts (by status), total comments (by status), and moderation queue summaries.
- **Code Reference**: `Admin\DashboardController.php`, `admin/dashboard.blade.php`.
- **Verification**: `AdminTest.php`, `ROLE-12`.

### FR-14: Category Lifecycle & Deletion Protection
- **Description**: Administrators can create, edit, and delete categories.
- **Behavior**: When an administrator attempts to delete a category that contains active posts, deletion is blocked with an informative error message. The database enforces this via a `RESTRICT` foreign key constraint.
- **Code Reference**: `Admin\CategoryController.php`, migration `2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php`.
- **Verification**: `QASuiteTest::test_sec_06_case_b_category_with_posts_deletion_is_blocked`, `SEC-06`.

### FR-15: Profile Customization & Password Management
- **Description**: Authenticated users can update their profile information (name, email) and password, or delete their account.
- **Behavior**: Updating email resets verification timestamp. Account deletion requires current password confirmation and cascades related records.
- **Code Reference**: `ProfileController.php`, `PasswordController.php`.
- **Verification**: `tests/Feature/ProfileTest.php`.

---

## 3. Non-Functional Requirements (NFR)

### NFR-01: Performance & Query Optimization
- Eager loading (`with(['user', 'category', 'tags'])`, `withCount(['likers', 'favoritedBy', 'comments'])`) must be utilized on list endpoints to prevent N+1 query degradation.
- Key query columns (`posts.status`, `posts.published_at`, `posts.views`, `comments.status`, `users.is_locked`) are indexed in the schema.

### NFR-02: Security & Defense-in-Depth
- All web POST/PUT/DELETE requests enforce CSRF tokens via Laravel's `ValidateCsrfToken` middleware.
- All database operations utilize PDO prepared statements via Eloquent to eliminate SQL injection vulnerabilities.
- User input is escaped in Blade templates via `{{ $variable }}` to prevent XSS.
- Sensitive mass-assignment attributes (e.g. `is_locked`, `role`) are protected via explicit `$fillable` whitelists and stripped in FormRequest lifecycle.

### NFR-03: Authorization & Access Control
- Granular authorization enforced through `RoleMiddleware` and Laravel Policies (`PostPolicy`, `CommentPolicy`).
- Unauthenticated requests to protected endpoints return 302 redirects to login. Unprivileged requests return HTTP 403 Forbidden.

### NFR-04: Data Integrity & Constraints
- Database-level foreign keys ensure relational integrity (`ON DELETE CASCADE` for comments, likes, favorites, follows; `ON DELETE RESTRICT` for categories with active posts).
- Composite unique indexes prevent duplicate likes (`user_id`, `post_id`), duplicate favorites (`user_id`, `post_id`), duplicate follows (`follower_id`, `following_id`), and duplicate post-tag bindings (`post_id`, `tag_id`).

### NFR-05: Responsive UI/UX Across Device Viewports
- Layouts seamlessly scale across:
  - **Desktop** ($\ge$ 1280px): Full sidebar (`w-64`), centered 660px feed, right context area.
  - **Tablet** (768px - 1023px): Compact icon-only sidebar (`w-20`), centered feed.
  - **Mobile** ($\le$ 767px): Sidebar hidden, sticky minimal top header, fixed bottom navigation bar (`h-16`).
- Touch targets must adhere to $\ge$ 44px $\times$ 44px clickable areas on mobile.

### NFR-06: Accessibility & Contrast
- Semantic HTML elements (`<nav>`, `<header>`, `<main>`, `<article>`, `<section>`, `<footer>`) are used throughout.
- High-contrast visual palette meets WCAG 2.1 AA contrast standards in both dark and light modes.
- Focus outlines and interactive states provided for keyboard and screen-reader accessibility.

### NFR-07: Design System & Theme Engine Consistency
- Centralized CSS Custom Properties defined in `resources/css/app.css`.
- Synchronous inline theme script in `<head>` eliminates Flash of Unstyled Content (FOUC).
- Theme choice persisted in client `localStorage` with fallback to `prefers-color-scheme`.

### NFR-08: Code Maintainability & Framework Standards
- Strictly adheres to PSR-12 coding style and Laravel conventions, verified by `vendor/bin/pint`.
- Business logic isolated in FormRequests, Policies, and Eloquent Models rather than bloated controllers or Blade templates.

### NFR-09: Boundary Safety & Graceful Failure
- Out-of-bounds pagination parameters (`?page=0`, `?page=99999`) redirect to safe boundary pages without application crashes or 500 exceptions.
- Model route binding automatically generates clean HTTP 404 responses for non-existent slugs.

### NFR-10: Comprehensive Test Automation
- The application must maintain 100% pass rate across the full automated test suite (142 tests, 514 assertions) without skipped or failing tests.
