# BlogMNM Project Audit

## Audit Overview
**Project:** BlogMNM
**Framework:** Laravel 12 (13.17 based on composer.json)
**PHP Version:** 8.3
**Stack:** Blade, Tailwind CSS v4, Laravel Breeze

## Audit Matrix

| Area | Requirement | Current State | Evidence | Gap | Severity |
|------|-------------|---------------|----------|-----|----------|
| **Data Contract** | Tables: users, categories, tags, posts, post_tag, comments, favorites, likes, follows | Created | `database/migrations/` | None. All required tables exist. | LOW |
| **User Schema** | id, name, email, password, role, avatar, bio, is_locked | Created | `0001_01_01_000000_create_users_table.php` | None | LOW |
| **Post Schema** | status must support: draft, pending, published, rejected | Created | `2024_01_01_000003_create_posts_table.php` | None | LOW |
| **Models & Relations**| User, Post, Comment relationships (e.g. `User::posts()`, `Post::favoritedBy()`) | Created | `app/Models/` | Some models missing (Favorite, Like, Follow typically don't need dedicated models if using BelongsToMany pivots, but good to note). No Category/Tag logic beyond basics. | LOW |
| **Route Contract** | 30+ required routes for Guest, Viewer, Author, Admin | Partially Missing | `routes/web.php`, `routes/auth.php` | Only Auth routes & Profile routes exist. All Blog/CMS routes missing. | CRITICAL |
| **Controllers** | Controllers for Posts, Comments, Authors, Admin | Missing | `app/Http/Controllers/` | Only `Auth` and `ProfileController` exist. | CRITICAL |
| **Authorization** | Policies for ownership and workflow, Role middleware | Partially Created | `app/Policies/`, `RoleMiddleware.php` | PostPolicy & CommentPolicy exist. Form Requests and Controller integrations missing. | HIGH |
| **Views (UI/UX)** | Home, Feed, Post Detail, Author CMS, Admin CMS | Missing | `resources/views/` | Only default Breeze views (welcome, dashboard, auth). | CRITICAL |
| **Tests** | Feature/Unit coverage for all roles | Missing | `tests/Feature/` | Only default Breeze Auth tests exist (25 passing). | HIGH |

## Route Contract Audit (Phase 4)

| Route Name | Status |
|------------|--------|
| `posts.index` | MISSING |
| `posts.show` | MISSING |
| `posts.create` | MISSING |
| `posts.store` | MISSING |
| `posts.edit` | MISSING |
| `posts.update` | MISSING |
| `posts.destroy` | MISSING |
| `posts.submit` | MISSING |
| `posts.stats` | MISSING |
| `posts.like` | MISSING |
| `posts.favorite` | MISSING |
| `authors.show` | MISSING |
| `authors.follow` | MISSING |
| `comments.store` | MISSING |
| `favorites.index` | MISSING |
| `activity.index` | MISSING |
| `profile.show` | MISSING |
| `profile.edit` | MATCH |
| `profile.update` | MATCH |
| `profile.destroy`| MATCH (Requirement asked for update, Breeze provides destroy) |
| `admin.dashboard` | MISSING |
| `admin.users.*` | MISSING |
| `admin.categories.*` | MISSING |
| `admin.posts.*` | MISSING |
| `admin.posts.approve` | MISSING |
| `admin.posts.reject` | MISSING |
| `admin.comments.*` | MISSING |
| `login` | MATCH |
| `register` | MATCH |
| `logout` | MATCH |
