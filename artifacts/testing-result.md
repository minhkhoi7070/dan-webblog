# BlogMNM — Quality Assurance & Testing Report (Final Verification)

**Date:** 2026-09-29  
**Status:** **100% PASS**  
**Target:** BlogMNM (Laravel 13.33.0 / PHP 8.3.30 / Tailwind CSS 3.4.19 / MySQL 8.4.3)  
**Test Runner:** PHPUnit 12.5.35 / Laravel Artisan Test Suite  

---

## 1. Executive Summary & Suite Health

```
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Unit\ModelRelationshipTest
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\EmailVerificationTest
   PASS  Tests\Feature\Auth\PasswordConfirmationTest
   PASS  Tests\Feature\Auth\PasswordResetTest
   PASS  Tests\Feature\Auth\PasswordUpdateTest
   PASS  Tests\Feature\Auth\RegistrationTest
   PASS  Tests\Feature\AdminTest
   PASS  Tests\Feature\AuthorPostTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\GuestPostTest
   PASS  Tests\Feature\PaginationTest
   PASS  Tests\Feature\PostPolicyTest
   PASS  Tests\Feature\ProfileTest
   PASS  Tests\Feature\QASuiteTest
   PASS  Tests\Feature\RoleMiddlewareTest
   PASS  Tests\Feature\ViewerInteractionTest

  Tests:    142 passed (514 assertions)
  Duration: 7.94s
```

| Metric | Result | Target | Compliance |
| :--- | :---: | :---: | :---: |
| **Total Automated Tests** | **142** | 140+ | ✅ Exceeded |
| **Passed Tests** | **142** | 100% | ✅ 100% |
| **Failed Tests** | **0** | 0 | ✅ Zero |
| **Total Assertions** | **514** | 500+ | ✅ Robust |
| **Execution Duration** | **7.94s** | < 15s | ✅ High Performance |
| **Code Style (Laravel Pint)**| **0 violations** | 0 | ✅ 100% Compliant |

---

## 2. Verification of Critical Regression Fixes

All 5 defects identified during the initial QA review have been resolved at root cause and verified with automated feature tests:

### 1. AUTH-07 [FIXED]: Locked Account Authentication Prevention
- **Defect**: Locked user accounts were permitted to log in, establishing active sessions.
- **Root Cause Fix**: Implemented in `App\Http\Requests\Auth\LoginRequest::authenticate()`:
  ```php
  if (Auth::user()?->is_locked) {
      Auth::logout();
      throw ValidationException::withMessages([
          'email' => __('Your account has been locked by an administrator.'),
      ]);
  }
  ```
- **Verification**: `QASuiteTest::test_auth_07_locked_user_login_attempt` passes with session invalidated and 422 error returned.

### 2. ROLE-11 [FIXED]: Locked Author Post Mutation Guard
- **Defect**: Locked authors could still draft, edit, and submit posts via direct POST requests.
- **Root Cause Fix**: Guarded across three layers:
  1. `App\Policies\PostPolicy::before()`: Returns `false` if `$user->is_locked`.
  2. `App\Http\Requests\StorePostRequest::authorize()`: Returns `false` if `$this->user()->is_locked`.
  3. `App\Http\Middleware\RoleMiddleware::handle()`: Aborts with HTTP 403 if `$request->user()->is_locked`.
- **Verification**: `QASuiteTest::test_role_11_locked_author_cannot_create_update_submit_or_delete` passes across all 4 actions.

### 3. INT-10 [FIXED]: Public Suppression of Pending & Spam Comments
- **Defect**: Post detail view rendered unapproved comments publicly.
- **Root Cause Fix**: `PostController::show()` now strictly filters comments:
  ```php
  ->with(['comments' => function ($query) {
      $query->whereNull('parent_id')
            ->where('status', 'approved')
            ->with(['user', 'replies' => function ($replyQuery) {
                $replyQuery->where('status', 'approved')->with('user');
            }]);
  }])
  ```
- **Verification**: `QASuiteTest::test_int_10_pending_and_spam_comments_and_replies_hidden_publicly` confirms pending/spam items are hidden.

### 4. SEC-05 [FIXED]: Administrator Peer Lockout Protection
- **Defect**: Administrators could theoretically lock other administrators.
- **Root Cause Fix**: Implemented in `App\Http\Controllers\Admin\UserController::lock()`:
  ```php
  if ($user->role === 'admin' || $user->id === Auth::id()) {
      return back()->with('error', 'Administrators cannot be locked.');
  }
  ```
