# BlogMNM — Detailed Use Case Specifications

This document provides formal, step-by-step specifications for key system use cases across the authentication, publishing, social, and administration pipelines.

---

## UC-07: User Authentication & Locked Account Interception

- **Primary Actor**: Guest / Registered User
- **Preconditions**: User has registered an account.
- **Trigger**: User navigates to `/login`, fills credentials, and clicks "Log in".
- **Input Parameters**:
  - `email`: string, required, email format.
  - `password`: string, required.
  - `remember`: boolean, optional.
- **Main Success Scenario**:
  1. System checks rate limiter: ensures user has not exceeded 5 failed attempts per minute (`LoginRequest::ensureIsNotRateLimited()`).
  2. System attempts authentication against the `users` table via `Auth::attempt()`.
  3. System inspects `is_locked` attribute on the authenticated user model.
  4. User is confirmed unlocked (`is_locked === false`).
  5. System clears rate limiter hit count.
  6. System regenerates session ID via `$request->session()->regenerate()`.
  7. System redirects user to intended destination or `/dashboard`.
- **Alternative / Exception Flows**:
  - *Invalid Credentials*: System increments rate limiter counter and redirects back to `/login` with HTTP 302 and session error `email => 'These credentials do not match our records.'`.
  - *Rate Limited*: If attempts reach 5 within 60 seconds, system throws `ValidationException` displaying seconds until retry.
  - *Locked Account (AUTH-07)*: If `Auth::user()->is_locked === true`:
    1. System immediately executes `Auth::logout()`.
    2. System invalidates session.
    3. System throws `ValidationException` with `email => 'Your account has been locked by an administrator.'`.
    4. User remains unauthenticated.
- **Post-conditions**: Active session created with regenerated ID or user redirected with error.

---

## UC-08: Like / Unlike Post (Optimistic AJAX Interaction)

- **Primary Actor**: Viewer (or Author / Admin)
- **Preconditions**: User is authenticated and account is active (`is_locked === false`). Target post has `status = 'published'`.
- **Trigger**: User clicks the Heart icon on a post card or post detail view.
- **Endpoint**: `POST /posts/{id}/like`
- **Main Success Scenario (Toggle Like)**:
  1. Client sends asynchronous `fetch()` request with `X-CSRF-TOKEN` and `Accept: application/json`.
  2. Server verifies authentication and checks `!$user->is_locked`.
  3. Server inspects `likes` table for an existing record matching `(user_id, post_id)`.
  4. *Case A (Post not yet liked)*:
     - Server creates `Like` record (`user_id`, `post_id`).
     - Server queries new total like count via `likes()->count()`.
     - Server returns JSON response: `{"liked": true, "likes_count": N}` with HTTP 200.
  5. *Case B (Post already liked)*:
     - Server deletes existing `Like` record.
     - Server queries new total like count via `likes()->count()`.
     - Server returns JSON response: `{"liked": false, "likes_count": N - 1}` with HTTP 200.
  6. Client updates heart SVG fill color, heart scale animation, and updates live count in DOM.
- **Alternative / Exception Flows**:
  - *Guest User*: Returns HTTP 401 Unauthorized or redirects to `/login`.
  - *Locked User*: `abort_if($user->is_locked, 403, 'Account is locked.')` returns HTTP 403.
- **Post-conditions**: `likes` table record added or removed atomically.

---

## UC-11: Follow / Unfollow Author

- **Primary Actor**: Viewer (or Author / Admin)
- **Preconditions**: User is authenticated and not locked. Target user is an author.
- **Trigger**: User clicks "Follow" / "Following" button on author profile page (`/authors/{user}`).
- **Endpoint**: `POST /authors/{user}/follow`
- **Main Success Scenario**:
  1. Client sends `POST` request with CSRF token and JSON headers.
  2. Server verifies `!$currentUser->is_locked`.
  3. Server checks whether target user ID equals `$currentUser->id`.
  4. If target is different, server inspects `follows` table for `(follower_id, following_id)`.
  5. *If following exists*: Deletes follow record, returns `{"following": false}`.
  6. *If following does not exist*: Creates follow record, returns `{"following": true}`.
  7. Client toggles button styling (Inverted pill for "Follow" $\leftrightarrow$ Surface pill for "Following").
