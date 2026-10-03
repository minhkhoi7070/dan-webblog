@props(['active'])

<a {{ $attributes->merge(['class' => 'nav-link ' . (($active ?? false) ? 'active fw-bold' : '')]) }}>
    {{ $slot }}
</a>
