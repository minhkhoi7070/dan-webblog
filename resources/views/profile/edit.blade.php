<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[var(--color-text)] leading-tight">
            {{ __('Cài đặt tài khoản') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Appearance / Theme Settings -->
        <div class="p-6 sm:p-8 bg-[var(--color-surface)] border border-[var(--color-border)] shadow-xl rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.appearance-settings')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-[var(--color-surface)] border border-[var(--color-border)] shadow-xl rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-[var(--color-surface)] border border-[var(--color-border)] shadow-xl rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-[var(--color-surface)] border border-[var(--color-border)] shadow-xl rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
