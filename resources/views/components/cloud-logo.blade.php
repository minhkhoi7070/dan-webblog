@props([
    'size' => 'md', // xs (28px), sm (36px), md (44px), lg (64px), xl (88px), 2xl (120px)
    'withText' => true,
    'text' => 'CloudBlog',
    'textClass' => '',
    'animated' => true,
])

@php
    $logoClass = match($size) {
        'xs' => 'w-7 h-7',
        'sm' => 'w-9 h-9',
        'lg' => 'w-14 h-14',
        'xl' => 'w-20 h-20',
        '2xl' => 'w-28 h-28',
        default => 'w-11 h-11',
    };
    $textStyleClass = match($size) {
        'xs' => 'fs-6',
        'sm' => 'fs-5',
        'lg' => 'fs-3',
        'xl' => 'fs-2',
        '2xl' => 'fs-1',
        default => 'fs-4',
    };
@endphp

<div {{ $attributes->merge(['class' => 'brand-link d-inline-flex align-items-center gap-2.5 user-select-none text-decoration-none']) }}>
    <x-logo class="{{ $logoClass }}" />

    @if($withText)
        <span class="brand-text font-bold tracking-tight {{ $textStyleClass }} {{ $textClass }}">
            @if($slot->isNotEmpty())
                {{ $slot }}
            @else
                {{ $text }}
            @endif
        </span>
    @endif
</div>
