# BlogMNM — Pagination Architecture & Design Specification

## 1. Overview
BlogMNM employs Laravel's `LengthAwarePaginator` configured to present 10 articles per page. The pagination component is designed to ensure seamless navigation, preserve all active query string filters, maintain WCAG accessibility standards, and protect the system against out-of-bounds page requests.

---

## 2. Paginator Configuration & Query Preservation

In `PostController@index`:

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

### Query Parameter Persistence (`withQueryString`)
By appending `->withQueryString()`, the paginator automatically encodes and preserves all active search parameters across page links:
- `?q=architecture&category=web&page=2`
- `?tag=backend&page=3`

---

## 3. Boundary Safety & Out-of-Bounds Redirection

To ensure resilient URL handling, `PostController` validates requested page numbers against available page counts:

```php
$currentPage = (int) $request->query('page', 1);
$lastPage = $posts->lastPage();

if ($currentPage < 1) {
    return redirect()->route('posts.index', array_merge($request->query(), ['page' => 1]));
}

if ($lastPage > 0 && $currentPage > $lastPage) {
    return redirect()->route('posts.index', array_merge($request->query(), ['page' => $lastPage]));
}
```
This guarantees that invalid inputs (`?page=0`, `?page=-5`, `?page=99999`) redirect gracefully to boundary limits without blank screens or 500 errors (`SRCH-04`).

---

## 4. Custom Blade Pagination Template

The pagination UI is customized in `resources/views/vendor/pagination/tailwind.blade.php`, fully styled with the platform's Black/White design tokens:

- **Active Page Button**: Inverted high-contrast pill button (`bg-[var(--color-text)] text-[var(--color-bg)] rounded-full font-medium`).
- **Inactive Page Buttons**: Surface pills with subtle borders (`bg-[var(--color-surface)] border-[var(--color-border)] text-[var(--color-text)] hover:bg-[var(--color-surface-hover)] rounded-full`).
- **Previous / Next Controls**: Fully accessible arrow buttons with `aria-label="Previous Page"` and `aria-label="Next Page"`. Disabled states gracefully reduce opacity without breaking layout.
- **Mobile Responsive Layout**: Compact "Previous" / "Next" buttons with current page indicator on mobile viewports ($\le 640\text{px}$).
