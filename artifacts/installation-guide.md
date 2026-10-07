# BlogMNM — Installation & Setup Guide

This guide walks through setting up and running **BlogMNM** from source in a local development environment (Windows / macOS / Linux) using PHP 8.3+, Composer, Node.js, and MySQL.

---

## 1. Prerequisites & System Requirements

- **PHP**: `^8.3` (required extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`)
- **Composer**: `^2.5`
- **Node.js**: `^18.x` or `^20.x` LTS and `npm`
- **Database Engine**: MySQL 8.4+ (or MariaDB 10.11+)
- **Local Server**: Laragon, XAMPP, Laravel Herd, or PHP built-in server

---

## 2. Step-by-Step Installation

### Step 1: Clone Repository
```bash
git clone https://github.com/minhkhoi7070/dan-webblog.git
cd webblog
```

### Step 2: Environment Configuration
Copy the sample environment file and adjust your database parameters:
```bash
cp .env.example .env
```
Ensure your database credentials in `.env` match your local MySQL configuration:
```ini
APP_NAME="BlogMNM"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webblog
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Install PHP Dependencies
```bash
composer install
```

### Step 4: Generate Application Encryption Key
```bash
php artisan key:generate
```

### Step 5: Database Migration & Seeding
Execute the database migrations and seed default taxonomy, users, and demo articles:
```bash
php artisan migrate:fresh --seed
```

### Step 6: Create Storage Symbolic Link
Link the public storage disk so uploaded post thumbnails are accessible to web browsers:
```bash
php artisan storage:link
```

### Step 7: Install Frontend Dependencies & Compile Assets
```bash
npm install
npm run build
```
*(For active local development with hot module reloading, run `npm run dev` in a separate terminal).*

### Step 8: Start the Application Server
```bash
php artisan serve --port=8000
```
Open your browser and navigate to: **`http://127.0.0.1:8000`**

---

## 3. Seeded Accounts & Credentials

The database seeder provisions three pre-configured accounts representing each user role:

| Role | Email | Password | Primary Interface |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@example.com` | `password` | `/admin` (Admin Dashboard & Moderation) |
| **Author** | `author@example.com` | `password` | `/posts/create` & `/author/posts/stats` |
| **Viewer** | `viewer@example.com` | `password` | `/` (Home Feed) & `/favorites` |

---

## 4. Verification & Testing

Verify that your installation satisfies all system requirements and passes all test suites:

```bash
# Run automated feature and unit tests
php artisan test --compact

# Check code formatting compliance
vendor/bin/pint --test
```

Expected result: **142 tests passed (514 assertions), 0 failures**.

---

## 5. Troubleshooting & Frequently Encountered Issues

1. **Vite Manifest Exception (`Unable to locate file in Vite manifest`)**:
   - Cause: Assets have not been compiled.
   - Solution: Execute `npm run build` or launch `npm run dev`.
2. **Missing Post Thumbnails (HTTP 404)**:
   - Cause: Missing symlink between `public/storage` and `storage/app/public`.
   - Solution: Run `php artisan storage:link`.
3. **Database Connection Refused**:
   - Cause: MySQL server is not running or port 3306 is blocked.
   - Solution: Start MySQL in your Laragon / XAMPP dashboard and verify credentials in `.env`.
4. **Permission Denied on Windows/Linux**:
   - Ensure the web server has write permissions to `storage/` and `bootstrap/cache/`:
     ```bash
     chmod -R 775 storage bootstrap/cache
     ```
