<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-body m-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-body">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
