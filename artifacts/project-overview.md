# BlogMNM — Project Overview

## 1. Executive Summary

**BlogMNM** is a modern, high-performance web publishing and community blogging platform built with **Laravel 12**, **PHP 8.3**, **Tailwind CSS**, and **MySQL 8.4**. Designed around a contemporary, social-content-inspired aesthetic influenced by modern minimalist interfaces (such as Meta's Threads), BlogMNM combines the depth of long-form editorial publishing with the engaging, immediate interactions of social media.

The system features a strict multi-tier role-based access control (RBAC) system with 4 distinct actor profiles (**Guest**, **Viewer**, **Author**, **Administrator**), a complete post moderation lifecycle (Draft $\to$ Pending Review $\to$ Published / Rejected), a two-level threaded commenting system with moderation, asynchronous social interactions (Likes, Favorites, Follows), and an adaptive Black/White theme engine with zero layout shift or flash.

---

## 2. Project Vision & Design Philosophy

BlogMNM bridges the gap between traditional CMS platforms (like WordPress) and lightweight micro-blogging platforms:

- **Focused Reading Experience**: Centered 660px primary reading column maximizes readability and visual comfort across desktop, tablet, and mobile displays.
- **Threads-Inspired Visual Language**: Utilizes pure monochrome contrast (dark mode: `#000000` / `#101010` surfaces; light mode: `#ffffff` / `#f4f4f5` surfaces), subtle border lines (`#27272a` / `#e4e4e7`), high-contrast inverted pill buttons, and modern typography hierarchy.
- **Social Content Mechanics**: Readers can like posts with optimistic heart toggles, bookmark favorites into personal collections, follow favorite authors, and engage in threaded discussions.
- **Editorial Integrity**: Authors produce structured content with tags, categories, and image thumbnails, which undergo administrative peer-review and moderation prior to public release.

---

## 3. Technology Stack & Platform Specification

| Layer | Technology | Details / Versions |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x | Modern PHP web application framework |
| **Programming Language** | PHP 8.3+ | Strict types, constructor property promotion, attributes |
| **Database Management** | MySQL 8.4+ | Relational DB with InnoDB engine, foreign key constraints (`RESTRICT` / `CASCADE`) |
| **Frontend Styling** | Tailwind CSS v3 | Utility-first CSS configured with custom CSS design tokens |
| **Client-Side Scripting** | Vanilla JS / Alpine.js | Native fetch-based AJAX interactions, theme state management |
| **Bundling & Tooling** | Vite 6.x | Fast HMR, asset minification, cache-busted production builds |
| **Authentication Engine**| Laravel Breeze | Session-based stateful authentication, CSRF protection, rate limiting |
| **Quality & Formatting** | PHPUnit & Laravel Pint | 142 automated tests (514 assertions), PSR-12 code style compliance |

---

## 4. Core System Modules

### 4.1 Authentication & User Lifecycle
- Stateful session authentication with secure password hashing (Bcrypt).
- Brute-force throttling via `LoginRequest` (5 attempts per minute max).
- Administrative user locking: locked accounts are instantly ejected and prevented from logging in or mutating resources.
- Profile management with avatar support and password modification.

### 4.2 Content Publishing & Editorial Pipeline
- Authors create and edit draft posts with title, auto-slugging, summary excerpt, body content, category classification, tags, and custom image thumbnail.
- Thumbnail management with automatic cleanup of obsolete or orphaned media files upon replacement or post deletion.
- Four-state lifecycle: `draft` $\to$ `pending` $\to$ `published` or `rejected`.
- Author analytics dashboard tracking views, likes, bookmarks, and comments per post.

### 4.3 Social Interactions & Engagement
- **Likes**: Optimistic AJAX toggle recording user-post likes and returning live like counts.
- **Favorites**: Personal bookmarking system with a dedicated `/favorites` library.
- **Follows**: Bidirectional follower/following graph between users and authors, with self-follow prevention.
- **Comments**: Threaded discussions supporting root comments and single-level nested replies, with cross-post reference validation and admin spam/approval moderation.

### 4.4 Taxonomy & Content Discovery
- Keyword search querying `title`, `excerpt`, and `body` with safe subquery SQL grouping.
- Taxonomy filtering by Category and Tag with active filter badges.
- URL query string preservation across search, taxonomy filters, and paginated pages (`?q=...&category=...&page=...`).
- Out-of-bounds page protection redirecting invalid page requests safely.

### 4.5 Governance & Administration
- Dedicated admin portal (`/admin`) guarded by `RoleMiddleware`.
- Interactive review queue for approving or rejecting posts with author feedback notes.
- Comment moderation queue for approving comments, flagging spam, or hard deletion.
- Category lifecycle management with strict deletion protection (`RESTRICT`) preventing accidental orphan cascades when active posts exist.
- User management with lock/unlock actions and administrator lockout safeguards.

### 4.6 Design System & Theming
- Unified Black/White color tokens dynamically loaded via CSS variables (`--color-bg`, `--color-surface`, `--color-border`, `--color-text`, etc.).
- Inline zero-flash theme detection script in HTML `<head>` reading `localStorage` or OS `prefers-color-scheme`.
- Seamless responsiveness verified across Mobile (375px), Tablet (768px), and Desktop (1280px+).

---

## 5. System Health & Quality Metrics

- **Automated Tests**: 142 passing tests (514 assertions) with 0 failures across Feature and Unit test suites.
- **QA Verification Scenarios**: 52/52 verified scenarios passing (100% pass rate).
- **Code Formatting**: 100% formatted via Laravel Pint without style discrepancies.
- **Application Routes**: 63 registered routes across public, auth, author, and admin interfaces.
