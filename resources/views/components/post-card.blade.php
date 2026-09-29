@props(['post'])

<article class="group relative px-4 py-4 sm:px-5 sm:py-5 border-b border-[var(--color-border)] hover:bg-[var(--color-surface-hover)] transition-colors flex gap-3.5 sm:gap-4">
    <!-- Left Column: Circular Avatar & Thread Line Effect -->
    <div class="shrink-0 flex flex-col items-center">
        <a href="{{ route('authors.show', $post->user) }}" class="block group/avatar" aria-label="Xem trang cá nhân của {{ $post->user->name }}">
            <x-avatar :user="$post->user" size="md" class="group-hover/avatar:opacity-90 transition-opacity" />
        </a>
        <div class="w-[1.5px] bg-[var(--color-border)] flex-grow mt-2.5 rounded-full hidden sm:block"></div>
    </div>

    <!-- Right Column: Content Body -->
    <div class="flex-1 min-w-0">
        <!-- Author Row: Name, Timestamp, Category -->
        <div class="flex items-center justify-between gap-2 mb-1.5">
            <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                <a href="{{ route('authors.show', $post->user) }}" class="font-semibold text-sm sm:text-base text-[var(--color-text)] hover:text-indigo-400 truncate hover:underline">
                    {{ $post->user->name }}
                </a>
                <span class="text-[var(--color-text-secondary)] text-xs select-none opacity-60">&bull;</span>
                <span class="text-[var(--color-text-secondary)] text-xs select-none shrink-0" title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                    {{ $post->published_at ? $post->published_at->diffForHumans(null, true, true) : $post->created_at->diffForHumans(null, true, true) }}
                </span>
            </div>

            <!-- Category Pill -->
            <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
               class="shrink-0 text-[11px] font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text)] bg-[var(--color-surface-hover)] border border-[var(--color-border)] px-2.5 py-0.5 rounded-full transition-colors">
                {{ $post->category->name }}
            </a>
        </div>

        <!-- Post Title & Excerpt -->
        <div class="mt-1">
            <a href="{{ route('posts.show', $post->slug) }}" class="block group/text">
                <h2 class="text-base sm:text-lg font-bold text-[var(--color-text)] group-hover/text:text-indigo-400 transition-colors leading-snug mb-1.5">
                    {{ $post->title }}
                </h2>
                <p class="text-sm sm:text-[15px] text-[var(--color-text-secondary)] leading-relaxed line-clamp-3 sm:line-clamp-4">
                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
                </p>
            </a>
        </div>

        <!-- Thumbnail Image (if present) -->
        @if($post->thumbnail)
            <div class="mt-3.5">
                <a href="{{ route('posts.show', $post->slug) }}" class="block w-full rounded-2xl overflow-hidden border border-[var(--color-border)] bg-[var(--color-surface)] group/img">
                    <img src="{{ asset($post->thumbnail) }}"
                         alt="{{ $post->title }}"
                         loading="lazy"
                         class="w-full h-auto max-h-80 sm:max-h-96 object-cover group-hover/img:scale-[1.01] transition-transform duration-300">
                </a>
            </div>
        @endif

        <!-- Tags List -->
        @if($post->tags->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="text-xs font-medium text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-2.5 py-0.5 rounded-md transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Bottom Action Bar -->
        <div class="mt-4 pt-1">
            <x-post-action-bar :post="$post" />
        </div>
    </div>
</article>
