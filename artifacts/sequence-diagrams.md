# BlogMNM — Master Sequence Diagrams

This document consolidates UML Sequence Diagrams detailing the exact messaging patterns, lifecycle hooks, and database transactions across the platform's core operational flows.

---

## 1. Sequence Diagram: Post Submission, Review & Publication

```mermaid
sequenceDiagram
    autonumber
    actor Author
    actor Admin
    participant Browser
    participant Router as Routing Engine
    participant PostPolicy as PostPolicy
    participant PostCtrl as PostController
    participant AdminPostCtrl as Admin\PostController
    participant Model as Post Model
    participant DB as MySQL DB

    Author->>Browser: Click 'Submit for Review' on draft post
    Browser->>Router: POST /posts/{id}/submit (CSRF)
    Router->>PostCtrl: submit($post)
    PostCtrl->>PostPolicy: authorize('submit', $post)
    PostPolicy->>PostPolicy: Verify owner & status in ['draft', 'rejected'] & !is_locked
    PostPolicy-->>PostCtrl: Authorized
    PostCtrl->>Model: update(['status' => 'pending', 'rejection_reason' => null])
    Model->>DB: UPDATE posts SET status = 'pending' WHERE id = ?
    DB-->>Model: Query OK
    PostCtrl-->>Browser: Redirect back with flash 'Post submitted for review'

    Note over Admin, DB: Moderation Phase
    Admin->>Browser: Open /admin/posts & click 'Approve'
    Browser->>Router: POST /admin/posts/{id}/approve
    Router->>AdminPostCtrl: approve($post)
    AdminPostCtrl->>Model: update(['status' => 'published', 'reviewed_by' => admin.id, 'reviewed_at' => now(), 'published_at' => now()])
    Model->>DB: UPDATE posts SET status = 'published', ... WHERE id = ?
    DB-->>Model: Query OK
    AdminPostCtrl-->>Browser: Redirect with flash 'Post approved and published'
```

---

## 2. Sequence Diagram: Optimistic AJAX Social Interactions (Like Toggle)

```mermaid
sequenceDiagram
    autonumber
    actor Viewer
    participant UI as Browser DOM (interactions.js)
    participant Route as routes/web.php
    participant Ctrl as InteractionController
    participant LikeModel as Like Model
    participant DB as MySQL DB

    Viewer->>UI: Clicks Heart Icon on Post Card
    UI->>UI: Optimistically toggle SVG fill & scale animation
    UI->>Route: fetch('POST /posts/{id}/like', headers: {Accept: application/json, X-CSRF-TOKEN})
    Route->>Ctrl: like($post)
    Ctrl->>Ctrl: Verify auth & !is_locked
    
    Ctrl->>LikeModel: where('user_id', $user->id)->where('post_id', $post->id)->first()
    LikeModel->>DB: SELECT * FROM likes WHERE user_id = ? AND post_id = ?
    
    alt Post Not Previously Liked
        DB-->>LikeModel: null
        Ctrl->>LikeModel: create(['user_id' => $user->id, 'post_id' => $post->id])
        LikeModel->>DB: INSERT INTO likes (user_id, post_id, ...) VALUES (...)
        Ctrl->>LikeModel: $post->likes()->count()
        LikeModel->>DB: SELECT COUNT(*) FROM likes WHERE post_id = ?
        DB-->>Ctrl: Count = N
        Ctrl-->>UI: HTTP 200 {"liked": true, "likes_count": N}
    else Post Already Liked
        DB-->>LikeModel: Like instance
        Ctrl->>LikeModel: $like->delete()
        LikeModel->>DB: DELETE FROM likes WHERE id = ?
        Ctrl->>LikeModel: $post->likes()->count()
        LikeModel->>DB: SELECT COUNT(*) FROM likes WHERE post_id = ?
        DB-->>Ctrl: Count = N - 1
        Ctrl-->>UI: HTTP 200 {"liked": false, "likes_count": N - 1}
    end

    UI->>UI: Reconcile DOM with server count & state
```

---

## 3. Sequence Diagram: Threaded Commenting & Anti-Cross-Posting Validation

```mermaid
sequenceDiagram
    autonumber
    actor Viewer
    participant Browser
    participant Router as routes/web.php
    participant FormReq as StoreCommentRequest
    participant Ctrl as CommentController
    participant Model as Comment Model
    participant DB as MySQL DB

    Viewer->>Browser: Submits reply form with body & parent_id
    Browser->>Router: POST /posts/{post}/comments
    Router->>FormReq: Validate request payload
    
    FormReq->>FormReq: Validate body (min:2, max:2000)
    alt parent_id is present
        FormReq->>Model: Query parent comment post_id
        Model->>DB: SELECT post_id FROM comments WHERE id = ?
        DB-->>Model: Parent post_id
        FormReq->>FormReq: Assert parent post_id === current post.id (INT-09)
        alt Cross-Post Tampering Detected
            FormReq-->>Browser: HTTP 422: "The parent comment does not belong to this post."
        end
    end

    FormReq-->>Ctrl: Validated data passed
    Ctrl->>Model: create(['post_id' => $post->id, 'user_id' => $user->id, 'parent_id' => parent_id, 'body' => body, 'status' => 'approved'])
    Model->>DB: INSERT INTO comments (...) VALUES (...)
    DB-->>Model: Comment instance created (ID: X)
    Ctrl-->>Browser: HTTP 201 Created with JSON comment payload
    Browser->>Browser: Inject comment into DOM at correct thread hierarchy
```

---

## 4. Sequence Diagram: Category Deletion Protection (`RESTRICT`)

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant Router as routes/web.php
    participant CatCtrl as Admin\CategoryController
    participant Model as Category Model
    participant DB as MySQL DB

    Admin->>Browser: Clicks 'Delete' on Category
    Browser->>Router: DELETE /admin/categories/{id}
    Router->>CatCtrl: destroy($category)
    
    CatCtrl->>Model: $category->posts()->exists()
    Model->>DB: SELECT COUNT(*) FROM posts WHERE category_id = ?
    
    alt Active Posts Assigned to Category (SEC-06)
        DB-->>Model: Count > 0
        CatCtrl-->>Browser: 302 Redirect Back with Error Flash:<br/>"Cannot delete category because it has active posts assigned to it."
        Note over Browser: Category and all posts remain completely intact.<br/>No orphaned posts or unlinked files.
    else Zero Posts Assigned
        DB-->>Model: Count == 0
        CatCtrl->>Model: $category->delete()
        Model->>DB: DELETE FROM categories WHERE id = ?
        DB-->>Model: 1 row affected
        CatCtrl-->>Browser: 302 Redirect with Success Flash:<br/>"Category deleted successfully."
    end
```
