# BlogMNM — Database Schema Specification

## 1. Architectural Overview & ER Diagram

BlogMNM uses a relational database schema designed for high data integrity, performant indexing, and complete adherence to the system data contract.

```mermaid
erDiagram
    users ||--o{ posts : "authors"
    users ||--o{ posts : "reviews"
    users ||--o{ comments : "writes"
    users ||--o{ likes : "likes"
    users ||--o{ favorites : "bookmarks"
    users ||--o{ follows : "follower/following"
    
    categories ||--o{ posts : "categorizes (RESTRICT ON DELETE)"
    
    posts ||--o{ comments : "contains"
    posts ||--o{ post_tag : "tagged"
    tags ||--o{ post_tag : "tags"
    posts ||--o{ likes : "receives"
    posts ||--o{ favorites : "saved_in"
    
    comments ||--o{ comments : "replies"

    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at "nullable"
        string password
        enum role "admin, author, viewer"
        string avatar "nullable"
        text bio "nullable"
        boolean is_locked "default false"
        string remember_token "nullable"
        timestamps timestamps
    }

    categories {
        bigint id PK
        string name
        string slug UK
        timestamps timestamps
    }

    tags {
        bigint id PK
        string name
        string slug UK
        timestamps timestamps
    }

    posts {
        bigint id PK
        bigint user_id FK "users.id (CASCADE)"
        bigint category_id FK "categories.id (RESTRICT)"
        string title
        string slug UK
        text excerpt "nullable"
        longtext body
        string thumbnail "nullable"
        enum status "draft, pending, published, rejected"
        bigint reviewed_by FK "users.id (NULL ON DELETE, nullable)"
        timestamp reviewed_at "nullable"
        text rejection_reason "nullable"
        unsigned_bigint views "default 0, indexed"
        datetime published_at "nullable, indexed"
        timestamps timestamps
    }

    post_tag {
        bigint id PK
        bigint post_id FK "posts.id (CASCADE)"
        bigint tag_id FK "tags.id (CASCADE)"
        timestamps timestamps
    }

    comments {
        bigint id PK
        bigint post_id FK "posts.id (CASCADE)"
        bigint user_id FK "users.id (CASCADE)"
        bigint parent_id FK "comments.id (CASCADE, nullable)"
        text body
        enum status "pending, approved, spam (default pending, indexed)"
        timestamps timestamps
    }

    favorites {
        bigint id PK
        bigint user_id FK "users.id (CASCADE)"
        bigint post_id FK "posts.id (CASCADE)"
        timestamps timestamps
    }

    likes {
        bigint id PK
        bigint user_id FK "users.id (CASCADE)"
        bigint post_id FK "posts.id (CASCADE)"
        timestamps timestamps
    }

    follows {
        bigint id PK
        bigint follower_id FK "users.id (CASCADE)"
        bigint following_id FK "users.id (CASCADE)"
        timestamps timestamps
    }
```

---

## 2. Table Specifications & Relationships

### `users`
- **Primary Key**: `id` (bigint unsigned auto-increment)
- **Unique**: `email`
- **Defaults**: `role` = `'viewer'`, `is_locked` = `false`
- **Relations**:
  - `hasMany(Post::class)` — posts authored.
  - `hasMany(Comment::class)` — comments posted.
  - `belongsToMany(Post::class, 'favorites')` — bookmarked posts.
  - `belongsToMany(Post::class, 'likes')` — liked posts.
  - `belongsToMany(User::class, 'follows', 'follower_id', 'following_id')` — authors followed.
  - `belongsToMany(User::class, 'follows', 'following_id', 'follower_id')` — user followers.

### `categories`
- **Primary Key**: `id`
- **Unique**: `slug`
- **Relations**:
  - `hasMany(Post::class)`
- **Integrity Rule**: Foreign key constraint from `posts` is configured with `RESTRICT ON DELETE` (`2024_01_01_000010`). Prevents category deletion when active posts exist (`SEC-06`).

### `tags`
- **Primary Key**: `id`
- **Unique**: `slug`
- **Relations**:
  - `belongsToMany(Post::class, 'post_tag')`

### `posts`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `user_id` references `users(id)` ON DELETE CASCADE
  - `category_id` references `categories(id)` ON DELETE RESTRICT
  - `reviewed_by` references `users(id)` ON DELETE SET NULL (nullable)
- **Unique**: `slug`
- **Indexes**:
  - `index('status')` — rapid filtering of published vs pending posts.
  - `index('published_at')` — accelerates chronological queries.
  - `index('views')` — optimizes popular posts ordering.
- **Audit Columns**:
  - `reviewed_by`: user ID of administrator who reviewed the post.
  - `reviewed_at`: timestamp of approval or rejection.
  - `rejection_reason`: text feedback supplied when post is rejected.
- **Relations**:
  - `belongsTo(User::class, 'user_id')`
  - `belongsTo(User::class, 'reviewed_by')`
  - `belongsTo(Category::class)`
  - `belongsToMany(Tag::class, 'post_tag')`
  - `hasMany(Comment::class)`
  - `hasMany(Like::class)` / `belongsToMany(User::class, 'likes')`
  - `hasMany(Favorite::class)` / `belongsToMany(User::class, 'favorites')`

### `post_tag`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `post_id` references `posts(id)` ON DELETE CASCADE
  - `tag_id` references `tags(id)` ON DELETE CASCADE
- **Unique Constraint**: `['post_id', 'tag_id']` prevents duplicate tagging.

### `comments`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `post_id` references `posts(id)` ON DELETE CASCADE
  - `user_id` references `users(id)` ON DELETE CASCADE
  - `parent_id` references `comments(id)` ON DELETE CASCADE (nullable for top-level comments)
- **Indexes**:
  - `index('status')` — enables filtering by `'approved'`, `'pending'`, or `'spam'`.
- **Relations**:
  - `belongsTo(Post::class)`
  - `belongsTo(User::class)`
  - `hasMany(Comment::class, 'parent_id')` (replies)
  - `belongsTo(Comment::class, 'parent_id')` (parent comment)

### `favorites`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `user_id` references `users(id)` ON DELETE CASCADE
  - `post_id` references `posts(id)` ON DELETE CASCADE
- **Unique Constraint**: `['user_id', 'post_id']` prevents duplicate bookmarks.

### `likes`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `user_id` references `users(id)` ON DELETE CASCADE
  - `post_id` references `posts(id)` ON DELETE CASCADE
- **Unique Constraint**: `['user_id', 'post_id']` ensures exactly one like per user per post.

### `follows`
- **Primary Key**: `id`
- **Foreign Keys**:
  - `follower_id` references `users(id)` ON DELETE CASCADE
  - `following_id` references `users(id)` ON DELETE CASCADE
- **Unique Constraint**: `['follower_id', 'following_id']` prevents duplicate follow pairs.

---

## 3. Session & Cache Infrastructure Tables

### `sessions`
- **Columns**: `id` (PK, string), `user_id` (FK nullable, indexed), `ip_address` (string 45 nullable), `user_agent` (text nullable), `payload` (longtext), `last_activity` (integer, indexed).

### `cache` & `cache_locks`
- Standard Laravel database cache storage tables for rate limiting and temporary application caching.
