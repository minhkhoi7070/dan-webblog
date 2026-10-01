@props([
    'title' => 'Không tìm thấy dữ liệu',
    'description' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'p-4 p-sm-5 text-center text-theme-secondary']) }}>
    <div class="rounded-circle border-theme bg-theme-surface d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
        {{ $icon ?? '' }}
        @if(!isset($icon))
            <svg style="width: 24px; height: 24px;" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        @endif
    </div>
    <h3 class="h6 fw-bold text-theme">{{ $title }}</h3>
    @if($description)
        <p class="small text-theme-secondary mx-auto mb-0" style="max-width: 380px;">{{ $description }}</p>
    @endif
    @if($actionUrl && $actionLabel)
        <div class="mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary rounded-pill px-4">
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
