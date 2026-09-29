@props([
    'post',
    'showCount' => true,
])

@auth
    @php
        $isLiked = $post->isLikedBy(auth()->user());
        $count = $post->likers_count ?? $post->likers()->count();
    @endphp
    <button type="button"
            data-action="like"
            data-url="{{ route('posts.like', $post->id) }}"
            data-post-id="{{ $post->id }}"
            aria-label="{{ $isLiked ? 'Bỏ thích bài viết' : 'Thích bài viết' }}"
            class="flex items-center gap-1.5 transition-colors group cursor-pointer {{ $isLiked ? 'text-rose-500' : 'text-[var(--color-text-secondary)] hover:text-rose-500' }}">
        <svg class="w-5 h-5 {{ $isLiked ? 'fill-current' : 'fill-none' }} transition-transform group-hover:scale-110" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        @if($showCount)
            <span class="text-xs sm:text-sm font-medium like-count">{{ $count > 0 ? $count : '' }}</span>
        @endif
    </button>
@else
    @php
        $count = $post->likers_count ?? $post->likers()->count();
    @endphp
    <a href="{{ route('login') }}"
       aria-label="Đăng nhập để thích"
       class="flex items-center gap-1.5 text-[var(--color-text-secondary)] hover:text-rose-500 transition-colors group"
       title="Đăng nhập để thích">
        <svg class="w-5 h-5 fill-none transition-transform group-hover:scale-110" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        @if($showCount)
            <span class="text-xs sm:text-sm font-medium">{{ $count > 0 ? $count : '' }}</span>
        @endif
    </a>
@endauth
