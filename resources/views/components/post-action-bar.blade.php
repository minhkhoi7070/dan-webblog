@props([
    'post',
    'showLabels' => false,
])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center gap-4 text-theme-secondary']) }}>
    <!-- Like -->
    <x-like-button :post="$post" :show-count="true" />

    <!-- Comment -->
    <a href="{{ route('posts.show', $post->slug) }}#comments"
       aria-label="Xem bình luận bài viết"
       class="d-flex align-items-center gap-1 text-theme-secondary text-decoration-none">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="small font-medium">{{ ($post->comments_count ?? 0) > 0 ? $post->comments_count : '' }}</span>
    </a>

    <!-- Views -->
    <div class="d-flex align-items-center gap-1 text-theme-secondary opacity-75 user-select-none" title="Lượt xem">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <span class="small font-medium">{{ number_format($post->views ?? 0) }}</span>
    </div>

    <!-- Favorite / Save (right aligned) -->
    <div class="ms-auto">
        <x-favorite-button :post="$post" :show-label="$showLabels" />
    </div>
</div>
