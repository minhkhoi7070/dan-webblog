# BlogMNM — Critical Security & Business Logic Fix Plan
**Document:** Security & Business Logic Fix Plan  
**Target:** BlogMNM Application  
**Author:** Senior Laravel Backend Engineer / Security Engineer / QA Engineer  
**Date:** 2026-09-29  

---

## 1. Overview

Comprehensive QA testing of the BlogMNM system identified **5 failures** needing core remediation:
1. **AUTH-07**: Locked user accounts can still authenticate and access the dashboard.
2. **ROLE-11**: Locked author accounts can still create, edit, and submit posts.
3. **INT-10**: Comments marked as `spam` or `pending` are publicly rendered on post detail pages.
4. **SEC-05**: Administrators can lock peer administrator accounts without safeguards.
5. **SEC-06**: Category deletion drops posts via database engine cascade and leaves orphaned thumbnail assets in storage.

This document details the root causes, proposed solutions, affected files, risk assessments, and testing specifications.

---

## 2. Issues, Root Causes, and Proposed Fixes

### Issue 1: [AUTH-07] Locked User Can Still Log In
- **Root Cause:**
  In `app/Http/Requests/Auth/LoginRequest.php`, the `authenticate()` method attempts authentication via `Auth::attempt(...)` but does not verify whether the authenticated user has `is_locked === true`.
- **Proposed Fix:**
  1. In `app/Http/Requests/Auth/LoginRequest.php`, after successful credential check in `authenticate()`, verify `$user->is_locked`.
  2. If `is_locked` is true, immediately log the user out (`Auth::logout()`), invalidate the session, hit the rate limiter, and throw a `ValidationException` with an appropriate error message: `'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'`.
  3. Ensure no authenticated session is established and the user remains a guest.
- **Affected Files:**
  - `app/Http/Requests/Auth/LoginRequest.php`
- **Risk Assessment:**
  - *Risk:* Minimal. Legitimate users with `is_locked = false` continue to authenticate normally.
- **Targeted Test:**
  - `QASuiteTest::test_auth_07_locked_user_login_attempt`
  - Assert authentication fails, user remains guest, cannot access `/dashboard`.

---

### Issue 2: [ROLE-11] Locked Author Can Still Create, Edit, and Submit Posts
- **Root Cause:**
  1. `StorePostRequest::authorize()` only checks if the user role is `author` or `admin`, omitting `! $user->is_locked`.
  2. `PostPolicy` methods (`create`, `update`, `delete`, `submit`) do not check `! $user->is_locked`.
  3. `RoleMiddleware::handle()` checks the user's role against allowed roles, but does not check if the user is locked.
- **Proposed Fix:**
  1. In `app/Policies/PostPolicy.php`: In `before(User $user, string $ability)`, return `false` if `$user->is_locked === true`. This centralized policy gate forbids any post-related action for locked users.
  2. In `app/Http/Requests/StorePostRequest.php`: In `authorize()`, add `&& ! $this->user()->is_locked`.
  3. In `app/Http/Middleware/RoleMiddleware.php`: Abort with 403 Forbidden if `$request->user()->is_locked`.
- **Affected Files:**
  - `app/Policies/PostPolicy.php`
  - `app/Http/Requests/StorePostRequest.php`
  - `app/Http/Middleware/RoleMiddleware.php`
- **Risk Assessment:**
  - *Risk:* Low. Locked users are strictly prevented from executing author/admin actions. Unlocked authors and admins retain full access. Target users managed by admins are unaffected because admin is the actor.
- **Targeted Test:**
  - `QASuiteTest::test_role_05_locked_author_cannot_create_or_submit_post`
  - Test create, update, delete, submit for locked author.

---

### Issue 3: [INT-10] Spam and Pending Comments Render on Public Post Page
- **Root Cause:**
  In `app/Http/Controllers/PostController.php` (line 92), `$post->load(['comments' => fn ($query) => $query->whereNull('parent_id')->with(['user', 'replies.user'])->latest()])` lacks a condition filtering by `status = 'approved'`.
