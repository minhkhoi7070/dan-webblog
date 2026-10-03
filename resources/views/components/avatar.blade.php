@props([
    'user' => null,
    'size' => 'md',
    'indicator' => false,
])

@php
    $dimension = match($size) {
        'xs' => 24,
        'sm' => 32,
        'md' => 40,
        'lg' => 48,
        'xl' => 64,
        '2xl' => 80,
        default => 40,
    };
    $fontSize = match($size) {
        'xs' => 10,
        'sm' => 12,
        'md' => 14,
        'lg' => 16,
        'xl' => 20,
        '2xl' => 24,
        default => 14,
    };

    $name = $user?->name ?? 'Guest';
    $avatar = $user?->avatar ?? null;
    $initials = strtoupper(substr($name, 0, 1));
@endphp

<div {{ $attributes->merge(['class' => 'position-relative d-inline-block flex-shrink-0 user-select-none']) }} style="width: {{ $dimension }}px; height: {{ $dimension }}px;">
    <div class="w-100 h-100 rounded-circle overflow-hidden brand-gradient text-white fw-bold d-flex align-items-center justify-content-center border-theme shadow-sm" style="font-size: {{ $fontSize }}px;" data-theme-preserve>
        @if($avatar)
            <img src="{{ asset($avatar) }}" alt="{{ $name }}" class="w-100 h-100" style="object-fit: cover;">
        @else
            <span>{{ $initials }}</span>
        @endif
    </div>
    @if($indicator)
        <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 10px; height: 10px;"></span>
    @endif
</div>
