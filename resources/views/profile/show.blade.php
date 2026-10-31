@extends('layouts.public')

@section('content')
    <div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
        <div class="w-100 feed-container threads-profile-shell card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs">

            <!-- Top Context Bar (Desktop only) -->
            <div class="threads-profile-topbar d-none d-md-flex align-items-center justify-content-between sticky-top bg-theme-surface"
                 style="z-index: 1020;">
                <a href="{{ route('posts.index') }}" class="threads-topbar-link">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Quay lại bảng tin</span>
                </a>

                <div class="d-flex align-items-center gap-3">
                    @if ($user->isAuthor() || $user->isAdmin())
                        <a href="{{ route('authors.show', $user) }}" class="threads-topbar-link" title="Trang tác giả công khai">
                            <span>Trang tác giả</span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="threads-topbar-link">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cài đặt</span>
                    </a>
                </div>
            </div>

            <!-- 1. Threads-Inspired Minimalist Profile Hero -->
            <div class="threads-profile-hero">
                <div class="threads-profile-header-row">
                    <!-- Cột Trái: Tên + Username + Role -->
                    <div class="flex-grow-1 min-w-0">
                        <h1 class="threads-profile-name text-theme">
                            {{ $user->name }}
                        </h1>

                        <div class="threads-profile-handle-row">
                            <span class="threads-profile-username">
                                {{ ltrim($user->username ?? strtolower(str_replace(' ', '', $user->name)), '@') }}
                            </span>
                            <span class="threads-role-pill">
                                {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'author' ? 'Tác giả' : 'Người đọc') }}
                            </span>
                        </div>
                    </div>

                    <!-- Cột Phải: Avatar Tròn (không bị viền vuông) -->
                    <div class="threads-profile-avatar-wrap">
                        <x-avatar :user="$user" size="xl" />
                    </div>
                </div>

                <!-- Bio -->
                @if($user->bio)
                    <p class="threads-profile-bio text-theme">{{ $user->bio }}</p>
                @endif

                <!-- Dòng thống kê gọn kiểu Threads ("X đang theo dõi · Y đã lưu") + Icon nhanh bên phải -->
                <div class="threads-profile-meta-bar">
                    <div class="threads-profile-stats-inline">
                        <a href="{{ route('following.index') }}" class="threads-stat-inline-link">
                            <span class="threads-stat-inline-num">{{ $user->following_count }}</span> đang theo dõi
                        </a>
                        <span aria-hidden="true">&middot;</span>
                        <a href="{{ route('favorites.index') }}" class="threads-stat-inline-link">
                            <span class="threads-stat-inline-num">{{ $user->favorite_posts_count }}</span> đã lưu
                        </a>
                        @if($user->email)
                            <span class="d-none d-sm-inline" aria-hidden="true">&middot;</span>
                            <span class="d-none d-sm-inline text-truncate" style="max-width: 200px;">{{ $user->email }}</span>
                        @endif
                    </div>

                    <div class="threads-profile-quick-icons">
                        @if ($user->isAuthor() || $user->isAdmin())
                            <a href="{{ route('authors.show', $user) }}"
                               class="threads-quick-icon-btn"
                               title="Trang tác giả công khai"
                               aria-label="Trang tác giả công khai">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                        <a href="{{ route('favorites.index') }}"
                           class="threads-quick-icon-btn"
                           title="Danh sách bài viết đã lưu"
                           aria-label="Danh sách bài viết đã lưu">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 2 Nút Outlined Cân Đối 50/50 (Giống "Chỉnh sửa trang cá nhân" & "Xem thông tin chi tiết" ở Ảnh 1) -->
                <div class="threads-profile-btn-grid">
                    <button type="button"
                            class="threads-outline-action-btn"
                            id="btn-open-edit-profile"
                            data-bs-toggle="modal"
                            data-bs-target="#profileEditModal">
                        Chỉnh sửa trang cá nhân
                    </button>
                    <a href="{{ route('activity.index') }}" class="threads-outline-action-btn">
                        Xem thông tin chi tiết
                    </a>
                </div>
            </div>

            <!-- 2. Thanh Tabs Ngang Kiểu Threads -->
            <div class="threads-profile-tabs" role="tablist" aria-label="Nội dung trang cá nhân">
                <button type="button"
                        class="threads-profile-tab active"
                        role="tab"
                        aria-selected="true"
                        data-profile-tab-target="posts">
                    <span>Bài viết</span>
                    @if($user->published_posts_count > 0)
                        <span class="opacity-75">({{ $user->published_posts_count }})</span>
                    @endif
                </button>

                <button type="button"
                        class="threads-profile-tab"
                        role="tab"
                        aria-selected="false"
                        data-profile-tab-target="saved">
                    <span>Đã lưu</span>
                    @if($user->favorite_posts_count > 0)
                        <span class="opacity-75">({{ $user->favorite_posts_count }})</span>
                    @endif
                </button>

                <button type="button"
                        class="threads-profile-tab"
                        role="tab"
                        aria-selected="false"
                        data-profile-tab-target="stats">
                    <span>Thống kê</span>
                </button>
            </div>

            <!-- 3. TAB 1: Bài viết & Thanh đăng nhanh ("Có gì mới? [Đăng]") -->
            <div id="profile-panel-posts" data-profile-panel="posts">
                <!-- Dòng "Có gì mới? [Đăng]" chuẩn Ảnh 1 -->
                @if ($user->isAuthor() || $user->isAdmin())
                    <div class="threads-profile-composer">
                        <div class="threads-composer-avatar">
                            <x-avatar :user="$user" size="sm" />
                        </div>
                        <a href="{{ route('posts.create') }}" class="threads-composer-prompt">
                            Có gì mới?
                        </a>
                        <a href="{{ route('posts.create') }}" class="threads-composer-submit">
                            Đăng
                        </a>
                    </div>
                @elseif ($user->role === 'viewer')
                    <div class="threads-profile-composer">
                        <div class="threads-composer-avatar">
                            <x-avatar :user="$user" size="sm" />
                        </div>
                        <span class="threads-composer-prompt">
                            Nâng cấp tài khoản để bắt đầu viết bài trên CloudBlog
                        </span>
                        <form method="POST" action="{{ route('author.become') }}" class="m-0 flex-shrink-0">
                            @csrf
                            <button type="submit" class="threads-composer-submit">
                                Trở thành tác giả
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Danh sách bài viết đã đăng -->
                @if ($userPosts->isNotEmpty())
                    <div class="d-flex flex-column">
                        @foreach ($userPosts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>

                    @if ($user->published_posts_count > $userPosts->count())
                        <div class="p-3 text-center border-theme-top">
                            <a href="{{ route('authors.show', $user) }}" class="threads-topbar-link justify-content-center">
                                <span>Xem tất cả bài viết ({{ $user->published_posts_count }})</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-5 text-center">
                        <p class="text-theme-secondary mb-3" style="font-size: 14px;">
                            Bạn chưa có bài viết nào được xuất bản.
                        </p>
                        @if ($user->isAuthor() || $user->isAdmin())
                            <a href="{{ route('posts.create') }}" class="threads-outline-action-btn d-inline-flex px-4">
                                Tạo bài viết mới
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- 4. TAB 2: Bài viết đã lưu gần đây -->
            <div id="profile-panel-saved" data-profile-panel="saved" hidden>
                @if ($savedPosts->isNotEmpty())
                    <div class="d-flex flex-column">
                        @foreach ($savedPosts as $saved)
                            <a href="{{ route('posts.show', $saved->slug) }}" class="threads-saved-item">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <span class="threads-role-pill">
                                        {{ $saved->category->name }}
                                    </span>
                                    <span class="text-theme-muted" style="font-size: 12px;">
                                        {{ $saved->user->name }}
                                    </span>
                                </div>
                                <div class="fw-semibold text-theme text-break mt-1" style="font-size: 14.5px;">
                                    {{ $saved->title }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="p-3 text-center">
                        <a href="{{ route('favorites.index') }}" class="threads-topbar-link justify-content-center">
                            <span>Xem toàn bộ danh sách đã lưu ({{ $user->favorite_posts_count }})</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                @else
                    <div class="p-5 text-center">
                        <p class="text-theme-secondary mb-0" style="font-size: 14px;">
                            Bạn chưa lưu bài viết nào.
                        </p>
                    </div>
                @endif
            </div>

            <!-- 5. TAB 3: Thống kê tương tác chi tiết -->
            <div id="profile-panel-stats" data-profile-panel="stats" hidden>
                <div class="p-4">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3 border border-theme h-100 d-flex flex-column align-items-center justify-content-center">
                                <span class="fw-bold text-theme" style="font-size: 1.25rem;">{{ $user->comments_count }}</span>
                                <span class="text-theme-secondary mt-1" style="font-size: 12px;">Bình luận</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3 border border-theme h-100 d-flex flex-column align-items-center justify-content-center">
                                <span class="fw-bold text-theme" style="font-size: 1.25rem;">{{ $user->liked_posts_count }}</span>
                                <span class="text-theme-secondary mt-1" style="font-size: 12px;">Đã thích</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <a href="{{ route('favorites.index') }}" class="text-decoration-none d-block h-100">
                                <div class="p-3 rounded-3 border border-theme h-100 d-flex flex-column align-items-center justify-content-center">
                                    <span class="fw-bold text-theme" style="font-size: 1.25rem;">{{ $user->favorite_posts_count }}</span>
                                    <span class="text-theme-secondary mt-1" style="font-size: 12px;">Đã lưu</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-sm-3">
                            <a href="{{ route('following.index') }}" class="text-decoration-none d-block h-100">
                                <div class="p-3 rounded-3 border border-theme h-100 d-flex flex-column align-items-center justify-content-center">
                                    <span class="fw-bold text-theme" style="font-size: 1.25rem;">{{ $user->following_count }}</span>
                                    <span class="text-theme-secondary mt-1" style="font-size: 12px;">Đang theo dõi</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         Threads-Inspired Profile Edit Modal
         ========================================================================= -->
    <div class="modal fade"
         id="profileEditModal"
         tabindex="-1"
         aria-labelledby="profileEditModalLabel"
         aria-hidden="true"
         data-bs-backdrop="true"
         data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered threads-modal-dialog">
            <div class="modal-content threads-modal-content">
                <!-- Header -->
                <div class="threads-modal-header">
                    <h2 class="threads-modal-title" id="profileEditModalLabel">
                        Chỉnh sửa hồ sơ
                    </h2>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Đóng"></button>
                </div>

                <!-- Form -->
                <form id="profile-edit-modal-form"
                      action="{{ route('profile.update') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      novalidate>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="return_to_profile" value="1">

                    <div class="threads-modal-body">
                        <!-- Alert thông báo lỗi chung -->
                        <div id="modal-general-error" class="alert alert-danger py-2 px-3 small d-none mt-3 mb-0" role="alert"></div>

                        <!-- Row 1: Tên hiển thị (Trái) & Avatar (Phải) -->
                        <div class="threads-modal-row">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="flex-grow-1 min-w-0">
                                    <label for="modal-name-input" class="threads-modal-label">
                                        Tên hiển thị <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="threads-modal-input"
                                           id="modal-name-input"
                                           name="name"
                                           value="{{ $user->name }}"
                                           required
                                           maxlength="255"
                                           placeholder="Tên của bạn"
                                           autocomplete="name">
                                    <div class="invalid-feedback d-none mt-1" id="modal-name-error" style="font-size: 12px;"></div>
                                </div>

                                <!-- Avatar Picker 64px tròn -->
                                <div class="d-flex flex-column align-items-center gap-1 flex-shrink-0">
                                    <label for="modal-avatar-input"
                                           class="threads-modal-avatar-picker m-0"
                                           id="modal-avatar-picker-label"
                                           title="Thay đổi ảnh đại diện (Tối đa 2MB: JPG, PNG, WebP)"
                                           role="button"
                                           tabindex="0">
                                        <img id="modal-avatar-preview-img"
                                             src="{{ $user->avatar_url ?: asset('images/default-avatar.svg') }}"
                                             alt="{{ $user->name }}"
                                             data-original-src="{{ $user->avatar_url ?: '' }}">
                                        <div class="threads-modal-avatar-overlay" aria-hidden="true">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    </label>
                                    <input type="file"
                                           id="modal-avatar-input"
                                           name="avatar"
                                           accept="image/png,image/jpeg,image/jpg,image/webp"
                                           class="d-none">
                                    <button type="button"
                                            id="modal-btn-cancel-avatar"
                                            class="btn btn-link text-danger p-0 d-none text-decoration-none"
                                            style="font-size: 11px;">
                                        Hủy ảnh
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback d-none mt-1" id="modal-avatar-error" style="font-size: 12px;"></div>
                        </div>

                        <!-- Row 2: Tên người dùng (@username read-only) -->
                        <div class="threads-modal-row">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label for="modal-username-display" class="threads-modal-label mb-0">
                                    Tên người dùng
                                </label>
                                <span class="badge rounded-pill bg-theme-surface-hover text-theme-secondary border border-theme"
                                      style="font-size: 10.5px; font-weight: 500;">
                                    Được đồng bộ
                                </span>
                            </div>
                            <input type="text"
                                   class="threads-modal-input"
                                   id="modal-username-display"
                                   value="{{ '@' . ltrim($user->username ?? strtolower(str_replace(' ', '', $user->name)), '@') }}"
                                   readonly
                                   tabindex="-1"
                                   aria-describedby="modal-username-help">
                            <span class="d-block text-theme-secondary mt-1.5" id="modal-username-help" style="font-size: 11.5px;">
                                Tên người dùng định danh tự động theo tên tài khoản của bạn.
                            </span>
                        </div>

                        <!-- Row 3: Tiểu sử (Bio textarea) -->
                        <div class="threads-modal-row">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label for="modal-bio-input" class="threads-modal-label mb-0">
                                    Tiểu sử
                                </label>
                                <span id="modal-bio-counter" class="text-theme-secondary" style="font-size: 11.5px;">
                                    {{ mb_strlen($user->bio ?? '', 'UTF-8') }}/500
                                </span>
                            </div>
                            <textarea class="threads-modal-input"
                                      id="modal-bio-input"
                                      name="bio"
                                      rows="3"
                                      maxlength="500"
                                      placeholder="+ Viết tiểu sử để giới thiệu bản thân...">{{ $user->bio }}</textarea>
                            <div class="invalid-feedback d-none mt-1" id="modal-bio-error" style="font-size: 12px;"></div>
                        </div>
                    </div>

                    <!-- Footer: Nút Lưu thay đổi -->
                    <div class="threads-modal-footer p-3 pt-2">
                        <button type="submit"
                                id="modal-submit-btn"
                                class="threads-modal-save-btn">
                            <span class="spinner-border spinner-border-sm d-none" id="modal-submit-spinner" role="status" aria-hidden="true"></span>
                            <span id="modal-submit-label">Lưu thay đổi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function initProfileTabs() {
                const tabs = document.querySelectorAll('[data-profile-tab-target]');
                const panels = document.querySelectorAll('[data-profile-panel]');

                tabs.forEach(function (tab) {
                    if (tab.dataset.tabsBound) return;
                    tab.dataset.tabsBound = 'true';

                    tab.addEventListener('click', function () {
                        const target = tab.getAttribute('data-profile-tab-target');

                        tabs.forEach(function (t) {
                            const isActive = t === tab;
                            t.classList.toggle('active', isActive);
                            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        });

                        panels.forEach(function (panel) {
                            panel.hidden = panel.getAttribute('data-profile-panel') !== target;
                        });
                    });
                });
            }

            function initProfileEditModal() {
                const modalEl = document.getElementById('profileEditModal');
                if (!modalEl || modalEl.dataset.initialized) return;
                modalEl.dataset.initialized = 'true';

                const form = document.getElementById('profile-edit-modal-form');
                const nameInput = document.getElementById('modal-name-input');
                const bioInput = document.getElementById('modal-bio-input');
                const bioCounter = document.getElementById('modal-bio-counter');
                const avatarInput = document.getElementById('modal-avatar-input');
                const previewImg = document.getElementById('modal-avatar-preview-img');
                const btnCancelAvatar = document.getElementById('modal-btn-cancel-avatar');
                const submitBtn = document.getElementById('modal-submit-btn');
                const submitSpinner = document.getElementById('modal-submit-spinner');
                const submitLabel = document.getElementById('modal-submit-label');
                const generalError = document.getElementById('modal-general-error');
                const nameError = document.getElementById('modal-name-error');
                const avatarError = document.getElementById('modal-avatar-error');
                const bioError = document.getElementById('modal-bio-error');

                // Explicit open button binding for instant response
                const openBtn = document.getElementById('btn-open-edit-profile');
                if (openBtn) {
                    openBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (window.bootstrap?.Modal) {
                            const inst = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                            inst.show();
                        }
                    });
                }

                let tempPreviewUrl = null;

                // Bio live character counter
                if (bioInput && bioCounter) {
                    bioInput.addEventListener('input', function () {
                        bioCounter.textContent = `${this.value.length}/500`;
                    });
                }

                // Avatar file selection & preview
                if (avatarInput) {
                    avatarInput.addEventListener('change', function () {
                        if (avatarError) {
                            avatarError.classList.add('d-none');
                            avatarError.textContent = '';
                        }

                        const file = this.files && this.files[0];
                        if (!file) return;

                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            if (avatarError) {
                                avatarError.textContent = 'Định dạng ảnh không hỗ trợ. Vui lòng chọn JPG, PNG hoặc WebP.';
                                avatarError.classList.remove('d-none');
                            }
                            this.value = '';
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            if (avatarError) {
                                avatarError.textContent = 'Kích thước ảnh vượt quá giới hạn 2MB.';
                                avatarError.classList.remove('d-none');
                            }
                            this.value = '';
                            return;
                        }

                        if (tempPreviewUrl) {
                            URL.revokeObjectURL(tempPreviewUrl);
                        }
                        tempPreviewUrl = URL.createObjectURL(file);
                        if (previewImg) {
                            previewImg.src = tempPreviewUrl;
                        }
                        if (btnCancelAvatar) {
                            btnCancelAvatar.classList.remove('d-none');
                        }
                    });
                }

                // Cancel selected avatar
                if (btnCancelAvatar) {
                    btnCancelAvatar.addEventListener('click', function () {
                        if (avatarInput) avatarInput.value = '';
                        if (tempPreviewUrl) {
                            URL.revokeObjectURL(tempPreviewUrl);
                            tempPreviewUrl = null;
                        }
                        if (previewImg) {
                            previewImg.src = previewImg.dataset.originalSrc || '';
                        }
                        if (avatarError) {
                            avatarError.classList.add('d-none');
                            avatarError.textContent = '';
                        }
                        this.classList.add('d-none');
                    });
                }

                function clearErrors() {
                    if (generalError) {
                        generalError.classList.add('d-none');
                        generalError.textContent = '';
                    }
                    if (nameInput) nameInput.classList.remove('is-invalid');
                    if (bioInput) bioInput.classList.remove('is-invalid');
                    if (nameError) {
                        nameError.classList.add('d-none');
                        nameError.textContent = '';
                    }
                    if (avatarError) {
                        avatarError.classList.add('d-none');
                        avatarError.textContent = '';
                    }
                    if (bioError) {
                        bioError.classList.add('d-none');
                        bioError.textContent = '';
                    }
                }

                // Let Bootstrap compensate for the page scrollbar without shifting the profile shell.
                modalEl.addEventListener('show.bs.modal', function () {
                    document.documentElement.style.setProperty('scrollbar-gutter', 'auto');
                });

                modalEl.addEventListener('hidden.bs.modal', function () {
                    clearErrors();
                    if (tempPreviewUrl) {
                        URL.revokeObjectURL(tempPreviewUrl);
                        tempPreviewUrl = null;
                    }
                    if (avatarInput) avatarInput.value = '';
                    if (previewImg) {
                        previewImg.src = previewImg.dataset.originalSrc || '';
                    }
                    if (btnCancelAvatar) {
                        btnCancelAvatar.classList.add('d-none');
                    }
                    document.documentElement.style.removeProperty('scrollbar-gutter');

                    const openBtn = document.getElementById('btn-open-edit-profile');
                    if (openBtn) openBtn.focus();
                });

                // Focus name input on shown
                modalEl.addEventListener('shown.bs.modal', function () {
                    if (nameInput) {
                        nameInput.focus();
                        nameInput.select();
                    }
                });

                // AJAX Form Submit
                if (form) {
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        clearErrors();

                        if (submitBtn) submitBtn.disabled = true;
                        if (submitSpinner) submitSpinner.classList.remove('d-none');
                        if (submitLabel) submitLabel.textContent = 'Đang lưu...';

                        const formData = new FormData(form);

                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                                || form.querySelector('input[name="_token"]')?.value
                                || '';

                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            });

                            const data = await response.json().catch(() => ({}));

                            if (response.ok) {
                                // 1. Update DOM on Profile page
                                if (data.user) {
                                    const profileNameEl = document.querySelector('.threads-profile-name');
                                    if (profileNameEl && data.user.name) {
                                        profileNameEl.textContent = data.user.name;
                                    }

                                    const profileUsernameEl = document.querySelector('.threads-profile-username');
                                    if (profileUsernameEl && data.user.username) {
                                        profileUsernameEl.textContent = data.user.username.replace(/^@/, '');
                                    }

                                    const heroEl = document.querySelector('.threads-profile-hero');
                                    const metaBar = document.querySelector('.threads-profile-meta-bar');
                                    let profileBioEl = document.querySelector('.threads-profile-bio');

                                    if (data.user.bio) {
                                        if (profileBioEl) {
                                            profileBioEl.textContent = data.user.bio;
                                            profileBioEl.classList.remove('d-none');
                                        } else if (heroEl && metaBar) {
                                            profileBioEl = document.createElement('p');
                                            profileBioEl.className = 'threads-profile-bio text-theme';
                                            profileBioEl.textContent = data.user.bio;
                                            heroEl.insertBefore(profileBioEl, metaBar);
                                        }
                                    } else if (profileBioEl) {
                                        profileBioEl.remove();
                                    }

                                    const modalUsernameDisplay = document.getElementById('modal-username-display');
                                    if (modalUsernameDisplay && data.user.username) {
                                        modalUsernameDisplay.value = '@' + data.user.username.replace(/^@/, '');
                                    }

                                    // 2. Synchronize Avatar
                                    if (data.user.avatar_url) {
                                        if (previewImg) {
                                            previewImg.dataset.originalSrc = data.user.avatar_url;
                                            previewImg.src = data.user.avatar_url;
                                        }

                                        if (typeof window.syncUserAvatar === 'function') {
                                            window.syncUserAvatar(data.user.id, data.user.avatar_url, data.user.name);
                                        }
                                    }
                                }

                                // 3. Close Modal
                                const modalInstance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                                modalInstance.hide();

                                // 4. Toast notification
                                if (typeof window.showToast === 'function') {
                                    window.showToast(data.message || 'Cập nhật thông tin hồ sơ thành công!', 'success');
                                }
                            } else if (response.status === 422) {
                                if (data.errors) {
                                    if (data.errors.name && nameError) {
                                        nameError.textContent = data.errors.name[0];
                                        nameError.classList.remove('d-none');
                                        if (nameInput) nameInput.classList.add('is-invalid');
                                    }
                                    if (data.errors.avatar && avatarError) {
                                        avatarError.textContent = data.errors.avatar[0];
                                        avatarError.classList.remove('d-none');
                                    }
                                    if (data.errors.bio && bioError) {
                                        bioError.textContent = data.errors.bio[0];
                                        bioError.classList.remove('d-none');
                                        if (bioInput) bioInput.classList.add('is-invalid');
                                    }
                                } else if (generalError) {
                                    generalError.textContent = data.message || 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.';
                                    generalError.classList.remove('d-none');
                                }
                            } else {
                                if (generalError) {
                                    generalError.textContent = data.message || 'Đã xảy ra lỗi máy chủ. Vui lòng thử lại sau.';
                                    generalError.classList.remove('d-none');
                                }
                            }
                        } catch (err) {
                            console.error('Profile update error:', err);
                            if (generalError) {
                                generalError.textContent = 'Lỗi kết nối mạng. Vui lòng kiểm tra lại đường truyền.';
                                generalError.classList.remove('d-none');
                            }
                        } finally {
                            if (submitBtn) submitBtn.disabled = false;
                            if (submitSpinner) submitSpinner.classList.add('d-none');
                            if (submitLabel) submitLabel.textContent = 'Lưu thay đổi';
                        }
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                initProfileTabs();
                initProfileEditModal();
            });
            document.addEventListener('blogmnm:page-navigated', function () {
                initProfileTabs();
                initProfileEditModal();
            });

            if (document.readyState === 'interactive' || document.readyState === 'complete') {
                initProfileTabs();
                initProfileEditModal();
            }
        })();
    </script>
@endsection
