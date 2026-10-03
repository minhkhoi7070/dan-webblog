# BlogMNM — Visual Verification & Screenshot Plan

This document defines the formal visual test catalogue, layout targets, viewports, and specific UI elements to be captured and audited across all 19 core views in **BlogMNM**.

---

## 1. Test Viewports & Display Matrix

| Device Profile | Viewport Resolution | Key Responsive Characteristics to Verify |
| :--- | :--- | :--- |
| **Desktop** | $1280 \times 800\text{px}$ | Full navigation sidebar (`w-64`), centered 660px feed, right context area |
| **Tablet** | $768 \times 1024\text{px}$ | Icon-only collapsed sidebar (`w-20`), centered feed column |
| **Mobile** | $375 \times 812\text{px}$ | Minimal sticky top header, fixed bottom navigation bar (`h-16`), touch targets $\ge 44\text{px}$ |

---

## 2. Screenshot Capture Catalogue

### Group 1: Public Discovery & Social Feed

| ID | Page / State | URL / Action | Viewports | Themes | Key Verification Checklist |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SS-01** | Home Feed | `GET /` | Desktop, Tablet, Mobile | Dark & Light | Centered 660px feed, post cards, author avatars, view counters, like/bookmark icons |
| **SS-02** | Search Results | `GET /posts?q=laravel` | Desktop, Mobile | Dark & Light | Active search query banner, result count chip, highlighted clear button |
| **SS-03** | Category Filter | `GET /posts?category=technology` | Desktop, Mobile | Dark & Light | Active category pill highlighted, filtered post stream |
| **SS-04** | Post Detail | `GET /posts/{slug}` | Desktop, Mobile | Dark & Light | High-contrast headline, thumbnail banner, author badge, like toggle, bookmark toggle |
| **SS-05** | Threaded Comments | `GET /posts/{slug}#comments` | Desktop, Mobile | Dark & Light | Root comments, indented reply cards, reply button form, dynamic comment injection |
| **SS-06** | Author Profile | `GET /authors/{user}` | Desktop, Mobile | Dark & Light | Author avatar, bio, follower count, high-contrast Follow / Following pill button |
| **SS-07** | Favorites Library | `GET /favorites` | Desktop, Mobile | Dark & Light | Bookmarked post stream, remove bookmark button, empty state banner |
| **SS-08** | Activity Stream | `GET /activity` | Desktop, Mobile | Dark & Light | Chronological event cards (new posts, likes, comments) |

### Group 2: Authentication & Profile Management

| ID | Page / State | URL / Action | Viewports | Themes | Key Verification Checklist |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SS-09** | Login | `GET /login` | Desktop, Mobile | Dark & Light | Centered card, input contrast, remember me checkbox, error validation states |
| **SS-10** | Register | `GET /register` | Desktop, Mobile | Dark & Light | Full form, password confirmation, clean inputs, role default notice |
| **SS-11** | Profile Settings | `GET /profile` | Desktop, Mobile | Dark & Light | Profile information form, password update card, account deletion modal |

### Group 3: Author Workspace & Editorial CMS

| ID | Page / State | URL / Action | Viewports | Themes | Key Verification Checklist |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SS-12** | New Post Form | `GET /posts/create` | Desktop, Mobile | Dark & Light | Title input, category dropdown, tag pills, thumbnail dropzone, Save Draft button |
| **SS-13** | Edit Post & Feedback | `GET /posts/{post}/edit` | Desktop, Mobile | Dark & Light | Pre-filled content, current thumbnail preview, rejection reason feedback banner |
| **SS-14** | Author Dashboard | `GET /author/posts/stats` | Desktop, Mobile | Dark & Light | 8-metric portfolio grid, post status tabs (Draft, Pending, Published), management table |

### Group 4: Administrative Governance & Moderation

| ID | Page / State | URL / Action | Viewports | Themes | Key Verification Checklist |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SS-15** | Admin Dashboard | `GET /admin` | Desktop, Tablet | Dark & Light | System metrics cards, review queue pipeline summary, recent user activity |
| **SS-16** | Post Review Queue | `GET /admin/posts` | Desktop, Tablet | Dark & Light | Pending submissions list, Approve pill button, Reject modal with feedback textarea |
| **SS-17** | Comment Moderation | `GET /admin/comments` | Desktop, Tablet | Dark & Light | Comments table, Approve action, Mark Spam action, Delete action |
| **SS-18** | User Directory | `GET /admin/users` | Desktop, Tablet | Dark & Light | User role badges, lock/unlock buttons, protected admin accounts |
| **SS-19** | Category Manager | `GET /admin/categories` | Desktop, Tablet | Dark & Light | Category table, post count column, deletion safety error banner |

### Group 5: Pagination & Navigation Details

| ID | Component / Element | URL / Action | Viewports | Themes | Key Verification Checklist |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SS-20** | Pagination Controls | `GET /posts?page=2` | Desktop, Mobile | Dark & Light | Inverted active pill button, surface inactive buttons, preserved query strings |
| **SS-21** | Mobile Bottom Nav | Viewport $\le 640\text{px}$ | Mobile (375px) | Dark & Light | Fixed bottom bar (`h-16`), touch targets $\ge 44\text{px}$, theme switch icon |
