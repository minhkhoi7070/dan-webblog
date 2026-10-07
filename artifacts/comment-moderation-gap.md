# Phân Tích & Khắc Phục Lỗ Hổng Kiểm Duyệt Bình Luận (Comment Moderation Gap) — BlogMNM

> **Ngày thực hiện:** 29/09/2026  
> **Vấn đề:** Use Case gốc yêu cầu *"Tác giả (Author) có thể ẩn/xóa bình luận spam trên bài viết của mình"*, tuy nhiên hệ thống tồn tại sự không đồng nhất giữa quyền hạn thực tế và phân quyền chính sách (`CommentPolicy`).  
> **Trạng thái:** Đã khắc phục hoàn chỉnh, bảo đảm an toàn phân quyền và vượt qua 100% bài kiểm tra tự động.

---

## 1. Phân Tích Lỗ Hổng (Gap Analysis & Root Cause)

### 1.1. Hiện trạng trước khi sửa đổi
Tại giao diện chi tiết bài viết ([`resources/views/posts/show.blade.php`](file:///c:/laragon/www/webblog/resources/views/posts/show.blade.php)), đội ngũ phát triển đã tích hợp sẵn nút xóa bình luận được bao bọc bởi directive kiểm tra quyền hạn của Laravel:

```blade
@can('delete', $comment)
    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs text-[var(--color-text-secondary)] hover:text-rose-400 transition" title="Xóa">
            Xóa
        </button>
    </form>
@endcan
```

Tuy nhiên, tại tầng phân quyền chính sách ([`app/Policies/CommentPolicy.php`](file:///c:/laragon/www/webblog/app/Policies/CommentPolicy.php)), phương thức `delete()` ban đầu chỉ được cấu hình như sau:

```php
// Cấu hình ban đầu:
public function delete(User $user, Comment $comment): bool
{
    return $user->id === $comment->user_id || $user->isAdmin();
}
```

### 1.2. Hậu quả thực tế
1. **Đối với Tác giả sở hữu bài viết:** Khi một độc giả khác để lại bình luận mang tính chất spam, khiêu khích hoặc quảng cáo rác trên bài viết của tác giả:
   - `$user->id === $comment->user_id` trả về `false` (vì tác giả không phải người viết bình luận đó).
   - `$user->isAdmin()` trả về `false` (vì tác giả mang role `author`).
   - Do đó, `@can('delete', $comment)` đánh giá là `false`, **nút Xóa bị ẩn hoàn toàn** đối với tác giả.
   - Nếu tác giả cố tình gửi request trực tiếp bằng công cụ bên ngoài, hệ thống sẽ chặn lại với mã lỗi `HTTP 403 Forbidden`.
2. **Hệ quả nghiệp vụ:** Tác giả bị tước đi công cụ tự vệ cơ bản nhất để bảo vệ bài viết và độc giả của mình khỏi các nội dung độc hại mà phải phụ thuộc hoàn toàn vào Quản trị viên xử lý qua `/admin/comments`.

---

## 2. Giải Pháp Khắc Phục (Minimal & Precise Fix)

Tuân thủ nguyên tắc không thay đổi database schema, không thay đổi route contract và chỉ can thiệp vào đúng phần code thực sự thiếu sót, nhóm đã cập nhật phương thức `delete()` trong [`app/Policies/CommentPolicy.php`](file:///c:/laragon/www/webblog/app/Policies/CommentPolicy.php):

```php
/**
 * Determine whether the user can delete the model.
 * Allowed: Comment creator, Post author (moderating own post), or Administrator.
 */
public function delete(User $user, Comment $comment): bool
{
    return $user->id === $comment->user_id
        || $user->isAdmin()
        || $user->id === $comment->post?->user_id;
}
```

### Điểm mấu chốt của logic mới:
- Bổ sung điều kiện `$user->id === $comment->post?->user_id`.
- Tác giả sở hữu bài viết (`$comment->post->user_id`) lập tức có thẩm quyền xóa bình luận và các phản hồi con xuất hiện trên bài viết của chính mình.
- Tận dụng quan hệ Eloquent `$comment->post` đã định nghĩa sẵn trong model [`app/Models/Comment.php`](file:///c:/laragon/www/webblog/app/Models/Comment.php), hoạt động chuẩn xác cho cả bình luận gốc lẫn câu trả lời phân cấp (replies).

---

## 3. Ranh Giới Nghiệp Vụ Nghiêm Ngặt (Business Rule Boundaries)

Để đảm bảo không phát sinh lỗ hổng bảo mật mới, cơ chế phân quyền được rà soát và kiểm chứng theo 4 ranh giới bắt buộc:

```mermaid
flowchart TD
    Req([Yêu cầu xóa Comment]) --> CheckAuthor{User có phải người viết Comment?}
    CheckAuthor -- Có --> Allow[Cho phép Xóa (HTTP 200/Redirect)]
    CheckAuthor -- Không --> CheckAdmin{User có phải Admin?}
    
    CheckAdmin -- Có --> Allow
    CheckAdmin -- Không --> CheckPostOwner{User có phải Tác giả sở hữu Post này?}
    
    CheckPostOwner -- Có --> Allow
    CheckPostOwner -- Không (Tác giả khác / Viewer khác) --> Deny[Từ chối Xóa (HTTP 403 Forbidden)]
```

1. **Tác giả xóa bình luận trên bài viết của mình:** ✅ **HỢP LỆ**  
   Tác giả bảo vệ không gian thảo luận trên chính tác phẩm của mình.
2. **Tác giả cố xóa bình luận trên bài viết của Tác giả khác:** ❌ **BỊ CHẶN (HTTP 403)**  
   Tác giả A không có bất kỳ quyền hạn nào can thiệp vào khu vực bình luận của bài viết thuộc sở hữu của Tác giả B.
3. **Quản trị viên (Admin) điều duyệt toàn hệ thống:** ✅ **HỢP LỆ**  
   Admin có quyền xóa mọi bình luận vi phạm trên phạm vi toàn trang web thông qua giao diện `/admin/comments` hoặc trực tiếp tại trang bài viết.
4. **Độc giả (Viewer):** ✅ **CHỈ XÓA BÌNH LUẬN CỦA MÌNH**  
   Độc giả chỉ có quyền tự thu hồi bình luận do chính mình viết ra, không thể xóa bình luận của người khác.

---

## 4. Kiểm Thử Tự Động & Chứng Minh Tính Toàn Vẹn (Automated Verification)

Nhóm đã bổ sung 2 test case chuyên biệt vào file [`tests/Feature/AuthorPostTest.php`](file:///c:/laragon/www/webblog/tests/Feature/AuthorPostTest.php) để chứng minh tính đúng đắn:

### Test Case 1: Tác giả xóa thành công bình luận spam trên bài viết của mình
```php
public function test_author_can_delete_spam_comment_on_own_post(): void
{
    $author = User::factory()->author()->create();
    $viewer = User::factory()->viewer()->create();
    $post = Post::factory()->published()->create([
        'user_id' => $author->id,
    ]);

    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $viewer->id,
        'body' => 'Spam comment on author post.',
    ]);

    $response = $this->actingAs($author)->delete(route('comments.destroy', $comment));

    $response->assertRedirect();
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
}
```

### Test Case 2: Tác giả bị từ chối (403 Forbidden) khi cố xóa bình luận trên bài viết của tác giả khác
```php
public function test_author_cannot_delete_comment_on_other_authors_post(): void
{
    $authorA = User::factory()->author()->create();
    $authorB = User::factory()->author()->create();
    $viewer = User::factory()->viewer()->create();

    $postA = Post::factory()->published()->create([
        'user_id' => $authorA->id,
    ]);

    $comment = Comment::factory()->create([
        'post_id' => $postA->id,
        'user_id' => $viewer->id,
        'body' => 'Comment on Author A post.',
    ]);

    // Author B attempts to delete comment on Author A's post
    $response = $this->actingAs($authorB)->delete(route('comments.destroy', $comment));

    $response->assertForbidden();
    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
}
```

### Kết quả chạy kiểm thử toàn hệ thống:
```bash
php artisan test
# Kết quả: 167/167 tests passed (607 assertions)
# Duration: ~8.2s

vendor/bin/pint --test
# Kết quả: PASS (0 code style issues found)
```

---

## 5. Kết Luận

Lỗ hổng phân quyền kiểm duyệt bình luận đã được xử lý triệt để:
- **Khớp 100% với yêu cầu Use Case ban đầu.**
- **Bảo toàn hoàn hảo Data Contract & Route Contract hiện hành.**
- **Không phá vỡ chức năng kiểm duyệt Admin hiện hữu.**
- **Mã nguồn ngắn gọn, tường minh, chuẩn PSR-12 và đã được tự động kiểm thử an toàn.**
