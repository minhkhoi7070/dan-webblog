<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary rounded-pill px-4 py-2 fw-semibold small shadow-sm border-theme']) }}>
    {{ $slot }}
</button>
