<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// Admin Authentication (Public Guest/Admin)
// =============================================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
});

// =============================================================================
// Admin Routes (Sprint 4: Admin Only)
// =============================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/lock', [UserController::class, 'lock'])->name('users.lock');
    Route::post('/users/{user}/unlock', [UserController::class, 'unlock'])->name('users.unlock');

    // Category CRUD
    Route::resource('categories', CategoryController::class);

    // Post moderation
    Route::get('/posts', [App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/approve', [App\Http\Controllers\Admin\PostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [App\Http\Controllers\Admin\PostController::class, 'reject'])->name('posts.reject');
    Route::delete('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');

    // Comment moderation
    Route::get('/comments', [App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/approve', [App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{comment}/spam', [App\Http\Controllers\Admin\CommentController::class, 'spam'])->name('comments.spam');
    Route::delete('/comments/{comment}', [App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('comments.destroy');
});

// =============================================================================
// Author CMS Routes (Sprint 3: Author & Admin)
// Note: Declared before wildcard /posts/{post:slug} to prevent route conflicts
// =============================================================================
Route::middleware(['auth', 'role:author,admin'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/stats', [PostController::class, 'stats'])->name('posts.stats');
    Route::get('/author/posts/stats', [PostController::class, 'stats'])->name('author.posts.stats');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::match(['put', 'patch'], '/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/submit', [PostController::class, 'submit'])->name('posts.submit');
});

// =============================================================================
// Public Guest Routes (Sprint 1)
// =============================================================================
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/authors/{user}', [PostController::class, 'byAuthor'])->name('authors.show');
Route::get('/posts/author/{user}', [PostController::class, 'byAuthor'])->name('posts.author');

// =============================================================================
// Authenticated Viewer Routes (Sprint 2)
// =============================================================================
Route::middleware('auth')->group(function () {
    // Interactions (Like, Favorite, Follow)
    Route::post('/posts/{post}/like', [InteractionController::class, 'like'])->name('posts.like');
    Route::post('/posts/{post}/favorite', [InteractionController::class, 'favorite'])->name('posts.favorite');
    Route::post('/authors/{user}/follow', [InteractionController::class, 'follow'])->name('authors.follow');

    // Comments & Replies
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store.direct');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Viewer Personal Pages
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/overview', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Viewer to Author Upgrade
    Route::post('/become-author', [ProfileController::class, 'becomeAuthor'])->name('author.become');
});

Route::get('/dashboard', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
