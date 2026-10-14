# BlogMNM — Critical Security & Business Logic Fix Result

**Date:** 2026-09-29  
**Role:** Senior Laravel Backend Engineer / Security Engineer / QA Engineer  
**Status:** All 5 Defect Fixes Fully Implemented, Tested, and Verified  
**Total Tests:** 142 passed (514 assertions)  

---

## 1. Executive Summary

All 5 failures identified during QA full-system testing have been addressed at their source without adding extraneous features, breaking existing contracts, or compromising existing working business logic.

| Defect ID | Severity | Category | Pre-Fix Status | Post-Fix Status | Verification Test |
| :--- | :--- | :--- | :---: | :---: | :--- |
| **AUTH-07** | High | Auth / Login | **FAIL** | **PASS** | `test_auth_07_locked_user_login_attempt` |
| **ROLE-11** | High | Role / Author | **FAIL** | **PASS** | `test_role_11_locked_author_cannot_create_update_submit_or_delete` |
| **INT-10** | Medium | Post / Comments | **FAIL** | **PASS** | `test_int_10_pending_and_spam_comments_and_replies_hidden_publicly` |
| **SEC-05** | Medium | Security / Admin | **FAIL** | **PASS** | `test_sec_05_admin_cannot_lock_another_admin` |
| **SEC-06** | Medium | Database / Integrity | **FAIL** | **PASS** | `test_sec_06_case_b_category_with_posts_deletion_is_blocked` |

---

## 2. Detailed Fix Breakdown

### 1. [AUTH-07] Locked User Can Still Log In → FIXED (PASS)
- **Root Cause:** `LoginRequest::authenticate()` only verified `Auth::attempt(...)` credentials without checking whether the user was locked (`is_locked === true`).
- **Fix Implemented:**
  In `app/Http/Requests/Auth/LoginRequest.php`:
  After `Auth::attempt(...)`, added check:
  ```php
  if (Auth::user()?->is_locked) {
      Auth::logout();
      $this->session()->invalidate();
      $this->session()->regenerateToken();
      RateLimiter::hit($this->throttleKey());
      throw ValidationException::withMessages([
          'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
      ]);
  }
  ```
- **Files Modified:**
  - `app/Http/Requests/Auth/LoginRequest.php`
- **Result:** Locked users are immediately logged out, session is invalidated, and access to `/dashboard` is blocked with a 422 validation error. Unlocked users authenticate normally.

---

### 2. [ROLE-11] Locked Author Can Still Create, Edit, and Submit Posts → FIXED (PASS)
- **Root Cause:**
  1. `StorePostRequest::authorize()` only checked user role, omitting `! $this->user()->is_locked`.
  2. `PostPolicy` methods did not check `! $user->is_locked`.
  3. `RoleMiddleware` allowed users with valid roles through even if their account was locked.
- **Fix Implemented:**
  1. In `app/Policies/PostPolicy.php`: Updated `before(User $user, string $ability)` to immediately return `false` if `$user->is_locked === true`.
  2. In `app/Http/Requests/StorePostRequest.php`: Added `&& ! $this->user()->is_locked` to `authorize()`.
  3. In `app/Http/Middleware/RoleMiddleware.php`: Added `|| $request->user()->is_locked` check to abort with HTTP 403 Forbidden.
- **Files Modified:**
  - `app/Policies/PostPolicy.php`
  - `app/Http/Requests/StorePostRequest.php`
  - `app/Http/Middleware/RoleMiddleware.php`
- **Result:** Locked authors cannot view create/edit forms, cannot store new posts, cannot update existing posts, cannot submit posts for review, and cannot delete posts. All protected endpoints return HTTP 403.

---

### 3. [INT-10] Spam and Pending Comments Render on Public Post Page → FIXED (PASS)
- **Root Cause:** `PostController::show()` eager-loaded comments using only `whereNull('parent_id')` without filtering by `status = 'approved'`.
- **Fix Implemented:**
  1. In `app/Models/Comment.php`: Added `scopeApproved($query)` scope.
  2. In `app/Http/Controllers/PostController.php`:
     - Constrained both root comments and nested replies in `show()` to `where('status', 'approved')`.
     - Updated `loadCount` and `withCount` in `show()`, `index()`, and `byAuthor()` to count only approved comments.
     - Kept `Admin\CommentController` queries unconstrained so moderators can continue to inspect, approve, and flag pending/spam comments.
- **Files Modified:**
  - `app/Models/Comment.php`
  - `app/Http/Controllers/PostController.php`
