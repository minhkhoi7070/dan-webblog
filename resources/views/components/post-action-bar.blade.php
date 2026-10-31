@props([
    'post',
    'showLabels' => false,
])

<div {{ $attributes->merge(['class' => 'd-flex align-items-center text-theme-secondary threads-action-bar w-100']) }}>
    <!-- Left Group: Like – Comment – Share (Evenly spaced, identical vertical alignment) -->
    <div class="d-flex align-items-center gap-3 gap-sm-4">
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

        <!-- 3. Share (Lucide Send paper plane pointing to top-right, no fake count) -->
        <button type="button"
                onclick="if(navigator.share){navigator.share({title: '{{ addslashes($post->title) }}', url: '{{ route('posts.show', $post->slug) }}'});}else{navigator.clipboard.writeText('{{ route('posts.show', $post->slug) }}'); window.showToast ? window.showToast('Đã sao chép liên kết chia sẻ!') : alert('Đã sao chép liên kết!');}"
                class="btn p-0 border-0 bg-transparent d-flex align-items-center text-theme-secondary text-decoration-none cursor-pointer btn-action-interactive hover-accent threads-action-btn"
                title="Chia sẻ bài viết"
                aria-label="Chia sẻ bài viết">
            <svg style="width: 19px; height: 19px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </div>

    <!-- 4. Save / Favorite (Bookmark aligned to the far right margin) -->
    <div class="ms-auto d-flex align-items-center">
        <x-favorite-button :post="$post" :show-label="$showLabels" />
    </div>
</div>
