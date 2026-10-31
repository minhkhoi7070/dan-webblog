<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('[BlogMNM] Xác minh địa chỉ email của bạn')
                ->greeting('Xin chào '.$notifiable->name.',')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại BlogMNM. Vui lòng bấm vào nút bên dưới để xác minh địa chỉ email của bạn.')
                ->action('Xác minh địa chỉ email', $url)
                ->line('Liên kết xác minh này sẽ hết hạn sau 60 phút.')
                ->line('Nếu bạn không thực hiện đăng ký tài khoản này, vui lòng bỏ qua email.')
                ->salutation("Trân trọng,\nĐội ngũ BlogMNM");
        });
    }
}
