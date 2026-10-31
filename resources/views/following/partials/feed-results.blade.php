<div id="feed-results" tabindex="-1" aria-label="Bảng tin đang theo dõi"
     data-feed-url="{{ request()->fullUrl() }}"
     data-feed-total="{{ $tab === 'feed' ? ($posts ? $posts->total() : 0) : ($authors ? $authors->total() : 0) }}">
    @if($tab === 'feed')
        <!-- Feed of Followed Authors' Posts -->
        <div class="d-flex flex-column">
            @if($posts && $posts->isNotEmpty())
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach

                @if($posts->hasPages())
                    <div data-feed-pagination class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                @if($followingCount === 0)
                    <!-- Empty State: Not Following Anyone Yet -->
                    <div class="following-empty-state text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-surface-2 border border-theme text-theme-muted mb-3" style="width: 56px; height: 56px;">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <h2 class="h5 fw-bold text-theme mb-2">Bạn chưa theo dõi tác giả nào</h2>
                        <p class="text-theme-secondary small mx-auto mb-0" style="max-width: 440px; font-size: 14px;">
                            Theo dõi tác giả yêu thích để cập nhật bài viết mới.
                        </p>
                    </div>
                @else
                    <!-- Empty State: Followed Authors Have No Posts Yet -->
                    <div class="following-empty-state text-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-surface-2 border border-theme text-theme-muted mb-3" style="width: 56px; height: 56px;">
                            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        <h2 class="h5 fw-bold text-theme mb-2">Chưa có bài viết mới</h2>
                        <p class="text-theme-secondary small mx-auto mb-3" style="max-width: 440px; font-size: 14px;">
                            Các tác giả bạn đang theo dõi chưa có bài viết mới.
                        </p>
                        <a href="{{ route('home') }}" data-feed-type="for-you" class="btn btn-editorial-primary btn-sm rounded-pill px-4 py-2">
                            Khám phá bảng tin Dành cho bạn
                        </a>
                    </div>
                @endif

                <!-- Suggested Authors Section -->
                @if($suggestedAuthors->isNotEmpty())
                    <div class="p-3 p-sm-4 border-theme-top bg-surface-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h3 class="h6 fw-bold text-theme mb-0 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                <svg width="16" height="16" class="text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Gợi ý tác giả nổi bật dành cho bạn
                            </h3>
                            <a href="{{ route('home') }}" data-feed-type="for-you" class="small text-theme-secondary hover-accent text-decoration-none" style="font-size: 12.5px;">
                                Xem thêm bài viết &rarr;
                            </a>
                        </div>

                        <div class="following-author-grid">
                            @foreach($suggestedAuthors as $suggestedAuthor)
                                <div class="min-w-0">
                                    <div class="following-author-card card border-theme bg-theme-surface rounded-xl shadow-2xs h-100">
                                        <div class="following-author-header">
                                            <a href="{{ route('authors.show', $suggestedAuthor) }}" class="following-author-profile text-decoration-none min-w-0">
                                                <x-avatar :user="$suggestedAuthor" size="md" />
                                                <div class="min-w-0">
                                                    <div class="fw-semibold text-theme text-truncate" style="font-size: 14px;">
                                                        {{ $suggestedAuthor->name }}
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="flex-shrink-0">
                                                <x-follow-button :author="$suggestedAuthor" size="sm" />
                                            </div>
                                        </div>
                                        <div class="following-author-stats text-theme-muted small" style="font-size: 12px;">
                                            <span>{{ $suggestedAuthor->followers_count }} người theo dõi</span>
                                            <span>{{ $suggestedAuthor->posts_count }} bài viết</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

    @elseif($tab === 'authors')
        <!-- List of Followed Authors -->
        <div class="p-3 p-sm-4">
            @if($authors && $authors->isNotEmpty())
                <div class="following-author-list">
                    @foreach($authors as $followedAuthor)
                        <div class="min-w-0">
                            <div class="following-author-card card border-theme bg-theme-surface rounded-xl shadow-2xs h-100">
                                <div class="following-author-header">
                                    <a href="{{ route('authors.show', $followedAuthor) }}" class="following-author-profile text-decoration-none min-w-0">
                                        <x-avatar :user="$followedAuthor" size="md" />
                                        <div class="min-w-0">
                                            <h3 class="fw-bold text-theme mb-0 text-truncate" style="font-size: 15px;">
                                                {{ $followedAuthor->name }}
                                            </h3>
                                            <span class="small text-theme-secondary d-block text-truncate mt-1" style="font-size: 12px;">
                                                {{ $followedAuthor->role === 'admin' ? 'Quản trị viên' : 'Tác giả' }}
                                            </span>
                                        </div>
                                    </a>
                                    <div class="flex-shrink-0">
                                        <x-follow-button :author="$followedAuthor" size="sm" />
                                    </div>
                                </div>

                                @if($followedAuthor->bio)
                                    <p class="following-author-bio small text-theme-secondary mb-0 line-clamp-2" style="font-size: 13px; line-height: 1.45;">
                                        {{ $followedAuthor->bio }}
                                    </p>
                                @endif

                                <div class="following-author-stats text-theme-muted small" style="font-size: 12px;">
                                    <span>{{ $followedAuthor->followers_count }} người theo dõi</span>
                                    <span>{{ $followedAuthor->posts_count }} bài viết</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($authors->hasPages())
                    <div data-feed-pagination class="pt-4 d-flex justify-content-center">
                        {{ $authors->links() }}
                    </div>
                @endif
            @else
                <div class="following-empty-state text-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-surface-2 border border-theme text-theme-muted mb-3" style="width: 56px; height: 56px;">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h2 class="h5 fw-bold text-theme mb-2">Bạn chưa theo dõi tác giả nào</h2>
                    <p class="text-theme-secondary small mx-auto mb-0" style="max-width: 440px; font-size: 14px;">
                        Theo dõi tác giả yêu thích để cập nhật bài viết mới.
                    </p>
                </div>

                <!-- Suggested Authors Section -->
                @if($suggestedAuthors->isNotEmpty())
                    <div class="pt-3 border-theme-top">
                        <h3 class="h6 fw-bold text-theme mb-3 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                            <svg width="16" height="16" class="text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Gợi ý tác giả dành cho bạn
                        </h3>
                        <div class="following-author-grid">
                            @foreach($suggestedAuthors as $suggestedAuthor)
                                <div class="min-w-0">
                                    <div class="following-author-card card border-theme bg-theme-surface rounded-xl shadow-2xs h-100">
                                        <div class="following-author-header">
                                            <a href="{{ route('authors.show', $suggestedAuthor) }}" class="following-author-profile text-decoration-none min-w-0">
                                                <x-avatar :user="$suggestedAuthor" size="md" />
                                                <div class="min-w-0">
                                                    <div class="fw-semibold text-theme text-truncate" style="font-size: 14px;">
                                                        {{ $suggestedAuthor->name }}
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="flex-shrink-0">
                                                <x-follow-button :author="$suggestedAuthor" size="sm" />
                                            </div>
                                        </div>
                                        <div class="following-author-stats text-theme-muted small" style="font-size: 12px;">
                                            <span>{{ $suggestedAuthor->followers_count }} người theo dõi</span>
                                            <span>{{ $suggestedAuthor->posts_count }} bài viết</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
