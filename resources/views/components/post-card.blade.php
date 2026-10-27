@props(['post'])

@php
    $images = $post->gallery_images ?? [];
    $isFollowing = (auth()->check() && auth()->id() !== $post->user_id) ? auth()->user()->isFollowing($post->user) : false;
    $isOwnerOrAdmin = auth()->check() && (auth()->id() === $post->user_id || auth()->user()->isAdmin());
@endphp

<article class="threads-post px-3 px-sm-4 py-3 border-theme-bottom d-flex gap-3 text-theme position-relative" data-post-id="{{ $post->id }}">
    <!-- 1. Left Column: Avatar & Follow '+' Badge (Signature Threads Feature) -->
    <div class="d-flex flex-column align-items-center flex-shrink-0">
        <div class="position-relative d-inline-block flex-shrink-0" style="width: 40px; height: 40px;">
            <a href="{{ route('authors.show', $post->user) }}" class="d-block text-decoration-none" aria-label="Xem trang cá nhân của {{ $post->user->name }}">
                <x-avatar :user="$post->user" size="md" />
            </a>
            @auth
                @if(auth()->id() !== $post->user_id)
                    <button type="button"
                            data-action="follow"
                            data-url="{{ route('authors.follow', $post->user->id) }}"
                            data-author-id="{{ $post->user->id }}"
                            data-author-name="{{ $post->user->name }}"
                            data-login-url="{{ route('login') }}"
                            aria-pressed="{{ $isFollowing ? 'true' : 'false' }}"
                            title="{{ $isFollowing ? 'Đang theo dõi' : 'Theo dõi' }}"
                            aria-label="Theo dõi {{ $post->user->name }}"
                            class="threads-avatar-follow-btn {{ $isFollowing ? 'd-none' : '' }}">
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" aria-hidden="true">
                            <path d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="threads-avatar-follow-btn text-decoration-none"
                   title="Đăng nhập để theo dõi {{ $post->user->name }}"
                   aria-label="Đăng nhập để theo dõi {{ $post->user->name }}">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" aria-hidden="true">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            @endauth
        </div>
    </div>

    <!-- 2. Right Column: Header, Body, Media, Actions -->
    <div class="flex-grow-1 min-w-0">
        <!-- Header: Author › Category  Timestamp  [Pencil] [...] -->
        <div class="threads-post-header d-flex align-items-center justify-content-between gap-2 mb-1">
            <div class="d-flex align-items-center gap-1.5 min-w-0 flex-wrap">
                <a href="{{ route('authors.show', $post->user) }}" class="threads-author-name fw-bold text-theme hover-accent text-decoration-none text-truncate" style="font-size: 15px; line-height: 1.25; letter-spacing: -0.01em;">
                    {{ $post->user->name }}
                </a>

                @if($post->category)
                    <svg width="12" height="12" class="text-theme-muted opacity-60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
                       data-feed-category="{{ $post->category->slug }}"
                       class="threads-category-name fw-semibold text-theme hover-accent text-decoration-none text-truncate"
                       style="font-size: 15px; line-height: 1.25; letter-spacing: -0.01em;"
                       title="{{ $post->category->name }}">
                        {{ $post->category->name }}
                    </a>
                @endif

                <time datetime="{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}"
                      class="text-theme-muted ms-1 text-nowrap opacity-60 flex-shrink-0"
                      style="font-size: 13px;"
                      title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                    {{ $post->published_at ? $post->published_at->diffForHumans(null, true, true) : $post->created_at->diffForHumans(null, true, true) }}
                </time>
            </div>

            <!-- Header Actions: Pencil (if owner/admin) & More Menu (•••) -->
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                @if($isOwnerOrAdmin)
                    <a href="{{ route('posts.edit', $post) }}"
                       class="btn-icon-threads text-theme-muted hover-accent text-decoration-none p-1"
                       title="Chỉnh sửa bài viết">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </a>
                @endif

                <div class="dropdown">
                    <button type="button"
                            class="btn-icon-threads text-theme-muted hover-accent border-0 bg-transparent p-1"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Tùy chọn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
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
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Sao chép liên kết
                            </button>
                        </li>
                        <li>
                            <a class="dropdown-item small text-theme py-2 px-3 d-flex align-items-center gap-2" href="{{ route('authors.show', $post->user) }}">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Trang tác giả
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Post Title & Content Excerpt -->
        <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none d-block threads-post-link">
            <h2 class="threads-post-title text-break text-theme fw-normal mb-1" style="font-size: 15px; line-height: 1.48; letter-spacing: -0.01em;">
                <span class="fw-bold d-block">{{ $post->title }}</span>
                @if($post->excerpt || $post->body)
                    <span class="text-theme opacity-90 line-clamp-3 mt-1 d-block" style="font-size: 14.5px;">
                        {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                    </span>
                @endif
            </h2>
        </a>

        <!-- Tags (if any) -->
        @if($post->tags && $post->tags->isNotEmpty())
            <div class="d-flex align-items-center flex-wrap gap-1.5 mt-1.5 mb-1.5">
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="text-accent text-decoration-none small hover-underline"
                       style="font-size: 13px;">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Media Carousel / Single Card (Threads Style Gallery) -->
        @if(!empty($images))
            <div class="threads-media-wrapper position-relative mt-2 mb-2.5">
                @if(count($images) > 1)
                    <span class="threads-media-badge position-absolute top-0 end-0 m-2 badge rounded-pill px-2.5 py-1"
                          style="background: rgba(0, 0, 0, 0.68); color: #fff; font-size: 11px; z-index: 2; backdrop-filter: blur(4px);">
                        1/{{ count($images) }}
                    </span>
                    <div class="threads-media-gallery d-flex gap-2 overflow-x-auto no-scrollbar">
                        @foreach($images as $imgUrl)
                            <a href="{{ route('posts.show', $post->slug) }}" class="threads-media-card flex-shrink-0 text-decoration-none d-block" style="width: calc(65% - 8px); min-width: 200px; max-width: 320px;">
                                <img src="{{ $imgUrl }}" alt="{{ $post->title }}" loading="lazy" style="height: 280px; width: 100%; object-fit: cover; border-radius: 14px;">
                            </a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ route('posts.show', $post->slug) }}" class="threads-media-single d-block text-decoration-none" aria-label="{{ $post->title }}">
                        <div class="rounded-3 overflow-hidden border border-theme" style="border-radius: 14px; max-height: 420px; background-color: var(--color-bg-subtle);">
                            <img src="{{ $images[0] }}" alt="{{ $post->title }}" loading="lazy" class="w-100" style="max-height: 420px; object-fit: cover; border-radius: 14px;">
                        </div>
                    </a>
                @endif
            </div>
        @endif

        <!-- Bottom Action Bar (Heart, Bubble, Repost, Paper plane, Bookmark) -->
        <div class="mt-2.5 pt-1">
            <x-post-action-bar :post="$post" />
        </div>
    </div>
</article>
