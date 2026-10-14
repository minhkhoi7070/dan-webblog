@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
<<<<<<< HEAD
    <div class="card border-0 border-sm shadow-sm w-100 p-4 p-sm-5" style="max-width: 680px; border-radius: 1.25rem;">
        
        <!-- Social Header -->
        <div class="d-flex align-items-start justify-content-between gap-3 pb-4 border-bottom">
            <div class="flex-grow-1 overflow-hidden">
                <h1 class="h3 fw-bold text-body text-truncate m-0">
                    {{ $user->name }}
                </h1>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="small text-secondary fw-medium">
                        {{ '@' . ($user->username ?? strtolower(str_replace(' ', '', $user->name))) }}
                    </span>
                    <x-badge variant="status-draft" size="xs" class="text-capitalize">
                        {{ $user->role }}
                    </x-badge>
                </div>
                <p class="small text-secondary mt-1 mb-0">{{ $user->email }}</p>
            </div>

            <!-- Avatar -->
            <x-avatar :user="$user" size="2xl" class="shadow-sm" />
        </div>

        <!-- Bio -->
        <p class="mt-4 text-body small leading-relaxed">
            {{ $user->bio ?: 'Chưa cập nhật tiểu sử cá nhân. Hãy thêm vài dòng giới thiệu về bản thân bạn!' }}
        </p>

        <!-- Actions -->
        <div class="mt-4 d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('profile.edit') }}"
               class="btn btn-sm btn-dark rounded-pill px-4 py-2 fw-semibold flex-grow-1 text-center shadow-sm">
                Chỉnh sửa trang cá nhân
            </a>
            <a href="{{ route('favorites.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                Đã lưu
            </a>
            <a href="{{ route('activity.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                Hoạt động
            </a>
        </div>

        <!-- Statistics Grid -->
        <div class="mt-5 pt-4 border-top">
            <h3 class="small fw-bold text-uppercase text-secondary tracking-wider mb-3">Thống kê tương tác</h3>
            <div class="row g-2 text-center">
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-body-tertiary border">
                        <span class="d-block h4 fw-bold text-body m-0">{{ $user->comments_count }}</span>
                        <span class="small text-secondary mt-1 d-block fw-medium" style="font-size: 0.75rem;">Bình luận</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-body-tertiary border">
                        <span class="d-block h4 fw-bold text-danger m-0">{{ $user->liked_posts_count }}</span>
                        <span class="small text-secondary mt-1 d-block fw-medium" style="font-size: 0.75rem;">Đã thích</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-body-tertiary border">
                        <span class="d-block h4 fw-bold text-warning m-0">{{ $user->favorite_posts_count }}</span>
                        <span class="small text-secondary mt-1 d-block fw-medium" style="font-size: 0.75rem;">Đã lưu</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="p-3 rounded-3 bg-body-tertiary border">
                        <span class="d-block h4 fw-bold text-primary m-0">{{ $user->following_count }}</span>
                        <span class="small text-secondary mt-1 d-block fw-medium" style="font-size: 0.75rem;">Đang theo dõi</span>
