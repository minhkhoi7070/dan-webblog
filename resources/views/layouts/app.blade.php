<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BlogMNM') }} - Quản lý tài khoản</title>

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="d-flex flex-column flex-sm-row min-vh-100 bg-theme text-theme transition-colors">
        <!-- Desktop / Tablet Sidebar -->
        <x-sidebar />

        <!-- Mobile Top Navigation Header -->
        <header class="d-sm-none d-flex align-items-center justify-content-between px-3 py-2 sticky-top bg-theme-surface border-theme-bottom z-3 user-select-none">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" aria-label="BlogMNM">
                <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded shadow-sm" style="width: 32px; height: 32px; font-size: 14px; background: linear-gradient(to top right, #4f46e5, #8b5cf6);" data-theme-preserve>
                    B
                </div>
                <span class="fs-5 fw-bold text-theme mb-0">
                    Blog<span style="color: #818cf8;">MNM</span>
                </span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <button type="button"
                        onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                        aria-label="Chuyển đổi giao diện sáng/tối"
                        title="Chuyển đổi giao diện sáng/tối"
                        class="btn btn-sm btn-link text-theme-secondary text-decoration-none">
                    <svg class="d-none" style="width: 20px; height: 20px;" id="theme-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="d-block" style="width: 20px; height: 20px;" id="theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <script>
                    function updateThemeIcons() {
                        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                        document.getElementById('theme-icon-light').classList.toggle('d-block', isDark);
                        document.getElementById('theme-icon-light').classList.toggle('d-none', !isDark);
                        document.getElementById('theme-icon-dark').classList.toggle('d-block', !isDark);
                        document.getElementById('theme-icon-dark').classList.toggle('d-none', isDark);
                    }
                    window.addEventListener('DOMContentLoaded', updateThemeIcons);
                    // Add observer to catch theme changes
                    new MutationObserver(updateThemeIcons).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
                </script>

                @auth
                    <x-avatar :user="Auth::user()" size="sm" />
                @endauth
            </div>
        </header>

        <div class="flex-grow-1 w-100 min-vh-100 d-flex flex-column" style="margin-bottom: 64px; padding-left: 0;">
            <style>
                @media (min-width: 576px) { .flex-grow-1 { margin-bottom: 0 !important; margin-left: 80px !important; } }
                @media (min-width: 992px) { .flex-grow-1 { margin-left: 256px !important; } }
            </style>
            <div class="container-fluid py-4" style="max-width: 1024px;">
                @isset($header)
                    <div class="mb-4 pb-3 border-theme-bottom">
                        {{ $header }}
                    </div>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Mobile Bottom Nav -->
        <x-mobile-nav />

        <!-- Global Bootstrap Confirmation Modal -->
        <x-confirm-modal />

        <!-- Global Bootstrap Toast Notifications -->
        <x-toast />
    </body>
</html>
