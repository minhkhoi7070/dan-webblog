# BlogMNM — Domain Model Class Diagram

## 1. Overview
This document models the object-oriented structure of BlogMNM, depicting all Eloquent models, their relationships, attributes, query scopes, and interactions with Laravel Policies and FormRequests.

---

## 2. UML Class Diagram

```mermaid
classDiagram
    direction TB

    class User {
        +BigInteger id
        +String name
        +String email
        +String password
        +String role
        +String avatar
        +String bio
        +Boolean is_locked
        +Timestamp email_verified_at
        +posts() HasMany
        +comments() HasMany
        +likes() BelongsToMany
        +favorites() BelongsToMany
        +followers() BelongsToMany
        +following() BelongsToMany
        +isAdmin() Boolean
        +isAuthor() Boolean
        +isViewer() Boolean
    }

    class Post {
        +BigInteger id
        +BigInteger user_id
        +BigInteger category_id
        +String title
        +String slug
        +String excerpt
        +String body
        +String thumbnail
        +String status
        +BigInteger views
        +BigInteger reviewed_by
        +Timestamp reviewed_at
        +String rejection_reason
        +DateTime published_at
        +user() BelongsTo
        +reviewer() BelongsTo
        +category() BelongsTo
        +tags() BelongsToMany
        +comments() HasMany
        +likes() HasMany
        +likers() BelongsToMany
        +favorites() HasMany
        +favoritedBy() BelongsToMany
        +scopePublished(query) Builder
        +scopeSearch(query, search) Builder
        +scopeCategory(query, slug) Builder
        +scopeTag(query, slug) Builder
    }

    class Category {
        +BigInteger id
        +String name
        +String slug
        +posts() HasMany
    }

    class Tag {
        +BigInteger id
        +String name
        +String slug
        +posts() BelongsToMany
    }

    class Comment {
        +BigInteger id
        +BigInteger post_id
        +BigInteger user_id
        +BigInteger parent_id
        +String body
        +String status
        +post() BelongsTo
        +user() BelongsTo
        +parent() BelongsTo
        +replies() HasMany
        +scopeApproved(query) Builder
    }

    class Like {
        +BigInteger id
        +BigInteger user_id
        +BigInteger post_id
        +user() BelongsTo
        +post() BelongsTo
    }

    class Favorite {
        +BigInteger id
        +BigInteger user_id
        +BigInteger post_id
        +user() BelongsTo
        +post() BelongsTo
    }

    class Follow {
        +BigInteger id
        +BigInteger follower_id
        +BigInteger following_id
        +follower() BelongsTo
        +following() BelongsTo
    }

    class PostPolicy {
        +before(User, ability) Boolean
        +viewAny(User) Boolean
        +view(User, Post) Boolean
        +create(User) Boolean
        +update(User, Post) Boolean
        +delete(User, Post) Boolean
        +submit(User, Post) Boolean
        +approve(User, Post) Boolean
        +reject(User, Post) Boolean
    }

    class CommentPolicy {
        +delete(User, Comment) Boolean
    }

    class StorePostRequest {
        +authorize() Boolean
        +rules() Array
        +prepareForValidation() Void
    }

    class StoreCommentRequest {
        +authorize() Boolean
        +rules() Array
        +withValidator(validator) Void
    }

    %% Relationships
    User "1" --> "0..*" Post : authors
    User "1" --> "0..*" Post : reviews
    User "1" --> "0..*" Comment : writes
    User "1" --> "0..*" Like : reacts
    User "1" --> "0..*" Favorite : saves
    User "1" --> "0..*" Follow : follower/following

    Category "1" --> "0..*" Post : classifies
    Post "0..*" <--> "0..*" Tag : tags (post_tag)
    Post "1" --> "0..*" Comment : contains
    Post "1" --> "0..*" Like : receives
    Post "1" --> "0..*" Favorite : collected_by

    Comment "0..1" --> "0..*" Comment : replies (parent_id)

    PostPolicy ..> Post : guards
    PostPolicy ..> User : evaluates
    CommentPolicy ..> Comment : guards
    StorePostRequest ..> Post : validates
    StoreCommentRequest ..> Comment : validates
```

---

## 3. Core Class Descriptions

### 3.1 Model Layer
- **`User`**: Core identity model supporting role checks (`isAdmin()`, `isAuthor()`, `isViewer()`) and lockout state (`is_locked`). Defines bidirectional social relationships (`followers`, `following`).
- **`Post`**: Main publication model. Encapsulates query scopes for public viewing (`scopePublished`), keyword filtering (`scopeSearch`), and taxonomy queries (`scopeCategory`, `scopeTag`). Implements `Post::booted()` deletion observer for thumbnail unlinking.
- **`Category`**: Taxonomy container. Foreign key on `posts.category_id` is set to `RESTRICT` on delete to protect data integrity.
- **`Tag`**: Taxonomy keyword container linked via `post_tag` pivot.
- **`Comment`**: Threaded discussion entity. Contains `parent_id` for nested replies and `status` (`pending`, `approved`, `spam`) for moderation.
- **`Like`**, **`Favorite`**, **`Follow`**: Relational join models with composite unique indexes preventing duplicate interactions.

### 3.2 Security & Validation Layer
- **`PostPolicy`**: Enforces model-level access rules and locks down mutations for locked user accounts.
- **`CommentPolicy`**: Enforces comment deletion privileges.
- **`StorePostRequest`**: Strips client-injected status parameters and validates thumbnail uploads.
- **`StoreCommentRequest`**: Validates comment body and cross-post parent integrity.
