<section id="profile-info">
    <header class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </span>
                <h2 class="h5 fw-bold text-theme m-0">
                    Thông tin hồ sơ
                </h2>
            </div>

            <a href="{{ route('profile.show') }}" class="small text-accent hover-accent text-decoration-none fw-medium d-inline-flex align-items-center gap-1" style="font-size: 12.5px;">
                <span>Xem trang cá nhân</span>
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="d-flex flex-column gap-4" id="profile-update-form">
        @csrf
        @method('patch')

        <!-- 1. Avatar Section: Clean Horizontal Row [Avatar 68px] [Ảnh đại diện] [Nút thay ảnh] -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-1">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <!-- Circular Avatar (68px, aspect-ratio 1:1, object-fit: cover) -->
                <label for="avatar-input"
                       class="position-relative d-inline-block flex-shrink-0 cursor-pointer m-0"
                       id="avatar-picker-trigger"
                       title="Nhấp để thay đổi ảnh đại diện (Tối đa 2MB: JPG, PNG, WebP)"
                       role="button"
                       tabindex="0"
                       aria-label="Thay đổi ảnh đại diện">
                    <div id="avatar-preview-container"
                         class="rounded-circle overflow-hidden border border-theme shadow-xs flex-shrink-0"
                         style="width: 68px; height: 68px; min-width: 68px; min-height: 68px; aspect-ratio: 1/1;">
                        <img id="avatar-preview-img"
                             src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             data-original-src="{{ $user->avatar_url ?? '' }}"
                             class="w-100 h-100 {{ $user->avatar_url ? 'd-block' : 'd-none' }}"
                             style="object-fit: cover; aspect-ratio: 1/1; border-radius: 50%;">
                        <div id="avatar-preview-placeholder"
                             class="w-100 h-100 {{ $user->avatar_url ? 'd-none' : 'd-flex' }} align-items-center justify-content-center text-white fw-bold"
                             style="background: linear-gradient(135deg, #374151 0%, #111827 100%); font-size: 22px; aspect-ratio: 1/1; border-radius: 50%;">
                            {{ strtoupper(mb_substr($user->name, 0, 1, 'UTF-8')) }}
                        </div>
                    </div>

                    <!-- Subtle Camera Overlay Badge Button -->
                    <span id="camera-overlay-badge"
                          class="position-absolute bottom-0 end-0 rounded-circle border-0 d-flex align-items-center justify-content-center shadow-xs"
                          style="width: 24px; height: 24px; background-color: var(--color-text-primary, #000); color: var(--color-bg-surface, #fff); transform: translate(2px, 2px); z-index: 2; pointer-events: none;"
                          aria-hidden="true">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                </label>

                <!-- Label "Ảnh đại diện" -->
                <div class="d-flex flex-column justify-content-center min-w-0">
                    <span class="label-uppercase text-theme fw-semibold m-0" style="font-size: 11px; letter-spacing: 0.05em;">
                        Ảnh đại diện
                    </span>
                    <div id="avatar-error-feedback" class="invalid-feedback d-none mt-1" style="font-size: 12px;"></div>
                    @error('avatar')
                        <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Nút Thay ảnh & Hủy -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <label for="avatar-input"
                       id="btn-choose-avatar"
                       class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 cursor-pointer m-0"
                       style="font-size: 13px;"
                       title="Chọn ảnh mới (JPG, PNG, WebP tối đa 2MB)"
                       role="button"
                       tabindex="0">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Thay ảnh</span>
                </label>

                <button type="button"
                        id="btn-cancel-avatar"
                        class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 d-none"
                        style="font-size: 13px;">
                    Hủy
                </button>
            </div>

            <!-- Hidden File Input -->
            <input type="file"
                   id="avatar-input"
                   name="avatar"
                   accept="image/png,image/jpeg,image/jpg,image/webp"
                   class="d-none">
        </div>

        <!-- 2. Tên hiển thị -->
        <div>
            <label for="name" class="label-uppercase text-theme mb-2 d-block" style="font-size: 11px; letter-spacing: 0.05em;">
                Tên hiển thị <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autocomplete="name"
                class="form-control form-control-editorial rounded-3 w-100 @error('name') is-invalid @enderror"
                style="height: 48px; min-height: 48px; font-size: 14px;"
            >
            @error('name')
                <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- 3. Địa chỉ Email -->
        <div>
            <label for="email" class="label-uppercase text-theme mb-2 d-block" style="font-size: 11px; letter-spacing: 0.05em;">
                Địa chỉ Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="form-control form-control-editorial rounded-3 w-100 @error('email') is-invalid @enderror"
                style="height: 48px; min-height: 48px; font-size: 14px;"
            >
            @error('email')
                <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
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

        <!-- 4. Nút Lưu thay đổi & Trạng thái lưu -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-2">
            <div>
                <span id="profile-saved-msg" class="small text-success fw-medium {{ session('status') === 'profile-updated' ? 'd-inline-flex' : 'd-none' }} align-items-center gap-1" style="font-size: 12.5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Đã lưu thành công!
                </span>
            </div>

            <button type="submit"
                    class="btn btn-editorial-primary rounded-pill px-4 py-2 fw-semibold w-100 w-sm-auto ms-sm-auto d-inline-flex align-items-center justify-content-center"
                    style="min-height: 44px; font-size: 13.5px;">
                Lưu thay đổi
            </button>
        </div>
    </form>
</section>
