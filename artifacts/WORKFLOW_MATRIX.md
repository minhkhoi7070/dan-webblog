# BlogMNM — Workflow Matrix

Ngày cập nhật: 25/10/2026. Nguồn: [final-business-workflow.md](final-business-workflow.md), [use-case-detail.md](use-case-detail.md), [PostPolicy](../app/Policies/PostPolicy.php), [CommentPolicy](../app/Policies/CommentPolicy.php), controllers/requests và feature tests. Không dùng số lượng test cũ trong tài liệu lịch sử làm bằng chứng nghiệm thu hiện tại.

## Quy tắc chuyển trạng thái

| Entity | Initial State | Allowed Action | Actor | Next State | Validation / authorization / integrity | Test |
|---|---|---|---|---|---|---|
| Post | chưa tồn tại | Lưu nháp | Author/Admin unlocked | draft | StorePostRequest; create policy; title/category/body; tags/image validation | AuthorPostTest, PostTagWorkflowTest |
| Post | chưa tồn tại | Tạo và gửi duyệt | Author/Admin unlocked | pending | action=submit; cùng validation; không chấp nhận client Published | AuthorPostTest, ViewerToAuthorTest |
| Post | draft | Lưu sửa đổi | Owner/Admin unlocked | draft | update policy; validated editorial fields | AuthorPostTest |
| Post | draft | Submit | Owner/Admin unlocked | pending | submit policy; role; required fields trên form | AuthorPostTest, PostTagWorkflowTest |
| Post | rejected | Sửa/lưu | Owner/Admin unlocked | rejected | Nội dung được chỉnh; trạng thái không tự đổi khi chỉ lưu | PostTagWorkflowTest |
| Post | rejected | Resubmit | Owner/Admin unlocked | pending | submit policy; clear rejection_reason | AuthorPostTest, PostTagWorkflowTest |
| Post | pending | Sửa editorial fields | Owner/Admin unlocked | pending | Quyền update hiện hành, không tự publish | AuthorPostTest |
| Post | pending | Approve | Admin unlocked | published | Lock row trong transaction; kiểm tra Pending; reviewed_by/at; published_at; clear rejection_reason | AdminTest, WorkflowIntegrityTest |
| Post | pending | Reject | Admin unlocked | rejected | Lock row; Pending; lý do string bắt buộc, tối đa 2000 | AdminTest, WorkflowIntegrityTest |
| Post | draft/rejected/published | Approve hoặc Reject | Admin | giữ nguyên | 422 vì không phải Pending | WorkflowIntegrityTest |
| Post | published | Sửa nội dung | Owner/Admin unlocked | published | Quy tắc tài liệu hiện hành; không tự thêm reapproval | AuthorPostTest, final-business-workflow.md |
| Post | draft/rejected/pending/published | Delete theo policy hiện hành | Owner/Admin unlocked | deleted | Ownership/admin; FK cascade; model deleting guard bảo vệ ảnh dùng chung | AuthorPostTest, ThumbnailAndImageAlignmentTest, WorkflowIntegrityTest |
| Post | unpublished | Public read | Guest/nonowner | giữ nguyên | 404; không trả dữ liệu riêng tư | GuestPostTest, AuthorPostTest |
| Comment | chưa tồn tại | Root comment | Authenticated unlocked | approved | Published post; body 2–1000; server fixed user/status | CommentImmediatePublishTest |
| Comment | chưa tồn tại | Reply | Authenticated unlocked | approved, root parent | Parent tồn tại/cùng post; reply-to-reply chuẩn hóa root; không tạo cấp 3 mới | WorkflowIntegrityTest |
| Comment | đã tạo | Retry cùng client_request_id | Cùng user/post/body/root-parent | giữ nguyên ID | Nullable unique UUID; firstOrCreate; match payload trước trả JSON | WorkflowIntegrityTest |
| Comment | đã tạo | Dùng lại key với user/post/body/parent khác | Bất kỳ | giữ nguyên | 422, không trả comment của user khác, không tạo bản ghi mới | WorkflowIntegrityTest |
| Comment | pending/spam | Approve | Admin unlocked | approved | Admin route middleware | AdminTest, QASuiteTest |
| Comment | approved/pending | Mark spam | Admin unlocked | spam | Admin route middleware; không hiện công khai | AdminTest, CommentImmediatePublishTest |
| Comment | bất kỳ | Delete | Comment owner / post owner / Admin unlocked | deleted | CommentPolicy::delete; cascade replies | AuthorPostTest, CommentImmediatePublishTest |
| Comment | bất kỳ | Delete người khác trên bài không sở hữu | Viewer/other Author | giữ nguyên | 403 | AuthorPostTest |
| User | guest | Register | Guest | viewer unlocked | name/email/password whitelist; ignore submitted role | Auth/RegistrationTest, QASuiteTest |
| User | viewer unlocked | Become Author | Chính user | author | server fixed author role; không nhận role client | ViewerToAuthorTest |
| User | viewer locked | Become Author | Chính user | viewer locked | 403 dù session đã tồn tại | WorkflowIntegrityTest |
| User | author/admin | Become Author endpoint | Chính user | giữ nguyên | Chỉ Viewer được chuyển role | ViewerToAuthorTest |
| User | unlocked | Lock | Admin khác | locked | Không khóa self hoặc admin peer | AdminTest, QASuiteTest |
| User | locked | Unlock | Admin | unlocked | admin middleware | AdminTest |
| Session | chưa đăng nhập | Login | Unlocked user | authenticated | credentials + 5 failed attempts/minute; regenerate session | Auth/AuthenticationTest, QASuiteTest |
| Session | chưa đăng nhập | Login locked user | Locked user | unauthenticated | logout/invalidate + validation error | QASuiteTest, AdminLoginTest |
| Session | authenticated | Logout | User | unauthenticated | POST CSRF; invalidate/regenerate token | Auth/AuthenticationTest |
| Like | absent | Set liked=true | Authenticated unlocked | present | Published; firstOrCreate; unique user/post | WorkflowIntegrityTest |
| Like | present | Set liked=true | Same user | present | Idempotent; count không tăng lần nữa | WorkflowIntegrityTest |
| Like | present | Set liked=false | Same user | absent | detach đúng user/post; count không âm ở UI | ViewerInteractionTest, WorkflowIntegrityTest |
| Favorite | absent/present | Set saved=true/false | Authenticated unlocked | present/absent | Published; firstOrCreate/detach; unique user/post | ViewerInteractionTest, WorkflowIntegrityTest |
| Follow | absent/present | Set following=true/false | Authenticated unlocked | present/absent | Không tự follow; firstOrCreate/detach; unique follower/following | ViewerInteractionTest, WorkflowIntegrityTest |
| Like/Favorite/Follow | bất kỳ | Legacy POST không explicit state | Authenticated unlocked | toggle | Giữ contract JSON chính xác của caller cũ; client mới gửi desired state | ViewerInteractionTest, QASuiteTest |
| Like/Favorite/Follow | bất kỳ | GET state reconcile | Authenticated unlocked | giữ nguyên | Read only; published check cho post; count server cho likes/followers | WorkflowIntegrityTest |
| Category | có bài | Delete | Admin | giữ nguyên | Controller block + FK restrict; không mất bài | AdminTest, QASuiteTest |
| Category | không bài | Delete | Admin | deleted | Admin boundary | AdminTest |
| Tag | có/không liên kết | Delete | Admin | deleted, pivot detached | Không xóa Posts | PostTagWorkflowTest |

