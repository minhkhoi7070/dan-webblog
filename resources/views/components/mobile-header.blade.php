<!-- Mobile Top Navigation Header -->
<header {{ $attributes->merge(['class' => 'd-sm-none d-flex align-items-center justify-content-between px-3 sticky-top bg-theme-surface border-theme-bottom z-3 user-select-none']) }} style="height: 56px;">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" aria-label="BlogMNM Trang chủ">
        <div class="brand-gradient rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" style="width: 32px; height: 32px; font-size: 14px;" data-theme-preserve>
            B
        </div>
        <span class="fs-5 fw-bold text-theme mb-0 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
            Blog<span class="text-accent">MNM</span>
        </span>
    </a>

    <!-- Right Actions -->
    <div class="d-flex align-items-center gap-1">
        <!-- Mobile Quick Theme Toggle -->
        <button type="button"
                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                aria-label="Chuyển đổi giao diện sáng/tối"
                title="Chuyển đổi giao diện sáng/tối"
                class="btn btn-sm btn-link text-theme-secondary text-decoration-none p-0 d-flex align-items-center justify-content-center rounded-3"
                style="width: 44px; height: 44px;">
            <svg class="theme-icon-light" style="width: 20px; height: 20px;" id="theme-icon-light-pub" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg class="theme-icon-dark" style="width: 20px; height: 20px;" id="theme-icon-dark-pub" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        @auth
            <a href="{{ route('profile.show') }}"
               class="d-flex align-items-center justify-content-center text-decoration-none rounded-circle ms-1"
               style="width: 44px; height: 44px;"
               title="Trang cá nhân: {{ Auth::user()->name }}"
               aria-label="Trang cá nhân">
                <x-avatar :user="Auth::user()" size="sm" />
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-editorial-primary rounded-pill px-3 py-1 ms-1" title="Đăng nhập">
                Đăng nhập
            </a>
        @endauth
    </div>
</header>
