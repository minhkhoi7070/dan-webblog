<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] border border-transparent rounded-full font-bold text-xs uppercase tracking-wider transition-all ease-in-out duration-150 shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>

