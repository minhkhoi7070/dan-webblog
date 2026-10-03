@props([
    'title',
    'value',
    'subtitle' => null,
    'trend' => null,
    'color' => 'default',
])

@php
    $accentColor = match($color) {
        'indigo' => 'text-primary',
        'rose' => 'text-danger',
        'amber' => 'text-warning',
        'emerald' => 'text-success',
        default => 'text-theme',
    };
@endphp

<div {{ $attributes->merge(['class' => 'card p-3 p-sm-4 border-theme shadow-sm']) }}>
    <div class="d-flex align-items-center justify-content-between small text-theme-secondary mb-2">
        <span>{{ $title }}</span>
        @if(isset($icon))
            <div class="text-theme-secondary opacity-75">
                {{ $icon }}
            </div>
        @endif
    </div>
    <div class="h3 fw-bold tracking-tight mb-0 metric-number {{ $accentColor }}">
        {{ $value }}
    </div>
    @if($subtitle)
        <div class="mt-1 small text-theme-secondary">
            {{ $subtitle }}
        </div>
    @endif
</div>
