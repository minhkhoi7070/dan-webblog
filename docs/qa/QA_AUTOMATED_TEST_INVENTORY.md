# CloudBlog — inventory test tự động

## Bổ sung Playwright E2E 30/10/2026

`npm run test:e2e -- --reporter=dot` chạy **37 ca: 37 PASS, 0 FAIL** sau khi sửa BUG-004 và parser video. PHP/Node bên dưới vẫn **542/542 PASS**; tổng ba runner là **579 ca, 579 PASS, 0 FAIL**.

| Playwright spec | Ca | PASS | FAIL |
|---|---:|---:|---:|
| `admin-cms.spec.mjs` | 1 | 1 | 0 |
| `auth.spec.mjs` | 2 | 2 | 0 |
| `chromium-smoke.spec.mjs` | 1 | 1 | 0 |
| `guest.spec.mjs` | 2 | 2 | 0 |
| `interactions.spec.mjs` | 3 | 3 | 0 |
| `media.spec.mjs` | 1 | 1 | 0 |
| `navigation.spec.mjs` | 1 | 1 | 0 |
| `profile.spec.mjs` | 2 | 2 | 0 |
| `registration.spec.mjs` | 1 | 1 | 0 |
| `responsive.spec.mjs` | 16 | 16 | 0 |
| `workflow.spec.mjs` | 5 | 5 | 0 |
| `video-publication.spec.mjs` | 2 | 2 | 0 |

Để chạy lại trên máy hiện tại: mở QA server bằng `powershell -File tests/E2E/start-server.ps1` ở terminal riêng, kiểm tra `http://127.0.0.1:8001/__e2e/health`, rồi chạy `npm run test:e2e`. Mỗi test xác nhận health/env/schema/storage trước khi mutation. File `.env.e2e`, QA storage, traces và HTML report đều bị Git ignore; không xuất credentials hoặc OTP. Seeder `E2ESeeder` chỉ chạy khi env/schema/storage đều là QA. SMTP thật vẫn BLOCKED.

Nguồn: `vendor/bin/phpunit --list-tests` và `node --test tests/Unit/*.test.mjs`, 30/10/2026. Kết quả full suite sau fix: 504 PHP PASS, 38 Node PASS. Ba audit tests từng FAIL trước fix; lịch sử nằm ở `QA_BUG_REPORT.md`.

