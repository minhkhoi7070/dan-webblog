# BlogMNM — Comment & Reply Sequence Specification

## 1. Overview
The Comment feature allows authenticated Viewers to post comments on published articles and reply to existing comments in a nested thread structure. Parent ID validation prevents cross-post reply attacks.

```mermaid
sequenceDiagram
    autonumber
    actor Viewer as Authenticated Viewer
    participant Browser as Browser (interactions.js)
    participant Route as routes/web.php
    participant FormRequest as StoreCommentRequest
    participant Controller as CommentController@store
    participant DB as MySQL Database

    Viewer->>Browser: Types comment / reply and clicks "Gửi bình luận"
    Browser->>Browser: Set submit button loading (disabled, opacity-50)
    Browser->>Route: POST /posts/{post}/comments (FormData: body, parent_id?)
    
    Route->>FormRequest: Authorize & Validate
    Note over FormRequest: 1. User authenticated & not locked<br/>2. body: required, min:2, max:1000<br/>3. parent_id: exists in comments & belongs to post_id
    
    alt Validation Failure (422)
        FormRequest-->>Browser: 422 Unprocessable Entity ({errors: {body: [...]}})
        Browser-->>Viewer: Display inline error message under textarea
    else Validation Passed
        FormRequest->>Controller: store(StoreCommentRequest $request, Post $post)
        Controller->>DB: INSERT INTO comments (post_id, user_id, parent_id, body, created_at, updated_at)
        DB-->>Controller: Created Comment model instance
        Controller->>DB: Eager load comment user
        Controller-->>Browser: 201 Created (application/json)<br/>{"success": true, "message": "...", "comment": {...}}
        Browser->>Browser: Clear textarea, hide reply form if active
        alt Is Root Comment
            Browser->>Browser: Prepend new comment card to #comments-list
        else Is Reply Comment
            Browser->>Browser: Append reply into parent's .replies-container
        end
        Browser->>Browser: Increment .comment-count-badge
        Browser-->>Viewer: Toast notification ("Đã đăng bình luận thành công!")
    end
```

---

## 2. API Contract & Validation Rules

- **Endpoint**: `POST /posts/{post}/comments`
- **Headers**:
  - `X-CSRF-TOKEN`: `<csrf-token>`
  - `Accept`: `application/json`
- **Parameters**:
  - `body`: `string` (required, 2-1000 chars)
  - `parent_id`: `integer` (nullable, must reference a comment belonging to the same `post_id`)
- **Success Response (201 Created)**:
  ```json
  {
    "success": true,
    "message": "Comment posted successfully.",
    "comment": {
      "id": 105,
      "post_id": 12,
      "parent_id": 4,
      "body": "Thank you for the detailed breakdown!",
      "user_name": "Minh Khoi",
      "user_avatar": null,
      "created_at": "1 second ago"
    }
  }
  ```

---

## 3. Parent ID Security Verification
In `StoreCommentRequest::withValidator()`:
- If `parent_id` is passed, the parent comment is retrieved from the database.
- The request verifies `$parentComment->post_id === $post->id`.
- If a user tries to inject a `parent_id` from a different article, validation rejects the request immediately with HTTP 422.
