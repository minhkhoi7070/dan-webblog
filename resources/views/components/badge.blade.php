@props([
    'variant' => 'default',
    'size' => 'sm',
])

@php
    $variantClasses = match($variant) {
        'category' => 'bg-theme-surface text-theme-secondary border border-theme',
        'tag' => 'badge-status-pending',
        'status-draft' => 'badge-status-draft',
        'status-pending' => 'badge-status-pending',
        'status-published' => 'badge-status-published',
        'status-rejected' => 'badge-status-rejected',
        'active' => 'btn-primary text-decoration-none',
        default => 'bg-theme-surface-hover text-theme border border-theme',
    };

    $sizeClasses = match($size) {
        'xs' => 'py-0 px-2',
        'sm' => 'py-1 px-2',
        'md' => 'py-1 px-3 fs-6',
        default => 'py-1 px-2',
    };
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill fw-medium d-inline-flex align-items-center gap-1 small {$sizeClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</span>
