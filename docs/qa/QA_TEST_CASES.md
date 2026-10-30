# CloudBlog — ma trận ca kiểm thử

Trạng thái áp dụng cho **toàn bộ ca được mô tả**, không suy ra trạng thái toàn module. Sau sửa BUG-004 và parser video, ma trận 71 ca vẫn có **45 PASS, 0 FAIL, 18 BLOCKED, 7 NOT TESTED, 1 N/A**; các ca browser đã kiểm một phần vẫn BLOCKED nếu chưa đủ từng bước. Riêng bài cũ `/posts/php` chưa có media URL trong DB và cần chèn lại video. Test PHP sử dụng SQLite in-memory; Playwright dùng MySQL `webblog_qa` riêng. Toàn bộ 89 route và middleware ở `QA_ROUTE_INVENTORY.md`; inventory 542 PHP/Node tests là **trước** khi bổ sung 37 Playwright tests (37 PASS).

| ID | Module | Role | Test Case | Expected | Actual | Status | Evidence |
|---|---|---|---|---|---|---|---|
| ENV-001 | Environment | QA | Xác nhận framework và test DB | Version và isolation rõ ràng | Laravel 13.33; SQLite `:memory:` | PASS | `php artisan --version`, `phpunit.xml` |
| ENV-002 | Environment | QA | Inventory mọi route | Bao gồm route trực tiếp | 89 route | PASS | `php artisan route:list --json` |
| ENV-003 | Environment | QA | Đọc MySQL schema | Bảng/index/FK hiển thị | 18 bảng, FK/unique ở 4 bảng trọng yếu | PASS | `db:show`, `db:table` |
| ENV-004 | Environment | QA | Đối soát orphan/duplicate dữ liệu thật | Không có orphan/duplicate | Chưa truy vấn dữ liệu thật | NOT TESTED | Chỉ đọc metadata |
| ENV-005 | Environment | QA | Coverage | Đo phần trăm | Không có PCOV/Xdebug | BLOCKED | `php -m` |
| AUTH-001 | Register | Guest | Form đăng ký GET | 200 | 200 | PASS | Live `Invoke-WebRequest /register` |
| AUTH-002 | Register | Guest | Đăng ký hợp lệ, viewer mặc định | User tạo và cần verify | Test pass | PASS | `Auth/RegistrationTest.php` |
| AUTH-003 | Register | Guest | Email sai/trùng, password confirmation | Validation lỗi | Test pass | PASS | `Auth/RegistrationTest.php` |
| AUTH-004 | Register | Guest | Gửi `role=admin` | Không nâng quyền | Test pass | PASS | `QASuiteTest.php`, `Auth/RegistrationTest.php` |
| AUTH-005 | Register | Guest | Email case/whitespace, double submit, CSRF qua browser | Không tạo trùng/bypass | Chưa chạy browser | BLOCKED | Browser host unavailable |
| OTP-001 | Verify email | Viewer | OTP đúng, update verified | Xác minh email | Test pass | PASS | `Auth/EmailVerificationTest.php::test_email_can_be_verified_using_6_digit_otp` |
| OTP-002 | Verify email | Viewer | OTP sai, 5 lần sai, resend/cooldown | Từ chối/invalidate | Test pass | PASS | `Auth/EmailVerificationTest.php` các test OTP |
| OTP-003 | Verify email | Viewer | OTP của user khác, hết hạn, leading zero, replay | Từ chối hoặc hợp lệ đúng mục đích | Chưa có kiểm thử đầy đủ từng biên | NOT TESTED | Source `OtpService.php`; chưa assertion toàn bộ |
| OTP-004 | Verify email | Viewer | SMTP tới mailbox test | Nhận email thật | Không có mailbox được phép | BLOCKED | Không gửi SMTP |
| AUTH-006 | Login/logout | Viewer | Đăng nhập đúng/sai, logout, lock | Session đúng | Test pass | PASS | `Auth/AuthenticationTest.php`, `AdminLoginTest.php` |
| AUTH-007 | Login/logout | Viewer | Back button, session expiry, 419 trong browser | Không truy cập trái phép | Chưa chạy browser | BLOCKED | Browser host unavailable |
| RESET-001 | Reset OTP | Guest | Request OTP user tồn tại/không tồn tại | Tránh enumeration | Test pass | PASS | `Auth/PasswordResetTest.php` |
| RESET-002 | Reset OTP | Guest | Sai mã/5 lần sai/bỏ qua token | Từ chối | Test pass | PASS | `Auth/PasswordResetTest.php` |
| RESET-003 | Reset OTP | Guest | OTP đúng → reset → mật khẩu mới | Hash mới, token hết hiệu lực | Test pass | PASS | `Auth/PasswordResetTest.php` |
| RESET-004 | Reset OTP | Guest | Replay, hết hạn, user khác, browser flow đầy đủ | Từ chối | Chưa kiểm thử E2E đầy đủ | BLOCKED | Browser host unavailable |
| SEC-001 | Role | Viewer | Gọi author/admin route trực tiếp | 403/redirect theo route | Test pass | PASS | `RoleMiddlewareTest.php`, `AdminDirectPublishTest.php` |
| SEC-002 | Role | Author | Gọi admin route trực tiếp | Từ chối | Test pass | PASS | `RoleMiddlewareTest.php`, `PostPolicyTest.php` |
| SEC-003 | IDOR | Author | Sửa post người khác | Từ chối | Test pass | PASS | `PostPolicyTest.php`, `AuthorPostTest.php` |
| SEC-004 | Media IDOR | Author | Xóa video tác giả khác bằng filename | 403, file còn | 403, file còn | PASS | `AuditFindingsTest.php`; `VideoDeletionAuthorizationTest.php` |
| SEC-005 | Security | Guest | CSRF mutation không token trên live server | 419 | Không gửi live mutation | NOT TESTED | Test environment vô hiệu CSRF theo framework |
| SEC-006 | Security | QA | Kiểm tra APP_DEBUG hiện tại | Biết trạng thái | `true` trên local | PASS | `php artisan config:show app.debug` |
| SEC-007 | Security | QA | Kiểm tra debug production | `false` | Không có production | BLOCKED | Không giả định môi trường triển khai |
| POST-001 | Create | Author | Draft hợp lệ | Draft, owner và pivot đúng | Test pass | PASS | `AuthorPostTest.php`, `AdminDirectPublishTest.php` |
| POST-002 | Create | Author | Submit → Pending | Pending | Test pass | PASS | `AdminDirectPublishTest.php`, `PostEditorTest.php` |
| POST-003 | Create | Admin | Publish trực tiếp | Published, public feed, không pending | Test pass | PASS | `AdminDirectPublishTest.php` |
| POST-004 | Create | Author | Giả `publish/status/role` | Không bypass moderation | Test pass | PASS | `AdminDirectPublishTest.php::test_author_cannot_bypass_approval_via_publish_tampering` |
| POST-005 | Validation | Author | Title/category/tag/slug invalid | 422/validation | Test pass cho nhiều nhánh | PASS | `AuthorPostTest.php`, `CustomSlugTest.php`, `PostTagWorkflowTest.php` |
| POST-006 | Validation | Author | Body chỉ `<p><br></p>` | Không tạo post rỗng | 422, không tạo post | PASS | `AuditFindingsTest.php`; `PostContentValidationTest.php` |
| POST-007 | Moderation | Admin | Approve/reject pending | Published/Rejected | Test pass | PASS | `WorkflowIntegrityTest.php`, `AdminTest.php` |
| POST-008 | Moderation | Admin | Approve/reject non-pending | 422 | Test pass | PASS | `WorkflowIntegrityTest.php` data provider |
| POST-009 | Moderation | Admin | Submit Published author post | Không đổi trạng thái | 422, Published giữ nguyên | PASS | `AuditFindingsTest.php`; `PostSubmitTransitionTest.php` |
| POST-010 | Moderation | Admin | Double submit đồng thời trên MySQL | Chỉ một transition | Chưa chạy concurrency MySQL | NOT TESTED | SQLite test không chứng minh race thực |
| EDIT-001 | TipTap | Author | HTML an toàn/XSS sanitize | Không thực thi script | Test pass | PASS | `PostEditorTest.php::test_post_content_sanitizes_malicious_xss_and_protocols` |
| EDIT-002 | TipTap | Author | Heading/lists/links/images/video save/reopen | Nội dung giữ đúng | Test backend/source pass; thao tác editor chưa kiểm | PASS | `PostEditorTest.php` các test HTML/video |
| EDIT-003 | TipTap | Author | Toolbar bằng chuột, shortcut, draft reload | Đúng hành vi browser | Không có browser | BLOCKED | Browser host unavailable |
| MEDIA-001 | Upload | Author | Image/video hợp lệ và MIME sai/quá lớn | 201 hoặc validation | Test pass | PASS | `PostEditorTest.php`, `ThumbnailAndImageAlignmentTest.php` |
| MEDIA-002 | Upload | Author | PHP upload limit so với Laravel max | PHP limit ≥ Laravel limit | PHP upload/post max đều 2 GB; video limit ứng dụng 100 MB. Chưa thử upload sát ngưỡng | PASS | `php -r` đọc `ini_get`, `config:show blog.max_video_size_kb`; chỉ PASS cho đối chiếu cấu hình |
| MEDIA-003 | Upload | Author | File URL 200 sau reload | File tồn tại | Chưa chạy browser/HTTP authenticated upload | BLOCKED | Browser host unavailable |
| FEED-001 | Public | Guest | Home/posts/search GET | 200 | 200, Chrome cục bộ hiển thị Home Feed | PASS | Live HTTP 3 GET; Computer Use screenshot/accessibility Home Feed |
| FEED-002 | Public | Guest | Chỉ Published và filter/pagination | Đúng query | Test pass; Chrome click `Công nghệ` chuyển URL và hiển thị bài category | PASS | `GuestPostTest.php`, `PaginationTest.php`; Computer Use Chrome category smoke; pagination browser chưa kiểm |
| FEED-003 | Public | Guest | AJAX search fragment | 200 + fragment header | 200 + `X-BlogMNM-Feed: 1` | PASS | Live HTTP AJAX GET |
| FEED-004 | AJAX | All | Không reload, URL/Back/Forward, skeleton, race | Đúng trên browser | Chỉ Node unit pass, browser chưa chạy | BLOCKED | Browser host unavailable |
| FEED-005 | AJAX | All | Category có bài↔rỗng, layout shift | Không giật khung | Chưa đo frame/layout | BLOCKED | Browser host unavailable |
| INTER-001 | Like/Favorite/Follow | Viewer | Mutation/retry/idempotent | State/pivot đúng | Test pass | PASS | `WorkflowIntegrityTest.php`, `ViewerInteractionTest.php` |
| INTER-002 | Comment | Viewer | Reply, cross-post, duplicate request ID | Đúng root/validation | Test pass | PASS | `WorkflowIntegrityTest.php`, `CommentImmediatePublishTest.php` |
| INTER-003 | Quick Follow | Viewer | Popover confirm, Escape, outside click | Đúng UI | Chỉ static/feature test, browser chưa chạy | BLOCKED | Browser host unavailable |
| INTER-004 | Optimistic UI | Viewer | Rollback khi request lỗi | Đúng state | Node test pass | PASS | `InteractionState.test.mjs` |
| INTER-005 | Share | All | Copy/share link trong browser | Link đúng | Chưa kiểm | BLOCKED | Browser host unavailable |
| PROFILE-001 | Profile | Viewer | Update name/bio/avatar, validation | Dữ liệu đúng | Test pass | PASS | `ProfileTest.php`, `AvatarUploadTest.php` |
| PROFILE-002 | Modal | Viewer | Mở/đóng, focus, Escape, không layout shift | Đúng UI | Chưa kiểm browser | BLOCKED | Browser host unavailable |
| PROFILE-003 | Settings | Viewer | Password/current password, logout | Đúng | Test pass | PASS | `Auth/PasswordUpdateTest.php`, `SecurityAccountSettingsTest.php` |
| PROFILE-004 | Reading prefs | Viewer | Font/width persistence | Đúng state JS | Node test pass | PASS | `ReadingPreferences.test.mjs` |
| PROFILE-005 | Reading prefs | Viewer | Mobile Aa, chỉ đổi post detail | Đúng UI | Chưa kiểm browser | BLOCKED | Browser host unavailable |
| PERSONAL-001 | Favorites/Following/Activity | Viewer | Query sở hữu, filter, pagination | Đúng backend | Test pass một phần | PASS | `FavoritesPageRedesignTest.php`, `ViewerInteractionTest.php` |
| PERSONAL-002 | Favorites | Viewer | Unsave, counter và empty state live | Đúng UI | Chưa kiểm browser | BLOCKED | Browser host unavailable |
| ADMIN-001 | Admin CMS | Admin | Dashboard/metrics/CRUD routes | Đúng backend | Test pass | PASS | `AdminTest.php`, `AdminRealtimeMetricsTest.php` |
| ADMIN-002 | Admin CMS | Admin | Category/tag/user/comment CRUD boundary | Validation và FK đúng | Chưa chạy toàn bộ biên | NOT TESTED | Test hiện có không thay cho matrix đầy đủ |
| ADMIN-003 | Admin CMS | Admin | Browser AJAX filter/pagination | Không reload/mất filter | Chưa kiểm browser | BLOCKED | Browser host unavailable |
| DB-001 | Integrity | QA | Post slug unique, category FK restrict | Có constraint | Có trong MySQL metadata | PASS | `db:table posts` |
| DB-002 | Integrity | QA | OTP hash/index, comment request ID unique | Có constraint | Có trong MySQL metadata | PASS | `db:table email_otps/comments` |
| DB-003 | Integrity | QA | Orphans/duplicates/cascade thực trên MySQL | Không lỗi | Chưa truy vấn dữ liệu người dùng | NOT TESTED | Không chạy destructive DB test |
| PERF-001 | Performance | QA | N+1/query count feed/admin | Trong ngưỡng | Chưa đo query count thực | NOT TESTED | Source có eager load, không đủ để PASS |
| UI-001 | Responsive | All | 390×844, 794×1024, 992×900, 1440×900 | Không overflow/overlap | Đo width và local diagnostics 16 role×viewport đều đạt; overlap/modal chưa đo đầy đủ | BLOCKED | `responsive.spec.mjs` |
| UI-002 | Accessibility | All | Keyboard, focus, labels, modal trap | Dùng được | Chưa kiểm browser | BLOCKED | Browser host unavailable |
| UI-003 | Browser diagnostics | All | Console/network/broken images | Không lỗi | 35 Playwright tests PASS về lỗi cục bộ; avatar mặc định 200/render đúng | PASS | `profile.spec.mjs`, `responsive.spec.mjs`; lỗi Google Fonts ngoài origin còn ghi nhận |
| NAF-001 | Feature inventory | Admin | Force delete/restore post | Có route nếu hỗ trợ | Không có route/SoftDeletes | N/A | `routes/web.php`, `Post.php` |

## Phạm vi chưa được bao phủ bởi một ca đơn lẻ

Những test PHP hiện có có thêm assertions về authorization, validation, media, AJAX fragment, UI markup và admin. Tên test không chứng minh hành vi browser, SMTP hoặc MySQL production. Các ca chưa chạy chi tiết trong brief (đặc biệt input fuzzing, concurrent requests, tất cả viewport × role × page và thao tác E2E nhiều bước) vẫn là **NOT TESTED** hoặc **BLOCKED** tương ứng; không gộp chúng thành PASS.
