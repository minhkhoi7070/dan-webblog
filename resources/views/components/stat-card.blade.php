@props([
    'title',
    'value',
    'subtitle' => null,
    'trend' => null,
    'color' => 'default',
])

@php
    $accentColor = match($color) {
        'indigo' => 'text-indigo-400',
        'rose' => 'text-rose-400',
        'amber' => 'text-amber-400',
        'emerald' => 'text-emerald-400',
        default => 'text-zinc-100',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-zinc-900/70 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700/80 transition-colors']) }}>
    <div class="flex items-center justify-between text-xs font-medium text-zinc-400 mb-1.5">
        <span>{{ $title }}</span>
        @if(isset($icon))
            <div class="text-zinc-500">
                {{ $icon }}
            </div>
        @endif
    </div>
    <div class="text-2xl sm:text-3xl font-extrabold tracking-tight {{ $accentColor }}">
        {{ $value }}
    </div>
    @if($subtitle)
        <div class="mt-1 text-xs text-zinc-500">
            {{ $subtitle }}
        </div>
    @endif
</div>
