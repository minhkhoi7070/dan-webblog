<x-app-layout>
    <x-slot name="header">
        <div class="macos-profile-header mx-auto d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="macos-title mb-0">
                    Cài đặt tài khoản
                </h1>
            </div>

            <!-- Settings navigation -->
            <div class="macos-segmented-bar" role="tablist" aria-label="Điều hướng cài đặt">
                <a href="#profile-info" class="macos-segmented-tab active" id="tab-macos-profile" role="tab">
                    Hồ sơ
                </a>
                <a href="#security" class="macos-segmented-tab" id="tab-macos-security" role="tab">
                    Bảo mật
                </a>
                <a href="#appearance" class="macos-segmented-tab" id="tab-macos-appearance" role="tab">
                    Giao diện
                </a>
                <a href="#reading" class="macos-segmented-tab" id="tab-macos-reading" role="tab">
                    Trải nghiệm đọc
                </a>
            </div>
        </div>
    </x-slot>

    <div class="macos-profile-page mx-auto pb-5">
        @php
            $passwordPanelOpen = $errors->updatePassword->isNotEmpty() || session('status') === 'password-updated';
        @endphp
        <div class="d-flex flex-column settings-card-stack">
            <!-- 1. Apple Account Style Profile Group -->
            <div class="macos-card" id="profile-info-group">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- 2. Password & Security Group -->
            <div class="macos-card" id="security-group">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <h2 class="macos-card-title mb-0">Bảo mật & tài khoản</h2>
                    <button type="button" class="macos-btn macos-btn-secondary" data-bs-toggle="collapse" data-bs-target="#change-password-collapse" aria-controls="change-password-collapse" aria-expanded="{{ $passwordPanelOpen ? 'true' : 'false' }}">
                        Đổi mật khẩu
                    </button>
                </div>
                <div class="collapse{{ $passwordPanelOpen ? ' show' : '' }}" id="change-password-collapse">
                    <div class="mt-3 pt-3 macos-divider">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- 3. Appearance Settings Group -->
            <div class="macos-card" id="appearance-group">
                @include('profile.partials.appearance-settings')
            </div>

            <!-- 4. Reading preferences preserved from feature/sua -->
            <div class="macos-card" id="reading-group">
                @include('profile.partials.reading-settings')
            </div>

            <!-- 5. Session & Danger Zone Group -->
            <div class="macos-card" id="account-actions-group">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

</x-app-layout>
