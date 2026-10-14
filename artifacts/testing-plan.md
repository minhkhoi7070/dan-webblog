# BlogMNM — Quality Assurance & Testing Plan

## 1. Test Objectives & Scope

The primary objective of the testing strategy for **BlogMNM** is to ensure absolute functional correctness, robust security defense-in-depth, strict role authorization adherence, data integrity, and cross-device visual reliability.

### Scope of Testing
- **Authentication & Security**: Registration, login rate-limiting, session fixation defense, locked account rejection (`AUTH-07`).
- **Authorization & RBAC**: Guest vs Viewer vs Author vs Administrator route protection, `RoleMiddleware`, `PostPolicy`, `CommentPolicy`.
- **Content Lifecycle & Workflow**: Draft post creation, thumbnail upload/replacement/deletion, editorial submission, admin approval and rejection with feedback.
- **Social Interactions**: AJAX like toggling, bookmark favorites library, author following with self-follow prevention, threaded comments with cross-post parent validation.
- **Search, Taxonomy & Pagination**: Keyword search grouping, category/tag filtering, query string persistence, out-of-bounds page safety.
- **Administrative Governance**: Post moderation, comment moderation, category deletion safety (`RESTRICT`), user account locking with administrator protection (`SEC-05`).

---

## 2. Test Architecture & Tooling

| Component | Tool / Framework | Purpose |
| :--- | :--- | :--- |
| **Test Runner** | PHPUnit 11.x / `php artisan test` | Automated execution of Feature and Unit test suites |
| **Database Sandbox** | SQLite in-memory / MySQL Test DB | Isolated transaction rollback between tests (`RefreshDatabase`) |
| **Data Generation** | Laravel Model Factories & Faker | Deterministic test state setup |
| **Code Style** | Laravel Pint | Automated PSR-12 code style enforcement |

---

## 3. Test Suites Structure

```
tests/
├── Unit/
│   ├── ExampleTest.php
│   └── ModelRelationshipTest.php      # Verifies Eloquent model associations
└── Feature/
    ├── Auth/
    │   ├── AuthenticationTest.php     # Breeze login & lockout tests
    │   ├── RegistrationTest.php       # Registration & role assignment
    │   ├── PasswordConfirmationTest.php
    │   ├── PasswordResetTest.php
    │   └── PasswordUpdateTest.php
    ├── AdminTest.php                  # Moderation queue, user & category management
    ├── AuthorPostTest.php             # Author CMS, draft/edit/delete/submit, file cleanup
    ├── GuestPostTest.php              # Public feed, view counter, search & category filters
    ├── PaginationTest.php             # Pagination, query parameter preservation, safety bounds
    ├── PostPolicyTest.php             # Granular post policy ability tests
    ├── ProfileTest.php                # User profile edit & deletion
    ├── QASuiteTest.php                # Regression test suite for AUTH-07, ROLE-11, INT-10, SEC-05, SEC-06
    ├── RoleMiddlewareTest.php         # RoleMiddleware HTTP 403 & redirect behavior
    └── ViewerInteractionTest.php      # Likes, favorites, follows, threaded comments
```

---

## 4. Key Verification Scenarios & Quality Gates

### Group A: Authentication & Role Enforcement
- **TC-AUTH-01**: Registration defaults to `viewer` role; direct role injection ignored.
- **TC-AUTH-04**: Valid credentials authenticate and regenerate session ID.
- **TC-AUTH-06**: 5 consecutive failed attempts trigger rate limiter throttle.
- **TC-AUTH-07**: Locked account credentials rejected; session destroyed; 422 error returned.
- **TC-ROLE-06**: Viewers blocked from Author CMS (`/posts/create`) with HTTP 403.
- **TC-ROLE-09**: Authors and Viewers blocked from `/admin/*` with HTTP 403.
- **TC-ROLE-11**: Locked authors blocked from creating, editing, updating, and submitting posts.

### Group B: Editorial Workflow & Storage
- **TC-POST-01**: Newly created posts default to `draft` with `views = 0`.
- **TC-POST-03**: Updating a thumbnail deletes previous image from disk.
- **TC-POST-04**: Deleting a post unlinks image from disk via model observer.
- **TC-POST-06**: Author can submit draft or rejected post for review (`pending`).
- **TC-POST-07**: Admin approval sets `status = 'published'`, `reviewed_by`, and `published_at`.
- **TC-POST-08**: Admin rejection records `rejection_reason` and sets `status = 'rejected'`.

### Group C: Social Interactions & Security
- **TC-INT-01/02**: Like toggle returns exact JSON `{"liked": bool, "likes_count": int}`.
- **TC-INT-05/06**: Follow toggles author subscription; self-following rejected with HTTP 422.
- **TC-INT-07/08**: Threaded comment and reply creation return HTTP 201 Created.
- **TC-INT-09**: Cross-post reply attempt (replying to a comment from a different post) rejected with HTTP 422.
- **TC-INT-10**: Pending and spam comments hidden from public post detail.
- **TC-SEC-05**: Administrators blocked from locking other administrator accounts.
- **TC-SEC-06**: Category deletion blocked when active posts are assigned (`RESTRICT`).

---

## 5. Execution & Quality Acceptance Criteria

- **Pass Rate**: 100% of all automated tests must pass without any failed assertions.
- **Zero Style Discrepancies**: Codebase must pass `vendor/bin/pint --test` without formatting violations.
- **No Orphaned Files**: All temporary and test upload files must be cleaned up from the filesystem.