- **Alternative / Exception Flows**:
  - *Self-Follow Attempt (INT-06)*: If `$currentUser->id === $author->id`, server returns HTTP 422 with `{"message": "Cannot follow self."}`.
  - *Locked User*: Returns HTTP 403.
- **Post-conditions**: Record created or deleted in `follows` table.

---

## UC-12 & UC-13: Threaded Commenting & Reply with Anti-Cross-Posting

- **Primary Actor**: Viewer (or Author / Admin)
- **Preconditions**: User is authenticated and not locked. Target post is `published`.
- **Trigger**: User types message into comment input and clicks "Post comment" or "Reply".
- **Endpoint**: `POST /posts/{post}/comments`
- **Validation Rules**:
  - `body`: required, string, min: 2, max: 2000.
  - `parent_id`: nullable, exists in `comments,id`.
- **Main Success Scenario**:
  1. Request passes through `StoreCommentRequest`.
  2. Request verifies user is authenticated and `!$user->is_locked`.
  3. *Cross-Post Validation (INT-09)*: If `parent_id` is supplied, `withValidator()` ensures that `Comment::where('id', $parentId)->value('post_id') === $post->id`.
  4. Comment is persisted with `post_id = $post->id`, `user_id = $user->id`, `parent_id = $parentId`, and default `status = 'approved'` (or 'pending' based on site moderation rules).
  5. Server returns HTTP 201 Created with JSON representation of the comment and author.
  6. Client dynamically injects comment into DOM at the appropriate indentation level.
- **Alternative / Exception Flows**:
  - *Cross-Post Attack*: If `parent_id` belongs to another post, server rejects with HTTP 422: `"The parent comment does not belong to this post."`.
  - *Locked User*: Returns HTTP 403 Forbidden.
- **Post-conditions**: Comment saved in `comments` table with correct parent linkage.

---

## UC-15 & UC-16: Author Post Creation & Thumbnail Upload

- **Primary Actor**: Author or Administrator
- **Preconditions**: User has role `author` or `admin`, is authenticated, and is not locked.
- **Trigger**: Author fills the creation form at `/posts/create` and clicks "Save Draft".
- **Endpoint**: `POST /posts`
- **Validation Rules (`StorePostRequest`)**:
  - `title`: required, string, max: 255.
  - `category_id`: required, integer, exists in `categories,id`.
  - `body`: required, string, min: 10.
  - `excerpt`: nullable, string, max: 500.
  - `tags`: nullable, array of integer IDs existing in `tags,id`.
  - `thumbnail`: nullable, image (`jpg`, `jpeg`, `png`, `webp`), max: 2048 KB.
- **Main Success Scenario**:
  1. Author submits form with data and file attachment (`multipart/form-data`).
  2. `StorePostRequest` executes `prepareForValidation()` which forcibly strips any client-submitted `status` field, ensuring status cannot be escalated to `published` (`SEC-02`).
  3. Server generates unique URL slug from title (e.g. `my-awesome-post-83749`).
  4. If thumbnail is present, server stores it on the `public` storage disk under `thumbnails/` and generates the public path.
  5. Post is created in `posts` table with `user_id = $author->id`, `status = 'draft'`, `views = 0`.
  6. Post tags are attached via `$post->tags()->sync($request->tags)`.
  7. System redirects to `/posts/{post}/edit` with flash notification: `"Post created as draft."`.
- **Alternative / Exception Flows**:
  - *Locked Author (ROLE-11)*: Denied with HTTP 403 Forbidden.
  - *Invalid File Format*: Non-image or file $> 2\text{MB}$ returns HTTP 302 with session errors on `thumbnail`.
- **Post-conditions**: Post record saved in database with status `draft`. Thumbnail stored in `storage/app/public/thumbnails`.

---

## UC-18: Post Submission for Editorial Review

- **Primary Actor**: Author (Post Owner)
- **Preconditions**: Post exists in `draft` or `rejected` status. User owns the post.
- **Trigger**: Author clicks "Submit for Review" button on the post edit page or post stats table.
- **Endpoint**: `POST /posts/{id}/submit`
- **Main Success Scenario**:
  1. Request reaches `PostController::submit()`.
  2. `PostPolicy::submit($user, $post)` verifies:
     - User is not locked (`!$user->is_locked`).
     - User owns the post (`$user->id === $post->user_id` or user is admin).
     - Post status is strictly `'draft'` or `'rejected'`.
  3. Post status is updated to `'pending'`.
  4. Rejection reason is cleared (`rejection_reason = null`).
  5. System redirects back with success message: `"Post submitted for review."`.
