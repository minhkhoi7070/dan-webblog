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
        'md' => 13,
        'lg' => 16,
        'xl' => 20,
        '2xl' => 26,
        default => 13,
    };

    $name = $user?->name ?? 'Guest';
    $avatarUrl = $user?->avatar_url ?? ($user?->avatar ? (str_starts_with($user->avatar, 'avatars/') ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) : asset($user->avatar)) : null);
    $initials = strtoupper(mb_substr($name, 0, 1, 'UTF-8'));
@endphp

<div {{ $attributes->merge(['class' => 'position-relative d-inline-flex align-items-center justify-content-center flex-shrink-0 user-select-none avatar-wrapper']) }}
     style="width: {{ $dimension }}px; height: {{ $dimension }}px; min-width: {{ $dimension }}px; min-height: {{ $dimension }}px; max-width: {{ $dimension }}px; max-height: {{ $dimension }}px; aspect-ratio: 1 / 1;">
    <div class="w-100 h-100 rounded-circle overflow-hidden text-white fw-bold d-flex align-items-center justify-content-center border-theme shadow-xs flex-shrink-0"
         style="font-size: {{ $fontSize }}px; background: linear-gradient(135deg, #374151 0%, #111827 100%); aspect-ratio: 1 / 1; border-radius: 50% !important;"
         data-theme-preserve>
        @if($avatarUrl)
            <img src="{{ $avatarUrl }}"
                 alt="{{ $name }}"
                 class="w-100 h-100 d-block"
                 style="object-fit: cover; aspect-ratio: 1 / 1; border-radius: 50%;"
                 loading="lazy">
        @else
            <span style="line-height: 1; display: inline-block;">{{ $initials }}</span>
        @endif
    </div>
    @if($indicator)
        <span class="position-absolute bottom-0 end-0 bg-success border border-theme rounded-circle" style="width: 8px; height: 8px;"></span>
    @endif
</div>
