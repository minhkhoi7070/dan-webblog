@props([
    'title' => 'Không tìm thấy dữ liệu',
    'description' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'p-12 text-center text-zinc-500']) }}>
    <div class="w-14 h-14 mx-auto rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4 text-zinc-400">
        {{ $icon ?? '' }}
        @if(!isset($icon))
            <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        @endif
    </div>
    <h3 class="text-base font-semibold text-zinc-200">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-zinc-400 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @endif
    @if($actionUrl && $actionLabel)
        <div class="mt-5">
            <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-full text-zinc-950 bg-white hover:bg-zinc-200 transition shadow-sm">
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
