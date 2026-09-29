<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full font-semibold text-xs text-[var(--color-text)] uppercase tracking-wider shadow-sm hover:bg-[var(--color-surface-hover)] focus:outline-none focus:ring-2 focus:ring-[var(--color-text)] disabled:opacity-30 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>