=======
    <div class="w-100 feed-container card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs" style="border-radius: var(--radius-xl, 16px);">
        
        <!-- Top Context Bar -->
        <div class="p-3 px-sm-4 border-theme-bottom d-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('profile.edit') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Cài đặt
                </a>
            </div>
        </div>

        <!-- Minimal Editorial Profile Masthead -->
        <div class="p-4 p-sm-5 border-theme-bottom">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="flex-grow-1 min-w-0">
                    <h1 class="h3 fw-bold text-theme text-truncate mb-1 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                        {{ $user->name }}
                    </h1>
                    <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                        <span class="small text-theme-secondary fw-medium">
                            {{ '@' . ($user->username ?? strtolower(str_replace(' ', '', $user->name))) }}
                        </span>
                        <x-badge variant="status-draft" size="xs" class="text-capitalize">
                            {{ $user->role }}
                        </x-badge>
                    </div>
                    <p class="small text-theme-muted mt-1.5 mb-0" style="font-size: 13px;">{{ $user->email }}</p>
                </div>

                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <x-avatar :user="$user" size="2xl" class="border border-theme shadow-xs" />
                </div>
            </div>

            <!-- Bio -->
            <div class="mt-4">
                <p class="text-theme small lh-relaxed mb-0" style="max-width: 540px; font-family: var(--font-serif, 'Lora', serif); font-size: 14.5px;">
                    {{ $user->bio ?: 'Chưa cập nhật tiểu sử cá nhân. Hãy thêm vài dòng giới thiệu về bản thân bạn!' }}
                </p>
            </div>

            <!-- Action Toolbar -->
            <div class="mt-4 d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="btn btn-editorial-primary btn-sm rounded-pill px-3.5 py-1.5 fw-medium text-center shadow-xs">
                    Chỉnh sửa hồ sơ
                </a>
                <a href="{{ route('favorites.index') }}"
                   class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5">
                    <svg style="width: 14px; height: 14px;" class="text-warning" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    Đã lưu ({{ $user->favorite_posts_count }})
                </a>
                <a href="{{ route('activity.index') }}"
                   class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium">
                    Nhật ký hoạt động
                </a>
                @if($user->isAuthor() || $user->isAdmin())
                    <a href="{{ route('authors.show', $user) }}"
                       class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1.5 fw-medium text-theme-secondary">
                        Trang tác giả công khai
                    </a>
                @endif
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="p-4 p-sm-5 border-theme-bottom">
            <h3 class="label-uppercase mb-3">Thống kê tương tác</h3>
            <div class="row g-2 text-center">
                <div class="col-6 col-sm-3">
                    <div class="profile-stat-box">
                        <span class="d-block h4 fw-bold text-theme m-0">{{ $user->comments_count }}</span>
                        <span class="text-theme-secondary mt-1 d-block fw-medium text-2xs">Bình luận</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="profile-stat-box">
                        <span class="d-block h4 fw-bold m-0" style="color: var(--color-like);">{{ $user->liked_posts_count }}</span>
                        <span class="text-theme-secondary mt-1 d-block fw-medium text-2xs">Đã thích</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="profile-stat-box">
                        <span class="d-block h4 fw-bold m-0" style="color: var(--color-save);">{{ $user->favorite_posts_count }}</span>
                        <span class="text-theme-secondary mt-1 d-block fw-medium text-2xs">Đã lưu</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="profile-stat-box">
                        <span class="d-block h4 fw-bold m-0" style="color: var(--color-accent-text);">{{ $user->following_count }}</span>
                        <span class="text-theme-secondary mt-1 d-block fw-medium text-2xs">Đang theo dõi</span>
>>>>>>> origin/main
                    </div>
                </div>
            </div>
        </div>

        <!-- Become Author CTA (for Viewers) -->
        @if($user->role === 'viewer')
<<<<<<< HEAD
            <div class="mt-4 pt-4 border-top">
                <div class="card bg-body-tertiary border-primary border-opacity-25 p-3 p-sm-4 rounded-3">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                                    <svg class="bi" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </span>
                                <h4 class="h6 fw-bold text-body m-0">Trở thành tác giả BlogMNM</h4>
                            </div>
                            <p class="small text-secondary mt-2 mb-0">
                                Bắt đầu chia sẻ kiến thức, viết bài thảo luận công nghệ và tương tác với cộng đồng độc giả BlogMNM.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('author.become') }}" class="w-100 w-sm-auto flex-shrink-0">
                            @csrf
                            <button type="submit"
                                    class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold w-100 shadow-sm">
=======
            <div class="p-4 p-sm-5 border-theme-bottom">
                <div class="p-3.5 p-sm-4 rounded-3 border border-theme bg-surface-2">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex p-1.5 rounded-2 bg-accent-soft text-accent">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </span>
                                <h4 class="h6 fw-bold text-theme m-0">Trở thành tác giả BlogMNM</h4>
                            </div>
                            <p class="small text-theme-secondary mt-2 mb-0">
                                Bắt đầu chia sẻ kiến thức, viết bài thảo luận công nghệ và tương tác với cộng đồng độc giả BlogMNM.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('author.become') }}" class="w-100 w-sm-auto flex-shrink-0 m-0">
                            @csrf
                            <button type="submit"
                                    class="btn btn-editorial-primary btn-sm w-100 shadow-xs">
>>>>>>> origin/main
                                Trở thành tác giả
                            </button>
                        </form>
                    </div>
