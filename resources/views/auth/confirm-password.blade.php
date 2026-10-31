<x-guest-layout>
    <div class="mb-4 small text-theme-secondary">
        {{ __('Khu vực này được bảo vệ. Vui lòng xác nhận mật khẩu để tiếp tục.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <x-text-input id="password" class="w-100" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="d-flex justify-content-end mt-4">
            <x-primary-button>
                {{ __('Xác nhận') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