- **Proposed Fix:**
  1. In `app/Models/Comment.php`, add `scopeApproved(Builder $query)` to encapsulate the `where('status', 'approved')` condition.
  2. In `app/Http/Controllers/PostController.php` `show()` method, constrain both top-level comments and nested replies:
     ```php
     'comments' => fn ($query) => $query->whereNull('parent_id')
         ->where('status', 'approved')
         ->with([
             'user',
             'replies' => fn ($q) => $q->where('status', 'approved')->with('user'),
         ])
         ->latest(),
     ```
  3. Keep `Admin\CommentController` query unconstrained so moderators can continue to review `pending`, `approved`, and `spam` comments.
- **Affected Files:**
  - `app/Models/Comment.php`
  - `app/Http/Controllers/PostController.php`
- **Risk Assessment:**
  - *Risk:* Low. Only approved comments will be visible to public readers. Admin moderation remains fully functional.
- **Targeted Test:**
  - Test approved comments appear; pending and spam comments (both top-level and nested) are excluded from the public view.

---

### Issue 4: [SEC-05] Administrator Can Lock Peer Administrators
- **Root Cause:**
  In `app/Http/Controllers/Admin/UserController.php`, the `lock()` method guards against self-lockout (`$user->id === $request->user()->id`), but does not check if the target `$user` has the `admin` role (`$user->isAdmin()`).
- **Proposed Fix:**
  1. In `app/Http/Controllers/Admin/UserController.php`, add an explicit guard in `lock()`:
     ```php
     if ($user->isAdmin()) {
         return redirect()->back()
             ->with('error', 'Hành động bị chặn: Bạn không thể khóa tài khoản của Quản trị viên khác.');
     }
     ```
  2. In `resources/views/admin/users/index.blade.php` and `resources/views/admin/users/show.blade.php`, suppress the "Khóa tài khoản" button when `$user->isAdmin()`.
- **Affected Files:**
  - `app/Http/Controllers/Admin/UserController.php`
  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/users/show.blade.php`
- **Risk Assessment:**
  - *Risk:* None. Protects administrative hierarchy from accidental or malicious peer lockout.
- **Targeted Test:**
  - Admin cannot lock self, admin cannot lock peer admin; admin can lock authors and viewers; target admin remains unlocked.

---

### Issue 5: [SEC-06] Category Deletion Cascades Posts and Leaves Orphan Thumbnails
- **Root Cause:**
  1. `database/migrations/2024_01_01_000003_create_posts_table.php` set `cascadeOnDelete()` on `category_id`.
  2. Deleting a category drops posts directly in MySQL, bypassing Eloquent model deletion events and leaving thumbnail images in `storage/app/public/thumbnails/`.
- **Proposed Fix:**
  1. In `app/Http/Controllers/Admin/CategoryController.php`, prevent category deletion if active posts exist:
     ```php
     if ($category->posts()->exists()) {
         return redirect()->route('admin.categories.index')
             ->with('error', "Không thể xóa chuyên mục '{$category->name}' vì vẫn còn bài viết đang trực thuộc.");
     }
     ```
  2. Create a new migration `2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php` to alter the foreign key constraint on `posts.category_id` from `cascadeOnDelete` to `restrictOnDelete`.
  3. In `app/Models/Post.php`, add a `deleting` model event in `booted()` to ensure thumbnail file cleanup on `Storage::disk('public')` whenever any post is deleted through Eloquent.
- **Affected Files:**
  - `app/Http/Controllers/Admin/CategoryController.php`
  - `app/Models/Post.php`
  - `database/migrations/2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php` (new migration)
- **Risk Assessment:**
  - *Risk:* Low. Safeguards valuable post data from accidental bulk destruction. Empty categories can be deleted cleanly.
- **Targeted Test:**
  - Empty category deletes successfully.
  - Category with posts is blocked with error message.
  - Explicit post deletion verifies image unlinked from public storage.

---

## 3. Implementation Order

1. **Step 1:** Fix `AUTH-07` in `LoginRequest.php`.
2. **Step 2:** Fix `ROLE-11` in `PostPolicy.php`, `StorePostRequest.php`, and `RoleMiddleware.php`.
3. **Step 3:** Fix `INT-10` in `Comment.php` and `PostController.php`.
4. **Step 4:** Fix `SEC-05` in `Admin\UserController.php` and views.
5. **Step 5:** Fix `SEC-06` in `Admin\CategoryController.php`, `Post.php`, and new migration.
6. **Step 6:** Run targeted tests, regression tests, and format check (`vendor/bin/pint`).
