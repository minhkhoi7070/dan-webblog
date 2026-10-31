<x-guest-layout>
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-accent-subtle text-accent mb-3" style="width: 56px; height: 56px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>

        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight">
            Quên mật khẩu
        </h1>
        <p class="small text-theme-secondary mb-0">
            Nhập email tài khoản để nhận mã OTP xác thực 6 chữ số.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    @if (session('warning'))
        <div class="alert alert-warning small mb-3 py-2 px-3 rounded-3" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger small mb-3 py-2 px-3 rounded-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Địa chỉ Email
            </label>
            <input id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="ten@example.com"
                   class="form-control form-control-editorial @error('email') is-invalid @enderror"
                   aria-describedby="email-feedback">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 rounded-pill fw-semibold small d-flex align-items-center justify-content-center gap-2 mb-3">
            Gửi mã xác thực OTP
        </button>

        <div class="text-center pt-2 border-theme-top">
            <a href="{{ route('login') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Quay lại đăng nhập</span>
            </a>
        </div>
    </form>
</x-guest-layout>
