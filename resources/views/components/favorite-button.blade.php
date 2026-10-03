@props([
    'post',
    'showLabel' => false,
])

@auth
    @php
        $isSaved = $post->isSavedBy(auth()->user());
    @endphp
    <button type="button"
            data-action="favorite"
            data-url="{{ route('posts.favorite', $post->id) }}"
            data-post-id="{{ $post->id }}"
            aria-label="{{ $isSaved ? 'Xóa khỏi danh sách lưu' : 'Lưu bài viết' }}"
            title="{{ $isSaved ? 'Đã lưu' : 'Lưu bài viết' }}"
            class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-1.5 text-decoration-none cursor-pointer btn-action-interactive {{ $isSaved ? 'text-warning' : 'text-theme-secondary' }}">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        @if($showLabel)
            <span class="small fw-medium favorite-label">{{ $isSaved ? 'Đã lưu' : 'Lưu' }}</span>
        @endif
    </button>
@else
    <a href="{{ route('login') }}"
       aria-label="Đăng nhập để lưu bài viết"
       class="d-flex align-items-center gap-1.5 text-theme-secondary text-decoration-none btn-action-interactive hover-accent"
       title="Đăng nhập để lưu">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        @if($showLabel)
            <span class="small fw-medium">Lưu</span>
        @endif
    </a>
@endauth
