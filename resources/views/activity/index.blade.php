@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
    <div class="w-100 feed-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 overflow-hidden mb-4 activity-feed-wrapper"
         data-feed-shell>
        
        <!-- Minimalist Threads-Style Single Row Header -->
        <div class="sticky-md-top bg-theme-surface border-theme-bottom px-3 px-sm-4 py-3 d-flex align-items-center justify-content-between activity-header"
             style="z-index: 1020; min-height: 56px;">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <a href="{{ route('posts.index') }}"
                   class="btn-icon-threads text-theme-muted hover-accent text-decoration-none flex-shrink-0 d-none d-md-inline-flex align-items-center justify-content-center"
                   style="width: 32px; height: 32px; border-radius: 50%;"
                   title="Quay lại bảng tin"
                   aria-label="Quay lại bảng tin"
                   data-app-nav>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="fw-bold text-theme mb-0 text-truncate activity-title" style="font-size: 20px; line-height: 1.25; letter-spacing: -0.01em;">
                    Hoạt động
                    <span class="visually-hidden">Nhật ký hoạt động</span>
                </h1>
            </div>

            <!-- Filter Dropdown: Tất cả ▾ / Bình luận / Đã thích -->
            <div class="dropdown">
                <button type="button"
                        class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 fw-medium small activity-filter-btn"
                        id="activityFilterDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="font-size: 13px;">
                    <span data-active-filter-label>
                        @if($activeFilter === 'comments') Bình luận
                        @elseif($activeFilter === 'likes') Đã thích
                        @else Tất cả
                        @endif
                    </span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-theme rounded-3 py-1 bg-theme-surface" aria-labelledby="activityFilterDropdown">
                    <li>
                        <a href="{{ route('activity.index') }}"
                           class="dropdown-item d-flex align-items-center justify-content-between small py-2 px-3 {{ $activeFilter === 'all' ? 'active fw-semibold' : '' }}"
                           data-activity-filter="all">
                            <span>Tất cả</span>
                            <svg class="check-icon {{ $activeFilter === 'all' ? '' : 'd-none' }}" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('activity.index', ['type' => 'comments']) }}"
                           class="dropdown-item d-flex align-items-center justify-content-between small py-2 px-3 {{ $activeFilter === 'comments' ? 'active fw-semibold' : '' }}"
                           data-activity-filter="comments">
                            <span>Bình luận</span>
                            <svg class="check-icon {{ $activeFilter === 'comments' ? '' : 'd-none' }}" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('activity.index', ['type' => 'likes']) }}"
                           class="dropdown-item d-flex align-items-center justify-content-between small py-2 px-3 {{ $activeFilter === 'likes' ? 'active fw-semibold' : '' }}"
                           data-activity-filter="likes">
                            <span>Đã thích</span>
                            <svg class="check-icon {{ $activeFilter === 'likes' ? '' : 'd-none' }}" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Single Column Activity Stream -->
        <div class="d-flex flex-column activity-feed-stream" id="activity-stream">
            @forelse($allActivities as $activity)
                @php
                    $post = $activity['post'] ?? null;
                    $hasPost = !empty($post);
                    $postUrl = $hasPost ? route('posts.show', $post->slug) : '#';
                    $postTitle = $hasPost ? $post->title : 'Bài viết không còn tồn tại';
                    $thumbnail = $hasPost && $post->thumbnail ? asset($post->thumbnail) : null;
                    $isMatchFilter = ($activeFilter === 'all' || $activeFilter === $activity['type']);
                @endphp

                <article class="activity-item px-3 px-sm-4 py-3 border-theme-bottom d-flex gap-3 position-relative {{ $isMatchFilter ? '' : 'd-none' }}"
                         data-activity-item
                         data-activity-type="{{ $activity['type'] }}">

                    <!-- 1. Left Avatar Column with Activity Type Badge -->
                    <div class="activity-item-avatar-col flex-shrink-0 position-relative">
                        <x-avatar :user="auth()->user()" size="md" />
                        <span class="activity-item-badge badge-{{ $activity['type'] === 'comments' ? 'comment' : 'like' }}"
                              title="{{ $activity['type'] === 'comments' ? 'Bình luận' : 'Đã thích' }}"
                              aria-hidden="true">
                            @if($activity['type'] === 'comments')
                                <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            @else
                                <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                            @endif
                        </span>
                    </div>

                    <!-- 2. Right Content Column -->
                    <div class="activity-item-content-col flex-grow-1 min-w-0">
                        <!-- Action Title & Timestamp -->
                        <div class="d-flex align-items-baseline justify-content-between gap-2 mb-1">
                            <div class="d-flex align-items-baseline gap-1 min-w-0 flex-wrap">
                                <span class="fw-semibold text-theme" style="font-size: 14.5px;">
                                    {{ $activity['action_title'] }}
                                </span>
                                <span class="text-theme-muted mx-1" aria-hidden="true" style="font-size: 12px;">·</span>
                                <time class="text-theme-muted small" style="font-size: 12.5px;" datetime="{{ $activity['created_at']->toIso8601String() }}">
                                    {{ $activity['created_at']->diffForHumans() }}
                                </time>
                            </div>

                            @if($hasPost && $post->category)
                                <span class="small text-theme-muted d-none d-sm-inline text-truncate" style="font-size: 12px; max-width: 130px;">
                                    {{ $post->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Post Title & Comment Body -->
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                @if($hasPost)
                                    <a href="{{ $postUrl }}"
                                       class="fw-semibold text-theme text-decoration-none hover-accent d-block line-clamp-2 activity-post-link mb-1"
                                       style="font-size: 14.5px; line-height: 1.45;"
                                       title="{{ $postTitle }}">
                                        {{ $postTitle }}
                                    </a>
                                @else
                                    <span class="text-theme-muted fst-italic d-block mb-1" style="font-size: 14px;">
                                        {{ $postTitle }}
                                    </span>
                                @endif

                                @if($activity['type'] === 'comments' && !empty($activity['comment']))
                                    <p class="text-theme-secondary mb-0 line-clamp-2 activity-comment-body"
                                       style="font-size: 13.5px; line-height: 1.5; font-weight: 400;">
                                        {{ $activity['comment']->body }}
                                    </p>
                                @endif
                            </div>

                            <!-- Post Thumbnail Preview (if present) -->
                            @if($thumbnail)
                                <a href="{{ $postUrl }}" class="flex-shrink-0 d-block text-decoration-none" tabindex="-1" aria-hidden="true">
                                    <img src="{{ $thumbnail }}"
                                         alt=""
                                         class="activity-thumbnail-preview"
                                         loading="lazy">
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-4 p-sm-5 text-center text-theme-secondary" id="activity-empty-state">
                    <div class="rounded-circle border-theme bg-theme-surface d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <svg style="width: 24px; height: 24px;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="h6 fw-bold text-theme mb-1" id="activity-empty-title">Chưa có hoạt động</h2>
                    <p class="small text-theme-secondary mx-auto mb-3" id="activity-empty-description" style="max-width: 380px;">
                        Hoạt động của bạn sẽ xuất hiện tại đây.
                    </p>
                    <a href="{{ route('posts.index') }}" class="btn btn-sm btn-editorial-primary rounded-pill px-4" data-app-nav>
                        Khám phá bài viết
                    </a>
                </div>
            @endforelse

            <!-- Filter-driven Empty State (Shown when active filter has 0 items) -->
            <div class="p-4 p-sm-5 text-center text-theme-secondary {{ count($activities) === 0 && count($allActivities) > 0 ? '' : 'd-none' }}"
                 id="activity-filtered-empty-state">
                <div class="rounded-circle border-theme bg-theme-surface d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                    <svg style="width: 24px; height: 24px;" class="text-theme-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="h6 fw-bold text-theme mb-1" id="activity-filtered-empty-title">
                    {{ $activeFilter === 'comments' ? 'Chưa có bình luận' : ($activeFilter === 'likes' ? 'Chưa có bài viết đã thích' : 'Chưa có hoạt động') }}
                </h2>
                <p class="small text-theme-secondary mx-auto mb-3" id="activity-filtered-empty-description" style="max-width: 380px;">
                    {{ $activeFilter === 'comments' ? 'Bình luận của bạn trên các bài viết sẽ hiển thị tại đây.' : ($activeFilter === 'likes' ? 'Những bài viết bạn thả tim yêu thích sẽ xuất hiện tại đây.' : 'Hoạt động của bạn sẽ xuất hiện tại đây.') }}
                </p>
                <a href="{{ route('posts.index') }}" class="btn btn-sm btn-editorial-primary rounded-pill px-4" data-app-nav>
                    Khám phá bài viết
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        function initActivityFilter() {
            const filterLinks = document.querySelectorAll('[data-activity-filter]');
            const items = document.querySelectorAll('[data-activity-item]');
            const activeLabel = document.querySelector('[data-active-filter-label]');
            const filteredEmpty = document.getElementById('activity-filtered-empty-state');
            const filteredTitle = document.getElementById('activity-filtered-empty-title');
            const filteredDesc = document.getElementById('activity-filtered-empty-description');

            if (!filterLinks.length) return;

            const emptyMessages = {
                all: {
                    title: 'Chưa có hoạt động',
                    desc: 'Hoạt động của bạn sẽ xuất hiện tại đây.'
                },
                comments: {
                    title: 'Chưa có bình luận',
                    desc: 'Bình luận của bạn trên các bài viết sẽ hiển thị tại đây.'
                },
                likes: {
                    title: 'Chưa có bài viết đã thích',
                    desc: 'Những bài viết bạn thả tim yêu thích sẽ xuất hiện tại đây.'
                }
            };

            filterLinks.forEach(link => {
                // Avoid binding multiple times
                if (link.dataset.filterBound) return;
                link.dataset.filterBound = 'true';

                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const filterType = link.dataset.activityFilter;
                    const filterText = link.querySelector('span')?.textContent.trim() || 'Tất cả';

                    // 1. Update dropdown active states
                    filterLinks.forEach(l => {
                        const isCurrent = (l === link);
                        l.classList.toggle('active', isCurrent);
                        l.classList.toggle('fw-semibold', isCurrent);
                        const check = l.querySelector('.check-icon');
                        if (check) check.classList.toggle('d-none', !isCurrent);
                    });

                    // 2. Update toggle button label
                    if (activeLabel) activeLabel.textContent = filterText;

                    // 3. Close bootstrap dropdown
                    const dropdownBtn = document.getElementById('activityFilterDropdown');
                    if (dropdownBtn && window.bootstrap?.Dropdown) {
                        const instance = window.bootstrap.Dropdown.getInstance(dropdownBtn);
                        if (instance) instance.hide();
                    }

                    // 4. Filter items
                    let visibleCount = 0;
                    items.forEach(item => {
                        const match = (filterType === 'all' || item.dataset.activityType === filterType);
                        item.classList.toggle('d-none', !match);
                        if (match) visibleCount++;
                    });

                    // 5. Update filtered empty state
                    if (filteredEmpty) {
                        if (visibleCount === 0 && items.length > 0) {
                            filteredEmpty.classList.remove('d-none');
                            if (filteredTitle) filteredTitle.textContent = emptyMessages[filterType]?.title || emptyMessages.all.title;
                            if (filteredDesc) filteredDesc.textContent = emptyMessages[filterType]?.desc || emptyMessages.all.desc;
                        } else {
                            filteredEmpty.classList.add('d-none');
                        }
                    }

                    // 6. Update URL in browser history without full page reload
                    const targetUrl = link.getAttribute('href');
                    if (targetUrl && window.history?.replaceState) {
                        window.history.replaceState({ filter: filterType }, '', targetUrl);
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initActivityFilter);
        } else {
            initActivityFilter();
        }

        document.addEventListener('blogmnm:page-navigated', initActivityFilter);
    })();
</script>
@endsection