| Test | Status sau fix |
|---|---|
| `Tests\Unit\ExampleTest::test_that_true_is_true` | PASS |
| `Tests\Unit\ModelRelationshipTest::test_user_has_many_posts_and_comments` | PASS |
| `Tests\Unit\ModelRelationshipTest::test_category_and_tag_relationships` | PASS |
| `Tests\Unit\ModelRelationshipTest::test_comment_replies_relationship` | PASS |
| `Tests\Unit\ModelRelationshipTest::test_likes_and_favorites_relationships` | PASS |
| `Tests\Unit\ModelRelationshipTest::test_follow_relationships` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_creates_and_publishes_post_directly` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_creates_with_submit_action_also_publishes_directly` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_can_save_draft_post` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_publishes_own_draft_post_directly_via_update` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_publishes_own_draft_post_directly_via_submit_route` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_editing_already_published_post_retains_published_status` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_author_can_create_draft_post` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_author_submitting_post_goes_to_pending` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_author_cannot_bypass_approval_via_publish_tampering` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_viewer_is_forbidden_from_creating_or_publishing_posts` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_published_post_does_not_appear_in_pending_queue` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_admin_published_post_appears_in_public_feed` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_validation_failure_prevents_publication` | PASS |
| `Tests\Feature\AdminDirectPublishTest::test_editor_ui_button_renders_correctly_by_role` | PASS |
| `Tests\Feature\AdminLoginTest::test_01_admin_login_screen_can_be_rendered` | PASS |
| `Tests\Feature\AdminLoginTest::test_02_admin_login_successful_redirects_to_admin_dashboard` | PASS |
| `Tests\Feature\AdminLoginTest::test_03_viewer_login_via_admin_login_is_rejected` | PASS |
| `Tests\Feature\AdminLoginTest::test_04_author_login_via_admin_login_is_rejected` | PASS |
| `Tests\Feature\AdminLoginTest::test_05_admin_can_access_admin_dashboard` | PASS |
| `Tests\Feature\AdminLoginTest::test_06_viewer_accessing_admin_dashboard_is_forbidden` | PASS |
| `Tests\Feature\AdminLoginTest::test_07_author_accessing_admin_dashboard_is_forbidden` | PASS |
| `Tests\Feature\AdminLoginTest::test_08_admin_login_fails_with_wrong_password` | PASS |
| `Tests\Feature\AdminLoginTest::test_09_locked_admin_cannot_login` | PASS |
| `Tests\Feature\AdminLoginTest::test_10_csrf_protection_is_active_on_admin_login_routes` | PASS |
| `Tests\Feature\AdminRealtimeMetricsTest::test_guest_cannot_access_realtime_metrics` | PASS |
| `Tests\Feature\AdminRealtimeMetricsTest::test_viewer_or_author_cannot_access_realtime_metrics` | PASS |
| `Tests\Feature\AdminRealtimeMetricsTest::test_admin_can_retrieve_realtime_metrics` | PASS |
| `Tests\Feature\AdminRealtimeMetricsTest::test_clean_sample_data_command_removes_sample_records` | PASS |
| `Tests\Feature\AdminTest::test_guest_is_redirected_to_login_when_accessing_admin_routes` | PASS |
| `Tests\Feature\AdminTest::test_viewer_and_author_receive_403_on_admin_routes` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_access_admin_dashboard_and_routes` | PASS |
| `Tests\Feature\AdminTest::test_dashboard_displays_all_required_metrics` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_view_and_search_users` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_lock_and_unlock_user` | PASS |
| `Tests\Feature\AdminTest::test_admin_cannot_lock_own_account` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_crud_categories` | PASS |
| `Tests\Feature\AdminTest::test_category_creation_validation_rules_apply` | PASS |
| `Tests\Feature\AdminTest::test_category_update_validation_rules_apply` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_approve_pending_post` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_reject_pending_post` | PASS |
| `Tests\Feature\AdminTest::test_author_can_resubmit_rejected_post` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_delete_post` | PASS |
| `Tests\Feature\AdminTest::test_admin_can_moderate_comments` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_home_feed_ajax_pagination_preserves_query_parameters` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_following_feed_ajax_pagination_for_posts_and_authors` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_favorites_feed_ajax_pagination` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_author_profile_ajax_pagination` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_author_posts_stats_and_comments_ajax_pagination` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_admin_cms_ajax_pagination_for_posts_users_comments_categories_tags` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_pagination_boundary_and_empty_state` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_unauthorized_access_to_protected_paginated_routes` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_admin_posts_ajax_status_tabs_filtering` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_admin_posts_ajax_search_and_category_filtering` | PASS |
| `Tests\Feature\AjaxPaginationTest::test_admin_posts_combined_status_search_category_pagination` | PASS |
| `Tests\Feature\AuditFindingsTest::test_author_cannot_delete_a_video_used_by_another_author` | PASS |
| `Tests\Feature\AuditFindingsTest::test_admin_cannot_submit_an_already_published_post_back_to_pending` | PASS |
| `Tests\Feature\AuditFindingsTest::test_post_with_only_empty_html_content_is_rejected` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_login_screen_can_be_rendered` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_users_can_authenticate_using_the_login_screen` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_author_login_redirects_to_home` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_admin_login_redirects_to_admin_dashboard` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_users_can_not_authenticate_with_invalid_password` | PASS |
| `Tests\Feature\Auth\AuthenticationTest::test_users_can_logout` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_verification_screen_can_be_rendered` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_verified_user_visiting_verification_screen_is_redirected_to_dashboard` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_can_be_verified` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_is_not_verified_with_invalid_hash` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_verification_link_cannot_be_used_by_different_user` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_expired_verification_link_is_rejected` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_verification_notification_can_be_resent` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_user_cannot_like_post` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_user_cannot_favorite_post` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_user_cannot_follow_author` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_user_cannot_create_comment` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_author_cannot_access_post_creation` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_admin_cannot_access_admin_dashboard` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unverified_user_can_access_profile_to_update_email` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_updating_email_in_profile_resets_verification_and_resends_notification` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_vietnamese_verification_email_content` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_registration_handles_mail_delivery_failure_gracefully` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_resending_notification_handles_mail_delivery_failure_gracefully` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_unauthenticated_user_opening_verification_link_is_redirected_to_login_and_then_verified_after_login` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_can_be_verified_using_6_digit_otp` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_verification_fails_with_invalid_otp` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_email_verification_fails_after_5_wrong_attempts` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_resending_otp_invalidates_previous_otp` | PASS |
| `Tests\Feature\Auth\EmailVerificationTest::test_resending_otp_respects_cooldown` | PASS |
| `Tests\Feature\Auth\PasswordConfirmationTest::test_confirm_password_screen_can_be_rendered` | PASS |
| `Tests\Feature\Auth\PasswordConfirmationTest::test_password_can_be_confirmed` | PASS |
| `Tests\Feature\Auth\PasswordConfirmationTest::test_password_is_not_confirmed_with_invalid_password` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_forgot_password_screen_can_be_rendered` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_otp_can_be_requested_for_existing_user` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_request_for_nonexistent_email_does_not_reveal_enumeration` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_otp_screen_can_be_rendered_when_email_in_session` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_otp_screen_redirects_to_request_when_no_email_in_session` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_otp_can_be_verified_granting_reset_token` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_otp_fails_with_invalid_code` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_otp_fails_after_5_attempts` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_cannot_be_reset_without_valid_session_token` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_can_be_reset_with_valid_verified_token` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_does_not_verify_unverified_user_email` | PASS |
| `Tests\Feature\Auth\PasswordResetTest::test_password_reset_otp_cannot_be_used_for_email_verification` | PASS |
| `Tests\Feature\Auth\PasswordUpdateTest::test_password_can_be_updated` | PASS |
| `Tests\Feature\Auth\PasswordUpdateTest::test_correct_password_must_be_provided_to_update_password` | PASS |
| `Tests\Feature\Auth\RegistrationTest::test_registration_screen_can_be_rendered` | PASS |
| `Tests\Feature\Auth\RegistrationTest::test_new_users_can_register` | PASS |
| `Tests\Feature\Auth\RegistrationTest::test_registration_validation_vietnamese_messages` | PASS |
| `Tests\Feature\Auth\RegistrationTest::test_registration_duplicate_email_vietnamese_message` | PASS |
| `Tests\Feature\AuthorCommentManagementTest::test_example` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_view_create_page` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_create_draft_post_with_tags` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_edit_and_update_own_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_delete_own_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_cannot_edit_other_authors_posts` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_cannot_publish_directly_or_tamper_workflow_fields` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_submit_draft_post_for_review` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_resubmit_rejected_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_cannot_submit_already_pending_or_published_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_validation_rules_apply_to_post_creation` | PASS |
| `Tests\Feature\AuthorPostTest::test_thumbnail_upload_works_through_public_filesystem` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_dashboard_displays_all_eight_statistics_metrics` | PASS |
| `Tests\Feature\AuthorPostTest::test_viewer_or_guest_cannot_access_author_cms` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_delete_spam_comment_on_own_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_cannot_delete_comment_on_other_authors_post` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_inherits_viewer_social_capabilities` | PASS |
| `Tests\Feature\AuthorPostTest::test_author_can_submit_post_directly_for_review_on_create` | PASS |
| `Tests\Feature\AuthorPostTest::test_unpublished_posts_are_not_visible_publicly` | PASS |
| `Tests\Feature\AuthorPostTest::test_post_create_and_edit_sidebar_cards_render_correctly` | PASS |
| `Tests\Feature\AuthorPostTest::test_post_status_labels_and_helpers_work_consistently` | PASS |
| `Tests\Feature\AuthorPostTest::test_editor_toolbar_layout_consistency_across_draft_saving_and_workflows` | PASS |
| `Tests\Feature\AvatarUploadTest::test_user_can_upload_valid_jpeg_avatar` | PASS |
| `Tests\Feature\AvatarUploadTest::test_user_can_upload_valid_png_and_webp_avatar` | PASS |
| `Tests\Feature\AvatarUploadTest::test_upload_rejects_invalid_file_types` | PASS |
| `Tests\Feature\AvatarUploadTest::test_upload_rejects_files_larger_than_2mb` | PASS |
| `Tests\Feature\AvatarUploadTest::test_previous_custom_avatar_is_deleted_when_new_avatar_uploaded` | PASS |
| `Tests\Feature\AvatarUploadTest::test_default_or_external_avatar_is_not_deleted` | PASS |
| `Tests\Feature\AvatarUploadTest::test_avatar_url_accessor_handles_null_storage_and_external_paths` | PASS |
| `Tests\Feature\AvatarUploadTest::test_avatar_component_handles_vietnamese_utf8_initials` | PASS |
| `Tests\Feature\AvatarUploadTest::test_quick_follow_badge_rendered_for_viewer_on_other_authors_posts` | PASS |
| `Tests\Feature\AvatarUploadTest::test_quick_follow_badge_not_rendered_for_author_on_own_posts` | PASS |
| `Tests\Feature\AvatarUploadTest::test_quick_follow_badge_links_to_login_for_guest` | PASS |
| `Tests\Feature\AvatarUploadTest::test_quick_follow_toggle_does_not_create_duplicate_records` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_admin_users_view_renders_bootstrap_confirm_modal_and_toast` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_admin_user_show_view_renders_bootstrap_confirm_attributes` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_admin_posts_index_and_show_render_bootstrap_confirm_and_prompt` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_admin_categories_index_renders_bootstrap_confirm_modal` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_admin_comments_index_and_dashboard_render_bootstrap_confirm_modal` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_author_post_edit_renders_bootstrap_confirm` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_public_post_show_renders_bootstrap_confirm_for_comment_deletion` | PASS |
| `Tests\Feature\BootstrapModalConfirmationTest::test_lock_and_unlock_user_business_logic_remains_functional` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_viewer_can_create_comment_and_it_is_approved_immediately` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_author_can_create_comment_and_it_is_approved_immediately` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_viewer_can_reply_and_reply_is_approved_immediately` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_guest_cannot_create_comment` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_locked_user_cannot_create_comment` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_newly_created_comment_appears_immediately_in_public_post_detail` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_newly_created_reply_appears_immediately` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_spam_comment_remains_hidden_from_public` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_admin_can_still_manage_comment_after_publication` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_author_can_moderate_comments_on_own_post` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_author_cannot_moderate_comments_on_another_authors_post` | PASS |
| `Tests\Feature\CommentImmediatePublishTest::test_creating_a_normal_comment_does_not_create_a_pending_moderation_item` | PASS |
| `Tests\Feature\CustomSlugTest::test_author_can_create_post_with_custom_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_empty_slug_is_automatically_generated_from_title` | PASS |
| `Tests\Feature\CustomSlugTest::test_generated_slug_stays_within_column_limit_after_a_long_title_collision` | PASS |
| `Tests\Feature\CustomSlugTest::test_generated_slug_stays_within_column_limit_when_transliteration_expands_title` | PASS |
| `Tests\Feature\CustomSlugTest::test_duplicate_slug_is_rejected_on_create` | PASS |
| `Tests\Feature\CustomSlugTest::test_reserved_custom_slug_is_rejected_on_create"create"` | PASS |
| `Tests\Feature\CustomSlugTest::test_reserved_custom_slug_is_rejected_on_create"stats"` | PASS |
| `Tests\Feature\CustomSlugTest::test_reserved_custom_slug_is_rejected_on_create"check-slug"` | PASS |
| `Tests\Feature\CustomSlugTest::test_generated_slug_skips_reserved_post_paths"create"` | PASS |
| `Tests\Feature\CustomSlugTest::test_generated_slug_skips_reserved_post_paths"stats"` | PASS |
| `Tests\Feature\CustomSlugTest::test_generated_slug_skips_reserved_post_paths"check-slug"` | PASS |
| `Tests\Feature\CustomSlugTest::test_author_can_update_post_with_new_custom_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_author_can_update_post_retaining_same_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_author_cannot_update_post_to_colliding_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_author_cannot_update_post_to_reserved_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_empty_slug_on_update_regenerates_from_title` | PASS |
| `Tests\Feature\CustomSlugTest::test_check_slug_api_returns_availability` | PASS |
| `Tests\Feature\CustomSlugTest::test_check_slug_api_forbids_ignoring_another_authors_post` | PASS |
| `Tests\Feature\CustomSlugTest::test_check_slug_api_rejects_reserved_post_paths"create"` | PASS |
| `Tests\Feature\CustomSlugTest::test_check_slug_api_rejects_reserved_post_paths"stats"` | PASS |
| `Tests\Feature\CustomSlugTest::test_check_slug_api_rejects_reserved_post_paths"check-slug"` | PASS |
| `Tests\Feature\CustomSlugTest::test_legacy_numeric_id_url_redirects_301_to_canonical_slug` | PASS |
| `Tests\Feature\CustomSlugTest::test_create_and_edit_views_render_slug_controls` | PASS |
| `Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response` | PASS |
| `Tests\Feature\FavoritesPageRedesignTest::test_favorites_page_renders_minimalist_header_and_data_attributes` | PASS |
| `Tests\Feature\FavoritesPageRedesignTest::test_favorites_page_excludes_old_redundant_slogans_and_indicators` | PASS |
| `Tests\Feature\FavoritesPageRedesignTest::test_favorites_empty_state_renders_correct_threads_minimalist_copy` | PASS |
| `Tests\Feature\FavoritesPageRedesignTest::test_favorites_preserves_post_card_and_ajax_pagination_hooks` | PASS |
| `Tests\Feature\FeedNavigationTest::test_full_page_load_includes_feed_shell_categories_and_results` | PASS |
| `Tests\Feature\FeedNavigationTest::test_ajax_category_request_returns_only_partial_fragment_with_headers` | PASS |
| `Tests\Feature\FeedNavigationTest::test_category_filter_matches_only_posts_in_category_for_both_full_page_and_fragment` | PASS |
| `Tests\Feature\FeedNavigationTest::test_all_categories_view_returns_all_published_posts` | PASS |
| `Tests\Feature\FeedNavigationTest::test_array_parameters_are_rejected_with_validation_error` | PASS |
| `Tests\Feature\FeedNavigationTest::test_authenticated_user_interaction_state_is_present_in_fragment` | PASS |
| `Tests\Feature\FeedNavigationTest::test_empty_category_displays_empty_state_without_crashing` | PASS |
| `Tests\Feature\FeedNavigationTest::test_home_feed_has_unified_tabs_with_proper_data_attributes` | PASS |
| `Tests\Feature\FeedNavigationTest::test_ajax_following_tab_request_returns_only_following_partial_fragment` | PASS |
| `Tests\Feature\FeedNavigationTest::test_ajax_home_tab_request_returns_only_posts_partial_fragment` | PASS |
| `Tests\Feature\FeedNavigationTest::test_following_view_direct_access_has_matching_layout_shell_and_categories_nav` | PASS |
| `Tests\Feature\FeedNavigationTest::test_guest_cannot_access_following_fragment_via_ajax` | PASS |
| `Tests\Feature\FeedNavigationTest::test_guest_only_sees_for_you_tab_on_home_feed` | PASS |
| `Tests\Feature\FeedNavigationTest::test_viewer_author_and_admin_see_both_tabs_on_home_feed` | PASS |
| `Tests\Feature\GuestPostTest::test_guest_can_see_published_posts` | PASS |
| `Tests\Feature\GuestPostTest::test_guest_cannot_see_unpublished_posts` | PASS |
| `Tests\Feature\GuestPostTest::test_search_works` | PASS |
| `Tests\Feature\GuestPostTest::test_pagination_keeps_query_parameter` | PASS |
| `Tests\Feature\GuestPostTest::test_category_filter_works` | PASS |
| `Tests\Feature\GuestPostTest::test_post_detail_increments_views` | PASS |
| `Tests\Feature\GuestPostTest::test_guest_can_view_author_profile_and_posts` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_guest_mobile_shell_has_header_drawer_and_three_tab_bottom_nav` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_viewer_mobile_shell_has_five_tab_bottom_nav` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_author_mobile_shell_has_five_tab_bottom_nav_with_create_action` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_admin_mobile_shell_has_five_tab_bottom_nav_with_admin_hub` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_author_stats_view_renders_both_desktop_table_and_mobile_cards` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_admin_users_view_renders_both_desktop_table_and_mobile_cards` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_subpage_mobile_header_shows_back_button_and_title` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_mobile_header_logo_is_constrained_and_body_has_flex_md_row` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_admin_control_bar_is_hidden_on_mobile_to_prevent_duplicate_headers` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_settings_page_renders_clean_without_verbose_instruction_blocks` | PASS |
| `Tests\Feature\MobileResponsiveDesignSystemTest::test_author_post_editor_renders_responsive_action_buttons` | PASS |
| `Tests\Feature\PaginationTest::test_case_1_twenty_results_ten_per_page` | PASS |
| `Tests\Feature\PaginationTest::test_case_2_eighteen_results_ten_per_page` | PASS |
| `Tests\Feature\PaginationTest::test_case_3_ten_results_ten_per_page` | PASS |
| `Tests\Feature\PaginationTest::test_case_4_nine_results_ten_per_page` | PASS |
| `Tests\Feature\PaginationTest::test_case_5_search_query_preserves_pagination_parameter` | PASS |
| `Tests\Feature\PaginationTest::test_case_6_category_filter_preserves_pagination_parameter` | PASS |
| `Tests\Feature\PaginationTest::test_case_7_invalid_page_redirects_safely` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_figure_and_caption_styles_cannot_overlay_the_page_or_load_remote_css_urls` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_figure_and_caption_styles_preserve_safe_typography` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_ten_cards_read_viewer_state_in_one_query_without_per_card_lookups` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_preloaded_state_for_one_viewer_is_not_reused_for_another_user` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_profile_renders_three_authored_cards_without_queries_in_each_card` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_author_metrics_are_scoped_and_do_not_load_all_post_bodies` | PASS |
| `Tests\Feature\PerformanceIntegrityTest::test_empty_admin_content_metrics_are_zero_and_use_bounded_queries` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_post_card_renders_author_category_and_timestamp_on_header` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_post_action_bar_excludes_repost_button` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_post_action_bar_contains_like_comment_share_and_save` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_share_button_renders_lucide_send_svg_and_has_no_fake_count` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_like_and_comment_counts_only_display_when_greater_than_zero` | PASS |
| `Tests\Feature\PostCardThreadsRedesignTest::test_tags_are_rendered_with_proper_spacing_and_not_glued` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"empty paragraph"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"line break paragraph"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"spaces"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"nonbreaking space"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"empty bold"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"empty italic"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"empty span"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"zero width unicode"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"zero width entity"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_empty_or_invisible_editor_content_cannot_create_a_draft"script only"` | PASS |
| `Tests\Feature\PostContentValidationTest::test_vietnamese_rich_text_remains_valid_and_scripts_are_removed` | PASS |
| `Tests\Feature\PostContentValidationTest::test_existing_local_image_alone_is_valid_editor_content` | PASS |
| `Tests\Feature\PostContentValidationTest::test_existing_local_video_alone_is_valid_editor_content` | PASS |
| `Tests\Feature\PostContentValidationTest::test_youtube_embed_alone_remains_valid` | PASS |
| `Tests\Feature\PostContentValidationTest::test_nonexistent_or_unsafe_media_alone_does_not_create_a_post` | PASS |
| `Tests\Feature\PostContentValidationTest::test_invalid_update_keeps_existing_content_and_status` | PASS |
| `Tests\Feature\PostEditorTest::test_author_can_upload_image_successfully` | PASS |
| `Tests\Feature\PostEditorTest::test_unauthenticated_user_cannot_upload_image` | PASS |
| `Tests\Feature\PostEditorTest::test_viewer_cannot_upload_image` | PASS |
| `Tests\Feature\PostEditorTest::test_locked_author_cannot_upload_image` | PASS |
| `Tests\Feature\PostEditorTest::test_upload_rejects_non_image_files` | PASS |
| `Tests\Feature\PostEditorTest::test_upload_rejects_oversized_images` | PASS |
| `Tests\Feature\PostEditorTest::test_author_can_create_post_with_wysiwyg_content_including_images_and_links` | PASS |
| `Tests\Feature\PostEditorTest::test_post_content_sanitizes_malicious_xss_and_protocols` | PASS |
| `Tests\Feature\PostEditorTest::test_legacy_markdown_content_renders_cleanly_on_show` | PASS |
| `Tests\Feature\PostEditorTest::test_editor_content_accessor_converts_legacy_markdown_for_visual_editor` | PASS |
| `Tests\Feature\PostEditorTest::test_saving_draft_and_submitting_for_review_preserves_content_and_status` | PASS |
| `Tests\Feature\PostEditorTest::test_content_image_includes_editor_content_image_class_for_unified_styling` | PASS |
| `Tests\Feature\PostEditorTest::test_post_editor_toolbar_contains_full_block_dropdown_and_formatting_controls` | PASS |
| `Tests\Feature\PostEditorTest::test_post_content_preserves_all_heading_levels_h1_through_h6_and_rich_marks` | PASS |
| `Tests\Feature\PostEditorTest::test_post_content_preserves_safe_css_styles_and_strips_malicious_styles` | PASS |
| `Tests\Feature\PostEditorTest::test_legacy_markdown_with_h1_to_h6_and_blockquotes_converts_properly` | PASS |
| `Tests\Feature\PostEditorTest::test_author_and_admin_role_permissions_remain_intact` | PASS |
| `Tests\Feature\PostEditorTest::test_nested_ordered_and_bulleted_lists_are_preserved_in_content_service_and_public_views` | PASS |
| `Tests\Feature\PostEditorTest::test_text_color_and_text_highlight_are_independent_and_can_coexist_or_be_cleared_separately` | PASS |
| `Tests\Feature\PostEditorTest::test_shortcuts_modal_is_independent_and_contains_complete_categorized_shortcuts` | PASS |
| `Tests\Feature\PostEditorTest::test_post_content_service_allows_youtube_iframes_and_wraps_in_responsive_container` | PASS |
| `Tests\Feature\PostEditorTest::test_post_content_service_strips_malicious_and_non_youtube_iframes` | PASS |
| `Tests\Feature\PostEditorTest::test_post_creation_and_update_preserves_youtube_video` | PASS |
| `Tests\Feature\PostEditorTest::test_editor_ui_renders_video_toolbar_button_and_modal` | PASS |
| `Tests\Feature\PostEditorTest::test_author_can_upload_video_successfully` | PASS |
| `Tests\Feature\PostEditorTest::test_author_can_upload_webm_video_successfully` | PASS |
| `Tests\Feature\PostEditorTest::test_unauthenticated_and_unauthorized_users_cannot_upload_video` | PASS |
| `Tests\Feature\PostEditorTest::test_video_upload_rejects_non_video_and_disallowed_formats` | PASS |
| `Tests\Feature\PostEditorTest::test_author_can_delete_uploaded_video` | PASS |
| `Tests\Feature\PostEditorTest::test_post_creation_and_lifecycle_preserves_html5_video` | PASS |
| `Tests\Feature\PostPolicyTest::test_owner_can_update_their_own_post` | PASS |
| `Tests\Feature\PostPolicyTest::test_owner_can_delete_their_own_post` | PASS |
| `Tests\Feature\PostPolicyTest::test_admin_can_manage_all_posts` | PASS |
| `Tests\Feature\PostPolicyTest::test_other_author_cannot_update_or_delete_post` | PASS |
| `Tests\Feature\PostPolicyTest::test_viewer_cannot_update_or_delete_post` | PASS |
| `Tests\Feature\PostPolicyTest::test_author_can_create_post` | PASS |
| `Tests\Feature\PostSubmitTransitionTest::test_admin_cannot_submit_an_authors_published_post_and_counters_stay_correct` | PASS |
| `Tests\Feature\PostSubmitTransitionTest::test_admin_cannot_submit_their_own_already_published_post_again` | PASS |
| `Tests\Feature\PostSubmitTransitionTest::test_author_cannot_resubmit_an_already_pending_post` | PASS |
| `Tests\Feature\PostSubmitTransitionTest::test_admin_can_publish_own_rejected_post_without_review_queue` | PASS |
| `Tests\Feature\PostSubmitTransitionTest::test_admin_cannot_submit_an_authors_pending_post_instead_of_moderating_it` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_author_can_select_and_save_multiple_tags_successfully` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_tags_are_preserved_when_reopening_post_for_editing` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_editing_post_can_add_and_remove_tags` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_duplicate_tags_are_not_saved_for_post` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_invalid_or_non_existent_tag_ids_are_rejected` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_exceeding_max_tag_limit_is_rejected` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_author_and_admin_can_create_new_tags_via_api` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_viewer_and_guest_cannot_create_or_manage_tags` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_guest_cannot_view_unpublished_post_via_direct_url` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_admin_tag_management_requires_admin_role` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_saving_post_as_draft_sets_status_to_draft` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_editing_draft_does_not_create_new_record` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_saving_draft_preserves_tags_category_and_thumbnail` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_submitting_post_for_review_transitions_status_to_pending_without_auto_publishing` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_submitting_with_missing_required_fields_fails_validation_and_does_not_transition_status` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_multiple_submit_clicks_do_not_create_duplicate_posts` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_resubmitting_rejected_post_transitions_to_pending` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_author_cannot_edit_or_submit_another_authors_post` | PASS |
| `Tests\Feature\PostTagWorkflowTest::test_tag_seeder_creates_all_sample_tags_and_is_idempotent` | PASS |
| `Tests\Feature\ProfileTest::test_profile_page_is_displayed` | PASS |
| `Tests\Feature\ProfileTest::test_profile_information_can_be_updated` | PASS |
| `Tests\Feature\ProfileTest::test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged` | PASS |
| `Tests\Feature\ProfileTest::test_user_can_upload_avatar_successfully` | PASS |
| `Tests\Feature\ProfileTest::test_user_can_upload_png_and_webp_avatar` | PASS |
| `Tests\Feature\ProfileTest::test_old_avatar_is_deleted_when_new_avatar_is_uploaded` | PASS |
| `Tests\Feature\ProfileTest::test_avatar_must_be_a_valid_image_file` | PASS |
| `Tests\Feature\ProfileTest::test_avatar_size_cannot_exceed_2mb` | PASS |
| `Tests\Feature\ProfileTest::test_updating_profile_without_avatar_keeps_existing_avatar` | PASS |
| `Tests\Feature\ProfileTest::test_ajax_profile_update_returns_json_with_avatar_url` | PASS |
| `Tests\Feature\ProfileTest::test_profile_update_clears_email_verified_at_and_triggers_otp_when_email_changes` | PASS |
| `Tests\Feature\ProfileTest::test_user_can_delete_their_account` | PASS |
| `Tests\Feature\ProfileTest::test_correct_password_must_be_provided_to_delete_account` | PASS |
| `Tests\Feature\ProfileTest::test_profile_show_page_renders_edit_modal_and_trigger_button` | PASS |
| `Tests\Feature\ProfileTest::test_user_can_update_name_and_bio_via_ajax_without_email` | PASS |
| `Tests\Feature\ProfileTest::test_user_can_update_avatar_via_ajax_modal` | PASS |
| `Tests\Feature\ProfileTest::test_profile_modal_validation_errors_returned_as_json` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_01_user_registration_with_valid_data` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_02_user_registration_validation_errors` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_03_user_registration_cannot_escalate_role` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_04_user_login_with_valid_credentials` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_05_user_login_with_invalid_credentials` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_06_user_login_rate_limiting` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_07_locked_user_login_attempt` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_07_unlocked_user_can_still_login_normally` | PASS |
| `Tests\Feature\QASuiteTest::test_auth_08_user_logout` | PASS |
| `Tests\Feature\QASuiteTest::test_role_01_guest_can_access_public_routes_only` | PASS |
| `Tests\Feature\QASuiteTest::test_role_02_guest_cannot_view_unpublished_post` | PASS |
| `Tests\Feature\QASuiteTest::test_role_03_viewer_cannot_access_author_cms` | PASS |
| `Tests\Feature\QASuiteTest::test_role_04_author_cannot_access_admin_panel` | PASS |
| `Tests\Feature\QASuiteTest::test_role_11_locked_author_cannot_create_update_submit_or_delete` | PASS |
| `Tests\Feature\QASuiteTest::test_role_11_unlocked_author_can_perform_post_operations` | PASS |
| `Tests\Feature\QASuiteTest::test_int_10_approved_comments_and_replies_render_publicly` | PASS |
| `Tests\Feature\QASuiteTest::test_int_10_pending_and_spam_comments_and_replies_hidden_publicly` | PASS |
| `Tests\Feature\QASuiteTest::test_int_10_admin_can_still_moderate_pending_and_spam_comments` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_05_admin_cannot_lock_self` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_05_admin_cannot_lock_another_admin` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_05_admin_can_lock_author_and_viewer` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_06_case_a_empty_category_deletion_succeeds` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_06_case_b_category_with_posts_deletion_is_blocked` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_06_case_d_explicit_post_deletion_cleans_up_thumbnail` | PASS |
| `Tests\Feature\QASuiteTest::test_post_01_create_draft_and_sync_tags` | PASS |
| `Tests\Feature\QASuiteTest::test_post_02_post_detail_increments_views` | PASS |
| `Tests\Feature\QASuiteTest::test_post_03_author_can_update_own_post` | PASS |
| `Tests\Feature\QASuiteTest::test_post_04_author_can_delete_own_post` | PASS |
| `Tests\Feature\QASuiteTest::test_post_05_author_can_submit_draft_post` | PASS |
| `Tests\Feature\QASuiteTest::test_post_06_admin_can_approve_post` | PASS |
| `Tests\Feature\QASuiteTest::test_post_07_admin_can_reject_post` | PASS |
| `Tests\Feature\QASuiteTest::test_post_08_author_can_resubmit_rejected_post` | PASS |
| `Tests\Feature\QASuiteTest::test_int_01_like_post_exact_json` | PASS |
| `Tests\Feature\QASuiteTest::test_int_02_favorite_post_exact_json` | PASS |
| `Tests\Feature\QASuiteTest::test_int_03_follow_author_exact_json` | PASS |
| `Tests\Feature\QASuiteTest::test_int_04_user_cannot_follow_self` | PASS |
| `Tests\Feature\QASuiteTest::test_int_05_create_comment_and_reply_validation` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_01_upload_validation_rejects_non_image` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_02_upload_validation_rejects_oversized_file` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_03_author_cannot_force_published_status_on_create_or_update` | PASS |
| `Tests\Feature\QASuiteTest::test_sec_04_author_cannot_modify_other_author_post` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_post_card_renders_quick_follow_open_trigger_for_unfollowed_author` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_post_card_renders_quick_follow_open_trigger_for_followed_author_without_hiding_button` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_post_card_does_not_render_quick_follow_trigger_for_post_author` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_post_card_renders_guest_quick_follow_trigger_with_login_metadata` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_author_follow_and_unfollow_endpoints_return_correct_json_payload` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_cannot_follow_oneself_via_endpoint` | PASS |
| `Tests\Feature\QuickFollowPopoverTest::test_guest_cannot_follow_via_endpoint` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_admin_can_access_admin_only_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_author_cannot_access_admin_only_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_viewer_cannot_access_admin_only_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_author_can_access_author_or_admin_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_admin_can_access_author_or_admin_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_viewer_cannot_access_author_or_admin_route` | PASS |
| `Tests\Feature\RoleMiddlewareTest::test_guest_cannot_access_protected_role_routes` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_security_and_account_section_is_rendered_in_settings_page` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_password_form_collapsible_is_expanded_when_validation_errors_occur` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_password_form_collapsible_is_expanded_when_password_updated_successfully` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_password_update_requires_matching_confirmation` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_user_can_logout_via_session_row` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_delete_account_modal_requires_password_and_fails_on_wrong_password` | PASS |
| `Tests\Feature\SecurityAccountSettingsTest::test_delete_account_succeeds_with_correct_password` | PASS |
| `Tests\Feature\SidebarDesignStandardTest::test_guest_sidebar_renders_design_standard_card_with_login_and_register` | PASS |
| `Tests\Feature\SidebarDesignStandardTest::test_viewer_sidebar_renders_public_and_personal_items_without_author_or_admin_menus` | PASS |
| `Tests\Feature\SidebarDesignStandardTest::test_author_sidebar_matches_standard_with_author_group_and_badge` | PASS |
| `Tests\Feature\SidebarDesignStandardTest::test_admin_sidebar_matches_design_standard_with_admin_group_and_badge` | PASS |
| `Tests\Feature\SidebarSeamlessNavigationTest::test_persistent_app_shell_elements_and_progress_bar_render_on_all_pages` | PASS |
| `Tests\Feature\SidebarSeamlessNavigationTest::test_sidebar_editor_link_is_seamlessly_navigable_without_data_no_app_nav` | PASS |
| `Tests\Feature\SidebarSeamlessNavigationTest::test_author_can_fetch_create_post_via_seamless_app_nav_ajax` | PASS |
| `Tests\Feature\SidebarSeamlessNavigationTest::test_app_nav_ajax_request_returns_valid_html_with_main_content` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_pending_post_displays_redesigned_status_banner` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_rejected_post_displays_redesigned_status_banner_with_reason` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_draft_post_displays_draft_status_banner` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_published_post_displays_published_status_banner_with_live_link` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_image_alt_attribute_is_preserved_on_save_and_reopen` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_multiple_images_maintain_distinct_alt_attributes` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_update_post_image_alt_does_not_affect_other_images` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_author_dashboard_search_by_title_case_insensitive_and_trimmed` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_author_dashboard_search_coordinates_with_status_filter` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_author_dashboard_displays_friendly_empty_state_on_no_search_results` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_action_buttons_displayed_appropriately_by_status` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_sidebar_user_card_renders_separated_name_and_role_badge` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_sidebar_admin_user_card_renders_admin_badge` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_dashboard_header_and_stat_cards_render_redesigned_classes` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_dashboard_search_input_has_dedicated_classes_and_full_placeholder` | PASS |
| `Tests\Feature\StatusBannerAndImageAltTest::test_dashboard_table_renders_timestamp_and_action_button_classes` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_author_can_create_post_with_thumbnail` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_author_can_replace_thumbnail_with_new_image` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_author_can_remove_thumbnail_completely` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_author_retains_thumbnail_when_not_changing_it` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_shared_thumbnail_is_protected_when_one_post_removes_or_replaces_it` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_shared_thumbnail_is_protected_when_post_is_deleted` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_unauthorized_author_cannot_update_or_remove_other_authors_thumbnail` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_thumbnail_validation_rejects_oversized_file` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_post_content_service_preserves_figure_figcaption_and_alignment` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_post_content_service_preserves_left_and_right_alignment` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_post_content_service_sanitizes_malicious_scripts_in_figures_and_captions` | PASS |
| `Tests\Feature\ThumbnailAndImageAlignmentTest::test_create_and_edit_views_render_thumbnail_uploader_widget` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_author_can_delete_only_their_own_temporary_uploads` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_video_referenced_by_a_post_cannot_be_deleted_even_by_its_uploader` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_untracked_public_video_cannot_be_deleted_by_filename` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_guest_and_viewer_cannot_delete_a_video` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_admin_does_not_gain_ownership_of_another_users_temporary_video` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_invalid_video_filename_is_rejected"traversal"` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_invalid_video_filename_is_rejected"nested path"` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_invalid_video_filename_is_rejected"parent directory"` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_invalid_video_filename_is_rejected"other file type"` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_missing_owned_video_returns_not_found_without_changing_post_data` | PASS |
| `Tests\Feature\VideoDeletionAuthorizationTest::test_failed_storage_deletion_keeps_upload_ownership_for_a_retry` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_like_a_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_unlike_a_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_duplicate_like_is_handled_safely` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_guest_cannot_like_a_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_favorite_a_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_unfavorite_a_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_view_favorites_page` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_follow_an_author` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_unfollow_an_author` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_user_cannot_follow_self` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_post_comment_on_published_post` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_reply_to_existing_comment` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_comment_validation_fails_for_invalid_parent_id` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_comment_validation_fails_for_empty_body` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_locked_user_cannot_interact` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_view_activity_page` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_view_profile_overview` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_guest_is_redirected_to_login_when_accessing_following` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_view_following_feed_with_only_followed_authors_posts` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_viewer_can_view_followed_authors_tab` | PASS |
| `Tests\Feature\ViewerInteractionTest::test_feed_header_links_to_following_route_not_activity` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_01_viewer_login_redirects_to_home` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_02_author_login_redirects_to_home` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_03_admin_login_redirects_to_admin_dashboard` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_04_viewer_sees_become_author_cta` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_05_author_sees_write_post_cta` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_06_viewer_after_becoming_author_can_access_create_post` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_07_viewer_cannot_access_create_post_before_becoming_author` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_08_viewer_cannot_self_escalate_to_admin` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_09_author_cannot_self_publish_posts` | PASS |
| `Tests\Feature\ViewerToAuthorTest::test_10_admin_dashboard_still_functions_and_blocks_viewer_and_author` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_explicit_interaction_state_is_idempotent_and_readable"like"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_explicit_interaction_state_is_idempotent_and_readable"favorite"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_explicit_interaction_state_is_idempotent_and_readable"follow"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_invalid_explicit_state_returns_422_without_a_like` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_state_reads_require_authentication_and_reject_locked_sessions` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_state_read_does_not_expose_unpublished_post` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_comment_retry_returns_same_record_and_approved_count` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_reusing_comment_request_id_with_different_content_returns_422` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_reusing_another_users_request_id_does_not_return_their_comment` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_reply_to_reply_is_attached_to_root_and_retry_is_idempotent` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_cross_post_reply_is_rejected_before_thread_normalization` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_invalid_comment_request_id_is_rejected` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_approve_non_pending_post"draft"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_approve_non_pending_post"published"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_approve_non_pending_post"rejected"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_reject_non_pending_post"draft"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_reject_non_pending_post"published"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_cannot_reject_non_pending_post"rejected"` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_rejection_requires_reason_and_keeps_post_pending_on_error` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_approval_clears_previous_rejection_reason` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_locked_viewer_cannot_become_author_from_existing_session` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_comment_search_preserves_status_when_user_matches` | PASS |
| `Tests\Feature\WorkflowIntegrityTest::test_admin_deleting_post_keeps_shared_thumbnail` | PASS |
| Node: isEligibleLink validates same-origin sidebar navigation links | PASS |
| Node: isEligibleLink rejects modifier keys (Ctrl/Meta/Shift/Alt) | PASS |
| Node: isEligibleLink rejects target blank, downloads, and data-no-app-nav | PASS |
| Node: isEligibleLink excludes feed-pagination links (delegated to feed-navigation.js) | PASS |
| Node: isEligibleLink excludes external domains and auth routes | PASS |
| Node: createAppNavigator aborts earlier in-flight navigation when new link is clicked (Race Condition Guard) | PASS |
| Node: isEligibleLink allows mobile bottom navigation links | PASS |
| Node: isEligibleLink allows mobile drawer links with data-app-nav | PASS |
| Node: updateMobileHeader updates header left container for subpages and home | PASS |
| Node: isEligibleLink allows sidebar "Viết bài" route when data-no-app-nav is removed | PASS |
| Node: isEligibleLink allows cancel buttons and dashboard create button via action nav | PASS |
| Node: Editor dirty state lifecycle: tracks unsaved changes and resets on destroy | PASS |
| Node: confirmNavigationLeave uses fallback window.confirm when Bootstrap modal is unavailable | PASS |
| Node: confirmNavigationLeave resolves to false when user chooses to stay ("Tiếp tục chỉnh sửa") | PASS |
| Node: Editor can undergo consecutive navigation cycles (Viết bài <-> Trang chủ <-> Viết bài) cleanly | PASS |
| Node: createFeedLoader sends X-BlogMNM-Feed and Accept headers | PASS |
| Node: createFeedLoader handles sequential pagination transitions: page 1 -> 2 -> 3 | PASS |
| Node: createFeedLoader aborts earlier in-flight request when new request arrives (Race Condition guard) | PASS |
| Node: createFeedLoader throws on HTTP error response | PASS |
| Node: createFeedLoader loads Following tab via AJAX with X-BlogMNM-Feed header | PASS |
| Node: rapid switching between For You and Following aborts previous request and resolves final tab | PASS |
| Node: createFeedLoader preserves query parameters during pagination requests | PASS |
| Node: rapid status tabs switching aborts intermediate requests and resolves final tab | PASS |
| Node: ten rapid clicks render immediately and serialize the final target | PASS |
| Node: like unlike like coalesces without an unnecessary second mutation | PASS |
| Node: a rejected mutation reconciles and rolls back without blindly retrying | PASS |
| Node: an intent entered during reconciliation is preserved and sent afterward | PASS |
| Node: a lost mutation and failed reconciliation expose uncertainty and allow a safe new attempt | PASS |
| Node: fragment hydration preserves pending intent and rebases idle state | PASS |
| Node: optimistic counts never become negative | PASS |
| Node: an old feed fragment cannot undo a mutation completed after navigation started | PASS |
| Node: a fragment requested during a pending mutation preserves its completed result | PASS |
| Node: a lost response confirmed by reconciliation does not report a false failure | PASS |
| Node: Reading Preferences: defaults to md font and standard width | PASS |
| Node: Reading Preferences: changes and persists font size (sm, md, lg) | PASS |
| Node: Reading Preferences: changes and persists reading width (standard, comfortable, wide) | PASS |
| Node: Password Toggle: toggles type between password and text and switches eye icons | PASS |
| Node: Form Submission: prevents duplicate submit and adds loading spinner | PASS |
