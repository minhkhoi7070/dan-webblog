@props(['post'])

@php
    $images = $post->gallery_images ?? [];
    $isFollowing = (auth()->check() && auth()->id() !== $post->user_id) ? auth()->user()->isFollowing($post->user) : false;
    $isOwnerOrAdmin = auth()->check() && (auth()->id() === $post->user_id || auth()->user()->isAdmin());
@endphp

<article class="threads-post px-3 px-sm-4 py-3 border-theme-bottom d-flex gap-3 text-theme position-relative" data-post-id="{{ $post->id }}">
    <!-- 1. Left Column: Avatar & Follow '+' Badge (Signature Threads Feature) -->
    <div class="threads-avatar-col flex-shrink-0">
        <div class="threads-avatar-box">
            <a href="{{ route('authors.show', $post->user) }}" class="d-block text-decoration-none" aria-label="Xem trang cá nhân của {{ $post->user->name }}">
                <x-avatar :user="$post->user" size="md" />
            </a>
            @auth
                @if(auth()->id() !== $post->user_id)
                    <button type="button"
                            data-action="quick-follow-open"
                            data-url="{{ route('authors.follow', $post->user->id) }}"
                            data-author-id="{{ $post->user->id }}"
                            data-author-name="{{ $post->user->name }}"
                            data-following="{{ $isFollowing ? 'true' : 'false' }}"
                            data-login-url="{{ route('login') }}"
                            aria-pressed="{{ $isFollowing ? 'true' : 'false' }}"
                            aria-haspopup="dialog"
                            aria-expanded="false"
                            title="{{ $isFollowing ? 'Đang theo dõi ' . $post->user->name : 'Theo dõi ' . $post->user->name }}"
                            aria-label="{{ $isFollowing ? 'Đang theo dõi ' . $post->user->name : 'Theo dõi ' . $post->user->name }}"
                            class="threads-avatar-follow-btn">
                        <svg class="follow-icon-plus {{ $isFollowing ? 'd-none' : '' }}" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" aria-hidden="true">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                        <svg class="follow-icon-check {{ $isFollowing ? '' : 'd-none' }}" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </button>
                @endif
            @else
                <button type="button"
                        data-action="quick-follow-open"
                        data-guest="true"
                        data-login-url="{{ route('login') }}"
                        data-author-id="{{ $post->user->id }}"
                        data-author-name="{{ $post->user->name }}"
                        aria-haspopup="dialog"
                        aria-expanded="false"
                        title="Đăng nhập để theo dõi {{ $post->user->name }}"
                        aria-label="Đăng nhập để theo dõi {{ $post->user->name }}"
                        class="threads-avatar-follow-btn">
                    <svg class="follow-icon-plus" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            @endauth
        </div>
    </div>

    <!-- 2. Right Column: Content Column (Header, Body, Media, Actions) -->
    <div class="flex-grow-1 min-w-0">
        <!-- Header: Row 1 Author Name + Actions | Row 2 Category · Timestamp -->
        <div class="threads-post-header">
            <div class="threads-header-meta">
                <div class="d-flex align-items-center min-w-0">
                    <a href="{{ route('authors.show', $post->user) }}" class="threads-author-name text-theme hover-accent text-decoration-none">
                        {{ $post->user->name }}
                    </a>
                </div>

                <div class="threads-submeta-row">
                    @if($post->category)
                        <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
                           data-feed-category="{{ $post->category->slug }}"
                           class="threads-category-name text-theme-muted hover-accent text-decoration-none"
                           title="{{ $post->category->name }}">
                            {{ $post->category->name }}
                        </a>
                        <span class="threads-meta-dot text-theme-muted" aria-hidden="true">&middot;</span>
                    @endif

                    <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}"
                          class="threads-post-time text-theme-muted"
                          title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                        {{ $post->published_at ? $post->published_at->diffForHumans(null, true, true) : $post->created_at->diffForHumans(null, true, true) }}
                    </time>
                </div>
            </div>

            <!-- Header Actions: Pencil (if owner/admin) & More Menu (•••) -->
            <div class="threads-header-actions">
                @if($isOwnerOrAdmin)
                    <a href="{{ route('posts.edit', $post) }}"
                       class="btn-icon-threads text-theme-muted hover-accent text-decoration-none p-1"
                       title="Chỉnh sửa bài viết"
                       aria-label="Chỉnh sửa bài viết">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </a>
                @endif

                <div class="dropdown">
                    <button type="button"
                            class="btn-icon-threads text-theme-muted hover-accent border-0 bg-transparent p-1"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Tùy chọn"
                            aria-label="Tùy chọn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <circle cx="5" cy="12" r="1.8"/>
                            <circle cx="12" cy="12" r="1.8"/>
                            <circle cx="19" cy="12" r="1.8"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-md border-theme bg-theme-surface py-1" style="min-width: 175px; border-radius: 12px;">
                        <li>
                            <button type="button"
                                    class="dropdown-item small text-theme py-2 px-3 d-flex align-items-center gap-2"
                                    onclick="navigator.clipboard.writeText('{{ route('posts.show', $post->slug) }}'); window.showToast ? window.showToast('Đã sao chép liên kết bài viết!') : alert('Đã sao chép liên kết!');">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Sao chép liên kết
                            </button>
                        </li>
                        <li>
                            <a class="dropdown-item small text-theme py-2 px-3 d-flex align-items-center gap-2" href="{{ route('authors.show', $post->user) }}">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Trang tác giả
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Post Title (h2) & Content Excerpt (p) separated semantically -->
        <a href="{{ route('posts.show', $post->slug) }}" class="threads-post-link">
            <h2 class="threads-post-title text-break text-theme">
                {{ $post->title }}
            </h2>
            @if($post->excerpt || $post->body)
                <p class="threads-post-excerpt text-break text-theme line-clamp-3">
                    {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                </p>
            @endif
        </a>

        <!-- Tags: Threads minimalist style, evenly spaced -->
        @if($post->tags && $post->tags->isNotEmpty())
            <div class="threads-hashtag-list">
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="threads-hashtag-item">
                        #{{ ltrim($tag->name, '#') }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Media Carousel / Single Card (Threads Style Gallery) -->
        @if(!empty($images))
            <div class="threads-media-wrapper position-relative">
                @if(count($images) > 1)
                    <span class="threads-media-badge position-absolute top-0 end-0 m-2 badge rounded-pill">
                        1/{{ count($images) }}
                    </span>
                    <div class="threads-media-gallery no-scrollbar">
                        @foreach($images as $imgUrl)
                            <a href="{{ route('posts.show', $post->slug) }}" class="threads-media-card flex-shrink-0 text-decoration-none d-block" style="width: calc(65% - 8px); min-width: 200px; max-width: 320px;">
                                <img src="{{ $imgUrl }}" alt="{{ $post->title }}" loading="lazy" style="height: 280px; width: 100%; object-fit: cover; border-radius: 14px;">
                            </a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ route('posts.show', $post->slug) }}" class="threads-media-single d-block text-decoration-none" aria-label="{{ $post->title }}">
                        <div class="rounded-3 overflow-hidden border border-theme" style="border-radius: 14px; aspect-ratio: 16 / 9; max-height: 420px; background-color: var(--color-bg-subtle);">
                            <img src="{{ $images[0] }}" alt="{{ $post->title }}" loading="lazy" class="w-100 h-100" style="max-height: 420px; object-fit: cover; border-radius: 14px;">
                        </div>
                    </a>
                @endif
            </div>
        @endif

        <!-- Bottom Action Bar (Like, Comment, Share, Save) -->
        <div class="threads-action-wrapper">
            <x-post-action-bar :post="$post" />
        </div>
    </div>
</article>
