<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="h5 fw-bold text-theme">Đăng nhập BlogMNM</h2>
        <p class="small text-theme-secondary mb-0">Khám phá và tương tác cùng cộng đồng công nghệ</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
                autocomplete="username"
                placeholder="ten@example.com"
                class="form-control @error('email') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="password" class="form-label small fw-semibold text-theme mb-0">
                    Mật khẩu
                </label>
                @if (Route::has('password.request'))
                    <a class="small text-decoration-none" style="color: #818cf8; font-size: 11px;" href="{{ route('password.request') }}">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="form-control @error('password') is-invalid @enderror"
            >
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label small text-theme-secondary user-select-none">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold small shadow-sm">
                Đăng nhập
            </button>
        </div>

        <!-- Register Link -->
        <div class="pt-3 text-center border-theme-top small text-theme-secondary">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1" style="color: #818cf8;">
                Đăng ký ngay
            </a>
        </div>
    </form>
</x-guest-layout>
