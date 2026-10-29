<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : Địa chỉ email người nhận để kiểm tra}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi một email kiểm tra kết nối dịch vụ thư thật (SMTP/Transactional)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->argument('email');

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Địa chỉ email '{$recipient}' không đúng định dạng.");

            return self::FAILURE;
        }

        $mailer = config('mail.default');
        $host = config("mail.mailers.{$mailer}.host");
        $port = config("mail.mailers.{$mailer}.port");
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info('--- THÔNG TIN CẤU HÌNH GỬI THƯ ---');
        $this->line("Mailer Driver : {$mailer}");
        if ($host) {
            $this->line("SMTP Host     : {$host}:{$port}");
        }
        $this->line("Người gửi     : {$fromName} <{$fromAddress}>");
        $this->line("Người nhận    : {$recipient}");
        $this->line('----------------------------------');

        if ($mailer === 'log') {
            $this->warn('CẢNH BÁO: Dịch vụ đang ở chế độ MAIL_MAILER=log.');
            $this->warn('Thư sẽ được ghi vào tệp storage/logs/laravel.log thay vì gửi ra hộp thư Internet.');
        }

        $this->info("Đang gửi email kiểm tra đến {$recipient}...");

        try {
            Mail::raw("Xin chào,\n\nĐây là email kiểm tra kết nối dịch vụ gửi thư thật từ CloudBlog.\nNếu bạn nhận được email này trong hộp thư đến (hoặc thư mục Spam), nghĩa là cấu hình SMTP đã hoạt động chính xác!\n\nThời gian gửi: ".now()->toDateTimeString()."\nTrân trọng,\nĐội ngũ CloudBlog", function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('[CloudBlog] Kiểm tra kết nối dịch vụ gửi thư thành công');
            });

            $this->info("✔ THÀNH CÔNG: Email kiểm tra đã được gửi đến {$recipient}.");
            $this->line('Vui lòng mở hộp thư (bao gồm cả thư mục Spam/Quảng cáo) để kiểm tra kết quả.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('✖ THẤT BẠI: Không thể gửi email do lỗi:');
            $this->line($e->getMessage());
            $this->newLine();
            $this->comment('GỢI Ý KHẮC PHỤC:');
            $this->line('1. Nếu dùng Gmail: Phải dùng App Password (16 ký tự), không dùng mật khẩu Gmail thông thường.');
            $this->line('2. Nếu dùng Brevo: Kiểm tra SMTP Key và bảo đảm MAIL_FROM_ADDRESS là email đã verify sender trong Brevo.');
            $this->line('3. Kiểm tra cổng mạng (port 587/465) và tường lửa/antivirus không chặn kết nối SMTP.');

            return self::FAILURE;
        }
    }
}
