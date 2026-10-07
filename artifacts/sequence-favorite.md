# BlogMNM — Favorite Interaction Sequence Specification

## 1. Overview
The Favorite feature allows authenticated Viewers to bookmark articles for later reading. The action operates asynchronously via `fetch()` and updates UI elements seamlessly.

```mermaid
sequenceDiagram
    autonumber
    actor Viewer as Authenticated Viewer
    participant Browser as Browser (interactions.js)
    participant Route as routes/web.php
    participant Controller as InteractionController@favorite
    participant DB as MySQL Database

    Viewer->>Browser: Click Bookmark icon on post card / detail
    Browser->>Browser: Set button loading (opacity-60, disabled)
    Browser->>Route: POST /posts/{post}/favorite (Headers: X-CSRF-TOKEN, Accept: application/json)
    
    Route->>Controller: favorite(Request $request, Post $post)
    
    alt Unauthenticated (401)
        Controller-->>Browser: 401 Unauthorized
        Browser-->>Viewer: Redirect to /login
    else Account Locked (403)
        Controller-->>Browser: 403 Forbidden ("Account is locked.")
    else Post is not Published (404)
        Controller-->>Browser: 404 Not Found
    else Successful Toggle
        Controller->>DB: Check if record exists in `favorites` (user_id, post_id)
        alt Already Saved -> Unsave
            Controller->>DB: DELETE FROM favorites WHERE user_id = ? AND post_id = ?
            Note over Controller: saved = false
        else Not Saved -> Save
            Controller->>DB: INSERT INTO favorites (user_id, post_id, timestamps)
            Note over Controller: saved = true (idempotent sync)
        end
        Controller-->>Browser: 200 OK (application/json)<br/>{"saved": true}
        Browser->>Browser: Update Bookmark icon (text-amber-600 fill-current) and label
        Browser-->>Viewer: Toast notification ("Đã lưu vào danh sách yêu thích!")
    end
```

---

## 2. API Contract & Response Format

- **Endpoint**: `POST /posts/{post}/favorite`
- **Headers**:
  - `X-CSRF-TOKEN`: `<csrf-token>`
  - `Accept`: `application/json`
  - `Content-Type`: `application/json`
- **Success Response Payload (Exact)**:
  ```json
  {
    "saved": true
  }
  ```
  *(Or when un-favoriting: `{ "saved": false }`)*

---

## 3. Dedicated Favorites Page (`GET /favorites`)
- **Route**: `favorites.index`
- **Controller**: `FavoriteController@index`
- **Functionality**:
  - Retrieves all posts favorited by `Auth::user()` using `favoritePosts()`.
  - Filters strictly to `published()` posts.
  - Eager-loads `user`, `category`, `tags`, and counts to eliminate N+1 queries.
  - Paginates results (9 items per page) with query string preservation.
