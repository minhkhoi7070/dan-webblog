# BlogMNM — Like Interaction Sequence Specification

## 1. Overview
The Like feature allows authenticated Viewers to endorse published articles via asynchronous AJAX requests (`fetch()`). The UI toggles state smoothly without a full page reload, and concurrent duplicate like requests are handled idempotently.

```mermaid
sequenceDiagram
    autonumber
    actor Viewer as Authenticated Viewer
    participant Browser as Browser (interactions.js)
    participant Route as routes/web.php
    participant Controller as InteractionController@like
    participant DB as MySQL Database

    Viewer->>Browser: Click Heart (Like) button
    Browser->>Browser: Set loading state (opacity-60, disabled)
    Browser->>Route: POST /posts/{post}/like (Headers: X-CSRF-TOKEN, Accept: application/json)
    
    Route->>Controller: like(Request $request, Post $post)
    
    alt Unauthenticated (401)
        Controller-->>Browser: 401 Unauthorized
        Browser-->>Viewer: Redirect to /login
    else User is Locked (403)
        Controller-->>Browser: 403 Forbidden ("Account is locked.")
        Browser-->>Viewer: Display Error Toast
    else Post is not Published (404)
        Controller-->>Browser: 404 Not Found
    else Successful Like / Unlike Toggle
        Controller->>DB: Check if record exists in `likes` (user_id, post_id)
        alt Already Liked -> Unlike
            Controller->>DB: DELETE FROM likes WHERE user_id = ? AND post_id = ?
            Note over Controller: liked = false
        else Not Yet Liked -> Like
            Controller->>DB: INSERT IGNORE INTO likes (user_id, post_id, timestamps)
            Note over Controller: liked = true (idempotent sync)
        end
        Controller->>DB: SELECT COUNT(*) FROM likes WHERE post_id = ?
        DB-->>Controller: likes_count
        Controller-->>Browser: 200 OK (application/json)<br/>{"liked": true, "likes_count": 25}
        Browser->>Browser: Update Heart color (text-rose-600) and count element
        Browser->>Viewer: Display Success Toast ("Đã thích bài viết!")
    end
```

---

## 2. API Contract & Response Format

- **Endpoint**: `POST /posts/{post}/like`
- **Headers**:
  - `X-CSRF-TOKEN`: `<csrf-token>`
  - `Accept`: `application/json`
  - `Content-Type`: `application/json`
- **Success Response Payload (Exact)**:
  ```json
  {
    "liked": true,
    "likes_count": 25
  }
  ```
  *(Or when unliking: `{ "liked": false, "likes_count": 24 }`)*

---

## 3. Concurrency & Duplicate Like Protection
- Database schema enforces a composite unique key: `UNIQUE KEY ('user_id', 'post_id')`.
- Controller uses Eloquent `syncWithoutDetaching([$user->id])` when adding likes, ensuring race conditions never result in SQL duplicate key exceptions or inflated like counts.
