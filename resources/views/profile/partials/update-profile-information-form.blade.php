<section id="profile-info">
    <!-- Apple Account Header Row -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 macos-divider">
        <div class="d-flex align-items-center gap-3.5">
            <!-- Apple Style Round Avatar (84px, neutral soft grey, no purple-blue gradients) -->
            <div class="position-relative flex-shrink-0 cursor-pointer" id="avatar-picker-trigger" role="button" tabindex="0" title="Nhấp để thay đổi ảnh">
                <div id="avatar-preview-container" class="macos-avatar-box">
                    @if($user->avatar_url)
                        <img id="avatar-preview-img" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover d-block rounded-circle">
                    @else
                        <div id="avatar-preview-placeholder" class="macos-avatar-placeholder">
                            {{ strtoupper(mb_substr($user->name, 0, 1, 'UTF-8')) }}
                        </div>
                    @endif
                </div>

                <!-- Hidden Native File Input -->
                <input type="file"
                       id="avatar-input"
                       name="avatar"
                       form="profile-update-form"
                       accept="image/png,image/jpeg,image/webp"
                       class="d-none"
                       aria-describedby="avatar-help">
            </div>

            <!-- User Summary & Change Avatar Action -->
            <div class="min-w-0">
                <h2 class="macos-account-name text-truncate mb-0">
                    {{ $user->name }}
                </h2>
                <div class="macos-account-email text-truncate mb-2">
                    {{ $user->email }}
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button"
                            id="btn-choose-avatar"
                            class="macos-btn macos-btn-secondary"
                            style="font-size: 12px; padding: 4px 10px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Thay đổi ảnh...</span>
                    </button>
                    <span id="avatar-help" class="macos-hint-text">
                        JPEG, PNG, WebP (tối đa 2MB)
                    </span>
                </div>
            </div>
        </div>

        <a href="{{ route('profile.show') }}" class="macos-btn macos-btn-secondary align-self-start" style="font-size: 12px; padding: 4px 10px;">
            <span>Xem trang cá nhân</span>
            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Hidden Form for Email Verification Resend -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Main Update Profile Form -->
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-update-form">
        @csrf
        @method('patch')

        @error('avatar')
            <div class="alert alert-danger py-1.5 px-2.5 mb-3 rounded-2 small" style="font-size: 12px;">{{ $message }}</div>
        @enderror

        <!-- Two Equal Columns for Name & Email -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label for="name" class="macos-label">
                    Tên hiển thị <span class="text-danger">*</span>
                </label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autocomplete="name"
                    class="form-control macos-input @error('name') is-invalid @enderror"
                    placeholder="Nhập họ và tên..."
                >
                @error('name')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="email" class="macos-label mb-0">
                        Địa chỉ Email <span class="text-danger">*</span>
                    </label>
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && $user->hasVerifiedEmail())
                        <span class="macos-badge-verified">
                            <svg width="9" height="9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Đã xác thực</span>
                        </span>
                    @endif
                </div>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                    class="form-control macos-input @error('email') is-invalid @enderror"
                    placeholder="email@example.com"
                >
                @error('email')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-2.5 rounded-2 bg-warning-subtle border border-warning border-opacity-25 mb-3">
                <p class="small text-warning-emphasis mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 12px;">
                    <span>Email của bạn chưa được xác thực.</span>
                    <button form="send-verification" class="btn btn-sm btn-link p-0 text-warning-emphasis fw-medium text-decoration-underline" style="font-size: 12px;">
                        Gửi lại email xác thực
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="small text-success fw-medium mt-1 mb-0" style="font-size: 11.5px;">
                        Liên kết xác thực mới đã được gửi tới email của bạn.
                    </p>
                @endif
            </div>
        @endif

        <!-- Form Action Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
            <div>
                @if (session('status') === 'profile-updated')
                    <span id="profile-saved-msg" class="macos-status-saved">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Đã lưu thay đổi
                    </span>
                    <script>
                        setTimeout(() => {
                            const el = document.getElementById('profile-saved-msg');
                            if (el) {
                                el.style.opacity = '0';
                                setTimeout(() => el.remove(), 250);
                            }
                        }, 2500);
                    </script>
                @endif
            </div>

            <!-- Apple-style Primary Action Button (Blue #007AFF) -->
            <button type="submit" class="macos-btn macos-btn-primary">
                Lưu thay đổi
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const avatarInput = document.getElementById('avatar-input');
            const chooseBtn = document.getElementById('btn-choose-avatar');
            const triggerArea = document.getElementById('avatar-picker-trigger');
            const previewContainer = document.getElementById('avatar-preview-container');

            const openPicker = () => avatarInput?.click();

            chooseBtn?.addEventListener('click', openPicker);
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
                        previewContainer.innerHTML = `<img src="${event.target.result}" alt="Xem trước ảnh" class="w-100 h-100 object-fit-cover d-block rounded-circle">`;
                    }
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</section>
