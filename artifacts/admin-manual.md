# BlogMNM — Administrator Operations Manual

This manual provides instructions for platform administrators responsible for content quality control, discussion moderation, user governance, and taxonomy management in **BlogMNM**.

---

## 1. Accessing the Administrator Portal

1. Log in with an administrator account (e.g. `admin@example.com`).
2. Click **Admin** in the primary navigation sidebar or navigate to: **`http://127.0.0.1:8000/admin`**.
3. Access is guarded by `RoleMiddleware`. Non-admin accounts receive HTTP 403 Forbidden.

---

## 2. Dashboard Analytics & Operations Center

The central dashboard (`/admin/dashboard`) aggregates real-time health metrics:
- **System Metrics Grid**: Total registered users, total published articles, total comments, and active categories.
- **Editorial Pipeline**: Immediate visibility into the count of articles awaiting review (`Pending Review`).
- **Moderation Queues**: Pending comments awaiting inspection and recently flagged spam discussions.

---

## 3. Post Moderation & Editorial Review Workflow

### 3.1 Inspecting Pending Submissions
1. Click **Posts** in the admin subnavigation or visit `/admin/posts`.
2. Filter the queue by status (Pending, Published, Draft, Rejected).
3. Click on any post title to read the complete article body, view the attached thumbnail, and inspect author information and taxonomy tags.

### 3.2 Approving an Article
1. Verify that the article adheres to quality guidelines, formatting standards, and legal compliance.
2. Click the high-contrast **Approve** button.
3. The system executes the following state transitions:
   - Updates `status` to `'published'`.
   - Records your administrator ID in `reviewed_by`.
   - Populates `reviewed_at` and `published_at` with the current timestamp.
   - Clears any prior `rejection_reason`.
4. The article becomes visible in the public home feed and search engine immediately.

### 3.3 Rejecting an Article with Feedback
1. If the article requires revision or violates standards, click **Reject**.
2. A rejection modal opens. Enter constructive feedback explaining why the post was rejected and what changes the author needs to make.
3. Click **Confirm Rejection**.
4. The system updates `status` to `'rejected'`, records `reviewed_by` and `reviewed_at`, and saves your feedback in `rejection_reason`.
5. The author can view this feedback in their edit view and resubmit once revised.

### 3.4 Deleting an Article
- To delete an article permanently, click **Delete Post**.
- The post record is removed, and its associated thumbnail is deleted from disk via the `Post::booted()` cleanup hook.

---

## 4. Comment & Discussion Moderation

Navigate to **Comments** (`/admin/comments`) to manage platform conversations:

### 4.1 Approving Comments
- Comments submitted in the system can be reviewed. Click **Approve** to ensure the comment is displayed in the public discussion tree.

### 4.2 Handling Inappropriate / Spam Comments
- Click **Mark Spam** on any unwanted, promotional, or abusive comment.
- The comment status transitions to `'spam'`, and it is immediately suppressed from the public article view.

### 4.3 Permanent Comment Deletion
- Click **Delete** to permanently purge a comment and all of its nested replies from the database.

---

## 5. User Management & Account Lockout

Navigate to **Users** (`/admin/users`) to manage the platform user base:

### 5.1 Locking an Abusive Account
1. Search or locate the target user in the directory.
2. Click **Lock Account** (`POST /admin/users/{user}/lock`).
3. The user's `is_locked` attribute is set to `true`.
4. **Immediate Impact**:
   - The user's active session is terminated upon their next request.
   - Subsequent login attempts are blocked with a 422 error.
   - The user cannot create, edit, or submit posts, comment, like, or follow authors.

### 5.2 Unlocking an Account
- To restore access, click **Unlock Account** (`POST /admin/users/{user}/unlock`). The account returns to normal standing.

### 5.3 Administrator Safety Safeguard (`SEC-05`)
- The system includes a built-in safety safeguard: **Administrators cannot lock other administrators or lock themselves**.
- Attempting to lock an administrator account is intercepted and rejected with the notification: *"Administrators cannot be locked."*

---

## 6. Category & Taxonomy Management

Navigate to **Categories** (`/admin/categories`):

### 6.1 Creating & Editing Categories
- Click **New Category** (`/admin/categories/create`), enter a category name and URL slug, and click Save.
- To modify an existing category, click **Edit** (`/admin/categories/{category}/edit`).

### 6.2 Deleting Categories & Post Safety Guard (`SEC-06`)
- Click **Delete** on a category with zero posts to remove it.
- **Safety Restriction**: If a category has active articles assigned to it, deletion is blocked:
  - Error banner: *"Cannot delete category because it has active posts assigned to it."*
  - The database enforces this via `ON DELETE RESTRICT` on foreign key `posts.category_id`.
  - To delete the category, you must first reassign or delete the associated articles.
