<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-[var(--color-text)]">Đăng ký tài khoản</h2>
        <p class="text-xs text-[var(--color-text-secondary)] mt-1">Tham gia cùng các tác giả và độc giả tại BlogMNM</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">
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
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('name') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-400" />
        </div>

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
                autocomplete="username"
                placeholder="ten@example.com"
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('email') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">
                Mật khẩu
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Tối thiểu 8 ký tự"
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('password') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">
                Xác nhận mật khẩu
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Nhập lại mật khẩu"
                class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] border @error('password_confirmation') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:ring-1 focus:ring-indigo-500 transition"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-2.5 px-4 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] transition shadow-md">
                Đăng ký tài khoản
            </button>
        </div>

        <!-- Already Registered Link -->
        <div class="pt-4 text-center border-t border-[var(--color-border)] text-xs text-[var(--color-text-secondary)]">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition ml-1">
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-guest-layout>
