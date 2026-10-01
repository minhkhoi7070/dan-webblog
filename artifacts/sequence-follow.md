# BlogMNM — Follow Author Sequence Specification

## 1. Overview
The Follow feature allows authenticated Viewers to subscribe to authors. Authors cannot follow themselves. The interaction executes asynchronously via `fetch()` and updates UI elements without a page refresh.

```mermaid
sequenceDiagram
    autonumber
    actor Viewer as Authenticated Viewer
    participant Browser as Browser (interactions.js)
    participant Route as routes/web.php
    participant Controller as InteractionController@follow
    participant DB as MySQL Database

    Viewer->>Browser: Click "+ Theo dõi" on author card / profile
    Browser->>Browser: Set button loading (opacity-60, disabled)
    Browser->>Route: POST /authors/{user}/follow (Headers: X-CSRF-TOKEN, Accept: application/json)
    
    Route->>Controller: follow(Request $request, User $user)
    
    alt Unauthenticated (401)
        Controller-->>Browser: 401 Unauthorized
        Browser-->>Viewer: Redirect to /login
    else Self-Follow Attempt (422)
        Controller-->>Browser: 422 Unprocessable Entity<br/>{"message": "Cannot follow self."}
        Browser-->>Viewer: Toast error ("Không thể theo dõi chính mình.")
    else Account Locked (403)
        Controller-->>Browser: 403 Forbidden ("Account is locked.")
    else Successful Toggle
        Controller->>DB: Check if record exists in `follows` (follower_id, following_id)
        alt Already Following -> Unfollow
            Controller->>DB: DELETE FROM follows WHERE follower_id = ? AND following_id = ?
            Note over Controller: following = false
        else Not Following -> Follow
            Controller->>DB: INSERT INTO follows (follower_id, following_id, timestamps)
            Note over Controller: following = true (idempotent sync)
        end
        Controller-->>Browser: 200 OK (application/json)<br/>{"following": true}
        Browser->>Browser: Update button text to "Đang theo dõi" and styling
        Browser->>Browser: Update follower count if on author page
        Browser-->>Viewer: Toast notification ("Đã theo dõi tác giả!")
    end
```

---

## 2. API Contract & Response Format

- **Endpoint**: `POST /authors/{user}/follow`
- **Headers**:
  - `X-CSRF-TOKEN`: `<csrf-token>`
  - `Accept`: `application/json`
  - `Content-Type`: `application/json`
- **Success Response Payload (Exact)**:
  ```json
  {
    "following": true
  }
  ```
  *(Or when unfollowing: `{ "following": false }`)*
- **Self-Follow Error Response (422)**:
  ```json
  {
    "message": "Cannot follow self."
  }
  ```

---

## 3. Database Constraints
- Unique pair enforced at database level: `UNIQUE KEY ('follower_id', 'following_id')`.
- Cascade deletion: If an author or user is deleted, all their following/follower associations are removed automatically.
