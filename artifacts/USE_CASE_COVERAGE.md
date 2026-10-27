# BlogMNM — Use Case Coverage

Ngày kiểm tra: 25/10/2026. Phạm vi: source hiện hành, routes, controllers, policies, requests, Blade và feature tests. Danh mục UC-01–UC-25 lấy từ [use-case.md](use-case.md); các chức năng bổ sung được đặt tên riêng vì [usecase-update-recommendation.md](usecase-update-recommendation.md) dùng lại một số ID với ý nghĩa khác.

## Cách đọc trạng thái

- **Complete**: có đường đi backend, view, kiểm soát quyền/validation liên quan và feature test đã chạy thành công cho chức năng nêu trong dòng. Không đồng nghĩa đã nghiệm thu trình duyệt.
- **Partial**: có chức năng chính nhưng còn phần yêu cầu chưa xác minh hoặc chưa đủ.
- **Missing**: không tìm thấy implementation cần thiết.
- **Broken**: có lỗi đã tái hiện.
- **Unverified**: chưa có đủ bằng chứng chạy thực tế.

Kết quả kiểm tra browser, responsive, theme, race condition và History API được theo dõi trong TEST_REPORT.md/FINAL_ACCEPTANCE_REPORT.md; không suy ra từ feature tests.

## Ma trận đối chiếu

Tên controller viết tắt `Post` là `app/Http/Controllers/PostController.php`; `Admin/*` thuộc thư mục controller Admin. Các route đầy đủ nằm trong [web.php](../routes/web.php) và [auth.php](../routes/auth.php).

