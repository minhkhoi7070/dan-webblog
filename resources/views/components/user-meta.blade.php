@props([
    'user',
    'timestamp' => null,
    'showFollow' => true,
    'size' => 'md',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between gap-3']) }}>
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('authors.show', $user) }}" class="shrink-0 group">
            <x-avatar :user="$user" :size="$size" class="group-hover:opacity-90 transition-opacity" />
        </a>
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('authors.show', $user) }}" class="font-semibold text-zinc-100 hover:text-white truncate hover:underline text-sm sm:text-base">
                    {{ $user->name }}
                </a>
                @if($timestamp)
                    <span class="text-zinc-600 text-xs">&bull;</span>
                    <span class="text-zinc-400 text-xs truncate">{{ $timestamp }}</span>
                @endif
            </div>
            <div class="text-xs text-zinc-400 truncate">
                {{ '@' . ($user->username ?? strtolower(str_replace(' ', '', $user->name))) }}
            </div>
        </div>
    </div>

    @if($showFollow)
        <div class="shrink-0">
            <x-follow-button :author="$user" size="sm" />
        </div>
    @endif
</div>
