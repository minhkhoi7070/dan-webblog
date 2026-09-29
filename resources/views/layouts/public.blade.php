<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'BlogMNM') }} - Khám phá tin tức & góc nhìn công nghệ</title>

    <!-- Google Fonts: Inter -->
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

    <!-- Scripts and Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col sm:flex-row min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] font-sans antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200">

    <!-- Desktop / Tablet Left Sidebar Navigation -->
    <x-sidebar />

    <!-- Mobile Top Navigation Header -->
    <header class="sm:hidden flex items-center justify-between px-4 py-3 sticky top-0 bg-[var(--color-surface)]/90 backdrop-blur-md z-40 border-b border-[var(--color-border)] select-none">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="BlogMNM">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-600/20" data-theme-preserve>
                B
            </div>
            <span class="text-lg font-black tracking-tight text-[var(--color-text)]">
                Blog<span class="text-indigo-400">MNM</span>
            </span>
        </a>

        <div class="flex items-center gap-2">
            <!-- Mobile Quick Theme Toggle -->
            <button type="button"
                    onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                    aria-label="Chuyển đổi giao diện sáng/tối"
                    title="Chuyển đổi giao diện sáng/tối"
                    class="p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:bg-[var(--color-surface-hover)] transition">
                <svg class="w-5 h-5 hidden [html[data-theme='dark']_&]:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg class="w-5 h-5 block [html[data-theme='dark']_&]:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            @auth
                <a href="{{ route('profile.show') }}" class="shrink-0" aria-label="Trang cá nhân">
                    <x-avatar :user="Auth::user()" size="sm" />
                </a>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-[var(--color-text)] hover:opacity-80 transition">
                    Đăng nhập
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 sm:ml-20 lg:ml-64 mb-16 sm:mb-0 w-full min-h-screen flex flex-col">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <x-mobile-nav />

</body>
</html>
