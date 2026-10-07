# BlogMNM — Editorial Approval Sequence Specification

## 1. Overview
The Editorial Approval process transitions an article from `pending` review to `published`. During this process, the administrator's ID and current timestamp are recorded for accountability, and the post becomes immediately discoverable in public feeds.

---

## 2. Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Admin as System Administrator
    participant Browser as Browser (Admin Control Center)
    participant Route as routes/web.php (/admin/posts/{post}/approve)
    participant Middleware as RoleMiddleware ('auth', 'role:admin')
    participant Controller as Admin\PostController@approve
    participant DB as MySQL Database
    participant Feed as Public Feed (posts.index / show)

    Admin->>Browser: Clicks "Duyệt & Xuất bản" (Approve)
    Browser->>Browser: Confirmation dialog prompt
    Browser->>Route: POST /admin/posts/{post}/approve (CSRF Token)

    Route->>Middleware: Verify authenticated & role === 'admin'
    alt Not Authorized (Author / Viewer / Guest)
        Middleware-->>Browser: 403 Forbidden / 302 Login Redirect
    else Authorized Admin
        Middleware->>Controller: approve(Request $request, Post $post)
        
        Controller->>DB: UPDATE posts SET<br/>status = 'published',<br/>reviewed_by = ?,<br/>reviewed_at = NOW(),<br/>published_at = COALESCE(published_at, NOW())<br/>WHERE id = ?
        DB-->>Controller: Success (1 row updated)

        alt Expects JSON
            Controller-->>Browser: 200 OK<br/>{"success": true, "status": "published", "message": "..."}
        else Web Form Redirect
            Controller-->>Browser: 302 Redirect Back (session 'success')
        end

        Browser-->>Admin: Display Green "Đã xuất bản (Published)" badge
        
        Note over Feed: Article is now accessible via scopePublished()<br/>and appears on homepage / search.
    end
```

---

## 3. Data Contract Attributes Mutated

```json
{
  "status": "published",
  "reviewed_by": 1,
  "reviewed_at": "2026-09-28T23:45:00.000000Z",
  "published_at": "2026-09-28T23:45:00.000000Z"
}
```
