<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtpNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $otp) {}

    /**
     * Get the notification's delivery channels.
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

        return (new MailMessage)
            ->subject("[CloudBlog] Mã OTP đặt lại mật khẩu: {$this->otp}")
            ->view('emails.reset-password-otp', [
                'userName' => $userName,
                'userEmail' => $userEmail,
                'otp' => $this->otp,
                'emailTitle' => 'Mã OTP đặt lại mật khẩu',
            ])
            ->text('emails.reset-password-otp-text', [
                'userName' => $userName,
                'userEmail' => $userEmail,
                'otp' => $this->otp,
            ])
            ->greeting("Xin chào {$userName},")
            ->line("Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản CloudBlog ({$userEmail}) của bạn.")
            ->line('Mã xác thực OTP gồm 6 chữ số để đặt lại mật khẩu là:')
            ->line('**'.$this->otp.'**')
            ->line('Mã này có hiệu lực trong vòng 5 phút và tối đa 5 lần thử. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.')
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn được bảo vệ an toàn.')
            ->salutation("Trân trọng,\nĐội ngũ CloudBlog");
    }
}