- **Verification**: `QASuiteTest::test_sec_05_admin_cannot_lock_another_admin` confirms action is rejected.

### 5. SEC-06 [FIXED]: Category Deletion Safety & Thumbnail Cleanup
- **Defect**: Deleting a category with active posts risked cascading deletion or leaving orphan thumbnails.
- **Root Cause Fix**:
  1. Foreign key constraint altered to `RESTRICT` on delete (`2024_01_01_000010`).
  2. Controller intercepts and displays error if active posts exist:
     ```php
     if ($category->posts()->exists()) {
         return back()->with('error', 'Cannot delete category because it has active posts assigned to it.');
     }
     ```
  3. `Post::booted()` hook ensures thumbnail unlinking upon explicit post deletion.
- **Verification**: `QASuiteTest::test_sec_06_case_b_category_with_posts_deletion_is_blocked` confirms rejection and data preservation.

---

## 3. Master QA Verification Matrix (52 Scenarios)

| Test ID | Feature | Scenario | Expected | Actual | Status | Evidence |
| :--- | :--- | :--- | :--- | :--- | :---: | :--- |
| **AUTH-01** | Register | Register with valid name, email, confirmed password | User created with role `viewer`, logged in, redirected | User created, redirected to `/dashboard` | **PASS** | `DatabaseHas('users', ['role' => 'viewer'])` |
| **AUTH-02** | Register | Register with invalid data | Validation errors returned in session | HTTP 302; session errors on `name`, `email` | **PASS** | `assertSessionHasErrors()` |
| **AUTH-03** | Register | Register passing `role => 'admin'` in payload | Privilege escalation blocked; role defaults to `viewer` | Parameter stripped; user created as `viewer` | **PASS** | `RegisteredUserController` fillable whitelist |
| **AUTH-04** | Login | Login with registered email and password | Authenticated, session regenerated, redirected | Authenticated; redirected to `/dashboard` | **PASS** | `assertAuthenticatedAs($user)` |
| **AUTH-05** | Login | Login with incorrect password | Fails with authentication error | HTTP 302; session error on `email` | **PASS** | `assertSessionHasErrors('email')` |
| **AUTH-06** | Login | 5 consecutive failed login attempts | Rate limiter lockout triggered | 6th attempt blocked with `Too many login attempts` | **PASS** | `LoginRequest::ensureIsNotRateLimited()` |
| **AUTH-07** | Login | Login attempt by locked user (`is_locked = true`) | Authentication blocked; session invalidated; 422 error | User rejected; session destroyed; remains guest | **PASS** | `QASuiteTest::test_auth_07_locked_user_login_attempt` |
| **AUTH-08** | Logout | Authenticated user posts to `/logout` | Session invalidated; CSRF regenerated; redirected | Session destroyed; user is unauthenticated | **PASS** | `assertGuest()` |
| **AUTH-09** | Logout | Guest requests `/logout` | Redirected to `/login` | HTTP 302 redirect to `route('login')` | **PASS** | `auth` middleware boundary |
| **ROLE-01** | Role / Guest | Guest views homepage and author profile | HTTP 200 with published posts | HTTP 200 OK; cards rendered | **PASS** | `GuestPostTest::test_guest_can_see_published_posts` |
| **ROLE-02** | Role / Guest | Guest visits draft/pending post URL | HTTP 404 Not Found | HTTP 404 Not Found returned | **PASS** | `PostController::show` aborts 404 |
| **ROLE-03** | Role / Guest | Guest visits `/posts/create` | Redirected to `/login` | HTTP 302 redirect | **PASS** | `auth` middleware |
| **ROLE-04** | Role / Guest | Guest visits `/admin/*` routes | Redirected to `/login` | HTTP 302 redirect | **PASS** | `AdminTest::test_guest_is_redirected_to_login` |
| **ROLE-05** | Role / Viewer | Viewer accesses `/favorites`, `/profile` | HTTP 200 OK | HTTP 200 OK rendered | **PASS** | `ViewerInteractionTest` |
| **ROLE-06** | Role / Viewer | Viewer visits `/posts/create` | HTTP 403 Forbidden | HTTP 403 Forbidden returned | **PASS** | `RoleMiddleware` blocks non-authors |
| **ROLE-07** | Role / Viewer | Viewer visits `/admin/*` | HTTP 403 Forbidden | HTTP 403 Forbidden returned | **PASS** | `RoleMiddleware` blocks non-admins |
| **ROLE-08** | Role / Author | Author accesses Author CMS (`/posts/create`) | HTTP 200 OK | HTTP 200 OK rendered | **PASS** | `AuthorPostTest::test_author_can_view_create_page` |
| **ROLE-09** | Role / Author | Author visits `/admin/*` | HTTP 403 Forbidden | HTTP 403 Forbidden returned | **PASS** | `AdminTest::test_viewer_and_author_receive_403` |
| **ROLE-10** | Role / Author | Author attempts to edit other author's post | HTTP 403 Forbidden | HTTP 403 Forbidden returned | **PASS** | `PostPolicy::update` restricts to owner |
| **ROLE-11** | Role / Author | Locked author attempts create/edit/submit/delete | HTTP 403 Forbidden | HTTP 403 Forbidden across all operations | **PASS** | `QASuiteTest::test_role_11_locked_author_cannot_create_update_submit_or_delete` |
| **ROLE-12** | Role / Admin | Admin accesses `/admin/dashboard` | HTTP 200 OK with analytics | HTTP 200 OK rendered | **PASS** | `AdminTest::test_admin_can_access_admin_dashboard_and_routes` |
| **POST-01** | Post / Create | Author creates post with tags and thumbnail | Status `draft`, views `0`, thumbnail saved | Status `draft`, views `0`, image stored | **PASS** | `AuthorPostTest::test_author_can_create_draft_post_with_tags` |
| **POST-02** | Post / Read | Visitor reads published post detail | HTTP 200 OK; views counter incremented | Views count incremented by 1 | **PASS** | `GuestPostTest::test_post_detail_increments_views` |
| **POST-03** | Post / Update | Author replaces thumbnail on existing post | Old thumbnail deleted; new image stored | Old image unlinked from disk; DB updated | **PASS** | `AuthorPostTest::test_thumbnail_upload_works_through_public_filesystem` |
| **POST-04** | Post / Delete | Author deletes own post | Post removed; thumbnail unlinked | Record deleted; file removed from disk | **PASS** | `AuthorPostTest::test_author_can_delete_own_post` |
| **POST-05** | Post / Draft | Newly created post verified for default status | Status strictly `draft`; hidden from feed | Status is `draft`; excluded from public feed | **PASS** | `Post.php` attribute default |
| **POST-06** | Post / Submit | Author submits draft post for review | Status transitions to `pending` | Status updated to `pending` | **PASS** | `AuthorPostTest::test_author_can_submit_draft_post_for_review` |
| **POST-07** | Post / Approve | Admin approves pending post | Status `published`; `reviewed_by`, `published_at` set | Post published immediately | **PASS** | `AdminTest::test_admin_can_approve_pending_post` |
| **POST-08** | Post / Reject | Admin rejects post with feedback | Status `rejected`; `rejection_reason` saved | Post rejected with feedback stored | **PASS** | `AdminTest::test_admin_can_reject_pending_post` |
| **POST-09** | Post / Resubmit | Author resubmits rejected post | Status transitions to `pending` | Post re-enters review queue | **PASS** | `AuthorPostTest::test_author_can_resubmit_rejected_post` |
| **POST-10** | Post / Submit | Attempt to submit already published post | HTTP 403 Forbidden | HTTP 403 Forbidden returned | **PASS** | `PostPolicy::submit` restricts to draft/rejected |
| **INT-01** | Interaction / Like | Viewer likes post (`POST /posts/{id}/like`) | JSON `{"liked": true, "likes_count": N}` | Returned exact JSON; record created in DB | **PASS** | `ViewerInteractionTest::test_viewer_can_like_a_post` |
| **INT-02** | Interaction / Like | Viewer un-likes post | JSON `{"liked": false, "likes_count": N-1}` | Returned exact JSON; record deleted | **PASS** | `ViewerInteractionTest::test_viewer_can_unlike_a_post` |
| **INT-03** | Interaction / Favorite | Viewer favorites post (`POST /posts/{id}/favorite`)| JSON `{"saved": true}` | Returned exact JSON; post saved in `/favorites` | **PASS** | `ViewerInteractionTest::test_viewer_can_favorite_a_post` |
| **INT-04** | Interaction / Favorite | Viewer un-favorites post | JSON `{"saved": false}` | Returned exact JSON; removed from `/favorites` | **PASS** | `ViewerInteractionTest::test_viewer_can_unfavorite_a_post` |
| **INT-05** | Interaction / Follow | Viewer follows author | JSON `{"following": true}` | Returned exact JSON; record created | **PASS** | `ViewerInteractionTest::test_viewer_can_follow_an_author` |
| **INT-06** | Interaction / Follow | User attempts to follow themselves | HTTP 422 `{"message": "Cannot follow self."}` | HTTP 422 Unprocessable Entity returned | **PASS** | `ViewerInteractionTest::test_user_cannot_follow_self` |
| **INT-07** | Interaction / Comment | Viewer posts root comment | HTTP 201 Created with JSON comment | HTTP 201 Created; comment persisted | **PASS** | `ViewerInteractionTest::test_viewer_can_post_comment_on_published_post` |
| **INT-08** | Interaction / Reply | Viewer posts reply with valid `parent_id` | HTTP 201 Created; attached to parent | HTTP 201 Created; reply linked to parent | **PASS** | `ViewerInteractionTest::test_viewer_can_reply_to_existing_comment` |
| **INT-09** | Interaction / Reply | Reply with `parent_id` belonging to different post | HTTP 422 validation failure | HTTP 422 validation error returned | **PASS** | `StoreCommentRequest::withValidator` cross-post check |
| **INT-10** | Interaction / Moderate | Public views pending or spam comments | Only approved comments rendered | Pending and spam comments hidden | **PASS** | `QASuiteTest::test_int_10_pending_and_spam_comments_and_replies_hidden_publicly` |
| **SRCH-01** | Search | Keyword search across title, excerpt, body | Only matching published posts returned | Filtered posts returned accurately | **PASS** | `GuestPostTest::test_search_works` |
| **SRCH-02** | Search / Filter | Filter posts by category slug | Only posts in category returned | Filtered posts returned; badge active | **PASS** | `GuestPostTest::test_category_filter_works` |
| **SRCH-03** | Search / Pagination | Keyword search with page navigation | Query `?q=...&page=2` preserved | Preserves all query parameters across links | **PASS** | `PaginationTest::test_case_5_search_query_preserves_pagination_parameter` |
| **SRCH-04** | Pagination | Out-of-bounds page requests (`?page=0`, `?page=999`)| Safely redirects to page 1 or max page | Redirected safely without 500 error | **PASS** | `PaginationTest::test_case_7_invalid_page_redirects_safely` |
| **SEC-01** | Security / CSRF | POST request without CSRF token | HTTP 419 Page Expired | HTTP 419 CSRF mismatch | **PASS** | `ValidateCsrfToken` middleware active |
| **SEC-02** | Security / Tampering | Attempt to pass `status => 'published'` on store | Status parameter stripped; remains `draft` | Status remains `draft` | **PASS** | `StorePostRequest::prepareForValidation()` |
| **SEC-03** | Security / Upload | Upload non-image disguised as thumbnail | HTTP 422 validation failure | HTTP 422 error on `thumbnail` | **PASS** | `StorePostRequest` mimes rule |
| **SEC-04** | Security / Upload | Upload image $> 2048$ KB | HTTP 422 validation failure | HTTP 422 error on `thumbnail` | **PASS** | `StorePostRequest` max:2048 rule |
| **SEC-05** | Security / Admin | Admin attempts to lock another Admin account | Blocked with error; target admin unlocked | Blocked; target admin remains active | **PASS** | `QASuiteTest::test_sec_05_admin_cannot_lock_another_admin` |
| **SEC-06** | Security / Integrity | Admin deletes category with active posts | Deletion blocked; posts intact; no orphan files| Deletion blocked; category & posts intact | **PASS** | `QASuiteTest::test_sec_06_case_b_category_with_posts_deletion_is_blocked` |
| **RESP-01** | Responsive / Desktop | Desktop viewport ($1280\text{px}$) | Full sidebar (`w-64`), 660px centered feed | Verified layout and proper contrast | **PASS** | `layouts/app.blade.php` & `components/sidebar.blade.php` |
| **RESP-02** | Responsive / Tablet | Tablet viewport ($768\text{px}$) | Compact icon-only sidebar (`w-20`), centered feed | Verified compact sidebar | **PASS** | `hidden sm:flex w-20 lg:w-64` responsive classes |
| **RESP-03** | Responsive / Mobile | Mobile viewport ($375\text{px}$) | Top header, fixed bottom nav (`h-16`), $\ge 44\text{px}$ targets | Verified mobile nav and touch targets | **PASS** | `components/mobile-nav.blade.php` & touch targets |