- **Alternative / Exception Flows**:
  - *Post Already Pending or Published*: `PostPolicy::submit()` returns `false`, resulting in HTTP 403 Forbidden (`POST-10`).
  - *Unauthorized User*: Different author receives HTTP 403 Forbidden.
- **Post-conditions**: Post status updated to `'pending'` in database; visible in Admin moderation queue.

---

## UC-21: Administrator Post Review (Approve / Reject)

- **Primary Actor**: Administrator
- **Preconditions**: User has `role = 'admin'`. Post status is `'pending'`.
- **Trigger**: Admin inspects post in `/admin/posts` and clicks "Approve" or "Reject".
- **Endpoints**:
  - Approve: `POST /admin/posts/{id}/approve`
  - Reject: `POST /admin/posts/{id}/reject`
- **Main Success Scenario — Approve (POST-07)**:
  1. Admin clicks "Approve".
  2. Server updates post:
     - `status = 'published'`
     - `reviewed_by = $admin->id`
     - `reviewed_at = now()`
     - `published_at = now()` (if not previously set)
     - `rejection_reason = null`
  3. Post becomes immediately visible in public home feed and search results.
  4. Admin redirected back with success notice: `"Post approved and published."`.
- **Main Success Scenario — Reject (POST-08)**:
  1. Admin enters constructive feedback in rejection modal and submits.
  2. Server updates post:
     - `status = 'rejected'`
     - `reviewed_by = $admin->id`
     - `reviewed_at = now()`
     - `rejection_reason = $validatedReason`
  3. Post is excluded from public feeds; author can see the rejection reason on their edit page.
  4. Admin redirected back with notice: `"Post rejected."`.
- **Post-conditions**: Post status set to `published` or `rejected`, with audit trail populated.

---

## UC-23: Category Management & Deletion Safety

- **Primary Actor**: Administrator
- **Preconditions**: User has `role = 'admin'`.
- **Trigger**: Admin navigates to `/admin/categories` and clicks "Delete" on a category.
- **Endpoint**: `DELETE /admin/categories/{id}`
- **Main Success Scenario (Zero Posts)**:
  1. Category has 0 assigned posts (`$category->posts()->count() === 0`).
  2. Server deletes category record from `categories` table.
  3. Admin redirected back with success banner: `"Category deleted successfully."`.
- **Alternative / Exception Flow (Active Posts Exist — SEC-06)**:
  1. Admin attempts to delete a category that has 1 or more posts.
  2. Controller checks `$category->posts()->exists()` or database throws `QueryException` on foreign key `RESTRICT`.
  3. Controller intercepts and returns redirect back with error flash: `"Cannot delete category because it has active posts assigned to it."`.
  4. Category and all related posts remain 100% intact. No orphaned posts or images.
- **Post-conditions**: Category preserved safely if posts exist; removed cleanly if empty.

---

## UC-24: User Lock / Unlock with Administrator Protection

- **Primary Actor**: Administrator
- **Preconditions**: User has `role = 'admin'`. Target user exists.
- **Trigger**: Admin clicks "Lock Account" on a user in `/admin/users`.
- **Endpoint**: `POST /admin/users/{user}/lock`
- **Main Success Scenario**:
  1. Admin selects a viewer or author user.
  2. Server checks if target user has `role === 'admin'`.
  3. Target is not admin; server executes `$user->update(['is_locked' => true])`.
  4. Target user is now locked across all access points.
  5. Admin receives success notification: `"User account locked."`.
- **Alternative / Exception Flow (Admin Lockout Prevention — SEC-05)**:
  1. Admin attempts to lock another administrator or themselves.
  2. Server checks `if ($user->role === 'admin')` or `$user->id === Auth::id()`.
  3. Server blocks the request with error redirect: `"Administrators cannot be locked."`.
  4. Target administrator remains unlocked.
- **Post-conditions**: Target user `is_locked` attribute updated to true or prevented.
