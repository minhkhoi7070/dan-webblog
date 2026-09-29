@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-xs text-[var(--color-text)] mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>

