<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BlogMNM') }} - {{ $title ?? 'Đăng nhập & Đăng ký' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Early Theme Initialization to Prevent Flash of Unstyled Theme (FOUC) -->
        <script>
            (function() {
                try {
                    var theme = localStorage.getItem('blogmnm-theme') || 'dark';
                    document.documentElement.setAttribute('data-theme', theme);
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch(e) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="d-flex flex-column justify-content-center align-items-center min-vh-100 px-3 py-5 bg-theme text-theme position-relative">
        <!-- Floating Theme Toggle in Auth View -->
        <div class="position-absolute top-0 end-0 m-3">
            <button type="button"
                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                    aria-label="Chuyển đổi giao diện sáng/tối"
                    title="Chuyển đổi giao diện sáng/tối"
                    class="btn btn-sm btn-outline-theme rounded-circle p-2 d-flex align-items-center justify-content-center"
                    style="width: 38px; height: 38px;">
                <svg style="width: 18px; height: 18px;" id="guest-theme-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg style="width: 18px; height: 18px;" id="guest-theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
            <script>
                function updateGuestThemeIcons() {
                    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    const light = document.getElementById('guest-theme-icon-light');
                    const dark = document.getElementById('guest-theme-icon-dark');
                    if (light && dark) {
                        light.style.display = isDark ? 'block' : 'none';
                        dark.style.display = isDark ? 'none' : 'block';
                    }
                }
                window.addEventListener('DOMContentLoaded', updateGuestThemeIcons);
                new MutationObserver(updateGuestThemeIcons).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
            </script>
        </div>

        <!-- Brand Logo Header -->
        <div class="mb-4 text-center user-select-none">
            <a href="/" class="d-inline-flex flex-column align-items-center text-decoration-none">
                <div class="brand-gradient rounded-4 d-flex align-items-center justify-content-center text-white fw-bold shadow" style="width: 52px; height: 52px; font-size: 24px;" data-theme-preserve>
                    B
                </div>
                <span class="fs-4 fw-bold text-theme mt-2">
                    Blog<span style="color: #818cf8;">MNM</span>
                </span>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="card shadow-lg p-4 p-sm-5 border-theme w-100" style="max-width: 440px; border-radius: 1.5rem;">
            {{ $slot }}
        </div>

        <!-- Footer Back Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="small fw-semibold text-theme-secondary text-decoration-none">
                &larr; Quay lại BlogMNM
            </a>
        </div>
        <!-- Global Bootstrap Toast Notifications -->
        <x-toast />
    </body>
</html>
