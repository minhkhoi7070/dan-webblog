<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="h5 fw-bold text-theme">Đăng ký tài khoản</h2>
        <p class="small text-theme-secondary mb-0">Tham gia cùng các tác giả và độc giả tại BlogMNM</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label small fw-semibold text-theme mb-1">
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
                class="form-control @error('name') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold text-theme mb-1">
                Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="ten@example.com"
                class="form-control @error('email') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold text-theme mb-1">
                Mật khẩu
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Tối thiểu 8 ký tự"
                class="form-control @error('password') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-semibold text-theme mb-1">
                Xác nhận mật khẩu
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu"
                class="form-control @error('password_confirmation') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold small shadow-sm">
                Đăng ký tài khoản
            </button>
        </div>

        <!-- Already Registered Link -->
        <div class="pt-3 text-center border-theme-top small text-theme-secondary">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1" style="color: #818cf8;">
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-guest-layout>
