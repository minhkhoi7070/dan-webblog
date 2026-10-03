@extends('layouts.base')
@section('title', config('app.name', 'BlogMNM') . ' - ' . ($title ?? 'Đăng nhập & Đăng ký'))
@section('html_class', 'h-100')
@section('body_class', 'd-flex flex-column justify-content-center align-items-center min-vh-100 px-3 py-5 bg-theme text-theme position-relative')

@section('body')
    <!-- Accessibility Skip to Main Content Link -->
    <a class="visually-hidden-focusable" href="#main-content">
        Skip to main content
    </a>

    <!-- Floating Theme Toggle in Auth View -->
    <div class="position-absolute top-0 end-0 m-3">
        <button type="button"
                onclick="window.BlogMNMTheme && window.BlogMNMTheme.toggle()"
                aria-label="Chuyển đổi giao diện sáng/tối"
                title="Chuyển đổi giao diện sáng/tối"
                class="btn btn-sm btn-outline-theme rounded-circle p-0 d-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px;">
            <svg style="width: 20px; height: 20px;" class="theme-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg style="width: 20px; height: 20px;" class="theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <!-- Main Landmark -->
    <main id="main-content" class="w-100 d-flex flex-column align-items-center">
        <!-- Brand Logo Header -->
        <div class="mb-4 text-center user-select-none">
            <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2.5 text-decoration-none group">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-theme-surface border border-theme text-theme fw-bold shadow-xs" style="width: 40px; height: 40px; font-size: 20px; font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    B
                </div>
                <span class="fs-4 fw-bold text-theme m-0 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Blog<span class="text-accent">MNM</span>
                </span>
            </a>
        </div>

        <!-- Auth Card Container -->
        <div class="auth-wrapper px-2">
            <div class="auth-card-editorial w-100">
                {{ $slot }}
            </div>
        </div>
    </main>

        <!-- Footer Back Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại trang chủ BlogMNM
            </a>
        </div>
        <!-- Global Bootstrap Toast Notifications -->
        <x-toast />
@endsection
