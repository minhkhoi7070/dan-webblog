# BlogMNM — Database Schema Reconciliation Report

## 1. Executive Summary

- **Task**: Database Schema Reconciliation for `users` table without destroying data (`migrate:fresh` prohibited).
- **Incident**: Seeding failed with `SQLSTATE[HY000]: General error: 1 table users has no column named role` at `database/seeders/DatabaseSeeder.php:21`.
- **Status**: **RESOLVED & VERIFIED**.
- **Database Status**: Migration applied, schema verified, database seeded, 142/142 tests passing.

---

## 2. Root Cause Analysis

1. **Initial Migration Execution**:
   - The initial migration `0001_01_01_000000_create_users_table.php` was created during initial Breeze scaffolding (Commit `814d61a`) with standard authentication fields (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `timestamps`).
   - This migration was executed in Batch 1 against `database/database.sqlite`.

2. **Retroactive Migration Modification Trap**:
   - The project's requirements expanded to include roles (`admin`, `author`, `viewer`), avatars, biographies, and lockout states (`is_locked`).
   - The migration file `0001_01_01_000000_create_users_table.php` was retroactively edited to include these columns.
   - However, because `0001_01_01_000000_create_users_table` was already recorded as `Ran` in the `migrations` table, subsequent runs of `php artisan migrate` skipped it.
   - The physical SQLite database `database/database.sqlite` remained in its pre-edited state without `role`, `avatar`, `bio`, or `is_locked`.

3. **Discrepancy Between Testing and Local Database**:
   - Automated tests in `tests/TestCase.php` use Laravel's `RefreshDatabase` trait, which re-executes all migration files from scratch in an isolated database. Thus, tests passed because `0001_01_01_000000_create_users_table.php` ran with the additions during testing.
   - In the persistent local development environment (`database/database.sqlite`), the schema lacked these 4 columns, causing `DatabaseSeeder::run()` to fail immediately on Step 1 (`User::factory()->admin()->create()`).

---

## 3. Data Contract & Column Specification

The project's formal Data Contract (`artifacts/database-schema.md` and `artifacts/database-dictionary.md`) defines the following requirements for the missing columns:

| Column | Target Type | Nullable | Default | Description |
| :--- | :--- | :--- | :--- | :--- |
| `role` | `ENUM('admin', 'author', 'viewer')` | No | `'viewer'` | User authorization role |
| `avatar` | `VARCHAR(255)` | Yes | `NULL` | Relative storage path to avatar image |
| `bio` | `TEXT` | Yes | `NULL` | User biography description |
| `is_locked` | `BOOLEAN` | No | `false` (`0`) | Account lock flag |

---

## 4. Migration Created

Following the strict constraint to **never modify an already-ran migration** and **never drop existing tables**, a new forward migration was generated:

- **File**: `database/migrations/2026_09_29_143919_add_role_and_profile_columns_to_users_table.php`
- **Command Used**: `php artisan make:migration add_role_and_profile_columns_to_users_table --table=users --no-interaction`

### Migration Implementation

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'author', 'viewer'])->default('viewer')->after('password');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('bio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['is_locked', 'bio', 'avatar', 'role'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
```

> **Design Decision**: The migration uses defensive `if (! Schema::hasColumn('users', ...))` guards. This guarantees idempotency: on the persistent SQLite development database, it safely injects the 4 missing columns; during automated test runs with `RefreshDatabase` (where `0001_01_01_000000_create_users_table.php` already builds the columns), it cleanly skips duplicate additions without error.

---

## 5. Database Changes & Migration Status

### Migration Execution
Command executed:
```bash
php artisan migrate
```
Output:
```
INFO Running migrations.
2026_09_29_143919_add_role_and_profile_columns_to_users_table .. 39.16ms DONE
```

### Migration Status Check
Command executed:
```bash
php artisan migrate:status
```
Output:
```
0001_01_01_000000_create_users_table ........................... [1] Ran
0001_01_01_000001_create_cache_table ........................... [1] Ran
0001_01_01_000002_create_jobs_table ............................ [1] Ran
2024_01_01_000001_create_categories_table ...................... [2] Ran
2024_01_01_000002_create_tags_table ............................ [2] Ran
2024_01_01_000003_create_posts_table ........................... [2] Ran
2024_01_01_000004_create_post_tag_table ........................ [2] Ran
2024_01_01_000005_create_comments_table ........................ [2] Ran
2024_01_01_000006_create_favorites_table ....................... [2] Ran
2024_01_01_000007_create_likes_table ........................... [2] Ran
2024_01_01_000008_create_follows_table ......................... [2] Ran
2024_01_01_000009_add_admin_review_columns_to_tables ........... [2] Ran
2024_01_01_000010_update_posts_category_foreign_key_to_restrict  [2] Ran
2026_09_29_143919_add_role_and_profile_columns_to_users_table .. [3] Ran
```

### Verified Schema (`PRAGMA table_info(users)`)

```
cid | name              | type        | notnull | dflt_value | pk
----+-------------------+-------------+---------+------------+---
0   | id                | INTEGER     | 1       | NULL       | 1
1   | name              | varchar     | 1       | NULL       | 0
2   | email             | varchar     | 1       | NULL       | 0
3   | email_verified_at | datetime    | 0       | NULL       | 0
4   | password          | varchar     | 1       | NULL       | 0
5   | remember_token    | varchar     | 0       | NULL       | 0
6   | created_at        | datetime    | 0       | NULL       | 0
7   | updated_at        | datetime    | 0       | NULL       | 0
8   | role              | varchar     | 1       | 'viewer'   | 0
9   | avatar            | varchar     | 0       | NULL       | 0
10  | bio               | TEXT        | 0       | NULL       | 0
11  | is_locked         | tinyint(1)  | 1       | '0'        | 0
```

---

## 6. Seed Result & Verification

### Seeding Command
Command executed:
```bash
php artisan db:seed
```
Output:
```
INFO Seeding database.
[OK] Completed without errors.
```

### Record Count Verification

| Entity / Metric | Count | Contract Requirement | Status |
| :--- | :--- | :--- | :--- |
| **Total Users** | **24** | ≥ 20 | PASS |
| ↳ Admin Users | **1** | Exactly 1 (`admin@blogmnm.test`) | PASS |
| ↳ Author Users | **5** | 4 – 6 | PASS |
| ↳ Viewer Users | **18** | 15 – 20 | PASS |
| **Total Posts** | **35** | Published, draft, pending, rejected | PASS |
| **Categories** | **8** | Standard taxonomy set | PASS |
| **Tags** | **14** | Content discovery tags | PASS |
| **Comments** | **73** | Root comments + nested replies | PASS |

---

## 7. Regression Testing & Security Verification

1. **Authentication & Authorization**:
   - `User::isAdmin()`, `User::isAuthor()`, and `User::hasRole()` evaluate cleanly against the reconciled schema.
   - Account lockout (`is_locked`) prevents login in `LoginRequest.php` and blocks access in `PostPolicy::before()`.

2. **Automated Test Results**:
   - Full test suite: `php artisan test --compact`
   - **Result**: **142 tests passed, 514 assertions, 0 failures**.
   - Security-critical tests (`AdminTest`, `RoleMiddlewareTest`, `PostPolicyTest`, `AuthenticationTest`): 31/31 passed.

3. **Code Style**:
   - Formatted with `vendor/bin/pint --dirty --format agent`.
   - **Result**: Passed.
