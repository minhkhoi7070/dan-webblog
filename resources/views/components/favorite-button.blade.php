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
            class="flex items-center gap-1.5 transition-colors group cursor-pointer {{ $isSaved ? 'text-amber-400' : 'text-[var(--color-text-secondary)] hover:text-amber-400' }}">
        <svg class="w-5 h-5 {{ $isSaved ? 'fill-current' : 'fill-none' }} transition-transform group-hover:scale-110" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        @if($showLabel)
            <span class="text-xs sm:text-sm font-medium favorite-label">{{ $isSaved ? 'Đã lưu' : 'Lưu' }}</span>
        @endif
    </button>
@else
    <a href="{{ route('login') }}"
       aria-label="Đăng nhập để lưu bài viết"
       class="flex items-center gap-1.5 text-[var(--color-text-secondary)] hover:text-amber-400 transition-colors group"
       title="Đăng nhập để lưu">
        <svg class="w-5 h-5 fill-none transition-transform group-hover:scale-110" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>
        @if($showLabel)
            <span class="text-xs sm:text-sm font-medium">Lưu</span>
        @endif
    </a>
@endauth
