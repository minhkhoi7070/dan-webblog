# BlogMNM — Database Data Dictionary

## Overview
This document serves as the formal Data Dictionary for BlogMNM, recording all tables, columns, data types, nullability, defaults, keys, and descriptions based on the actual database schema and migrations.

---

### 1. `users`
Stores account identities, authentication credentials, roles, and administrative lockout states.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Unique user record identifier |
| `name` | VARCHAR(255) | No | None | None | Display / full name of the user |
| `email` | VARCHAR(255) | No | None | UNIQUE | Login email address |
| `email_verified_at`| TIMESTAMP | Yes | NULL | None | Email verification timestamp |
| `password` | VARCHAR(255) | No | None | None | Bcrypt hashed password |
| `role` | ENUM('admin','author','viewer') | No | 'viewer' | None | Authorization role determining user permissions |
| `avatar` | VARCHAR(255) | Yes | NULL | None | Storage path or URL to user avatar image |
| `bio` | TEXT | Yes | NULL | None | User profile bio |
| `is_locked` | BOOLEAN | No | false | None | Account lock flag; blocks login and all mutations |
| `remember_token` | VARCHAR(100) | Yes | NULL | None | "Remember me" session token |
| `created_at` | TIMESTAMP | Yes | NULL | None | Account creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Account last update timestamp |

---

### 2. `categories`
Taxonomy classification for articles.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Category identifier |
| `name` | VARCHAR(255) | No | None | None | Display title of the category |
| `slug` | VARCHAR(255) | No | None | UNIQUE | URL-friendly unique identifier |
| `created_at` | TIMESTAMP | Yes | NULL | None | Category creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Category last update timestamp |

---

### 3. `tags`
Keywords for cross-category content discovery.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Tag identifier |
| `name` | VARCHAR(255) | No | None | None | Tag name |
| `slug` | VARCHAR(255) | No | None | UNIQUE | URL-friendly unique identifier |
| `created_at` | TIMESTAMP | Yes | NULL | None | Tag creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Tag last update timestamp |

---

### 4. `posts`
Core content entity authored by users, organized into categories, and moderated by administrators.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Post record identifier |
| `user_id` | BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | Author of the post |
| `category_id` | BIGINT UNSIGNED | No | None | FK -> categories.id (RESTRICT) | Category assigned (deletion restricted if posts exist) |
| `title` | VARCHAR(255) | No | None | None | Article headline |
| `slug` | VARCHAR(255) | No | None | UNIQUE | SEO-friendly unique URL slug |
| `excerpt` | TEXT | Yes | NULL | None | Short summary preview |
| `body` | LONGTEXT | No | None | None | Full article HTML/Markdown content |
| `thumbnail` | VARCHAR(255) | Yes | NULL | None | Storage path to featured banner image |
| `status` | ENUM('draft','pending','published','rejected') | No | 'draft' | INDEX | Editorial review lifecycle status |
| `reviewed_by` | BIGINT UNSIGNED | Yes | NULL | FK -> users.id (NULL ON DELETE) | Administrator who reviewed the post |
| `reviewed_at` | TIMESTAMP | Yes | NULL | None | Timestamp when post was approved or rejected |
| `rejection_reason` | TEXT | Yes | NULL | None | Editor feedback when post is rejected |
| `views` | BIGINT UNSIGNED | No | 0 | INDEX | Article page view counter |
| `published_at` | TIMESTAMP | Yes | NULL | INDEX | Publication timestamp |
| `created_at` | TIMESTAMP | Yes | NULL | None | Record creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Record last modification timestamp |

---

### 5. `post_tag`
Pivot table establishing Many-to-Many association between posts and tags.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Pivot record identifier |
| `post_id` | BIGINT UNSIGNED | No | None | FK -> posts.id (CASCADE) | Target post |
| `tag_id` | BIGINT UNSIGNED | No | None | FK -> tags.id (CASCADE) | Target tag |
| `created_at` | TIMESTAMP | Yes | NULL | None | Attachment creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Attachment update timestamp |
| **Unique** | `(post_id, tag_id)` | | | UNIQUE | Enforces unique association |

---

### 6. `comments`
User comments on published posts, supporting two-level threading and editorial moderation.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Comment identifier |
| `post_id` | BIGINT UNSIGNED | No | None | FK -> posts.id (CASCADE) | Post being commented on |
| `user_id` | BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | Author of the comment |
| `parent_id` | BIGINT UNSIGNED | Yes | NULL | FK -> comments.id (CASCADE) | Parent comment for replies (null for root) |
| `body` | TEXT | No | None | None | Comment text content |
| `status` | ENUM('pending','approved','spam') | No | 'pending' | INDEX | Moderation status of comment |
| `created_at` | TIMESTAMP | Yes | NULL | None | Creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Last update timestamp |

---

### 7. `favorites`
Saved/bookmarked posts for authenticated users.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Favorite record identifier |
| `user_id` | BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | User who bookmarked |
| `post_id` | BIGINT UNSIGNED | No | None | FK -> posts.id (CASCADE) | Post bookmarked |
| `created_at` | TIMESTAMP | Yes | NULL | None | Bookmark timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Update timestamp |
| **Unique** | `(user_id, post_id)` | | | UNIQUE | Prevents duplicate bookmarks |

---

### 8. `likes`
Reaction/endorsement counter per post per user.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Like record identifier |
| `user_id` | BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | User who reacted |
| `post_id` | BIGINT UNSIGNED | No | None | FK -> posts.id (CASCADE) | Post reacted to |
| `created_at` | TIMESTAMP | Yes | NULL | None | Creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Update timestamp |
| **Unique** | `(user_id, post_id)` | | | UNIQUE | One like per user per post |

---

### 9. `follows`
Author subscription graph between users.

| Column | Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Follow relationship identifier |
| `follower_id`| BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | Subscriber user |
| `following_id`| BIGINT UNSIGNED | No | None | FK -> users.id (CASCADE) | Author being followed |
| `created_at` | TIMESTAMP | Yes | NULL | None | Subscription timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | None | Update timestamp |
| **Unique** | `(follower_id, following_id)` | | | UNIQUE | Enforces unique following pair |

---

### 10. `sessions` & Infrastructure Tables

| Table | Purpose | Key Columns |
| :--- | :--- | :--- |
| `sessions` | Session storage | `id` (PK, string), `user_id` (FK nullable, indexed), `ip_address`, `user_agent`, `payload`, `last_activity` |
| `password_reset_tokens` | Password reset tokens | `email` (PK), `token`, `created_at` |
| `cache` / `cache_locks` | Rate limiting & caching | `key` (PK), `value`, `expiration` |
