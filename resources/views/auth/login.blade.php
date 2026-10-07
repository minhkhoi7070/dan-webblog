@extends('layouts.base')
@section('title', 'Đăng nhập - ' . config('app.name', 'BlogMNM'))
@section('html_class', 'h-100')
@section('body_class', 'login-page-body')

@section('body')
    <!-- Accessibility Skip to Main Content Link -->
    <a class="visually-hidden-focusable" href="#main-content">
        Chuyển đến nội dung chính
    </a>

    <!-- Floating Theme Toggle in Auth View -->
    <div class="position-absolute top-0 end-0 m-3 z-3">
        <button type="button" onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
            aria-label="Chuyển đổi giao diện sáng/tối" title="Chuyển đổi giao diện sáng/tối"
            class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center login-glass-toggle"
            style="width: 42px; height: 42px;">
            <svg style="width: 18px; height: 18px;" class="theme-icon-light" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg style="width: 18px; height: 18px;" class="theme-icon-dark" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <!-- Main Fullscreen Atmospheric Glassmorphism Viewport -->
    <div class="login-page-wrapper">
        <main id="main-content" class="login-glass-card mx-auto">
            <!-- Brand Logo Header -->
            <div class="text-center mb-3 user-select-none">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none"
                    aria-label="Trang chủ BlogMNM">
                    <div class="d-flex align-items-center justify-content-center rounded-3 text-white fw-bold shadow-xs login-glass-logo-badge"
                        style="width: 38px; height: 38px; font-size: 19px; font-family: var(--font-serif, 'Lora', Georgia, serif);">
                        B
                    </div>
                    <span class="fs-4 fw-bold text-white m-0 tracking-tight"
                        style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                        Blog<span class="text-white opacity-75">MNM</span>
                    </span>
                </a>
            </div>

            <!-- Heading & Subtitle -->
            <div class="text-center mb-4">
                <h1 class="h4 fw-bold text-white mb-1.5 tracking-tight login-glass-title"
                    style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Chào mừng trở lại
                </h1>
                <p class="small text-white-75 mb-0">
                    Đăng nhập để tiếp tục với BlogMNM
                </p>
            </div>

            <!-- Session Status Alert -->
            <x-auth-session-status class="mb-3" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-medium text-white-90 mb-1.5 d-block">
                        Địa chỉ Email
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" placeholder="ten@example.com"
                        class="form-control form-control-glass @error('email') is-invalid @enderror"
                        aria-describedby="email-feedback">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <label for="password" class="form-label small fw-medium text-white-90 mb-0">
                            Mật khẩu
                        </label>

                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Nhập mật khẩu"
                        class="form-control form-control-glass @error('password') is-invalid @enderror"
                        aria-describedby="password-feedback">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div class="my-3 d-flex align-items-center justify-content-between">
                    <div class="form-check d-flex align-items-center gap-2 m-0">
                        <input id="remember_me" type="checkbox" class="form-check-input form-check-input-glass m-0"
                            name="remember">

                        <label for="remember_me"
                            class="form-check-label small text-white-80 user-select-none cursor-pointer">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="small text-white-75 hover-white text-decoration-underline">
                            Quên mật khẩu?
                        </a>
                    @endif
                </div>
               

                <!-- Submit Button -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-glass-primary w-100 py-2.5 fw-semibold shadow-sm">
                        Đăng nhập
                    </button>
                </div>

                <!-- Register Link CTA -->
                @if (Route::has('register'))
                    <div class="pt-3 mt-4 text-center login-glass-divider small text-white-75">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}" class="text-white fw-semibold text-decoration-underline ms-1">
                            Đăng ký ngay
                        </a>
                    </div>
                @endif
            </form>
        </main>

        <!-- Back to Home Link -->
        <div class="mt-3 text-center">
            <a href="{{ route('home') }}"
                class="small text-white-80 hover-white text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại BlogMNM
            </a>
        </div>
    </div>

    <!-- Global Bootstrap Toast Notifications -->
    <x-toast />
@endsection
