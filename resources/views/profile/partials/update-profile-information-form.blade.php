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
        <p class="small text-theme-muted mb-0" style="font-size: 13px;">
            Cập nhật tên hiển thị và địa chỉ email liên kết với tài khoản của bạn.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="d-flex flex-column gap-4" id="profile-update-form">
        @csrf
        @method('patch')

        <!-- Avatar Upload Section (Threads Minimalist Style) -->
        <div class="p-3.5 rounded-3 border border-theme bg-surface-2">
            <label class="label-uppercase text-theme mb-2.5 d-block" style="font-size: 11px;">
                Ảnh đại diện
            </label>

            <div class="d-flex align-items-center gap-3.5 flex-wrap">
                <!-- Avatar Preview with Camera Overlay -->
                <div class="position-relative d-inline-block flex-shrink-0 cursor-pointer" id="avatar-picker-trigger" title="Nhấp để thay đổi ảnh đại diện" role="button" tabindex="0">
                    <div id="avatar-preview-container" class="rounded-circle overflow-hidden border border-theme shadow-xs flex-shrink-0" style="width: 76px; height: 76px; aspect-ratio: 1/1;">
                        @if($user->avatar_url)
                            <img id="avatar-preview-img" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-100 h-100 d-block" style="object-fit: cover; aspect-ratio: 1/1; border-radius: 50%;">
                        @else
                            <div id="avatar-preview-placeholder" class="w-100 h-100 d-flex align-items-center justify-content-center text-white fw-bold" style="background: linear-gradient(135deg, #374151 0%, #111827 100%); font-size: 24px; aspect-ratio: 1/1; border-radius: 50%;">
                                {{ strtoupper(mb_substr($user->name, 0, 1, 'UTF-8')) }}
                            </div>
                        @endif
                    </div>

                    <!-- Camera Overlay Badge Button -->
                    <button type="button"
                            id="btn-camera-trigger"
                            class="position-absolute bottom-0 end-0 rounded-circle border-0 d-flex align-items-center justify-content-center shadow-xs cursor-pointer"
                            style="width: 26px; height: 26px; background-color: var(--color-text-primary, #000); color: var(--color-bg-surface, #fff); transform: translate(3px, 3px); z-index: 2;"
                            aria-label="Chọn ảnh đại diện mới"
                            title="Chọn ảnh đại diện">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <!-- Hidden File Input -->
                <input type="file"
                       id="avatar-input"
                       name="avatar"
                       accept="image/png,image/jpeg,image/webp"
                       class="d-none"
                       aria-describedby="avatar-help">

                <!-- Controls & Instructions -->
                <div class="d-flex flex-column gap-1.5 min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button"
                                id="btn-choose-avatar"
                                class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5"
                                style="font-size: 13px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Thay đổi ảnh đại diện
                        </button>

                        <button type="button"
                                id="btn-cancel-avatar"
                                class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 d-none"
                                style="font-size: 13px;">
                            Hủy
                        </button>
                    </div>

                    <div id="avatar-help" class="small text-theme-muted" style="font-size: 12px;">
                        Hỗ trợ định dạng JPEG, PNG, WebP. Dung lượng tối đa 2MB.
                    </div>

                    @error('avatar')
                        <div class="invalid-feedback d-block mt-0">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const avatarInput = document.getElementById('avatar-input');
                const chooseBtn = document.getElementById('btn-choose-avatar');
                const cameraBtn = document.getElementById('btn-camera-trigger');
                const cancelBtn = document.getElementById('btn-cancel-avatar');
                const triggerArea = document.getElementById('avatar-picker-trigger');
                const previewContainer = document.getElementById('avatar-preview-container');
                const originalHtml = previewContainer ? previewContainer.innerHTML : '';

                const openPicker = () => avatarInput?.click();

                chooseBtn?.addEventListener('click', openPicker);
                cameraBtn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openPicker();
                });
                triggerArea?.addEventListener('click', openPicker);
                triggerArea?.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openPicker();
                    }
                });

                avatarInput?.addEventListener('change', (e) => {
                    const file = e.target.files?.[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        alert('Dung lượng ảnh vượt quá 2MB. Vui lòng chọn ảnh nhỏ hơn.');
                        avatarInput.value = '';
                        return;
                    }

                    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Chỉ chấp nhận định dạng ảnh JPEG, PNG hoặc WebP.');
                        avatarInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (previewContainer) {
                            previewContainer.innerHTML = `<img src="${event.target.result}" alt="Xem trước ảnh mới" class="w-100 h-100 d-block" style="object-fit: cover; aspect-ratio: 1/1; border-radius: 50%;">`;
                        }
                        if (cancelBtn) {
                            cancelBtn.classList.remove('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                });

                cancelBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (avatarInput) avatarInput.value = '';
                    if (previewContainer) previewContainer.innerHTML = originalHtml;
                    cancelBtn.classList.add('d-none');
                });
            });
        </script>

        <div>
            <label for="name" class="label-uppercase text-theme mb-2 d-block" style="font-size: 11px;">
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
            <label for="email" class="label-uppercase text-theme mb-2 d-block" style="font-size: 11px;">
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

        <div class="d-flex align-items-center justify-content-end flex-wrap gap-3">
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

            <button type="submit" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-2 fw-semibold" style="font-size: 13px;">
                Lưu thay đổi
            </button>
        </div>
    </form>
</section>

