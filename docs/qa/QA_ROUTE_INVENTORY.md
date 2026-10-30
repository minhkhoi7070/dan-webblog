# QA route inventory

Nguồn: `php artisan route:list --json`, 30/10/2026. Bao gồm cả route không có nút trên UI. Trạng thái kiểm thử theo ca nằm trong `QA_TEST_CASES.md`.

| Nhóm route | Role/backend gate | Validation/authorization | Model/DB | View/JS và test |
|---|---|---|---|---|
| Public `/`, `/posts`, `/authors` | Guest và mọi role | `PostController` lọc Published; `PostPolicy` cho unpublished | `posts`, `categories`, `tags` | `posts/*`, `authors/*`, `feed-navigation.js`; `GuestPostTest`, `PaginationTest`; HTTP live PASS |
| `register/login/verify/forgot/reset` | `guest` hoặc `auth`, throttle nơi có OTP | `RegisterRequest`, `LoginRequest`, `OtpService` | `users`, `email_otps`, `sessions` | `auth/*`, `security-settings.js`; `Auth/*` PASS; SMTP/browser BLOCKED |
| Author `/posts` write/upload, `/tags` | `auth,verified,role:author,admin` | `StorePostRequest`, `PostPolicy`, validation media | `posts`, `post_tag`, storage | `author/posts/*`, `editor.js`; `AuthorPostTest`, `PostEditorTest`; BUG-001/002/003 FIXED trong test |
| Interactions `/comments`, `/like`, `/favorite`, `/follow` | `auth,verified` | `StoreCommentRequest`, `CommentPolicy`, explicit guards | `comments`, `likes`, `favorites`, `follows` | post cards/detail, `interactions.js`; `ViewerInteractionTest`, `WorkflowIntegrityTest` PASS |
| Personal `/profile`, `/activity`, `/favorites`, `/following` | `auth` | `ProfileUpdateRequest`, owner từ session | `users` và interaction tables | `profile/*`, `activity/*`, `favorites/*`, `following/*`; `ProfileTest`, `ViewerInteractionTest` PASS backend |
| Admin `/admin/*` | `auth,verified,role:admin` | Inline validation, state checks trong moderation | `users`, `posts`, `categories`, `tags`, `comments` | `admin/*`; `AdminTest`, `AdminDirectPublishTest`, `WorkflowIntegrityTest` PASS happy path |
| Vendor `_boost`, `storage/{path}` | Theo package | Ngoài business route | Package/storage | Chưa kiểm riêng; NOT TESTED |

