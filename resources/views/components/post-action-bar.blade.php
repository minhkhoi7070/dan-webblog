@props([
    'post',
    'showLabels' => false,
])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center gap-3 gap-sm-4 text-theme-secondary threads-action-bar']) }}>
    <!-- 1. Like (Heart) -->
    <x-like-button :post="$post" :show-count="true" />

    <!-- 2. Comment (Speech Bubble) -->
    <a href="{{ route('posts.show', $post->slug) }}#comments"
       aria-label="Xem bình luận bài viết"
       class="d-flex align-items-center gap-1.5 text-theme-secondary text-decoration-none btn-action-interactive hover-accent threads-action-btn"
       title="Bình luận">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="small fw-medium interaction-count comment-action-count" data-comment-count data-post-id="{{ $post->id }}">{{ ($post->comments_count ?? 0) > 0 ? $post->comments_count : '' }}</span>
    </a>

    <!-- 3. Repost / Loop (Views / Shares) -->
    <div class="d-flex align-items-center gap-1.5 text-theme-secondary user-select-none btn-action-interactive cursor-default threads-action-btn"
         title="{{ number_format($post->views ?? 0) }} lượt tương tác"
         style="font-size: 13.5px;">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span class="small fw-medium">{{ ($post->views ?? 0) > 0 ? ($post->views >= 1000 ? number_format($post->views / 1000, 1, ',', '') . 'K' : $post->views) : '' }}</span>
    </div>

    <!-- 4. Share (Paper plane) -->
    <button type="button"
            onclick="if(navigator.share){navigator.share({title: '{{ addslashes($post->title) }}', url: '{{ route('posts.show', $post->slug) }}'});}else{navigator.clipboard.writeText('{{ route('posts.show', $post->slug) }}'); window.showToast ? window.showToast('Đã sao chép liên kết chia sẻ!') : alert('Đã sao chép liên kết!');}"
            class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-1.5 text-theme-secondary text-decoration-none cursor-pointer btn-action-interactive hover-accent threads-action-btn"
            title="Chia sẻ bài viết"
            aria-label="Chia sẻ bài viết">
        <svg style="width: 19px; height: 19px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
        <span class="small fw-medium">{{ ($post->favorited_by_count ?? 0) > 0 ? $post->favorited_by_count : '' }}</span>
    </button>

    <!-- 5. Favorite / Save (Bookmark) -->
    <div class="ms-auto">
        <x-favorite-button :post="$post" :show-label="$showLabels" />
    </div>
</div>
