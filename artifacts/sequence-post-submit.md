# BlogMNM — Post Submission & Resubmission Sequence Specification

## 1. Overview
The Post Submission flow enables an author to transition an article from `draft` or `rejected` to `pending` review. This sequence enforces authorization via `PostPolicy@submit` to guarantee that only the post owner can submit their own work, and only from valid pre-review states.

---

## 2. Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Author as Authenticated Author
    participant Browser as Client Browser (Author CMS)
    participant Route as routes/web.php
    participant Middleware as RoleMiddleware ('auth', 'role:author,admin')
    participant Controller as PostController@submit
    participant Policy as PostPolicy@submit
    participant DB as MySQL Database

    Author->>Browser: Clicks "Gửi xét duyệt" / "Gửi lại xét duyệt (Resubmit)"
    Browser->>Route: POST /posts/{post}/submit (CSRF Token)

    Route->>Middleware: Verify user logged in & role in ['author', 'admin']
    alt Not Authorized / Wrong Role
        Middleware-->>Browser: 403 Forbidden
    else Authorized Role
        Middleware->>Controller: submit(Request $request, Post $post)
        
        Controller->>Policy: Gate::authorize('submit', $post)
        Note over Policy: Check: $user->id === $post->user_id<br/>AND status in ['draft', 'rejected']

        alt Ownership or State Failure
            Policy-->>Controller: AuthorizationException (403)
            Controller-->>Browser: 403 Forbidden ("Action not allowed on this post.")
        else Policy Check Passed
            Policy-->>Controller: True
            Controller->>DB: UPDATE posts SET status = 'pending', updated_at = NOW() WHERE id = ?
            DB-->>Controller: Success
            
            alt Expects JSON (AJAX)
                Controller-->>Browser: 200 OK<br/>{"success": true, "status": "pending", "message": "..."}
            else Standard Form Submission
                Controller-->>Browser: 302 Redirect Back (with session flash 'success')
            end
            
            Browser-->>Author: Display updated status badge (Pending / Chờ duyệt)
        end
    end
```

---

## 3. Policy Specification (`PostPolicy@submit`)

```php
public function submit(User $user, Post $post): bool
{
    // 1. Author must be the creator of the post
    // 2. Post status must currently be 'draft' or 'rejected'
    return $user->id === $post->user_id
        && in_array($post->status, ['draft', 'rejected'], true);
}
```

- **Draft -> Pending**: Initial review submission after drafting.
- **Rejected -> Pending**: Editorial revision submission after addressing feedback.
- **Pending / Published**: Submitting is blocked (returns `403`), preventing race conditions or duplicate reviews.
