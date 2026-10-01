# BlogMNM — Admin Module Architecture Specification

## 1. Executive Summary
The Administrative Module (`App\Http\Controllers\Admin`) provides central command, content moderation, taxonomy management, and user lifecycle control for the BlogMNM platform. Access is strictly constrained to users holding the `admin` role via layered authentication and authorization middleware.

---

## 2. Namespace & Controller Hierarchy

All administrative controllers reside under the dedicated namespace `App\Http\Controllers\Admin` to separate operational moderation from public and author workflows:

```
App\Http\Controllers\Admin\
├── DashboardController.php   # System-wide metrics & moderation quick queues
├── UserController.php        # User directory, search, lock/unlock safeguards
├── CategoryController.php    # Taxonomy CRUD & URL slug management
├── PostController.php        # Editorial queue, review, approve, reject, delete
└── CommentController.php     # Community feedback approval, spam, delete
```

---

## 3. Route Routing & Security Boundary

```php
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Central Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/lock', [UserController::class, 'lock'])->name('users.lock');
        Route::post('/users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');

        // Taxonomy CRUD
        Route::resource('categories', CategoryController::class);

        // Post Moderation
        Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::post('/posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
        Route::post('/posts/{post}/reject', [PostController::class, 'reject'])->name('posts.reject');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Comment Moderation
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::post('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::post('/comments/{comment}/spam', [CommentController::class, 'spam'])->name('comments.spam');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
```

- **Unauthenticated Guests**: Redirected to `/login` (302).
- **Viewers & Authors**: Blocked by `RoleMiddleware` with HTTP `403 Forbidden`.

---

## 4. Dashboard Metrics Data Contract

| Metric Group | Key | Target Source | Semantic Meaning |
|---|---|---|---|
| **Users** | `users` | `User::count()` | Total registered users |
| | `authors` | `User::where('role', 'author')->count()` | Content contributors |
| | `viewers` | `User::where('role', 'viewer')->count()` | Reading audience |
| **Posts** | `posts` | `Post::count()` | Total articles created |
| | `draft` | `Post::where('status', 'draft')->count()` | In-progress articles |
| | `pending` | `Post::where('status', 'pending')->count()` | Awaiting editorial decision |
| | `published` | `Post::where('status', 'published')->count()` | Publicly accessible articles |
| | `rejected` | `Post::where('status', 'rejected')->count()` | Returned for author revision |
| **Comments** | `comments` | `Comment::count()` | Total comments across all posts |
| | `pending_comments`| `Comment::where('status', 'pending')->count()`| Reader comments awaiting review |
| **Traffic** | `views` | `Post::sum('views')` | Total cumulative page impressions |

---

## 5. Destructive Action Defense & Confirmation Safeguards

1. **Self-Lockout Prevention**:
   ```php
   if ($user->id === $request->user()->id) {
       return redirect()->back()->with('error', 'Hành động bị chặn: Không thể tự khóa tài khoản của chính mình.');
   }
   ```
2. **Explicit User Prompts**: UI actions for account lock/unlock, post rejection with reason prompt, and entity deletion enforce native browser confirmation dialogs (`onsubmit="return confirm(...)";`).
3. **Storage Cleanup on Delete**: When a post is removed by an admin, the thumbnail stored on `public` disk is cleaned up via `Storage::disk('public')->delete()`.
