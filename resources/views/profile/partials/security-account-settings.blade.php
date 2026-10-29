<section id="security">
    <header class="mb-4">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0">
                Bảo mật & tài khoản
            </h2>
        </div>
    </header>

    <div class="d-flex flex-column">
        <!-- 1. Hàng Đổi mật khẩu (Collapsible) -->
        <div class="settings-row-interactive py-2.5 px-2 rounded-3 border-theme-bottom"
             role="button"
             tabindex="0"
             data-bs-toggle="collapse"
             data-bs-target="#change-password-collapse"
             aria-expanded="{{ ($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated') ? 'true' : 'false' }}"
             aria-controls="change-password-collapse"
             aria-label="Mở biểu mẫu đổi mật khẩu">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <span class="settings-row-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </span>
                    <div class="min-w-0">
                        <span class="fw-semibold text-theme d-block" style="font-size: 14px;">Đổi mật khẩu</span>
                        <span class="text-theme-muted d-none d-sm-block text-truncate" style="font-size: 12px;">Cập nhật mật khẩu bảo vệ tài khoản</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @if (session('status') === 'password-updated')
                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 11px;">
                            Đã cập nhật
                        </span>
                    @endif
                    <span class="settings-chevron">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Biểu mẫu Thay đổi mật khẩu (Ẩn/Hiện dạng Collapsible) -->
        <div class="collapse {{ ($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated') ? 'show' : '' }}" id="change-password-collapse">
            <div class="settings-collapse-panel">
                <form id="password-update-form"
                      method="post"
                      action="{{ route('password.update') }}"
                      class="d-flex flex-column gap-3"
                      data-has-errors="{{ $errors->updatePassword->isNotEmpty() ? 'true' : 'false' }}">
                    @csrf
                    @method('put')

                    <!-- 1. Mật khẩu hiện tại -->
                    <div>
                        <label for="update_password_current_password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                            Mật khẩu hiện tại <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input
                                id="update_password_current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Nhập mật khẩu hiện tại"
                                class="form-control form-control-editorial rounded-3 pe-5 @error('current_password', 'updatePassword') is-invalid @enderror"
                                style="height: 44px; min-height: 44px; font-size: 14px;"
                            >
                            <button type="button"
                                    class="password-toggle-btn"
                                    data-target="update_password_current_password"
                                    aria-label="Hiện mật khẩu"
                                    tabindex="-1">
                                <svg class="eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Mật khẩu mới -->
                    <div>
                        <label for="update_password_password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                            Mật khẩu mới <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input
                                id="update_password_password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Tối thiểu 8 ký tự"
                                class="form-control form-control-editorial rounded-3 pe-5 @error('password', 'updatePassword') is-invalid @enderror"
                                style="height: 44px; min-height: 44px; font-size: 14px;"
                            >
                            <button type="button"
                                    class="password-toggle-btn"
                                    data-target="update_password_password"
                                    aria-label="Hiện mật khẩu"
                                    tabindex="-1">
                                <svg class="eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 3. Xác nhận mật khẩu mới -->
                    <div>
                        <label for="update_password_password_confirmation" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                            Xác nhận mật khẩu mới <span class="text-danger">*</span>
                        </label>
                        <div class="password-input-group">
                            <input
                                id="update_password_password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Nhập lại mật khẩu mới"
                                class="form-control form-control-editorial rounded-3 pe-5 @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                style="height: 44px; min-height: 44px; font-size: 14px;"
                            >
                            <button type="button"
                                    class="password-toggle-btn"
                                    data-target="update_password_password_confirmation"
                                    aria-label="Hiện mật khẩu"
                                    tabindex="-1">
                                <svg class="eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nút thao tác -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5 pt-1.5">
                        @if (session('status') === 'password-updated')
                            <span class="small text-success fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Mật khẩu đã được cập nhật thành công!
                            </span>
                        @else
                            <span></span>
                        @endif

                        <button type="submit"
                                id="btn-update-password"
                                class="btn btn-editorial-primary rounded-pill px-4 py-2 fw-semibold ms-auto d-inline-flex align-items-center justify-content-center"
                                style="min-height: 40px; font-size: 13.5px;">
                            <span>Cập nhật mật khẩu</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Hàng Phiên đăng nhập -->
        <div class="settings-row">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <span class="settings-row-icon">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </span>
                <div class="min-w-0">
                    <span class="fw-semibold text-theme d-block" style="font-size: 14px;">Phiên đăng nhập</span>
                    <span class="text-theme-muted d-none d-sm-block text-truncate" style="font-size: 12px;">Đăng xuất tài khoản khỏi thiết bị này</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0 flex-shrink-0">
                @csrf
                <button type="submit"
                        class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5"
                        style="font-size: 13px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </div>

        <!-- 3. Hàng Xóa tài khoản -->
        <div class="settings-row">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <span class="settings-row-icon bg-danger-subtle text-danger border-danger border-opacity-25">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </span>
                <div class="min-w-0">
                    <span class="fw-semibold text-danger d-block" style="font-size: 14px;">Xóa tài khoản</span>
                    <span class="text-theme-muted d-none d-sm-block text-truncate" style="font-size: 12px;">Xóa vĩnh viễn tài khoản và toàn bộ dữ liệu</span>
                </div>
            </div>

            <button type="button"
                    class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold flex-shrink-0"
                    style="font-size: 13px;"
                    data-bs-toggle="modal"
                    data-bs-target="#confirm-user-deletion">
                Xóa tài khoản
            </button>
        </div>
    </div>

    <!-- Modal xác nhận xóa tài khoản an toàn -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-1">
            @csrf
            @method('delete')

            <div class="d-flex align-items-center gap-2.5 mb-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger p-2 flex-shrink-0" style="width: 36px; height: 36px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </span>
                <h3 class="h5 fw-bold text-theme m-0">
                    Xác nhận xóa tài khoản
                </h3>
            </div>

            <p class="small text-theme-muted my-2.5" style="font-size: 13px; line-height: 1.6;">
                Sau khi xóa, tài khoản và toàn bộ bài viết, bình luận và dữ liệu liên quan sẽ bị xóa vĩnh viễn và không thể khôi phục. Vui lòng nhập mật khẩu tài khoản của bạn để xác nhận hành động này.
            </p>

            <div class="my-3">
                <label for="delete_account_password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                    Mật khẩu xác nhận <span class="text-danger">*</span>
                </label>
                <div class="password-input-group">
                    <input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        placeholder="Nhập mật khẩu để xác nhận"
                        class="form-control form-control-editorial rounded-3 pe-5 @error('password', 'userDeletion') is-invalid @enderror"
                        style="height: 44px; min-height: 44px; font-size: 14px;"
                    />
                    <button type="button"
                            class="password-toggle-btn"
                            data-target="delete_account_password"
                            aria-label="Hiện mật khẩu"
                            tabindex="-1">
                        <svg class="eye-open-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-closed-icon d-none" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
                </div>
                @error('password', 'userDeletion')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-theme-top">
                <button type="button" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5" data-bs-dismiss="modal">
                    Hủy
                </button>

                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-semibold">
                    Xác nhận xóa tài khoản
                </button>
            </div>
        </form>
    </x-modal>
</section>

