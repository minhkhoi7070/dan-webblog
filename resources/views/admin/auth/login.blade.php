<x-guest-layout title="Đăng nhập Quản trị viên">
    <div class="mb-6 text-center">
        <!-- Admin Security Shield Badge -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[11px] font-bold uppercase tracking-wider mb-3">
            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Quản trị viên
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-[var(--color-text)] tracking-tight">Đăng nhập Quản trị viên</h1>
        <p class="text-xs text-[var(--color-text-secondary)] mt-1.5">Khu vực dành riêng cho quản trị hệ thống BlogMNM</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Role / General Error Notification Banner -->
    @if ($errors->has('email') && str_contains($errors->first('email'), 'không có quyền quản trị'))
        <div class="mb-4 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-2.5">
            <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ $errors->first('email') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">
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
                placeholder="admin@blogmnm.test"
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('email') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-[var(--color-text)]">
                    Mật khẩu
                </label>
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('password') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="rounded border-[var(--color-border)] bg-[var(--color-input)] text-indigo-600 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs text-[var(--color-text-secondary)]">Ghi nhớ đăng nhập</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 px-4 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] transition shadow-md cursor-pointer">
                Đăng nhập
            </button>
        </div>

        <!-- Back to BlogMNM Link -->
        <div class="pt-4 text-center border-t border-[var(--color-border)] text-xs">
            <a href="{{ route('home') }}" class="font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition inline-flex items-center gap-1.5">
                <span>&larr;</span> Quay lại BlogMNM
            </a>
        </div>
    </form>
</x-guest-layout>
