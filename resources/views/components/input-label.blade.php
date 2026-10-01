@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-semibold small text-theme mb-1']) }}>
    {{ $value ?? $slot }}
</label>
