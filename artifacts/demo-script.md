# BlogMNM — System Demonstration Script

**Target Duration:** ~6–8 Minutes  
**Presenters:** Project Team / Engineering Lead  
**Audience:** Evaluators, Stakeholders, Technical Assessors  
**Prerequisites:** Server running on `http://127.0.0.1:8000` with seeded database (`php artisan migrate:fresh --seed && npm run build`).

---

## Scene 1: First Impression & Guest Discovery (~1.5 Minutes)

### Actions & Narration
1. **Open Browser** to `http://127.0.0.1:8000`:
   > *"Welcome to BlogMNM. Our design philosophy draws inspiration from modern social-content interfaces like Meta's Threads. Notice the centered 660-pixel reading column, crisp black/white visual palette, minimal borders, and generous whitespace designed to prioritize reading comfort."*
2. **Toggle Theme**:
   - Click the theme toggle icon in the sidebar. Show the switch from pure dark mode (`#000000`) to crisp light mode (`#ffffff`).
   - Refresh the page to prove **zero flash of unstyled content (FOUC)**.
3. **Search & Taxonomy Discovery**:
   - Type `"Laravel"` in the top search bar and press Enter.
   - Point out the active filter pill: *"Notice how the search query is preserved across category filters and pagination."*
   - Click a category chip (e.g. *Technology*) to stack filters together.
4. **Read Article & Attempt Interaction**:
   - Click on an article card to enter the post detail view.
   - Note that the view counter increments atomically.
   - Attempt to click the Heart or Bookmark icon: point out that the system prompts unauthenticated guests to log in.

---

## Scene 2: Viewer Community Engagement (~1.5 Minutes)

### Actions & Narration
1. **Log in as Viewer**:
   - Click **Log In**, enter `viewer@example.com` / `password`.
   - Directed smoothly to the dashboard / home feed.
2. **Asynchronous Social Interactions**:
   - Click the **Heart icon** on a post:
     > *"Observe the optimistic UI update: the heart illuminates and the live like count increments immediately via AJAX without a full page reload."*
   - Click the **Bookmark icon** on a post.
   - Click **Favorites** in the navigation sidebar: show the bookmarked post safely stored in the reader's personal collection (`/favorites`).
3. **Follow Author**:
   - Click the author's avatar to visit their profile (`/authors/{user}`).
   - Click **Follow**: the button transitions cleanly to a subtle "Following" state.
4. **Threaded Discussions**:
   - Return to any post detail view and scroll to the Discussion section.
   - Post a root comment: *"Great insights on software architecture!"*
   - Show the comment dynamically appearing in the discussion tree.
   - Click **Reply** on an existing comment and post a threaded response: point out the clear visual hierarchy and indentation.

---

## Scene 3: Author Writing & Editorial Workflow (~2 Minutes)

### Actions & Narration
1. **Switch to Author**:
   - Log out, then log in as `author@example.com` / `password`.
   - Point out the new navigation options available in the sidebar: **New Post** and **Author Stats**.
2. **Draft a New Article**:
   - Click **New Post** (`/posts/create`).
   - Enter title: *"Building Resilient Web Systems with Laravel 12"*.
   - Select category *Technology*, select tags `#backend`, `#architecture`.
   - Enter excerpt and body content.
   - Upload an image thumbnail via the dropzone.
   - Click **Save Draft**.
3. **Verify Draft Isolation**:
   - Point out the flash notification: *"Post created as draft."*
   - Open a private/incognito window to `http://127.0.0.1:8000`: prove that the draft post is **strictly invisible** to the public.
4. **Submit for Editorial Review**:
   - In the author view, click **Submit for Review**.
   - Show status transition to `pending`.
5. **Author Analytics Dashboard**:
   - Navigate to **Author Stats** (`/author/posts/stats`).
   - Highlight the 8-metric analytics grid showing total views, likes, comments, and post counts across all statuses.

---

## Scene 4: Administrator Governance & Quality Control (~2 Minutes)

### Actions & Narration
1. **Log in as Administrator**:
   - Log in as `admin@example.com` / `password`.
   - Click **Admin** to enter the Administrative Operations Center (`/admin/dashboard`).
2. **Review & Approve Post**:
   - Navigate to **Posts** (`/admin/posts`) and select the pending post submitted in Scene 3.
   - Inspect article details, author details, and thumbnail.
   - Click **Approve**:
     > *"Upon approval, the system populates the review audit trail (reviewed_by, reviewed_at, published_at). The article is now live."*
   - Switch back to the public homepage: show the newly approved article now leading the public feed!
3. **Security Defenses Demonstration**:
   - Navigate to **Users** (`/admin/users`).
   - Attempt to lock another administrator: demonstrate that the system **blocks peer administrator lockouts** (`SEC-05`).
   - Navigate to **Categories** (`/admin/categories`).
   - Attempt to delete a category containing active articles: demonstrate that the system **prevents deletion** with an informative banner and enforces `RESTRICT` database integrity (`SEC-06`).

---

## Scene 5: Responsive Multi-Device Experience (~1 Minute)

### Actions & Narration
1. **Open Chrome DevTools** (F12) and toggle device toolbar:
2. **Tablet Mode (768px)**:
   - Show the sidebar automatically collapsing into a sleek, icon-only navigation rail (`w-20`), keeping the reading column centered.
3. **Mobile Mode (375px)**:
   - Show the desktop sidebar disappearing.
   - Highlight the minimal sticky top header.
   - Showcase the fixed bottom navigation bar (`h-16`) with high-contrast active icons and touch targets exceeding $44\text{px} \times 44\text{px}$.
   - Demonstrate smooth navigation across Feed, Search, and Profile on mobile.

---

## Conclusion
> *"BlogMNM demonstrates how modern Laravel architecture, strict defense-in-depth security, and a contemporary, social-inspired design come together to deliver an exceptional reading and publishing experience."*
