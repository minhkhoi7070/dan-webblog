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
            title="{{ $isLiked ? 'Bỏ thích' : 'Thích' }}"
            class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-1.5 text-decoration-none cursor-pointer btn-action-interactive {{ $isLiked ? 'text-danger' : 'text-theme-secondary' }}">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        @if($showCount)
            <span class="small fw-medium like-count">{{ $count > 0 ? $count : '' }}</span>
        @endif
    </button>
@else
    @php
        $count = $post->likers_count ?? $post->likers()->count();
    @endphp
    <a href="{{ route('login') }}"
       aria-label="Đăng nhập để thích"
       class="d-flex align-items-center gap-1.5 text-theme-secondary text-decoration-none btn-action-interactive hover-accent"
       title="Đăng nhập để thích">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        @if($showCount)
            <span class="small fw-medium">{{ $count > 0 ? $count : '' }}</span>
        @endif
    </a>
@endauth
