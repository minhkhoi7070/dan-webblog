# BlogMNM — User & Author Manual

Welcome to **BlogMNM**. This user manual guides readers and content creators on how to discover stories, engage with the community, write engaging articles, and track portfolio performance.

---

## 1. Exploring Content & Reading Experience

### 1.1 Home Feed & Reading Column
- Upon visiting the homepage (`/`), you are greeted by a clean, centered feed column displaying published stories in reverse chronological order.
- Each post card presents the author's avatar, publish date, category chip, headline, preview excerpt, thumbnail image, view count, and social action buttons.
- Click any article card to enter the full reading view (`/posts/{slug}`). The view counter automatically updates.

### 1.2 Switching Between Dark & Light Theme
- Click the **Theme Toggle** icon in the sidebar (or bottom navigation on mobile) to switch between Dark Mode and Light Mode.
- Your theme selection is automatically saved in your browser and remembered across sessions.

### 1.3 Content Discovery (Search & Filters)
- **Keyword Search**: Type your search term into the search bar at the top of the feed and press Enter. The system searches across article titles, excerpts, and main content.
- **Category Filter**: Click any category pill (e.g. *Technology*, *Design*, *Culture*) to view articles published in that category.
- **Tag Filter**: Click any tag badge (`#laravel`, `#minimalism`) to discover stories with shared keywords.
- **Reset Filters**: Click the **Reset** or **Clear** icon on the active filter banner to return to the full feed.

---

## 2. Community Engagement & Social Interactions

*Note: You must be registered and logged in to participate in social interactions.*

### 2.1 Liking a Post
- Click the **Heart** icon on any article card or inside the article detail view.
- The heart icon illuminates, and the total like count increments in real time without refreshing the page. Click again to remove your like.

### 2.2 Bookmarking to Favorites
- Click the **Bookmark** icon on an article to save it to your personal library.
- To view all your saved stories, click **Favorites** in the navigation sidebar or visit `/favorites`.
- Click the bookmark icon again on any saved post to remove it from your collection.

### 2.3 Following an Author
- When viewing an article, click the author's name or avatar to visit their public profile page (`/authors/{user}`).
- Click the high-contrast **Follow** button. It transforms into a subtle **Following** button.
- You can unfollow at any time by clicking the button again.

### 2.4 Participating in Discussions (Comments & Threaded Replies)
- Scroll to the bottom of any article to view the **Discussion** section.
- **Posting a Comment**: Type your thoughts in the comment input box and click **Post comment**. Your comment appears immediately in the conversation thread.
- **Replying to a Comment**: Click the **Reply** button beneath any existing comment. An indented reply form will appear. Submit your message to create a nested reply.

---

## 3. Author Guide: Publishing & Editorial Workflow

*Note: Author capabilities are available to users assigned the Author or Administrator role.*

### 3.1 Creating a New Article
1. Click **New Post** in the sidebar or navigate to `/posts/create`.
2. Fill in the required fields:
   - **Title**: Enter a clear headline.
   - **Category**: Select the primary category from the dropdown.
   - **Excerpt**: Write a concise 1–2 sentence summary that will appear on feed cards.
   - **Body**: Enter your full article content.
   - **Tags**: Select one or more keyword tags.
   - **Featured Thumbnail**: Click or drag-and-drop an image (`.jpg`, `.png`, `.webp`, maximum size 2MB).
3. Click **Save Draft**. Your article is saved securely with status `draft`.

### 3.2 Submitting for Editorial Review
1. Once your draft is ready for publication, click **Submit for Review**.
2. Your post status transitions to `pending`, and it enters the editorial moderation queue.
3. While pending, the article cannot be edited until reviewed by an administrator.

### 3.3 Handling Rejection & Resubmission
- If an editor rejects your article, its status becomes `rejected`.
- Open your article in the editor (`/posts/{post}/edit`) to view the editor's **Rejection Feedback**.
- Revise your content based on the feedback, and click **Resubmit for Review** to re-enter the moderation queue.

### 3.4 Monitoring Author Statistics
- Navigate to **Author Stats** (`/author/posts/stats`) to view your personal dashboard.
- Track total views, likes, comments, and bookmark saves across your entire writing portfolio.
- Manage all your drafts, pending submissions, and published articles from the portfolio table.

---

## 4. Managing Account & Settings

- Visit **Profile** (`/profile`) from the user menu to:
  - Update your display name and email address.
  - Change your password.
  - Delete your account (permanently removes your profile and associated data).
