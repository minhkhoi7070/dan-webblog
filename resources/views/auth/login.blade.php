<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Đăng nhập BlogMNM</h2>
        <p class="text-xs text-[var(--color-text-secondary)] mt-1">Khám phá và tương tác cùng cộng đồng công nghệ</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                placeholder="ten@example.com"
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
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-400 hover:text-indigo-300 transition" href="{{ route('password.request') }}">
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
            <button type="submit" class="w-full py-2.5 px-4 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] transition shadow-md">
                Đăng nhập
            </button>
        </div>

        <!-- Register Link -->
        <div class="pt-4 text-center border-t border-[var(--color-border)] text-xs text-[var(--color-text-secondary)]">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition ml-1">
                Đăng ký ngay
            </a>
        </div>
    </form>
</x-guest-layout>
