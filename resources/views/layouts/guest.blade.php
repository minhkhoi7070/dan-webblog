<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="dark">
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[var(--color-text)] bg-[var(--color-bg)] min-h-screen flex flex-col justify-center items-center px-4 py-8 antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200 relative">
        <!-- Floating Theme Toggle in Auth View -->
        <div class="absolute top-4 right-4">
            <button type="button"
                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                    aria-label="Chuyển đổi giao diện sáng/tối"
                    title="Chuyển đổi giao diện sáng/tối"
                    class="p-2.5 rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)] transition shadow-sm">
                <svg class="w-5 h-5 hidden [html[data-theme='dark']_&]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg class="w-5 h-5 block [html[data-theme='dark']_&]:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>

        <!-- Brand Logo Header -->
        <div class="mb-6 flex flex-col items-center select-none">
            <a href="/" class="flex flex-col items-center gap-2 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-indigo-600/30 group-hover:scale-105 transition-transform" data-theme-preserve>
                    B
                </div>
                <span class="text-xl font-black tracking-tight text-[var(--color-text)] mt-1">
                    Blog<span class="text-indigo-400">MNM</span>
                </span>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="w-full sm:max-w-md p-6 sm:p-8 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-3xl shadow-xl">
            {{ $slot }}
        </div>

        <!-- Footer Back Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition">
                &larr; Quay lại BlogMNM
            </a>
        </div>
    </body>
</html>
