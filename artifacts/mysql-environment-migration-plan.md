# MySQL Environment Migration Plan

## 1. Current Environment Analysis

**Database Connection**
- The project was previously configured to use `DB_CONNECTION=sqlite`.
- The local environment is powered by **Laragon** with **MySQL 8.4.3** installed and available in the system PATH.

**Service Drivers**
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`
- All necessary foundational migrations for these services (`0001_01_01_000001_create_cache_table.php`, `0001_01_01_000002_create_jobs_table.php`) are present in `database/migrations/` and will execute correctly when migrating to a new database.

## 2. Migration Compatibility Audit

A thorough review of the existing `database/migrations/` directory was performed to identify potential MySQL incompatibilities:

- **Enums**: Columns such as `role` (users), `status` (posts), and `status` (comments) utilize `$table->enum()`. Laravel handles `enum` columns natively and maps them correctly to MySQL's `ENUM` type without issues.
- **Foreign Keys**: `dropForeign` is used via the array syntax (e.g., `$table->dropForeign(['category_id'])`). Because the foreign keys were initially created using `$table->foreignId()->constrained()`, Laravel will automatically resolve the default constraint name (e.g., `posts_category_id_foreign`), ensuring cross-compatibility between SQLite and MySQL.
- **String Lengths**: String columns like `slug` (unique) and `title` rely on the default length of `255`, well within MySQL's InnoDB indexing limits.

**Conclusion**: The existing migrations and seeders are fully compatible with MySQL 8.x. No modifications to source code, migrations, models, or business logic are required.

## 3. Implementation Steps

To transition the local development environment from SQLite to MySQL without disrupting the existing codebase, follow these steps:

### Phase 1: Database Provisioning
1. Open the Laragon MySQL terminal or local command prompt.
2. Create a new MySQL database for the project:
   ```sql
   CREATE DATABASE IF NOT EXISTS webblog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### Phase 2: Environment Configuration
Update the `.env` file to switch the driver and provide the correct credentials. Ensure the SQLite connection is removed or commented out.

**Modifications to `.env`:**
```dotenv
APP_NAME=BlogMNM
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webblog
DB_USERNAME=root
DB_PASSWORD=
```

### Phase 3: Cache Clearing
Clear Laravel's configuration cache to ensure the `.env` changes are picked up:
```bash
php artisan optimize:clear
```

### Phase 4: Migration & Seeding
Populate the new MySQL database using the existing migrations and the `DatabaseSeeder`.
```bash
php artisan migrate
php artisan db:seed
```

### Phase 5: Verification
1. Run `php artisan db:show` to verify the active connection is MySQL and all tables are present.
2. Run the test suite (`php artisan test`) to ensure tests pass with the new database driver.
3. Load the application in the browser and log in with the seeded Admin user to confirm session and cache drivers are functioning correctly over MySQL.
