# BlogMNM — Application Route Map

This document catalogues all 63 application routes defined in `routes/web.php` and `routes/auth.php`, detailing their HTTP methods, URI patterns, route names, controller actions, middleware stacks, and authorization constraints.

---

## 1. Public & Content Discovery Routes

Accessible to all visitors (Guests and Authenticated Users).

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` | `home` | `PostController@index` | `web` | Main home feed of published posts |
| `GET` | `posts` | `posts.index` | `PostController@index` | `web` | Published posts feed with search and filters |
| `GET` | `posts/{post:slug}` | `posts.show` | `PostController@show` | `web` | Post detail view (increments views counter) |
| `GET` | `posts/author/{user}` | `posts.author` | `PostController@byAuthor` | `web` | Posts by specific author |
| `GET` | `authors/{user}` | `authors.show` | `PostController@byAuthor` | `web` | Author profile view with post portfolio |
| `GET` | `activity` | `activity.index` | `ActivityController@index` | `web, auth` | Recent community activity stream |

---

## 2. Authentication & Verification Routes (`routes/auth.php`)

Managed via Laravel Breeze for session lifecycle and password recovery.

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `register` | `register` | `RegisteredUserController@create` | `web, guest` | Render registration form |
| `POST` | `register` | None | `RegisteredUserController@store` | `web, guest` | Register new user account |
| `GET` | `login` | `login` | `AuthenticatedSessionController@create` | `web, guest` | Render login form |
| `POST` | `login` | None | `AuthenticatedSessionController@store` | `web, guest` | Authenticate credentials (rate-limited) |
| `POST` | `logout` | `logout` | `AuthenticatedSessionController@destroy` | `web, auth` | Invalidate session & logout |
| `GET` | `forgot-password` | `password.request` | `PasswordResetLinkController@create` | `web, guest` | Password reset request form |
| `POST` | `forgot-password` | `password.email` | `PasswordResetLinkController@store` | `web, guest` | Send password reset email link |
| `GET` | `reset-password/{token}` | `password.reset` | `NewPasswordController@create` | `web, guest` | Password reset submission form |
| `POST` | `reset-password` | `password.store` | `NewPasswordController@store` | `web, guest` | Reset user password |
| `GET` | `verify-email` | `verification.notice` | `EmailVerificationPromptController` | `web, auth` | Email verification prompt |
| `GET` | `verify-email/{id}/{hash}` | `verification.verify` | `VerifyEmailController` | `web, auth, signed, throttle:6,1`| Verify email address |
| `POST` | `email/verification-notification` | `verification.send` | `EmailVerificationNotificationController@store` | `web, auth, throttle:6,1` | Resend verification email |
| `GET` | `confirm-password` | `password.confirm` | `ConfirmablePasswordController@show` | `web, auth` | Password confirmation prompt |
| `POST` | `confirm-password` | None | `ConfirmablePasswordController@store` | `web, auth` | Confirm password |
| `PUT` | `password` | `password.update` | `PasswordController@update` | `web, auth` | Update password |

---

## 3. User Profile & Preferences Routes

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `profile` | `profile.edit` | `ProfileController@edit` | `web, auth` | Profile edit form |
| `PATCH` | `profile` | `profile.update` | `ProfileController@update` | `web, auth` | Update profile information |
| `DELETE` | `profile` | `profile.destroy` | `ProfileController@destroy` | `web, auth` | Delete user account |
| `GET` | `profile/overview`| `profile.show` | `ProfileController@show` | `web, auth` | User profile overview & statistics |
| `GET` | `dashboard` | `dashboard` | Inline Closure | `web, auth, verified` | Authenticated dashboard redirect |

---

## 4. Social Interactions & Engagement Routes

Interactive endpoints returning JSON payloads or dedicated views.

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `POST` | `posts/{post}/like` | `posts.like` | `InteractionController@like` | `web, auth` | Toggle post like (returns JSON) |
| `POST` | `posts/{post}/favorite` | `posts.favorite` | `InteractionController@favorite` | `web, auth` | Toggle post favorite bookmark |
| `GET` | `favorites` | `favorites.index` | `FavoriteController@index` | `web, auth` | View bookmarked favorites library |
| `POST` | `authors/{user}/follow` | `authors.follow` | `InteractionController@follow` | `web, auth` | Toggle follow author |
| `POST` | `posts/{post}/comments` | `comments.store` | `CommentController@store` | `web, auth` | Submit root or threaded comment |
| `POST` | `comments` | `comments.store.direct` | `CommentController@store` | `web, auth` | Direct comment submission |
| `DELETE`| `comments/{comment}` | `comments.destroy` | `CommentController@destroy` | `web, auth` | Delete comment (policy guarded) |

---

## 5. Author CMS & Editorial Management Routes

Restricted to authenticated authors and administrators (`role:author,admin`).

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `posts/create` | `posts.create` | `PostController@create` | `web, auth, role:author,admin` | Post creation form |
| `POST` | `posts` | `posts.store` | `PostController@store` | `web, auth, role:author,admin` | Store newly created draft post |
| `GET` | `posts/{post}/edit` | `posts.edit` | `PostController@edit` | `web, auth, role:author,admin` | Post edit form (owner/admin) |
| `PUT/PATCH` | `posts/{post}` | `posts.update` | `PostController@update` | `web, auth, role:author,admin` | Update post content |
| `DELETE` | `posts/{post}` | `posts.destroy` | `PostController@destroy` | `web, auth, role:author,admin` | Delete post & unlink thumbnail |
| `POST` | `posts/{post}/submit` | `posts.submit` | `PostController@submit` | `web, auth, role:author,admin` | Submit post for editorial review |
| `GET` | `posts/stats` | `posts.stats` | `PostController@stats` | `web, auth, role:author,admin` | Author portfolio analytics |
| `GET` | `author/posts/stats` | `author.posts.stats` | `PostController@stats` | `web, auth, role:author,admin` | Author dashboard alias |

---

## 6. Administrator Governance Routes

Restricted strictly to administrators (`role:admin`).

| Method | URI | Route Name | Action | Middleware | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `admin` | `admin.dashboard` | `Admin\DashboardController@index` | `web, auth, role:admin` | Admin dashboard overview |
| `GET` | `admin/dashboard` | `admin.` | `Admin\DashboardController@index` | `web, auth, role:admin` | Admin dashboard alias |
| `GET` | `admin/posts` | `admin.posts.index` | `Admin\PostController@index` | `web, auth, role:admin` | Review moderation queue |
| `GET` | `admin/posts/{post}` | `admin.posts.show` | `Admin\PostController@show` | `web, auth, role:admin` | View post for moderation |
| `POST` | `admin/posts/{post}/approve` | `admin.posts.approve` | `Admin\PostController@approve` | `web, auth, role:admin` | Approve & publish pending post |
| `POST` | `admin/posts/{post}/reject` | `admin.posts.reject` | `Admin\PostController@reject` | `web, auth, role:admin` | Reject pending post with note |
| `DELETE` | `admin/posts/{post}` | `admin.posts.destroy` | `Admin\PostController@destroy` | `web, auth, role:admin` | Delete post as administrator |
| `GET` | `admin/comments` | `admin.comments.index` | `Admin\CommentController@index` | `web, auth, role:admin` | Moderate comment queue |
| `POST` | `admin/comments/{comment}/approve` | `admin.comments.approve` | `Admin\CommentController@approve` | `web, auth, role:admin` | Approve comment |
| `POST` | `admin/comments/{comment}/spam` | `admin.comments.spam` | `Admin\CommentController@spam` | `web, auth, role:admin` | Mark comment as spam |
| `DELETE` | `admin/comments/{comment}` | `admin.comments.destroy` | `Admin\CommentController@destroy` | `web, auth, role:admin` | Delete comment |
| `GET` | `admin/categories` | `admin.categories.index` | `Admin\CategoryController@index` | `web, auth, role:admin` | List categories |
| `GET` | `admin/categories/create` | `admin.categories.create` | `Admin\CategoryController@create` | `web, auth, role:admin` | Category create form |
| `POST` | `admin/categories` | `admin.categories.store` | `Admin\CategoryController@store` | `web, auth, role:admin` | Store new category |
| `GET` | `admin/categories/{category}` | `admin.categories.show` | `Admin\CategoryController@show` | `web, auth, role:admin` | Category detail view |
| `GET` | `admin/categories/{category}/edit` | `admin.categories.edit` | `Admin\CategoryController@edit` | `web, auth, role:admin` | Category edit form |
| `PUT/PATCH` | `admin/categories/{category}` | `admin.categories.update` | `Admin\CategoryController@update` | `web, auth, role:admin` | Update category |
| `DELETE` | `admin/categories/{category}` | `admin.categories.destroy` | `Admin\CategoryController@destroy` | `web, auth, role:admin` | Delete category (restricted if posts exist) |
| `GET` | `admin/users` | `admin.users.index` | `Admin\UserController@index` | `web, auth, role:admin` | List platform users |
| `GET` | `admin/users/{user}` | `admin.users.show` | `Admin\UserController@show` | `web, auth, role:admin` | View user profile & activity |
| `POST` | `admin/users/{user}/lock` | `admin.users.lock` | `Admin\UserController@lock` | `web, auth, role:admin` | Lock user account |
| `POST` | `admin/users/{user}/unlock` | `admin.users.unlock` | `Admin\UserController@unlock` | `web, auth, role:admin` | Unlock user account |