| Method | URI | Name | Action | Middleware |
|---|---|---|---|---|
| `GET|HEAD` | `/` | `home` | `PostController@index` | `web` |
| `POST` | `_boost/browser-logs` | `boost.browser-logs` | `Closure` | `` |
| `GET|HEAD` | `activity` | `activity.index` | `ActivityController@index` | `web, auth` |
| `GET|HEAD` | `admin` | `admin.dashboard` | `Admin\DashboardController@index` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/categories` | `admin.categories.index` | `Admin\CategoryController@index` | `web, auth, verified, role:admin` |
| `POST` | `admin/categories` | `admin.categories.store` | `Admin\CategoryController@store` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/categories/create` | `admin.categories.create` | `Admin\CategoryController@create` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/categories/{category}` | `admin.categories.show` | `Admin\CategoryController@show` | `web, auth, verified, role:admin` |
| `PUT|PATCH` | `admin/categories/{category}` | `admin.categories.update` | `Admin\CategoryController@update` | `web, auth, verified, role:admin` |
| `DELETE` | `admin/categories/{category}` | `admin.categories.destroy` | `Admin\CategoryController@destroy` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/categories/{category}/edit` | `admin.categories.edit` | `Admin\CategoryController@edit` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/comments` | `admin.comments.index` | `Admin\CommentController@index` | `web, auth, verified, role:admin` |
| `DELETE` | `admin/comments/{comment}` | `admin.comments.destroy` | `Admin\CommentController@destroy` | `web, auth, verified, role:admin` |
| `POST` | `admin/comments/{comment}/approve` | `admin.comments.approve` | `Admin\CommentController@approve` | `web, auth, verified, role:admin` |
| `POST` | `admin/comments/{comment}/spam` | `admin.comments.spam` | `Admin\CommentController@spam` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/dashboard` | `admin.` | `Admin\DashboardController@index` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/login` | `admin.login` | `Admin\AuthController@create` | `web` |
| `POST` | `admin/login` | `admin.login.store` | `Admin\AuthController@store` | `web` |
| `GET|HEAD` | `admin/metrics/realtime` | `admin.metrics.realtime` | `Admin\DashboardController@realtimeMetrics` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/posts` | `admin.posts.index` | `Admin\PostController@index` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/posts/{post}` | `admin.posts.show` | `Admin\PostController@show` | `web, auth, verified, role:admin` |
| `DELETE` | `admin/posts/{post}` | `admin.posts.destroy` | `Admin\PostController@destroy` | `web, auth, verified, role:admin` |
| `POST` | `admin/posts/{post}/approve` | `admin.posts.approve` | `Admin\PostController@approve` | `web, auth, verified, role:admin` |
| `POST` | `admin/posts/{post}/reject` | `admin.posts.reject` | `Admin\PostController@reject` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/tags` | `admin.tags.index` | `Admin\TagController@index` | `web, auth, verified, role:admin` |
| `POST` | `admin/tags` | `admin.tags.store` | `Admin\TagController@store` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/tags/create` | `admin.tags.create` | `Admin\TagController@create` | `web, auth, verified, role:admin` |
| `PUT|PATCH` | `admin/tags/{tag}` | `admin.tags.update` | `Admin\TagController@update` | `web, auth, verified, role:admin` |
| `DELETE` | `admin/tags/{tag}` | `admin.tags.destroy` | `Admin\TagController@destroy` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/tags/{tag}/edit` | `admin.tags.edit` | `Admin\TagController@edit` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/users` | `admin.users.index` | `Admin\UserController@index` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `admin/users/{user}` | `admin.users.show` | `Admin\UserController@show` | `web, auth, verified, role:admin` |
| `POST` | `admin/users/{user}/lock` | `admin.users.lock` | `Admin\UserController@lock` | `web, auth, verified, role:admin` |
| `POST` | `admin/users/{user}/unlock` | `admin.users.unlock` | `Admin\UserController@unlock` | `web, auth, verified, role:admin` |
| `GET|HEAD` | `author/comments` | `author.comments.index` | `AuthorCommentController@index` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `author/posts/stats` | `author.posts.stats` | `PostController@stats` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `authors/{user}` | `authors.show` | `PostController@byAuthor` | `web` |
| `GET|POST|HEAD` | `authors/{user}/follow` | `authors.follow` | `InteractionController@follow` | `web, auth, verified` |
| `POST` | `become-author` | `author.become` | `ProfileController@becomeAuthor` | `web, auth, verified` |
| `POST` | `comments` | `comments.store.direct` | `CommentController@store` | `web, auth, verified` |
| `DELETE` | `comments/{comment}` | `comments.destroy` | `CommentController@destroy` | `web, auth, verified` |
| `GET|HEAD` | `confirm-password` | `password.confirm` | `Auth\ConfirmablePasswordController@show` | `web, auth` |
| `POST` | `confirm-password` | `` | `Auth\ConfirmablePasswordController@store` | `web, auth` |
| `GET|HEAD` | `dashboard` | `dashboard` | `Closure` | `web, auth` |
| `POST` | `email/verification-notification` | `verification.send` | `Auth\EmailVerificationNotificationController@store` | `web, auth, throttle:6,1` |
| `GET|HEAD` | `favorites` | `favorites.index` | `FavoriteController@index` | `web, auth` |
| `GET|HEAD` | `following` | `following.index` | `FollowingController@index` | `web, auth` |
| `GET|HEAD` | `forgot-password` | `password.request` | `Auth\PasswordResetLinkController@create` | `web, guest` |
| `POST` | `forgot-password` | `password.email` | `Auth\PasswordResetLinkController@store` | `web, guest, throttle:6,1` |
| `GET|HEAD` | `forgot-password/otp` | `password.otp` | `Auth\PasswordResetLinkController@showOtpForm` | `web, guest` |
| `POST` | `forgot-password/resend-otp` | `password.otp.resend` | `Auth\PasswordResetLinkController@resendOtp` | `web, guest, throttle:6,1` |
| `POST` | `forgot-password/verify-otp` | `password.otp.verify` | `Auth\PasswordResetLinkController@verifyOtp` | `web, guest, throttle:10,1` |
| `GET|HEAD` | `login` | `login` | `Auth\AuthenticatedSessionController@create` | `web, guest` |
| `POST` | `login` | `` | `Auth\AuthenticatedSessionController@store` | `web, guest` |
| `POST` | `logout` | `logout` | `Auth\AuthenticatedSessionController@destroy` | `web, auth` |
| `PUT` | `password` | `password.update` | `Auth\PasswordController@update` | `web, auth` |
| `POST` | `posts` | `posts.store` | `PostController@store` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `posts` | `posts.index` | `PostController@index` | `web` |
| `GET|HEAD` | `posts/author/{user}` | `posts.author` | `PostController@byAuthor` | `web` |
| `GET|HEAD` | `posts/check-slug` | `posts.check-slug` | `PostController@checkSlug` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `posts/create` | `posts.create` | `PostController@create` | `web, auth, verified, role:author,admin` |
| `DELETE` | `posts/delete-video` | `posts.delete-video` | `PostController@deleteVideo` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `posts/stats` | `posts.stats` | `PostController@stats` | `web, auth, verified, role:author,admin` |
| `POST` | `posts/upload-image` | `posts.upload-image` | `PostController@uploadImage` | `web, auth, verified, role:author,admin` |
| `POST` | `posts/upload-video` | `posts.upload-video` | `PostController@uploadVideo` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `posts/{post:slug}` | `posts.show` | `PostController@show` | `web` |
| `PUT|PATCH` | `posts/{post}` | `posts.update` | `PostController@update` | `web, auth, verified, role:author,admin` |
| `DELETE` | `posts/{post}` | `posts.destroy` | `PostController@destroy` | `web, auth, verified, role:author,admin` |
| `POST` | `posts/{post}/comments` | `comments.store` | `CommentController@store` | `web, auth, verified` |
| `GET|HEAD` | `posts/{post}/edit` | `posts.edit` | `PostController@edit` | `web, auth, verified, role:author,admin` |
| `GET|POST|HEAD` | `posts/{post}/favorite` | `posts.favorite` | `InteractionController@favorite` | `web, auth, verified` |
| `GET|POST|HEAD` | `posts/{post}/like` | `posts.like` | `InteractionController@like` | `web, auth, verified` |
| `POST` | `posts/{post}/submit` | `posts.submit` | `PostController@submit` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `profile` | `profile.edit` | `ProfileController@edit` | `web, auth` |
| `PATCH` | `profile` | `profile.update` | `ProfileController@update` | `web, auth` |
| `DELETE` | `profile` | `profile.destroy` | `ProfileController@destroy` | `web, auth` |
| `GET|HEAD` | `profile/overview` | `profile.show` | `ProfileController@show` | `web, auth` |
| `GET|HEAD` | `register` | `register` | `Auth\RegisteredUserController@create` | `web, guest` |
| `POST` | `register` | `` | `Auth\RegisteredUserController@store` | `web, guest` |
| `POST` | `reset-password` | `password.store` | `Auth\NewPasswordController@store` | `web, guest` |
| `GET|HEAD` | `reset-password/{token}` | `password.reset` | `Auth\NewPasswordController@create` | `web, guest` |
| `GET|HEAD` | `storage/{path}` | `storage.local` | `Closure` | `` |
| `PUT` | `storage/{path}` | `storage.local.upload` | `Closure` | `` |
| `POST` | `tags` | `tags.store` | `TagController@store` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `tags/search` | `tags.search` | `TagController@search` | `web, auth, verified, role:author,admin` |
| `GET|HEAD` | `up` | `` | `Closure` | `` |
| `GET|HEAD` | `verify-email` | `verification.notice` | `Auth\EmailVerificationPromptController` | `web, auth` |
| `POST` | `verify-email` | `verification.otp.verify` | `Auth\VerifyEmailController@verifyOtp` | `web, auth, throttle:10,1` |
| `GET|HEAD` | `verify-email/{id}/{hash}` | `verification.verify` | `Auth\VerifyEmailController` | `web, auth, signed, throttle:6,1` |
