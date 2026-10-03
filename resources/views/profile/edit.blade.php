<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="label-uppercase text-accent" style="font-size: 10px; letter-spacing: 0.08em;">
                        Tùy biến & Cài đặt
                    </span>
                    <span class="text-theme-muted" style="font-size: 11px;">&bull;</span>
                    <span class="small text-theme-muted" style="font-size: 12px;">BlogMNM Preferences</span>
                </div>
                <h1 class="h4 fw-bold text-theme mb-0 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Cài đặt tài khoản
                </h1>
            </div>

            <!-- Notion-style Quick Section Jumps -->
            <nav class="d-flex flex-wrap align-items-center gap-1.5" aria-label="Điều hướng cài đặt">
                <a href="#appearance" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary hover-accent" style="font-size: 12px;">
                    Giao diện
                </a>
                <a href="#reading" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary hover-accent" style="font-size: 12px;">
                    Trải nghiệm đọc
                </a>
                <a href="#profile-info" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary hover-accent" style="font-size: 12px;">
                    Hồ sơ
                </a>
                <a href="#security" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary hover-accent" style="font-size: 12px;">
                    Bảo mật
                </a>
            </nav>
        </div>
    </x-slot>

    <!-- Main Settings Body (Centered Notion/Readwise Document Feel) -->
    <div class="mx-auto d-flex flex-column gap-4 pb-5" style="max-width: 780px;">
        <!-- 1. Appearance / Theme Settings -->
        <div class="settings-section-card">
            @include('profile.partials.appearance-settings')
        </div>

        <!-- 2. Reading Experience Settings (Font Size & Content Width) -->
        <div class="settings-section-card">
            @include('profile.partials.reading-settings')
        </div>

        <!-- 3. Profile Information -->
        <div class="settings-section-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- 4. Security & Password Update -->
        <div class="settings-section-card">
            @include('profile.partials.update-password-form')
        </div>

        <!-- 5. Session & Danger Zone (Logout + Delete Account) -->
        <div class="settings-section-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

