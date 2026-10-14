<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="@yield('html_class', 'h-full')" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', ($title ?? config('app.name', 'BlogMNM')) . ' - Khám phá tin tức & góc nhìn công nghệ')</title>

    <!-- Google Fonts: Inter, Lora, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Early Theme & Reading Preferences Initialization to Prevent FOUC -->
    <script>
        (function() {
            try {
                var mode = localStorage.getItem('blogmnm-theme') || 'dark';
                var resolved = mode;
                if (mode === 'system') {
                    resolved = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', resolved);
                document.documentElement.setAttribute('data-theme-mode', mode);
                if (resolved === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                var readingFont = localStorage.getItem('blogmnm-reading-font') || 'md';
                document.documentElement.setAttribute('data-reading-font', readingFont);

                var readingWidth = localStorage.getItem('blogmnm-reading-width') || 'standard';
                document.documentElement.setAttribute('data-reading-width', readingWidth);
            } catch(e) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Scripts and Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="@yield('body_class', 'd-flex flex-column flex-sm-row min-vh-100 bg-theme text-theme transition-colors')">
    @yield('body')
</body>
</html>
