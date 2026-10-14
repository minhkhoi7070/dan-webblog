# BlogMNM — Known Limitations & Architectural Boundaries

This document provides a transparent record of the current system boundaries, technical trade-offs, and deliberate design decisions in **BlogMNM**.

---

## 1. Content & Taxonomy Boundaries

### 1.1 Single Category Association
- **Current Behavior**: Each post is bound to exactly one Category via a `category_id` foreign key in the `posts` table.
- **Rationale**: Keeps taxonomy structure clean and unambiguous for URL routing, breadcrumbs, and filtering.
- **Alternative**: Multi-category tagging is achieved through the Many-to-Many `tags` relationship (`post_tag` pivot).

### 1.2 Two-Level Discussion Threading
- **Current Behavior**: The commenting system supports exactly two levels of conversation: **Root Comments** (`parent_id = null`) and **Direct Replies** (`parent_id = root_comment.id`). Replies to replies are attached to the root comment thread.
- **Rationale**: Prevents infinite nesting recursion that degrades readability and causes severe margin indentation collapse on narrow mobile viewports ($375\text{px}$).

---

## 2. Media & Filesystem Constraints

### 2.1 Synchronous Media Processing
- **Current Behavior**: Post thumbnails are uploaded, validated, and written to the public disk synchronously during the `POST /posts` or `PUT /posts/{post}` request cycle.
- **Boundary**: Strict validation limits uploads to $\le 2048\text{ KB}$ and image MIME types (`jpg`, `jpeg`, `png`, `webp`). Large image batch processing or asynchronous background queue transcoding (e.g. with Intervention Image / FFmpeg) is not enabled in this baseline release.

### 2.2 Single Featured Image per Post
- **Current Behavior**: Each article supports one featured banner thumbnail stored in the `thumbnail` column.
- **Boundary**: No dedicated multi-image gallery or media library manager exists in v1.

---

## 3. Search & Indexing Engine

### 3.1 SQL LIKE Pattern Matching
- **Current Behavior**: `Post::scopeSearch()` queries MySQL using grouped `LIKE '%keyword%'` pattern matching across `title`, `excerpt`, and `body`.
- **Boundary**: While highly optimized with indexes on `status` and `published_at` for current blog scales, SQL `LIKE` queries do not provide natural language stemming, phonetics, typo-tolerance, or term-frequency relevance scoring that specialized search engines (like Meilisearch or Elasticsearch) deliver.

---

## 4. Realtime & Notification Infrastructure

### 4.1 On-Demand & Polled Social Interactions
- **Current Behavior**: Like counters and follow states update optimistically via client-side fetch requests (`interactions.js`).
- **Boundary**: The platform does not maintain persistent bidirectional WebSocket connections (such as Laravel Reverb or Pusher). Real-time live notification popups when another reader likes your post are not present in this release.

### 4.2 Mail Delivery Driver
- **Current Behavior**: Password reset and verification emails default to `MAIL_MAILER=log` in local development.
- **Operational Requirement**: Production deployments require configuring external SMTP credentials (e.g. Mailgun, Postmark, AWS SES) in `.env` to dispatch real emails.
