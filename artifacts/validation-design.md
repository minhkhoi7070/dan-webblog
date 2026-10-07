# BlogMNM — Form Request & Validation Architecture

## 1. Executive Summary
Post creation and updates are governed by `StorePostRequest`. This request handles input sanitization, alias reconciliation (`content` vs `body`), file upload rules, relation constraints, and protection against unauthorized workflow status tampering.

---

## 2. Validation Rules Matrix (`StorePostRequest`)

| Field | Type | Rules | Behavior & Edge Cases |
|---|---|---|---|
| `title` | string | `required`, `string`, `max:255` | Generates URL-friendly unique slug automatically. |
| `category_id` | integer | `required`, `integer`, `exists:categories,id` | Must match an existing category in the database. |
| `excerpt` | string | `nullable`, `string`, `max:500` | Optional. If null, controller derives 150-char excerpt from body. |
| `content` | string | `required_without:body`, `nullable`, `string` | Reconciled with `body` during `prepareForValidation`. |
| `body` | string | `required_without:content`, `nullable`, `string` | Database persistence column. |
| `thumbnail` | file | `nullable`, `image`, `mimes:jpg,jpeg,png,webp`, `max:2048` | Validates image type and 2MB size cap. |
| `tags` | array | `nullable`, `array` | List of tag IDs to synchronize. |
| `tags.*` | integer | `integer`, `exists:tags,id` | Every tag ID must exist in `tags` table. |

---

## 3. Input Normalization & Security Preprocessing

```php
protected function prepareForValidation(): void
{
    // 1. Support both 'content' and 'body' aliases seamlessly
    if ($this->has('content') && ! $this->has('body')) {
        $this->merge(['body' => $this->input('content')]);
    } elseif ($this->has('body') && ! $this->has('content')) {
        $this->merge(['content' => $this->input('body')]);
    }

    // 2. Security rule: Strip workflow escalation fields for non-admin authors
    if ($this->user() && ! $this->user()->isAdmin()) {
        $this->request->remove('status');
        $this->request->remove('publish_now');
        $this->request->remove('reviewed_by');
        $this->request->remove('reviewed_at');
    }
}
```

---

## 4. File Upload Lifecycle & Public Disk Storage

```mermaid
flowchart TD
    A[Author uploads thumbnail] --> B[StorePostRequest validates mime & size]
    B -->|Invalid| C[Return 422 with thumbnail error]
    B -->|Valid| D[PostController checks hasFile 'thumbnail']
    D --> E[Store to 'thumbnails' directory on 'public' disk]
    E --> F[Storage path: 'storage/thumbnails/...']
    F --> G[Save path into posts.thumbnail]
    G --> H[On update/delete: Old file deleted via Storage::disk('public')->delete()]
```

1. **Storage Disk**: `public` (configured in `config/filesystems.php`).
2. **Directory**: `storage/app/public/thumbnails`.
3. **Symbolic Link**: Linked to `public/storage` via `php artisan storage:link`.
4. **Cleanup Guarantee**: On thumbnail replacement or post deletion (`PostController@update` and `PostController@destroy`), old files stored under `storage/` are actively unlinked from disk to prevent storage leaks.

---

## 5. Tag Synchronization

Tag management strictly utilizes Eloquent's `BelongsToMany::sync`:
```php
$post->tags()->sync($request->input('tags', []));
```
- Attaches new tag relations.
- Detaches unselected tag relations without duplicate key errors.
- Handles empty/cleared tag selections gracefully.
