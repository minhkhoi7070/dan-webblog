<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BlogMNM') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-body text-body d-flex flex-column align-items-center justify-content-center min-vh-100 p-4">
        <div class="card p-5 border shadow-sm text-center" style="max-width: 480px; border-radius: 1.5rem;">
            <div class="rounded-circle brand-gradient text-white mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-weight: 800; font-size: 1.25rem;">
                M
            </div>
            <h1 class="h4 fw-bold mb-2">BlogMNM</h1>
            <p class="small text-secondary mb-4">Nền tảng blog công nghệ và tin tức hiện đại.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-4 fw-semibold small">
                    Trang chủ
                </a>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold small">
                        Đăng nhập
                    </a>
                @endguest
            </div>
        </div>
    </body>
</html>
