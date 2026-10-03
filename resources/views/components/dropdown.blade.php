@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
$alignClass = match ($align) {
    'left' => 'dropdown-menu-start',
    default => 'dropdown-menu-end',
};
@endphp

<div class="dropdown d-inline-block">
    <div data-bs-toggle="dropdown" aria-expanded="false" class="d-inline-block" role="button">
        {{ $trigger }}
    </div>

    <ul class="dropdown-menu {{ $alignClass }} shadow border-theme bg-theme-surface p-1 {{ $contentClasses }}" style="min-width: 12rem;">
        {{ $content }}
    </ul>
</div>