## Invariant cần bảo toàn

1. Author không tự Publish qua payload; chỉ Admin approve bài Pending mới mở công khai.
2. Viewer tự nâng cấp Author là nghiệp vụ hiện có. Không tạo quy trình xét duyệt role mới.
3. Chỉnh sửa Published giữ Published là nghiệp vụ được tài liệu hiện hành cho phép.
4. Comments mới approved trực tiếp. Admin moderation là hậu kiểm; không đưa bình luận hợp lệ trở lại Pending.
5. Author chỉ quản lý comment thuộc phạm vi own-post hoặc comment mình viết; global moderation vẫn Admin.
6. Like/Favorite/Follow dùng unique constraints của database. AJAX optimism không thay auth/validation phía server.
7. client_request_id chỉ chống duplicate submission; không cấp quyền và không được dùng để đọc comment người khác.
8. Existing third-level comments trong database không được tự xóa/move bởi migration. Chuẩn hóa chỉ áp dụng khi ghi reply mới; dữ liệu cũ cần kiểm tra riêng nếu có.

## Các điểm chưa tự mở rộng

- Author moderation CMS cho comment đã ẩn/spam chưa có trong source; policy delete có nhưng public detail chỉ hiển thị approved. Đây là phần coverage cần hoàn thiện hoặc xác nhận phạm vi UI.
- Target follow hiện là User bất kỳ trừ self; tài liệu dùng chữ Author. Giữ contract đang hoạt động cho đến khi có quyết định siết role.
- Locked profile/password policy chưa có quy tắc tập trung rõ ràng; chỉ sửa bypass đã xác minh ở become-author, không tự thay các quyền khác.
- Không thay schema mới ngoài việc hoàn thiện migration UUID nullable unique đã có từ đợt optimistic interactions. Không chạy migrate:fresh trên database hiện có.

Kết quả test thực thi cuối cùng và các ca browser chưa chạy nằm trong TEST_REPORT.md; bảng này mô tả quy tắc, không phải tuyên bố mọi kịch bản UI đã được nghiệm thu.