| ID | Actor | Use Case | Route | Controller | View | Policy / boundary | Test | Trạng thái | Vấn đề / giới hạn |
|---|---|---|---|---|---|---|---|---|---|
| UC-01 | Guest+ | Home Feed | GET /, /posts | Post@index | posts.index, posts.partials.feed-results | published scope | GuestPostTest, PaginationTest, FeedNavigationTest | Unverified | Full/fragment integration đang được kiểm tra; browser xem TEST_REPORT |
| UC-02 | Guest+ | Search | GET /posts?q= | Post@index | posts.index | query validation + published scope | GuestPostTest, FeedNavigationTest | Unverified | Full page và fragment phải dùng cùng query |
| UC-03 | Guest+ | Category/Tag filter | GET /posts?category=&tag= | Post@index | posts.index | published/category/tag predicates | GuestPostTest, PostTagWorkflowTest, FeedNavigationTest | Unverified | AJAX/Back/Forward cần browser evidence |
| UC-04 | Guest+ | Read post / views | GET /posts/{slug} | Post@show | posts.show | PostPolicy::view; 404 cho bài riêng tư | GuestPostTest, AuthorPostTest | Complete | Chỉ bài Published công khai; owner/admin xem preview |
| UC-05 | Guest+ | Author profile | GET /authors/{user} | Post@byAuthor | authors.show | published posts only | GuestPostTest | Complete | Target profile hiện chấp nhận mọi User; không tự siết role |
| UC-06 | Guest+ | Light/Dark | client theme.js | — | layouts.base, theme controls | localStorage + early initialization | browser checklist | Unverified | Cần kiểm tra flash, contrast và reduced motion trên browser |
| UC-07 | Guest/User | Register/Login/Logout | /register, /login, /logout | Auth/* | auth.* | guest/auth; LoginRequest limiter + locked check | Auth/*, QASuiteTest | Complete | Đăng ký chỉ ghi name/email/password, không nhận role client |
| UC-08 | Viewer+ | Like/Unlike | POST/GET /posts/{id}/like | Interaction@like/likeState | like-button | auth + unlocked + published; unique user/post | ViewerInteractionTest, WorkflowIntegrityTest | Partial | Feature API đạt; rapid-click/browser nghiệm thu riêng |
| UC-09 | Viewer+ | Save/Unsave | POST/GET /posts/{id}/favorite | Interaction@favorite/favoriteState | favorite-button | auth + unlocked + published; unique user/post | ViewerInteractionTest, WorkflowIntegrityTest | Partial | Feature API đạt; UI sau thay feed cần browser |
| UC-10 | Viewer+ | Favorites library | GET /favorites | Favorite@index | favorites.index | auth + published posts | ViewerInteractionTest | Complete | Card bỏ lưu được xử lý theo UX tránh nhảy layout |
| UC-11 | Viewer+ | Follow/Unfollow | POST/GET /authors/{id}/follow | Interaction@follow/followState | follow-button | auth + unlocked; chặn self; unique pair | ViewerInteractionTest, WorkflowIntegrityTest | Partial | Optimistic/browser cần kiểm tra; giữ target-role contract hiện có |
| UC-12 | Viewer+ | Root comment | POST /posts/{id}/comments | Comment@store | posts.show | StoreCommentRequest; published post; UUID deduplication | CommentImmediatePublishTest, WorkflowIntegrityTest | Partial | API giữ approved trực tiếp; pending/error UI cần browser |
| UC-13 | Viewer+ | Reply | POST /posts/{id}/comments + parent_id | Comment@store | posts.show | same post; chuẩn hóa parent về root | ViewerInteractionTest, WorkflowIntegrityTest | Partial | Reply vào reply gắn root theo quyết định đã xác nhận |
| UC-14 | Viewer+ | Profile/password | PATCH/DELETE /profile; PUT /password | Profile; Auth/Password | profile.edit/show | auth; field whitelist; current_password | ProfileTest, Auth/PasswordUpdateTest | Complete | Không có quyền đổi role/is_locked qua profile |
| UC-15 | Author+ | Create draft | POST /posts | Post@store | author.posts.create | role + unlocked; PostPolicy::create; StorePostRequest | AuthorPostTest, PostTagWorkflowTest | Complete | action=draft/submit; không nhận publish từ Author |
| UC-16 | Author+ | Thumbnail | POST/PUT post | Post@store/update | author.posts.create/edit | image/mime/2 MB; owner | ThumbnailAndImageAlignmentTest, AuthorPostTest | Complete | Bảo vệ ảnh dùng chung khi xóa, kể cả admin path |
| UC-17 | Author+ | Edit own post | PUT/PATCH /posts/{id} | Post@update | author.posts.edit | PostPolicy::update + role/unlocked | AuthorPostTest, PostPolicyTest | Complete | Published→Published được tài liệu hiện hành cho phép |
| UC-18 | Author+ | Submit/resubmit | POST /posts/{id}/submit | Post@submit | author.posts.edit/stats | owner; draft/rejected; role/unlocked | AuthorPostTest, PostTagWorkflowTest | Complete | Xóa lý do từ chối khi gửi lại; không tự Published |
| UC-19 | Author+ | Delete own post | DELETE /posts/{id} | Post@destroy | author.posts.edit/stats | PostPolicy::delete + role/unlocked | AuthorPostTest, ThumbnailAndImageAlignmentTest | Complete | Cascade quan hệ theo migration; không xóa ảnh dùng chung |
| UC-20 | Author+ | Author stats | GET /author/posts/stats | Post@stats | author.posts.stats | role; scoped owner (admin toàn hệ thống) | AuthorPostTest | Complete | Thống kê phải giữ đủ tám trường hiện có |
| UC-21a/b | Admin | Approve/Reject | POST /admin/posts/{id}/approve, reject | Admin/Post | admin.posts.index/show | auth + admin + unlocked; pending row lock; required reason | AdminTest, WorkflowIntegrityTest | Complete | Chỉ Pending; invalid state trả 422; approval clear rejection_reason |
| UC-22a/b/c | Admin | Moderate comments | approve/spam/delete /admin/comments/{id} | Admin/Comment | admin.comments.index | auth + admin + unlocked | AdminTest, CommentImmediatePublishTest, WorkflowIntegrityTest | Complete | Search OR đã nhóm để không bỏ qua status filter |
| UC-23 | Admin | Category CRUD | resource /admin/categories | Admin/Category | admin.categories.* | auth + admin + unlocked; unique; block nonempty delete | AdminTest, QASuiteTest | Complete | FK category restrict, không cascade mất bài |
| UC-24 | Admin | Lock/Unlock users | POST /admin/users/{id}/lock, unlock | Admin/User | admin.users.index/show | admin; chặn self/peer-admin lock | AdminTest, QASuiteTest | Complete | Locked current session bị chặn ở CMS/social/upgrade |
| UC-25 | Admin | Platform dashboard | GET /admin | Admin/Dashboard | admin.dashboard | auth + admin + unlocked | AdminTest | Complete | Metrics được giữ nguyên ý nghĩa khi gom query |

## Chức năng bổ sung có bằng chứng

| Tên extension | Route / module | View / policy | Tests | Trạng thái / lưu ý |
|---|---|---|---|---|
| Activity | GET /activity, ActivityController | activity.index, auth | ViewerInteractionTest | Complete ở feature layer |
| Become Author | POST /become-author, Profile@becomeAuthor | CTA/profile, Viewer only + unlocked | ViewerToAuthorTest, WorkflowIntegrityTest | Complete; nâng cấp trực tiếp, không có quy trình request/approval mới |
| Dedicated Admin Login | /admin/login, Admin/Auth | admin.auth.login, LoginRequest + admin check | AdminLoginTest | Complete |
| Author comment deletion | DELETE /comments/{comment} | CommentPolicy::delete; own comment hoặc own post | AuthorPostTest, CommentImmediatePublishTest | Partial: API có; chưa có CMS riêng cho comment đã ẩn/spam |
| Tags | /admin/tags; /tags; /tags/search | admin.tags.*, author tag selector | PostTagWorkflowTest | Complete ở feature layer |
| TipTap / embedded images | /posts/upload-image; editor.js | author form, PostPolicy::create; upload validation | PostEditorTest, ThumbnailAndImageAlignmentTest | Complete ở feature layer; browser editor cần nghiệm thu riêng |
| Following feed | /posts?feed=following | posts.index; auth guidance | FeedNavigationTest | Unverified đến khi integration tests hoàn tất |

## Tài liệu và quyết định

- Nguồn hiện có: use-case.md, use-case-detail.md, final-business-workflow.md, database-dictionary.md, database-schema.md, architecture.md cùng source/tests. Không tìm thấy hai file Word được nêu trong brief trong workspace: Template_BaoCao_DoAn_LTMNM.docx và Phan_cong_5_thanh_vien_BlogMNM_UIUX.docx. Chưa xác nhận rubric gốc của môn học.
- Không dùng các con số test lịch sử trong markdown cũ làm kết quả hiện tại.
- Giữ bình luận approved trực tiếp theo workflow mới hơn và tests hiện hành.
- Giữ quyền Author sửa bài Published, Viewer tự nâng cấp Author, và Author xóa comment trong phạm vi own-post.
- Thay đổi đã được xác nhận cho đợt này: Admin chỉ duyệt/từ chối Pending, lý do từ chối bắt buộc; reply vào reply được gắn root để giữ hai cấp.
- Chưa tự bổ sung workflow duyệt yêu cầu Author, duyệt lại bài Published hoặc quyền Author quản trị bình luận bài người khác.

## Bằng chứng kiểm thử của phần workflow

WorkflowIntegrityTest kiểm tra explicit state idempotent, GET bảo vệ quyền, UUID comment không trùng/không lộ comment người khác, root-normalized reply, invalid cross-post/UUID, pending guards, required reason, clear reason, locked upgrade, grouped comment search và shared-thumbnail deletion. Kết quả cuối cùng được tổng hợp trong TEST_REPORT.md sau khi toàn bộ phase tích hợp xong.
