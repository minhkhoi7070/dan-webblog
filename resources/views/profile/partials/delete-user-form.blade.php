<section id="account-actions">
    <!-- 1. Logout Row (macOS Clean Row) -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 macos-divider">
        <div>
            <h2 class="macos-card-title mb-0.5">
                Phiên làm việc
            </h2>
            <p class="macos-card-subtitle mb-0">
                Đăng xuất tài khoản của bạn trên thiết bị hiện tại.
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="macos-btn macos-btn-secondary">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>

    <!-- 2. Danger Zone: Delete Account (Subtle Minimal macOS Destructive Row) -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="fw-semibold mb-0.5" style="font-size: 14.5px; color: #FF3B30;">
                Xóa tài khoản vĩnh viễn
            </h3>
            <p class="macos-card-subtitle mb-0">
                Toàn bộ dữ liệu bài viết, bình luận sẽ bị gỡ bỏ vĩnh viễn và không thể khôi phục.
            </p>
        </div>

        <button
            type="button"
            class="macos-btn macos-btn-destructive"
            data-bs-toggle="modal"
            data-bs-target="#confirm-user-deletion"
        >
            Xóa tài khoản
        </button>
    </div>

    <!-- Confirmation Modal (Preserved Functionality) -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <h3 class="h5 fw-bold mb-2" style="color: #FF3B30;">
                Xác nhận xóa tài khoản vĩnh viễn
            </h3>

            <p class="small text-theme-secondary mb-3.5" style="font-size: 13px; line-height: 1.6;">
                Hành động này không thể hoàn tác. Mọi dữ liệu liên quan đến tài khoản của bạn sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận.
            </p>

            <div class="mb-3.5">
                <label for="delete_password" class="macos-label">
                    Mật khẩu xác nhận <span class="text-danger">*</span>
                </label>

                <div class="position-relative macos-password-wrap">
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        placeholder="Nhập mật khẩu của bạn..."
                        class="form-control macos-input pe-5 @error('password', 'userDeletion') is-invalid @enderror"
                    />
                    <button type="button"
                            class="btn-toggle-password position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent p-1 d-flex align-items-center justify-content-center"
                            data-target="delete_password"
                            aria-label="Hiện hoặc ẩn mật khẩu"
                            title="Hiện/Ẩn mật khẩu">
                        <svg class="eye-open" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-closed d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                @error('password', 'userDeletion')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 macos-divider">
                <button type="button" class="macos-btn macos-btn-secondary" data-bs-dismiss="modal">
                    Hủy bỏ
                </button>

                <button type="submit" class="macos-btn macos-btn-danger">
                    Tôi hiểu, xóa tài khoản
                </button>
            </div>
        </form>
    </x-modal>
</section>
