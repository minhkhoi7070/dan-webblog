<x-guest-layout title="Đăng nhập Quản trị viên">
    <div class="mb-4 text-center">
        <!-- Admin Security Shield Badge -->
        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill mb-2.5 border border-theme bg-surface-2" style="font-size: 11px;">
            <svg style="width: 14px; height: 14px; color: var(--color-danger, #1e36b9ff);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span class="label-uppercase text-danger m-0" style="font-size: 10px; letter-spacing: 0.08em;">Quản trị viên</span>
        </div>
        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
             Admin
        </h1>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <!-- Role / General Error Notification Banner -->
    @if ($errors->has('email') && str_contains($errors->first('email'), 'không có quyền quản trị'))
        <div class="alert alert-danger d-flex align-items-center gap-2 small py-2.5 px-3 mb-3 border border-danger border-opacity-25 rounded-3">
            <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ $errors->first('email') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-medium text-theme mb-1.5 d-block">
                Tài khoản Email Quản trị
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="admin@blogmnm.test"
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
                autocomplete="current-password"
                placeholder="Nhập mật khẩu"
                class="form-control form-control-editorial @error('password') is-invalid @enderror"
                aria-describedby="password-feedback"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4 d-flex align-items-center gap-2">
            <input id="remember_me" type="checkbox" class="form-check-input form-check-input-editorial m-0" name="remember">
            <label for="remember_me" class="form-check-label small text-theme-secondary user-select-none cursor-pointer">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mb-3">
            <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 fw-semibold shadow-xs">
                Đăng nhập Quản trị viên
            </button>
        </div>

        
    </form>
</x-guest-layout>
