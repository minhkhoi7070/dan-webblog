# BlogMNM — Future Development Roadmap

This roadmap outlines strategic feature expansions and architectural enhancements planned for future iterations (v2.0 and beyond) of the **BlogMNM** platform.

---

## 1. Editorial Experience & Media Engine (v2.0)

### 1.1 Rich Block Editor & Live Markdown Preview
- Replace standard textarea authoring with a modern, distraction-free block editor (e.g., **TipTap** or **Editor.js**) supporting inline code syntax highlighting, callouts, and drag-and-drop image embeds.
- Offer split-pane live Markdown preview for technical writers.

### 1.2 Scheduled Article Publication
- Enable authors to set a future `published_at` timestamp.
- Deploy an automated background scheduled command (`php artisan posts:publish-scheduled`) running via Laravel Task Scheduling to transition scheduled articles to `published` at the designated time.

### 1.3 Cloud Object Storage & Media Management
- Integrate AWS S3, Cloudflare R2, or DigitalOcean Spaces via Flysystem.
- Implement asynchronous image optimization queues (e.g. generating responsive WebP/AVIF variants using Intervention Image).
- Provide an author media library for reusing uploaded assets across multiple articles.

---

## 2. Real-Time Social Interactions & Notifications (v2.2)

### 2.1 WebSockets via Laravel Reverb
- Introduce real-time bidirectional messaging via **Laravel Reverb**.
- Push instant notifications to authors when readers like their post, leave a comment, or follow their profile.
- Live comment streaming: new comments appear dynamically without requiring page refreshes or polling.

### 2.2 Community Mentions & Discussions
- Support `@username` mentions in article bodies and comments with automated notification alerts.
- Introduce reader polls and interactive bookmark collections.

---

## 3. High-Performance Search & Discovery (v2.4)

### 3.1 Laravel Scout & Meilisearch Integration
- Integrate **Laravel Scout** backed by **Meilisearch**.
- Deliver instantaneous search-as-you-type modal overlays with highlighted matched keywords, phonetic typo-tolerance, and facet filtering by author, category, and date.

### 3.2 Content Recommendation Engine
- Implement a content recommendation algorithm based on shared tags, category embeddings, and reader engagement patterns ("Related Stories" / "Trending This Week").

---

## 4. Security, Governance & AI Moderation (v2.5)

### 4.1 Two-Factor Authentication (2FA)
- Introduce TOTP-based two-factor authentication (Google Authenticator, 1Password) for enhanced administrative account security.

### 4.2 Automated Content Moderation via Gemini API
- Integrate Google Gemini API or Perspective API to pre-scan incoming comments for spam, hate speech, or harassment, automatically triaging suspect comments into the admin review queue.

### 4.3 Multi-Tier Editorial Hierarchy
- Expand RBAC into granular editorial departments: *Staff Writer*, *Section Editor*, *Managing Editor*, and *Super Administrator*, supporting department-level publishing permissions.
