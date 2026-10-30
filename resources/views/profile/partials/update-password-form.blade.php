<section id="security">
    <header class="mb-3.5">
        <h2 class="macos-card-title mb-1">
            Bảo mật & Mật khẩu
        </h2>
        <p class="macos-card-subtitle mb-0">
            Đổi mật khẩu định kỳ để bảo vệ tài khoản của bạn.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="d-flex flex-column gap-3" id="password-update-form">
        @csrf
        @method('put')

        <!-- 1. Current Password (Vertical) -->
        <div>
            <label for="update_password_current_password" class="macos-label">
                Mật khẩu hiện tại <span class="text-danger">*</span>
            </label>
            <div class="position-relative macos-password-wrap">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Nhập mật khẩu hiện tại..."
                    class="form-control macos-input pe-5 @error('current_password', 'updatePassword') is-invalid @enderror"
                >
                <button type="button"
                        class="btn-toggle-password password-toggle-btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent p-1 d-flex align-items-center justify-content-center"
                        data-target="update_password_current_password"
                        aria-label="Hiện hoặc ẩn mật khẩu"
                        title="Hiện/Ẩn mật khẩu">
                    <svg class="eye-open eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-closed eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- 2. New Password (Vertical) -->
        <div>
            <label for="update_password_password" class="macos-label">
                Mật khẩu mới <span class="text-danger">*</span>
            </label>
            <div class="position-relative macos-password-wrap">
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Tối thiểu 8 ký tự..."
                    class="form-control macos-input pe-5 @error('password', 'updatePassword') is-invalid @enderror"
                >
                <button type="button"
                        class="btn-toggle-password password-toggle-btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent p-1 d-flex align-items-center justify-content-center"
                        data-target="update_password_password"
                        aria-label="Hiện hoặc ẩn mật khẩu"
                        title="Hiện/Ẩn mật khẩu">
                    <svg class="eye-open eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-closed eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <!-- Apple Style Minimalist Password Strength Meter -->
            <div class="mt-2" id="password-strength-box" style="display: none;">
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 11px;">
                    <span class="macos-hint-text">Độ an toàn:</span>
                    <span class="fw-medium" id="strength-label">Chưa nhập</span>
                </div>
                <div class="d-flex gap-1" style="height: 3px;">
                    <div class="strength-bar flex-fill rounded-pill" id="bar-1" style="background-color: rgba(0, 0, 0, 0.08);"></div>
                    <div class="strength-bar flex-fill rounded-pill" id="bar-2" style="background-color: rgba(0, 0, 0, 0.08);"></div>
                    <div class="strength-bar flex-fill rounded-pill" id="bar-3" style="background-color: rgba(0, 0, 0, 0.08);"></div>
                    <div class="strength-bar flex-fill rounded-pill" id="bar-4" style="background-color: rgba(0, 0, 0, 0.08);"></div>
                </div>
            </div>

            @error('password', 'updatePassword')
                <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- 3. Password Confirmation (Vertical) -->
        <div>
            <label for="update_password_password_confirmation" class="macos-label">
                Xác nhận mật khẩu mới <span class="text-danger">*</span>
            </label>
            <div class="position-relative macos-password-wrap">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Nhập lại mật khẩu mới..."
                    class="form-control macos-input pe-5 @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                >
                <button type="button"
                        class="btn-toggle-password password-toggle-btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent p-1 d-flex align-items-center justify-content-center"
                        data-target="update_password_password_confirmation"
                        aria-label="Hiện hoặc ẩn mật khẩu"
                        title="Hiện/Ẩn mật khẩu">
                    <svg class="eye-open eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg class="eye-closed eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <div id="password-match-hint" class="small mt-1" style="font-size: 11.5px; display: none;"></div>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button Right Aligned -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
            <div>
                @if (session('status') === 'password-updated')
                    <span id="password-saved-msg" class="macos-status-saved">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Mật khẩu đã được cập nhật thành công!
                    </span>
                @endif
            </div>

            <button type="submit" id="btn-update-password" class="macos-btn macos-btn-primary">
                Cập nhật mật khẩu
            </button>
        </div>
    </form>

</section>