<<<<<<< HEAD
=======
                </div>
            </div>
        @endif

        <!-- Authored Posts Section (If user has posts or is author) -->
        @php
            $userPosts = $user->posts()->published()->latest('published_at')->take(3)->with(['category', 'tags'])->withCount(['comments' => fn($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])->get();
        @endphp
        @if($userPosts->isNotEmpty())
            <div class="border-theme-bottom">
                <div class="p-3 px-sm-4 border-theme-bottom bg-theme-surface d-flex align-items-center justify-content-between">
                    <h3 class="label-uppercase m-0">Bài viết đã đăng</h3>
                    <a href="{{ route('authors.show', $user) }}" class="small text-theme-secondary hover-accent text-decoration-none">
                        Xem tất cả ({{ $user->posts()->published()->count() }}) &rarr;
                    </a>
                </div>
                <div class="d-flex flex-column">
                    @foreach($userPosts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        @elseif($user->isAuthor() || $user->isAdmin())
            <div class="p-4 p-sm-5 border-theme-bottom text-center">
                <h3 class="label-uppercase mb-2">Bài viết của bạn</h3>
                <p class="small text-theme-secondary mb-3">Bạn chưa xuất bản bài viết nào trên BlogMNM.</p>
                <a href="{{ route('posts.create') }}" class="btn btn-editorial-primary btn-sm">
                    Viết bài đầu tiên
                </a>
            </div>
        @endif

        <!-- Saved / Favorites Section (Recent bookmarks) -->
        @php
            $savedPosts = $user->favoritePosts()->published()->latest('favorites.created_at')->take(3)->with(['user', 'category'])->get();
        @endphp
        @if($savedPosts->isNotEmpty())
            <div class="p-4 p-sm-5 border-theme-bottom">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <svg style="width: 16px; height: 16px;" class="text-warning" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <h3 class="label-uppercase m-0">Bài viết đã lưu gần đây</h3>
                    </div>
                    <a href="{{ route('favorites.index') }}" class="small text-theme-secondary hover-accent text-decoration-none">
                        Xem danh sách đã lưu ({{ $user->favorite_posts_count }}) &rarr;
                    </a>
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach($savedPosts as $saved)
                        <a href="{{ route('posts.show', $saved->slug) }}" class="reading-list-row group">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <span class="badge bg-theme-surface border border-theme text-theme-secondary" style="font-size: 11px;">
                                    {{ $saved->category->name }}
                                </span>
                                <span class="small text-theme-muted" style="font-size: 11.5px;">
                                    {{ $saved->user->name }}
                                </span>
                            </div>
                            <div class="fw-semibold text-theme text-truncate group-hover:underline">
                                {{ $saved->title }}
                            </div>
                        </a>
                    @endforeach
>>>>>>> origin/main
                </div>
            </div>
        @endif

        <!-- Account Shortcuts -->
<<<<<<< HEAD
        <div class="mt-4 pt-4 border-top d-flex flex-column gap-2">
            <a href="{{ route('favorites.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary text-decoration-none border">
                <span class="small fw-semibold text-body">Xem danh sách bài viết đã lưu</span>
                <span class="text-secondary">&rarr;</span>
            </a>
            <a href="{{ route('activity.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary text-decoration-none border">
                <span class="small fw-semibold text-body">Xem toàn bộ nhật ký hoạt động</span>
                <span class="text-secondary">&rarr;</span>
=======
        <div class="p-4 p-sm-5 d-flex flex-column gap-2">
            <a href="{{ route('favorites.index') }}" class="profile-action-link">
                <span class="d-inline-flex align-items-center gap-2">
                    <svg style="width: 16px; height: 16px;" class="text-warning" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    Xem toàn bộ danh sách bài viết đã lưu
                </span>
                <span class="text-theme-secondary">&rarr;</span>
            </a>
            <a href="{{ route('activity.index') }}" class="profile-action-link">
                <span class="d-inline-flex align-items-center gap-2">
                    <svg style="width: 16px; height: 16px;" class="text-theme-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Xem toàn bộ nhật ký hoạt động
                </span>
                <span class="text-theme-secondary">&rarr;</span>
>>>>>>> origin/main
            </a>
        </div>
    </div>
</div>
@endsection

