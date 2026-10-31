<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $userName = $notifiable->name ?? 'bạn';
        $userEmail = $notifiable->email ?? '';
        $changedAt = now()->format('d/m/Y H:i:s');

        return (new MailMessage)
            ->subject('[CloudBlog] Mật khẩu tài khoản đã được thay đổi thành công')
            ->view('emails.password-changed', [
                'userName' => $userName,
                'userEmail' => $userEmail,
                'changedAt' => $changedAt,
                'emailTitle' => 'Mật khẩu đã được thay đổi thành công',
            ])
            ->text('emails.password-changed-text', [
                'userName' => $userName,
                'userEmail' => $userEmail,
                'changedAt' => $changedAt,
            ])
            ->greeting("Xin chào {$userName},")
            ->line("Mật khẩu cho tài khoản CloudBlog ({$userEmail}) của bạn vừa được thay đổi thành công.")
            ->line("Thời gian: {$changedAt} (GMT+7).")
            ->line('Nếu chính bạn đã thực hiện thao tác này, bạn có thể hoàn toàn an tâm bỏ qua thông báo này.')
            ->line('Nếu bạn KHÔNG thực hiện thay đổi này, hãy liên hệ ngay với quản trị viên hoặc sử dụng chức năng quên mật khẩu ngay lập tức để bảo vệ tài khoản.')
            ->salutation("Trân trọng,\nĐội ngũ CloudBlog");
    }
}
