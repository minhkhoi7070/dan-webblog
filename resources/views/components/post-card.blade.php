@props(['post'])

<article class="post-card p-3 p-sm-4 border-theme-bottom d-flex flex-column text-theme">
    <!-- 1. Author Header -->
    <header class="d-flex align-items-center justify-content-between gap-2 mb-2">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <a href="{{ route('authors.show', $post->user) }}" class="d-inline-flex flex-shrink-0 text-decoration-none" aria-label="Xem trang cá nhân của {{ $post->user->name }}">
                <x-avatar :user="$post->user" size="sm" />
            </a>
            <div class="d-flex align-items-center gap-1.5 min-w-0 flex-wrap">
                <a href="{{ route('authors.show', $post->user) }}" class="fw-semibold text-sm text-theme text-decoration-none hover-accent text-truncate">
                    {{ $post->user->name }}
                </a>
                <span class="text-theme-muted small user-select-none opacity-50">&bull;</span>
                <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}"
                      class="text-theme-muted text-2xs user-select-none flex-shrink-0"
                      title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                    {{ $post->published_at ? $post->published_at->diffForHumans(null, true, true) : $post->created_at->diffForHumans(null, true, true) }}
                </time>
            </div>
        </div>
    </header>

    <!-- 2. Post Title & Excerpt -->
    <div class="mt-0.5">
        <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none d-block">
            <h2 class="post-card-title text-break">
                {{ $post->title }}
            </h2>
            <p class="post-card-excerpt">
                {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
            </p>
        </a>
    </div>

    <!-- 3. Thumbnail Image (if present) -->
    @if($post->thumbnail)
        <div class="my-2 mb-3">
            <a href="{{ route('posts.show', $post->slug) }}" class="d-block post-thumbnail-wrapper" aria-label="{{ $post->title }}">
                <img src="{{ asset($post->thumbnail) }}"
                     alt="{{ $post->title }}"
                     loading="lazy"
                     class="img-fluid w-100">
            </a>
        </div>
    @endif

    <!-- 4. Category & Tags Metadata -->
    <div class="d-flex align-items-center flex-wrap gap-2 my-2">
        @if($post->category)
            <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
               class="badge rounded-pill bg-theme-surface text-theme-secondary border border-theme hover-accent flex-shrink-0 text-truncate text-decoration-none py-1.5 px-2.5 fw-medium text-11-5"
               title="{{ $post->category->name }}">
                {{ $post->category->name }}
            </a>
        @endif

        @if($post->tags->isNotEmpty())
            @foreach($post->tags as $tag)
                <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                   class="tag-pill text-theme-muted hover-accent text-decoration-none text-11-5">
                    #{{ $tag->name }}
                </a>
            @endforeach
        @endif
    </div>

    <!-- 5. Bottom Action Bar -->
    <footer class="mt-2.5 pt-2 border-theme-top">
        <x-post-action-bar :post="$post" />
    </footer>
</article>

