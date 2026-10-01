<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-body m-0">
            {{ __('Cài đặt tài khoản') }}
        </h2>
    </x-slot>

    <div class="d-flex flex-column gap-4">
        <!-- Appearance / Theme Settings -->
        <div class="card p-4 p-md-5 border shadow-sm">
            <div style="max-width: 700px;">
                @include('profile.partials.appearance-settings')
            </div>
        </div>

        <div class="card p-4 p-md-5 border shadow-sm">
            <div style="max-width: 600px;">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card p-4 p-md-5 border shadow-sm">
            <div style="max-width: 600px;">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card p-4 p-md-5 border shadow-sm">
            <div style="max-width: 600px;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
