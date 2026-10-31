<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="h4 fw-bold text-white mb-1.5 tracking-tight login-glass-title">
            Đăng ký tài khoản
        </h1>
        <p class="small text-white-75 mb-0">
            Gia nhập cộng đồng người viết và độc giả CloudBlog
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="needs-validation register-form" novalidate>
        @csrf

        <!-- Name -->
        <div class="register-field">
            <label for="name" class="form-label small fw-medium text-white-90 mb-0 d-block">
                Họ và tên
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autocomplete="name"
                placeholder="Nguyễn Văn A"
                class="form-control form-control-glass @error('name') is-invalid @enderror"
                aria-describedby="name-feedback"
            >
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="register-field">
            <label for="email" class="form-label small fw-medium text-white-90 mb-0 d-block">
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
                class="form-control form-control-glass @error('email') is-invalid @enderror"
                aria-describedby="email-feedback"
            >
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="register-field">
            <label for="password" class="form-label small fw-medium text-white-90 mb-0 d-block">
                Mật khẩu
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Tối thiểu 8 ký tự"
                class="form-control form-control-glass @error('password') is-invalid @enderror"
                aria-describedby="password-feedback"
            >
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="register-field">
            <label for="password_confirmation" class="form-label small fw-medium text-white-90 mb-0 d-block">
                Xác nhận mật khẩu
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu"
                class="form-control form-control-glass @error('password_confirmation') is-invalid @enderror"
                aria-describedby="password-confirmation-feedback"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Submit Button -->
        <div class="register-submit">
            <button type="submit" class="btn btn-glass-primary w-100 py-2.5 fw-semibold shadow-sm">
                Đăng ký tài khoản
            </button>
        </div>

        <!-- Already Registered Link -->
        <div class="pt-3 text-center login-glass-divider small text-white-75">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="text-white fw-semibold text-decoration-underline ms-1">
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-guest-layout>

