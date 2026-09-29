<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-500 active:bg-rose-700 border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-rose-500 transition ease-in-out duration-150 shadow-sm cursor-pointer disabled:opacity-50']) }}>
    {{ $slot }}
</button>

