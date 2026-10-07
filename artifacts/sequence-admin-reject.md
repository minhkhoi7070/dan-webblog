# BlogMNM — Editorial Rejection & Resubmission Sequence Specification

## 1. Overview
The Editorial Rejection process enables an Administrator to return an article to the author with structured feedback. The post status transitions to `rejected`, recording the reviewer ID, timestamp, and explanation. The Author can subsequently edit the post and resubmit it back to `pending`.

---

## 2. Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Admin as System Administrator
    actor Author as Article Author
    participant Browser as Browser (Admin Control Center)
    participant Route as routes/web.php (/admin/posts/{post}/reject)
    participant Middleware as RoleMiddleware ('auth', 'role:admin')
    participant Controller as Admin\PostController@reject
    participant DB as MySQL Database
    participant AuthorCMS as Author CMS (posts.edit)

    Admin->>Browser: Enters rejection reason and clicks "Từ chối"
    Browser->>Route: POST /admin/posts/{post}/reject (rejection_reason, CSRF)

    Route->>Middleware: Verify authenticated & role === 'admin'
    alt Not Authorized (Author / Viewer)
        Middleware-->>Browser: 403 Forbidden
    else Authorized Admin
        Middleware->>Controller: reject(Request $request, Post $post)
        
        Controller->>DB: UPDATE posts SET<br/>status = 'rejected',<br/>rejection_reason = ?,<br/>reviewed_by = ?,<br/>reviewed_at = NOW()<br/>WHERE id = ?
        DB-->>Controller: Success

        Controller-->>Browser: 302 Redirect Back (session 'success')
        Browser-->>Admin: Display Red "Bị từ chối (Rejected)" badge

        Note over Author,AuthorCMS: Author opens Author CMS
        Author->>AuthorCMS: Visits GET /posts/{post}/edit
        AuthorCMS-->>Author: Displays Rejection Guidance Banner<br/>and "Gửi lại xét duyệt (Resubmit)" button

        Author->>AuthorCMS: Edits content and clicks "Gửi lại xét duyệt"
        AuthorCMS->>DB: UPDATE posts SET status = 'pending' WHERE id = ?
        DB-->>AuthorCMS: Success (status transitioned back to pending review)
    end
```

---

## 3. Data Contract Attributes Mutated

```json
{
  "status": "rejected",
  "rejection_reason": "Nội dung bài viết chưa đáp ứng tiêu chuẩn biên tập.",
  "reviewed_by": 1,
  "reviewed_at": "2026-09-28T23:45:00.000000Z"
}
```
