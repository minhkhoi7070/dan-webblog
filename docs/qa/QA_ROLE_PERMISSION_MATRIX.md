# CloudBlog — ma trận vai trò và quyền

Nguồn: `routes/web.php`, `routes/auth.php`, policies/controllers và 504 PHP tests đã chạy sau fix. `UI` mô tả affordance trong view/source, không đồng nghĩa đã click trong browser. Ký hiệu: ✓ cho phép theo route/source; — bị chặn; ? chưa xác minh browser. Admin có `PostPolicy::before()` cho phép mọi post ability khi không khóa.

| Hành động / route | Guest | Viewer đã verify | Author đã verify | Admin đã verify | Backend và UI evidence |
|---|---|---|---|---|---|
| Feed, search, category/tag filter, post Published, author public | ✓ | ✓ | ✓ | ✓ | Public `PostController`; live GET 200 cho feed/search; UI browser ? |
| Register, login, forgot/reset password | ✓ | Guest middleware hạn chế | Guest middleware hạn chế | Guest middleware hạn chế | `routes/auth.php`; test `Auth/*` PASS; SMTP thật ? |
| Like/favorite/follow và comment | — | ✓ | ✓ | ✓ | `auth,verified`; `InteractionController`, `CommentController`; `ViewerInteractionTest`/`WorkflowIntegrityTest` PASS; UI ? |
| Favorites, following, activity | — | ✓ | ✓ | ✓ | `auth`; `FavoriteController`, `FollowingController`, `ActivityController`; UI ? |
| Profile overview/update, password, logout | — | ✓ | ✓ | ✓ | `auth`; `ProfileController`, Auth controllers; UI ? |
| Nâng Viewer thành Author | — | ✓ | trở về home | trở về home | `auth,verified`; `becomeAuthor()` kiểm tra role + lock; `ViewerToAuthorTest` PASS |
| Tạo/upload/sửa post của mình | — | — | ✓ | ✓ | `auth,verified,role:author,admin`; `StorePostRequest`, `PostPolicy`; `AuthorPostTest`/`PostPolicyTest` PASS |
| Sửa/xóa post người khác | — | — | — | ✓ | `PostPolicy::before()` cho admin; author bị từ chối; `PostPolicyTest` PASS |
| Submit Draft/Rejected của mình | — | — | ✓ → Pending | ✓ → Published | `PostController::submit`, `AdminDirectPublishTest` PASS |
| Publish trực tiếp qua create/update | — | — | — | ✓ | `StorePostRequest` loại workflow fields với author; `AdminDirectPublishTest` PASS |
| Admin dashboard, metrics, CRUD, moderation | — | — | — | ✓ | `auth,verified,role:admin`; `AdminTest`, `RoleMiddlewareTest` PASS; browser ? |
| Xóa video tạm của người khác | — | — | — | — | `posts.delete-video`; BUG-001 đã FIXED, test audit và ownership PASS |
| Submit post Published của người khác về Pending | — | — | — | — | `posts.submit`; BUG-002 đã FIXED, test audit và transition PASS |

**Lưu ý:** Các personal GET route chỉ dùng `auth`, nên user chưa verify vẫn xem được theo thiết kế hiện tại; các mutation interaction yêu cầu `verified`. User bị khóa trong session cũ được chặn tại một số controller/policy, nhưng toàn bộ GET route chưa được kiểm tra theo ma trận; ghi NOT TESTED cho tình huống đó. Guest web request thường redirect login, còn JSON request có thể trả 401; không áp một status chung cho mọi route.
