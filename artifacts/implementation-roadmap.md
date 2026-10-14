# Implementation Roadmap

## Sprint 0: Data Contract + Foundation (Completed)
- **Objectives:** Establish DB schema, models, relationships, base auth, and role middleware.
- **Status:** Done.

## Sprint 1: Guest Features
- **Objectives:** Allow unauthenticated users to read published posts, browse categories/tags, view author profiles, and search.
- **Files:**
  - `routes/web.php`
  - `app/Http/Controllers/PostController.php` (Public)
  - `app/Http/Controllers/AuthorController.php`
  - `resources/views/layouts/app.blade.php` (Main Layout)
  - `resources/views/posts/index.blade.php`, `show.blade.php`
- **Dependencies:** Tailwind CSS.
- **Risks:** N+1 queries when loading posts with author/category/tags.
- **Tests:** Feature tests for viewing public routes, checking search logic, and N+1 prevention.
- **Artifacts:** Update `project-audit.md` Route Contract.

## Sprint 2: Viewer Features
- **Objectives:** Allow authenticated users (Viewers) to interact (Like, Favorite, Follow, Comment) and view activity.
- **Files:**
  - `app/Http/Controllers/CommentController.php`
  - `app/Http/Controllers/FavoriteController.php`
  - `app/Http/Controllers/LikeController.php`
  - `app/Http/Controllers/FollowController.php`
  - `app/Http/Controllers/ActivityController.php`
  - `resources/views/activity/index.blade.php`
  - JS logic for AJAX requests (fetch API).
- **Dependencies:** JSON API responses for interactions.
- **Risks:** Security (CSRF for AJAX), Authorization (users only updating their own data).
- **Tests:** Feature tests for AJAX endpoints, Comment creation, Profile viewing.
- **Artifacts:** API Contract documentation (if needed).

## Sprint 3: Author Features
- **Objectives:** Author CMS for Post CRUD, image upload, workflow (draft -> pending), and statistics.
- **Files:**
  - `routes/web.php` (Route group with `role:author,admin`)
  - `app/Http/Controllers/Author/PostController.php`
  - `app/Http/Requests/StorePostRequest.php`, `UpdatePostRequest.php`
  - `resources/views/author/posts/*`
- **Dependencies:** File storage (`storage/app/public`).
- **Risks:** Bypassing workflow (Author setting status to published), unauthorized edits, insecure file uploads.
- **Tests:** Policies enforcement, File upload validation, Workflow state transitions.
- **Artifacts:** `use-case.md` for Author workflow.

## Sprint 4: Admin Features
- **Objectives:** Admin CMS for managing users, categories, and moderating posts/comments.
- **Files:**
  - `routes/web.php` (Route group with `role:admin`)
  - `app/Http/Controllers/Admin/*`
  - `resources/views/admin/*`
- **Dependencies:** None additional.
- **Risks:** Admin routes accidentally exposed.
- **Tests:** Middleware restriction tests, moderation action tests.
- **Artifacts:** Final `project-audit.md` and `testing-result.md`.
