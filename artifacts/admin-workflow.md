# BlogMNM — Editorial & Administrative Moderation Workflows

## 1. Overview
The Administrative Workflow oversees content gatekeeping and user compliance. It coordinates editorial decision-making between Authors and Administrators, ensuring platform quality and safety.

---

## 2. Global Moderation State Machine

```mermaid
stateDiagram-v2
    [*] --> PostSubmitted: Author clicks "Gửi xét duyệt"
    PostSubmitted --> PendingReview: status = 'pending'

    state PendingReview {
        [*] --> EditorialInspection: Admin reads post content
    }

    PendingReview --> Published: Admin Approves (status='published')
    PendingReview --> Rejected: Admin Rejects (status='rejected', reason)

    state Rejected {
        [*] --> AuthorRevision: Author sees rejection guidance
        AuthorRevision --> PendingReview: Author resubmits (status='pending')
    }

    Published --> [*]: Live on Feed
```

---

## 3. Post Moderation State Transition Matrix

| Action | Prior State | Target State | Mutated Attributes | Trigger Endpoint | Actor |
|---|---|---|---|---|---|
| **Approve** | `pending` | `published` | `status = 'published'`<br/>`reviewed_by = admin_id`<br/>`reviewed_at = NOW()`<br/>`published_at = NOW()` | `POST /admin/posts/{post}/approve` | Admin Only |
| **Reject** | `pending` | `rejected` | `status = 'rejected'`<br/>`rejection_reason = <text>`<br/>`reviewed_by = admin_id`<br/>`reviewed_at = NOW()` | `POST /admin/posts/{post}/reject` | Admin Only |
| **Resubmit**| `rejected` | `pending` | `status = 'pending'` | `POST /posts/{post}/submit` | Author (Owner) |
| **Delete** | Any | *Deleted* | Record removed, disk thumbnail deleted | `DELETE /admin/posts/{post}` | Admin Only |

---

## 4. User Moderation Lifecycle (Lock / Unlock)

```mermaid
stateDiagram-v2
    [*] --> Active: User registers (is_locked = false)
    Active --> Locked: Admin triggers lock (POST /admin/users/{user}/lock)
    Locked --> Active: Admin triggers unlock (POST /admin/users/{user}/unlock)
    
    note right of Locked
        User cannot log in.
        Existing session invalidated by RoleMiddleware.
    end note
```

- When locked (`is_locked = true`), user attempts to authenticate or interact trigger HTTP `403 Forbidden` or redirect to login.
- Admins are forbidden from locking their own account to prevent administrative deadlocks.

---

## 5. Comment Moderation Flow

```mermaid
flowchart LR
    A[Viewer Posts Comment] --> B[status = 'pending']
    B --> C{Admin Decision}
    C -->|Approve| D[status = 'approved'<br/>Visible to all users]
    C -->|Spam| E[status = 'spam'<br/>Hidden from public]
    C -->|Delete| F[Record deleted from DB]
```
