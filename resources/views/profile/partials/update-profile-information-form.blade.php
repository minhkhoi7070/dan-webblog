<section id="profile-info">
    <header class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </span>
                <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    Thông tin hồ sơ
                </h2>
            </div>

            <a href="{{ route('profile.show') }}" class="small text-accent hover-accent text-decoration-none fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                <span>Xem trang cá nhân</span>
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
        </div>
        <p class="small text-theme-muted mb-0" style="font-size: 13px;">
            Cập nhật tên hiển thị và địa chỉ email liên kết với tài khoản của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="d-flex flex-column gap-3.5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                Tên hiển thị <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="form-control form-control-editorial rounded-3 @error('name') is-invalid @enderror"
                style="font-size: 14px;"
            >
            @error('name')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="label-uppercase text-theme mb-1.5 d-block" style="font-size: 11px;">
                Địa chỉ Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="form-control form-control-editorial rounded-3 @error('email') is-invalid @enderror"
                style="font-size: 14px;"
            >
            @error('email')
                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2.5 p-2.5 rounded-3 bg-warning-subtle border border-warning border-opacity-25">
                    <p class="small text-warning-emphasis mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 12px;">
                        <span>Địa chỉ email của bạn chưa được xác thực.</span>
                        <button form="send-verification" class="btn btn-sm btn-link p-0 text-warning-emphasis fw-semibold text-decoration-underline">
                            Gửi lại email xác thực
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="small text-success fw-medium mt-1.5 mb-0" style="font-size: 12px;">
                            Liên kết xác thực mới đã được gửi tới email của bạn.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-2 fw-semibold" style="font-size: 13px;">
                Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
                <span id="profile-saved-msg" class="small text-success fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Đã lưu thành công!
                </span>
                <script>
                    setTimeout(() => {
                        const el = document.getElementById('profile-saved-msg');
                        if (el) el.style.display = 'none';
                    }, 3000);
                </script>
            @endif
        </div>
    </form>
</section>

