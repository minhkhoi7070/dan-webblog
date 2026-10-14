# BlogMNM — Search Architecture & Design Specification

## 1. Overview
The Search architecture in BlogMNM allows visitors and registered users to discover published articles by matching search terms across article headlines (`title`), short summaries (`excerpt`), and full article markdown/HTML content (`body`). The search engine operates seamlessly alongside taxonomy filters (Category and Tag) and preserves query strings across paginated result sets.

```mermaid
flowchart TD
    User([Visitor / Reader]) -->|Inputs query 'q'| SearchBar[Search Input Field]
    SearchBar -->|GET /posts?q={term}| Controller[PostController@index]
    Controller -->|published() scope| BaseQuery[Status = 'published']
    BaseQuery -->|scopeSearch('term')| SearchScope[Grouped SQL OR Clauses]
    SearchScope -->|scopeCategory('cat')| CatScope[Category Relation Filter]
    CatScope -->|scopeTag('tag')| TagScope[Tag Relation Filter]
    TagScope --> OrderBy[Order By published_at DESC]
    OrderBy --> Paginator[paginate(10)->withQueryString()]
    Paginator --> View[Render resources/views/posts/index.blade.php]
```

---

## 2. Query Logic & SQL Generation

The core query builder logic is implemented in `App\Models\Post::scopeSearch()`:

```php
/**
 * Scope to search posts by title, excerpt, or body.
 */
public function scopeSearch(Builder $query, ?string $search): Builder
{
    if (! $search) {
        return $query;
    }

    return $query->where(function (Builder $q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
            ->orWhere('excerpt', 'like', "%{$search}%")
            ->orWhere('body', 'like', "%{$search}%");
    });
}
```

### Subquery Grouping & Security Precedence
The closure `function (Builder $q)` ensures that MySQL evaluates the search criteria as an isolated grouped boolean expression. The resulting SQL query ensures unpublished posts are never leaked:

```sql
SELECT * FROM `posts`
WHERE `status` = 'published'
  AND (
    `title` LIKE '%laravel%'
    OR `excerpt` LIKE '%laravel%'
    OR `body` LIKE '%laravel%'
  )
ORDER BY `published_at` DESC
LIMIT 10 OFFSET 0;
```

---

## 3. Taxonomy Interoperability & Filter Stacking

Search terms can be combined with category and tag filters simultaneously:
- **Combined URL**: `/posts?q=framework&category=technology&tag=php&page=2`
- **Controller Assembly**:
```php
$posts = Post::published()
    ->with(['user', 'category', 'tags'])
    ->withCount(['likers', 'favoritedBy', 'comments'])
    ->search($request->query('q'))
    ->category($request->query('category'))
    ->tag($request->query('tag'))
    ->latest('published_at')
    ->paginate(10)
    ->withQueryString();
```

---

## 4. UI / UX Design & State Presentation

1. **Sticky Header Search Input**: Positioned within the centered feed column header, styled with minimal borders and design tokens (`bg-[var(--color-surface)] border-[var(--color-border)] text-[var(--color-text)]`).
2. **Active Search Pill**: When `?q=` is active, an informational pill is displayed showing the active term and total results found.
3. **Instant Reset Button**: A clear "×" button resets the search filter while retaining other active category filters.
4. **Empty State Experience**: If no posts match the query, a dedicated empty-state card is rendered with suggested popular topics.
