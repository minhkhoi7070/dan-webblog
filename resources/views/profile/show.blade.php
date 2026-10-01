@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Become Author CTA (for Viewers) -->
        @if($user->role === 'viewer')
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
                                Trở thành tác giả
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Account Shortcuts -->
        <div class="mt-4 pt-4 border-top d-flex flex-column gap-2">
            <a href="{{ route('favorites.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary text-decoration-none border">
                <span class="small fw-semibold text-body">Xem danh sách bài viết đã lưu</span>
                <span class="text-secondary">&rarr;</span>
            </a>
            <a href="{{ route('activity.index') }}" class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary text-decoration-none border">
                <span class="small fw-semibold text-body">Xem toàn bộ nhật ký hoạt động</span>
                <span class="text-secondary">&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection
