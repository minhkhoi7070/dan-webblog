@props([
    'variant' => 'default',
    'size' => 'sm',
])

@php
    $variantClasses = match($variant) {
        'category' => 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)] border border-[var(--color-border)]',
        'tag' => 'bg-indigo-500/10 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-500/20 border border-indigo-500/20',
        'status-draft' => 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] border border-[var(--color-border)]',
        'status-pending' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
        'status-published' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
        'status-rejected' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
        'active' => 'bg-[var(--color-text)] text-[var(--color-bg)] font-semibold shadow-xs',
        default => 'bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)]',
    };

    $sizeClasses = match($size) {
        'xs' => 'text-[11px] px-2 py-0.5',
        'sm' => 'text-xs px-2.5 py-1',
        'md' => 'text-sm px-3.5 py-1.5',
        default => 'text-xs px-2.5 py-1',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full transition-colors duration-150 {$sizeClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</span>
