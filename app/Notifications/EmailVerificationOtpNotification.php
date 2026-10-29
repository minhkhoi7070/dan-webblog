<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtpNotification extends Notification
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

        return (new MailMessage)
            ->subject("[CloudBlog] Xác minh địa chỉ email: {$this->otp}")
            ->view('emails.verify-email-otp', [
                'userName' => $userName,
                'otp' => $this->otp,
                'emailTitle' => 'Xác minh địa chỉ email',
            ])
            ->text('emails.verify-email-otp-text', [
                'userName' => $userName,
                'otp' => $this->otp,
            ])
            ->greeting("Xin chào {$userName},")
            ->line('Cảm ơn bạn đã đăng ký tài khoản tại CloudBlog.')
            ->line('Mã xác thực OTP gồm 6 chữ số của bạn là:')
            ->line('**'.$this->otp.'**')
            ->line('Mã này có hiệu lực trong vòng 5 phút và tối đa 5 lần thử. Tuyệt đối không chia sẻ mã này cho bất kỳ ai.')
            ->line('Nếu bạn không thực hiện đăng ký tài khoản tại CloudBlog, vui lòng bỏ qua email này.')
            ->salutation("Trân trọng,\nĐội ngũ CloudBlog");
    }
}