- **Result:** Public readers and guests only see approved comments and approved nested replies. Pending and spam comments are completely filtered out from the public post detail view.

---

### 4. [SEC-05] Administrator Can Lock Peer Administrators → FIXED (PASS)
- **Root Cause:** `Admin\UserController::lock()` only prevented self-lockout (`$user->id === $request->user()->id`), lacking a check on `$user->isAdmin()`.
- **Fix Implemented:**
  1. In `app/Http/Controllers/Admin/UserController.php`: Added guard in `lock()`:
     ```php
     if ($user->isAdmin()) {
         return redirect()->back()
             ->with('error', 'Hành động bị chặn: Bạn không thể khóa tài khoản của Quản trị viên khác.');
     }
     ```
  2. In `resources/views/admin/users/index.blade.php`: Replaced the "Khóa tài khoản" button for peer administrators with an informative `<span class="text-xs text-indigo-400 font-medium">Quản trị viên</span>` badge.
  3. In `resources/views/admin/users/show.blade.php`: Suppressed the lock action form for peer administrator profiles.
- **Files Modified:**
  - `app/Http/Controllers/Admin/UserController.php`
  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/users/show.blade.php`
- **Result:** Administrators can lock authors and viewers, can unlock eligible accounts, but are strictly blocked from locking peer administrators.

---

### 5. [SEC-06] Category Deletion Cascades Posts and Leaves Orphan Thumbnails → FIXED (PASS)
- **Root Cause:**
  1. `posts.category_id` was configured with `cascadeOnDelete()` at the database engine level.
  2. MySQL cascade deletion bypassed Eloquent model events, dropping posts while leaving thumbnail image files on the public storage disk.
- **Fix Implemented:**
  1. In `app/Http/Controllers/Admin/CategoryController.php`: Blocked deletion if the category has active posts:
     ```php
     if ($category->posts()->exists()) {
         return redirect()->route('admin.categories.index')
             ->with('error', "Không thể xóa chuyên mục '{$category->name}' vì vẫn còn bài viết đang trực thuộc.");
     }
     ```
  2. Created safe migration `database/migrations/2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php` to alter `posts.category_id` foreign key from `cascadeOnDelete` to `restrictOnDelete`.
  3. In `app/Models/Post.php`: Added `booted()` deleting lifecycle listener to automatically unlink uploaded thumbnails from `Storage::disk('public')` whenever any post is deleted through Eloquent.
- **Files Created:**
  - `database/migrations/2024_01_01_000010_update_posts_category_foreign_key_to_restrict.php`
- **Files Modified:**
  - `app/Http/Controllers/Admin/CategoryController.php`
  - `app/Models/Post.php`
- **Result:** Categories containing posts cannot be deleted (returns friendly error message). Empty categories can be deleted cleanly. Explicit post deletion ensures thumbnail images are removed from storage without leaving orphans.

---

## 3. Verification & Test Execution Results

### Targeted Tests (`tests/Feature/QASuiteTest.php`):
- **41 tests, 146 assertions — ALL PASSED (100%)**

### Full Regression Suite (`php artisan test`):
- **142 tests, 514 assertions — ALL PASSED (100%)**
- **Execution duration:** ~7.7 seconds
- **Code Style (Laravel Pint):** Clean, zero violations.

---

## 4. Regression Checklist Confirmation

- [x] **AUTH:** Normal user can log in
- [x] **AUTH:** Locked user cannot log in
- [x] **AUTH:** Logout still works
- [x] **AUTHOR:** Unlocked author can create, edit, submit, and delete posts
- [x] **AUTHOR:** Locked author cannot create, edit, submit, or delete posts
- [x] **COMMENTS:** Approved comments & replies visible publicly
- [x] **COMMENTS:** Pending comments & replies hidden publicly
- [x] **COMMENTS:** Spam comments & replies hidden publicly
- [x] **COMMENTS:** Admin moderation continues to view and manage pending/spam comments
- [x] **ADMIN USERS:** Admin can lock viewer and author
- [x] **ADMIN USERS:** Admin cannot lock another admin
- [x] **ADMIN USERS:** Admin cannot lock self
- [x] **CATEGORY:** Empty category can be deleted
- [x] **CATEGORY:** Category with posts cannot be deleted
- [x] **CATEGORY:** Existing posts remain intact (no unintended cascade deletion)
- [x] **STORAGE:** Post thumbnail behavior cleans up file upon deletion
