<section id="security">
    <header class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                Bảo mật & Mật khẩu
            </h2>
        </div>
        <p class="small text-theme-muted mb-0" style="font-size: 13px;">
            Đổi mật khẩu định kỳ và sử dụng chuỗi ký tự phức tạp để giữ an toàn cho tài khoản.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="d-flex flex-column gap-3.5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                Mật khẩu hiện tại <span class="text-danger">*</span>
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                placeholder="••••••••"
                class="form-control form-control-editorial rounded-3 @error('current_password', 'updatePassword') is-invalid @enderror"
                style="font-size: 14px;"
            >
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                Mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                placeholder="Tối thiểu 8 ký tự..."
                class="form-control form-control-editorial rounded-3 @error('password', 'updatePassword') is-invalid @enderror"
                style="font-size: 14px;"
            >
            @error('password', 'updatePassword')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                Xác nhận mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu mới..."
                class="form-control form-control-editorial rounded-3 @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                style="font-size: 14px;"
            >
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-2 fw-semibold" style="font-size: 13px;">
                Cập nhật mật khẩu
            </button>

            @if (session('status') === 'password-updated')
                <span id="password-saved-msg" class="small text-success fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Mật khẩu đã được cập nhật thành công!
                </span>
                <script>
                    setTimeout(() => {
                        const el = document.getElementById('password-saved-msg');
                        if (el) el.style.display = 'none';
                    }, 3000);
                </script>
            @endif
        </div>
    </form>
</section>

