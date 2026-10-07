@props([
    'user',
    'timestamp' => null,
    'showFollow' => true,
    'size' => 'md',
])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center justify-content-between gap-3']) }}>
    <div class="d-flex align-items-center gap-2 min-w-0">
        <a href="{{ route('authors.show', $user) }}" class="flex-shrink-0 text-decoration-none">
            <x-avatar :user="$user" :size="$size" />
        </a>
        <div class="min-w-0 text-truncate">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('authors.show', $user) }}" class="fw-semibold text-theme text-decoration-none text-truncate small">
                    {{ $user->name }}
                </a>
                @if($timestamp)
                    <span class="text-theme-secondary small">&bull;</span>
                    <span class="text-theme-secondary small text-truncate">{{ $timestamp }}</span>
                @endif
            </div>
            <div class="text-theme-secondary text-truncate" style="font-size: 11px;">
                {{ '@' . ($user->username ?? strtolower(str_replace(' ', '', $user->name))) }}
            </div>
        </div>
    </div>

    @if($showFollow)
        <div class="flex-shrink-0">
            <x-follow-button :author="$user" size="sm" />
        </div>
    @endif
</div>
