# BlogMNM

BlogMNM là website blog xây dựng bằng Laravel, Blade, Bootstrap, JavaScript và Vite. Ứng dụng hỗ trợ tài khoản độc giả, tác giả và quản trị viên; quản lý bài viết, duyệt bài, bình luận, theo dõi, lưu bài viết và xác thực email bằng OTP.

## Yêu cầu

- PHP 8.3 trở lên và Composer
- Node.js và npm
- MySQL

## Cài đặt

1. Cài các gói PHP và JavaScript:

   ```bash
   composer install
   npm install
   ```

2. Tạo tệp môi trường và khóa ứng dụng:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Trên Windows, có thể sao chép `.env.example` thành `.env` bằng File Explorer hoặc PowerShell.

3. Tạo database MySQL `blogmnm`, sau đó cập nhật `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` và `DB_PASSWORD` trong `.env`.

4. Tạo các bảng và dữ liệu ban đầu:

   ```bash
   php artisan migrate --seed
   ```

5. Biên dịch tài nguyên giao diện và khởi động website:

   ```bash
   npm run build
   php artisan serve
   ```

   Mở địa chỉ được Artisan hiển thị, mặc định là `http://127.0.0.1:8000`.

## Tài khoản quản trị demo

Seeder tạo tài khoản quản trị sau:

- Email: `admin@blogmnm.test`
- Mật khẩu demo: `password`

Đổi mật khẩu demo trước khi triển khai ứng dụng lên môi trường công khai. Email OTP được ghi vào log khi `MAIL_MAILER=log`; cấu hình dịch vụ email trong `.env` để gửi OTP thật.
