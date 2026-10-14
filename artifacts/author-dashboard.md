# BlogMNM — Author Dashboard & Statistics Architecture

## 1. Executive Summary
The Author Dashboard (`GET /posts/stats` and `GET /author/posts/stats`) is the primary workspace for content creators. It provides an 8-metric statistical summary, a filterable post management table, and direct lifecycle action controls.

---

## 2. Statistical Metrics Architecture

The dashboard aggregates 8 distinct performance and workflow metrics for the authenticated author:

| # | Metric Identifier | Label | Calculation Source | Purpose |
|---|---|---|---|---|
| 1 | `total` | Tổng bài viết | `Post::where('user_id', $id)->count()` | Total volume of content authored. |
| 2 | `draft` | Bản nháp | `->where('status', 'draft')->count()` | Posts currently in progress. |
| 3 | `pending` | Đang chờ duyệt | `->where('status', 'pending')->count()` | Posts queued for editorial decision. |
| 4 | `published` | Đã xuất bản | `->where('status', 'published')->count()` | Live posts visible to the public. |
| 5 | `rejected` | Bị từ chối | `->where('status', 'rejected')->count()` | Posts requiring author revision. |
| 6 | `views` | Tổng lượt xem | `->sum('views')` | Cumulative readership engagement. |
| 7 | `likes` | Tổng lượt thích | `sum(likers_count)` across author posts | Reader appreciation metric. |
| 8 | `comments` | Tổng bình luận | `sum(comments_count)` across author posts | Community interaction metric. |

---

## 3. Data Query & N+1 Prevention Strategy

The stats calculation and post list use optimized Eloquent queries:

```php
$query = $user->isAdmin() ? Post::query() : Post::where('user_id', $user->id);

// Metric aggregation
$totalPosts = (clone $query)->count();
$draftCount = (clone $query)->where('status', 'draft')->count();
$pendingCount = (clone $query)->where('status', 'pending')->count();
$publishedCount = (clone $query)->where('status', 'published')->count();
$rejectedCount = (clone $query)->where('status', 'rejected')->count();
$totalViews = (clone $query)->sum('views');
$totalLikes = (clone $query)->withCount('likers')->get()->sum('likers_count');
$totalComments = (clone $query)->withCount('comments')->get()->sum('comments_count');

// Paginated management table with relations loaded
$posts = (clone $query)
    ->with(['category', 'tags'])
    ->withCount(['comments', 'likers', 'favoritedBy'])
    ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
    ->when($search, fn ($q) => $q->search($search))
    ->latest()
    ->paginate(10)
    ->withQueryString();
```

---

## 4. UI/UX Interaction Matrix

- **Metric Cards as Fast Filters**: Clicking any of the workflow stat cards (`draft`, `pending`, `published`, `rejected`) directly filters the post table via `?status=<status>`.
- **Search Integration**: Full-text searching across title, excerpt, and body (`?q=<keyword>`).
- **Inline Post Actions**:
  - `draft`: Submit for Review (`POST /posts/{id}/submit`), Edit (`GET /posts/{id}/edit`), Delete (`DELETE /posts/{id}`).
  - `pending`: View review indicator, Edit, Delete.
  - `published`: View live post (`GET /posts/{slug}`), Edit, Delete.
  - `rejected`: Resubmit (`POST /posts/{id}/submit`), Edit with guidance banner, Delete.
