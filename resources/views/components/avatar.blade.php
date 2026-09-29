@props([
    'user' => null,
    'size' => 'md',
    'indicator' => false,
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-xl',
        '2xl' => 'w-20 h-20 text-2xl',
        default => 'w-10 h-10 text-sm',
    };

    $name = $user?->name ?? 'Guest';
    $avatar = $user?->avatar ?? null;
    $initials = strtoupper(substr($name, 0, 1));
@endphp

<div {{ $attributes->merge(['class' => "relative inline-block shrink-0 rounded-full {$sizeClasses}"]) }}>
    <div class="w-full h-full rounded-full overflow-hidden bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center border border-zinc-800 shadow-sm select-none">
        @if($avatar)
            <img src="{{ asset($avatar) }}" alt="{{ $name }}" class="w-full h-full object-cover">
        @else
            <span>{{ $initials }}</span>
        @endif
    </div>
    @if($indicator)
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-zinc-950 rounded-full"></span>
    @endif
</div>
