@props(['post'])

<article class="p-3 p-sm-4 border-theme-bottom d-flex gap-3 text-theme transition-colors">
    <!-- Left Column: Circular Avatar & Thread Line Effect -->
    <div class="d-flex flex-column align-items-center flex-shrink-0">
        <a href="{{ route('authors.show', $post->user) }}" class="d-block text-decoration-none" aria-label="Xem trang cá nhân của {{ $post->user->name }}">
            <x-avatar :user="$post->user" size="md" />
        </a>
        <div class="d-none d-sm-block bg-theme border-theme-left flex-grow-1 mt-2" style="width: 1px;"></div>
    </div>

    <!-- Right Column: Content Body -->
    <div class="flex-grow-1 min-w-0">
        <!-- Author Row: Name, Timestamp, Category -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <a href="{{ route('authors.show', $post->user) }}" class="fw-semibold small text-theme text-decoration-none text-truncate">
                    {{ $post->user->name }}
                </a>
                <span class="text-theme-secondary small user-select-none opacity-50">&bull;</span>
                <span class="text-theme-secondary small user-select-none flex-shrink-0" style="font-size: 11px;" title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                    {{ $post->published_at ? $post->published_at->diffForHumans(null, true, true) : $post->created_at->diffForHumans(null, true, true) }}
                </span>
            </div>

            <!-- Category Pill -->
            <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
               class="badge bg-theme-surface text-theme-secondary border border-theme rounded-pill text-decoration-none small flex-shrink-0">
                {{ $post->category->name }}
            </a>
        </div>

        <!-- Post Title & Excerpt -->
        <div class="mt-1">
            <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none text-theme d-block">
                <h2 class="h5 fw-bold text-theme mb-1 lh-sm">
                    {{ $post->title }}
                </h2>
                <p class="small text-theme-secondary mb-2 line-clamp-3 lh-base">
                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
                </p>
            </a>
        </div>

        <!-- Thumbnail Image (if present) -->
        @if($post->thumbnail)
            <div class="my-3">
                <a href="{{ route('posts.show', $post->slug) }}" class="d-block overflow-hidden border-theme rounded-4">
                    <img src="{{ asset($post->thumbnail) }}"
                         alt="{{ $post->title }}"
                         loading="lazy"
                         class="img-fluid w-100"
                         style="max-height: 380px; object-fit: cover;">
                </a>
            </div>
        @endif

        <!-- Tags List -->
        @if($post->tags->isNotEmpty())
            <div class="d-flex flex-wrap gap-1 my-2">
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="badge rounded-pill text-decoration-none small"
                       style="background-color: rgba(99, 102, 241, 0.1); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2);">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Bottom Action Bar -->
        <div class="mt-3 pt-1">
            <x-post-action-bar :post="$post" />
        </div>
    </div>
</article>
