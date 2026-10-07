<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
            Đăng ký tài khoản
        </h1>
        <p class="small text-theme-secondary mb-0">
            Gia nhập cộng đồng người viết và độc giả BlogMNM
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Họ và tên
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nguyễn Văn A"
                class="form-control form-control-editorial @error('name') is-invalid @enderror"
                aria-describedby="name-feedback"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Địa chỉ Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="ten@example.com"
                class="form-control form-control-editorial @error('email') is-invalid @enderror"
                aria-describedby="email-feedback"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Mật khẩu
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Tối thiểu 8 ký tự"
                class="form-control form-control-editorial @error('password') is-invalid @enderror"
                aria-describedby="password-feedback"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Xác nhận mật khẩu
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu"
                class="form-control form-control-editorial @error('password_confirmation') is-invalid @enderror"
                aria-describedby="password-confirmation-feedback"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <div class="mb-3">
            <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs">
                Đăng ký tài khoản
            </button>
        </div>

        <!-- Already Registered Link -->
        <div class="pt-3 mt-4 text-center border-theme-top small text-theme-secondary">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="text-theme fw-semibold text-decoration-underline-hover ms-1">
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-guest-layout>

