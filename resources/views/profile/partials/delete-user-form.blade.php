<section id="account-session">
    <!-- Logout Action Card -->
    <div class="mb-4 pb-4 border-theme-bottom">
        <header class="mb-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-theme-secondary p-1.5" style="width: 28px; height: 28px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </span>
                <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Phiên đăng nhập
                </h2>
            </div>
            <p class="small text-theme-muted mb-0" style="font-size: 13px;">
                Đăng xuất khỏi tài khoản trên thiết bị này khi bạn hoàn tất phiên làm việc.
            </p>
        </header>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-theme rounded-pill px-3.5 py-2 text-theme-secondary hover-accent fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 13px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                <span>Đăng xuất tài khoản</span>
            </button>
        </form>
    </div>

    <!-- Danger Zone: Delete Account -->
    <div>
        <header class="mb-3">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-danger-subtle border border-danger border-opacity-25 text-danger p-1.5" style="width: 28px; height: 28px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </span>
                <h2 class="h5 fw-bold text-danger m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Vùng nguy hiểm: Xóa tài khoản
                </h2>
            </div>
            <p class="small text-theme-muted mb-0" style="font-size: 13px;">
                Khi tài khoản bị xóa, toàn bộ dữ liệu, bài viết và tương tác của bạn sẽ bị gỡ bỏ vĩnh viễn và không thể phục hồi.
            </p>
        </header>

        <button
            type="button"
            class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-2 fw-semibold"
            style="font-size: 13px;"
            data-bs-toggle="modal"
            data-bs-target="#confirm-user-deletion"
        >
            Xóa tài khoản vĩnh viễn
        </button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-3">
                @csrf
                @method('delete')

                <h3 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Bạn có chắc chắn muốn xóa tài khoản?
                </h3>

                <p class="small text-theme-muted mt-2 mb-3" style="font-size: 13px; line-height: 1.6;">
                    Sau khi tài khoản bị xóa, toàn bộ dữ liệu liên quan sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu tài khoản của bạn để xác nhận hành động này.
                </p>

                <div class="mb-3">
                    <label for="password" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                        Mật khẩu xác nhận <span class="text-danger">*</span>
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="••••••••"
                        class="form-control form-control-editorial rounded-3 @error('password', 'userDeletion') is-invalid @enderror"
                        style="font-size: 14px;"
                    />

                    @error('password', 'userDeletion')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-theme-top">
                    <button type="button" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5" data-bs-dismiss="modal">
                        Hủy bỏ
                    </button>

                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 fw-semibold">
                        Xác nhận xóa tài khoản
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</section>

