# Final Architecture & Implementation Plan

## Architectural Guidelines

### 1. Migration Order (Sprint 0 - Completed)
1. `users` (independent)
2. `categories` (independent)
3. `tags` (independent)
4. `posts` (depends on users, categories)
5. `post_tag` (depends on posts, tags)
6. `comments` (depends on posts, users, self)
7. `favorites` (depends on users, posts)
8. `likes` (depends on users, posts)
9. `follows` (depends on users)
*Status: Verified. Order is strictly dependent and cascade deletion is implemented correctly.*

### 2. Eloquent Relationships
- **Eager Loading Boundary:** `Post` queries MUST eager load `user`, `category`, and `tags` to prevent N+1 in lists.
- **Aggregation:** Use `withCount(['comments', 'likers', 'favoritedBy'])` for post stats.
- **Polymorphism:** Excluded. Native belongsToMany used for simplicity and performance as per contract.

### 3. Middleware
- `auth`: Native Breeze middleware.
- `role`: Custom `RoleMiddleware` accepting parameters (`role:admin`, `role:author`, `role:viewer`).
- *Strict Layering:* Public routes (Guest), `auth` (Viewer), `auth + role:author,admin` (Author), `auth + role:admin` (Admin).

### 4. Policy (Authorization)
- `PostPolicy`:
  - `view`: Public if published; owner or admin if draft/pending/rejected.
  - `create`: Author, Admin.
  - `update`/`delete`: Owner, Admin.
  - `submit`: Owner (if draft/rejected).
  - `approve`/`reject`: Admin (if pending).
- `CommentPolicy`: Owner or Admin can update/delete.

### 5. Form Requests (Validation Layer)
- Separate FormRequests to isolate validation:
  - `StorePostRequest` / `UpdatePostRequest`: Enforce title uniqueness, status stripping (security: prevent author setting `published`).
  - `StoreCommentRequest`: Validate body length.
  - `UpdateProfileRequest`: Handled by Breeze.

### 6. Controller Boundaries
- **Public:** `PostController` (index, show, search).
- **Viewer:** `CommentController`, `ActivityController`.
- **Author:** `Author\PostController` (CRUD, submit, stats).
- **Admin:** `Admin\DashboardController`, `Admin\UserController`, `Admin\PostController`.
*Rule:* Avoid fat controllers. Use invokable controllers if actions don't fit CRUD.

### 7. AJAX Boundaries
- **Endpoints:** `posts.like`, `posts.favorite`, `authors.follow`.
- **Contract:** Return `{ "liked": true, "likes_count": 25 }`.
- **Security:** CSRF required, native Laravel Session Auth (fetch API with credentials).

### 8. Blade Components
- UI Components: `<x-post-card>`, `<x-comment-item>`, `<x-status-badge>`, `<x-flash-message>`, `<x-modal>`.
- Layouts: `guest` (Public), `app` (Dashboard), `admin` (Admin specific).

---

## Sprint Plans

### SPRINT 1: Guest Features
- **Goal:** Unauthenticated access to read content, search, and view profiles.
- **Files to Modify:** `routes/web.php`.
- **Files to Create:** 
  - `app/Http/Controllers/PublicPostController.php`
  - `app/Http/Controllers/AuthorProfileController.php`
  - `resources/views/posts/index.blade.php`, `show.blade.php`
  - `resources/views/authors/show.blade.php`
  - `<x-post-card>` component
- **Dependencies:** Tailwind CSS.
- **Database Impact:** Read-only.
- **Route Impact:** Added `posts.index`, `posts.show`, `authors.show`.
- **UI Impact:** Public homepage, article detail page, author profile page.
- **Test Plan:** Test `posts.index` status 200, search filters correct posts, unpublished posts return 404 for guests. N+1 assertion tests.
- **Artifact Plan:** Update `project-audit.md`.
- **Rollback Risk:** Low (purely read operations).

### SPRINT 2: Viewer Features
- **Goal:** Authenticated interactions (Like, Favorite, Follow, Comment).
- **Files to Modify:** `routes/web.php`.
- **Files to Create:**
  - `app/Http/Controllers/InteractionController.php` (for AJAX)
  - `app/Http/Controllers/CommentController.php`
  - `app/Http/Requests/StoreCommentRequest.php`
  - `resources/js/interactions.js`
- **Dependencies:** None.
- **Database Impact:** Writes to pivot tables (`likes`, `favorites`, `follows`), `comments` table.
- **Route Impact:** `posts.like`, `posts.favorite`, `authors.follow`, `comments.store`.
- **UI Impact:** Interactive buttons with JS fetch updates. Comment section below posts.
- **Test Plan:** Assert pivot tables update, JSON returns exact contract keys, unauthorized users receive 401/403.
- **Artifact Plan:** API Contract validation artifact.
- **Rollback Risk:** Medium (Introduces JS logic and state mutation).

### SPRINT 3: Author Features
- **Goal:** Secure CMS for authors to manage drafts, uploads, and submissions.
- **Files to Modify:** `routes/web.php`.
- **Files to Create:**
  - `app/Http/Controllers/Author/PostController.php`
  - `app/Http/Requests/StorePostRequest.php`, `UpdatePostRequest.php`
  - `resources/views/author/posts/*`
- **Dependencies:** Local/Public Storage symlink.
- **Database Impact:** Writes to `posts`, `post_tag`.
- **Route Impact:** `author.posts.*` (mapped to `posts.create`, `posts.store`, etc.).
- **UI Impact:** Author dashboard, WYSIWYG/Textarea form, Status badges.
- **Test Plan:** Assert an author cannot publish directly, cannot edit someone else's post, image upload validation works.
- **Artifact Plan:** Workflow state machine diagram.
- **Rollback Risk:** High (Complex authorization and file I/O).

### SPRINT 4: Admin Features
- **Goal:** Global moderation, user management, and taxonomy control.
- **Files to Modify:** `routes/web.php`.
- **Files to Create:**
  - `app/Http/Controllers/Admin/*`
  - `resources/views/admin/*`
- **Dependencies:** None.
- **Database Impact:** Updates `users.is_locked`, `posts.status`.
- **Route Impact:** `admin.*`.
- **UI Impact:** High-level dashboard, data tables for moderation.
- **Test Plan:** Strict middleware tests (Author accessing admin -> 403), state transition tests (pending -> published).
- **Artifact Plan:** `final-audit.md` and Rubric Matrix.
- **Rollback Risk:** High (System-wide permissions).
