<x-guest-layout>
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-accent-subtle text-accent mb-3" style="width: 56px; height: 56px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight">
            Đặt lại mật khẩu
        </h1>
        <p class="small text-theme-secondary mb-2">
            Thiết lập mật khẩu mới cho tài khoản của bạn:
        </p>

        @if(isset($email))
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-surface-2 border border-theme small text-theme fw-medium user-select-all mb-2">
                <svg style="width: 15px; height: 15px;" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
                <span class="text-truncate" style="max-width: 280px;">{{ $email }}</span>
            </div>
        @endif
    </div>

    <!-- Session Status Alert -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    @if (session('error'))
        <div class="alert alert-danger small mb-3 py-2 px-3 rounded-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Mật khẩu mới
            </label>
            <input id="password"
                   type="password"
                   name="password"
                   required
                   autofocus
                   autocomplete="new-password"
                   placeholder="Nhập ít nhất 8 ký tự"
                   class="form-control form-control-editorial @error('password') is-invalid @enderror"
                   aria-describedby="password-feedback">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Xác nhận mật khẩu mới
            </label>
            <input id="password_confirmation"
                   type="password"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu mới"
                   class="form-control form-control-editorial @error('password_confirmation') is-invalid @enderror"
                   aria-describedby="password-confirmation-feedback">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 rounded-pill fw-semibold small d-flex align-items-center justify-content-center gap-2 mb-3">
            Cập nhật mật khẩu
        </button>

        <div class="text-center pt-2 border-theme-top">
            <a href="{{ route('login') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Hủy và quay lại đăng nhập</span>
            </a>
        </div>
    </form>
</x-guest-layout>
