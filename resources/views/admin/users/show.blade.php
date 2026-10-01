@extends('layouts.admin')

@section('admin_title', 'Hồ sơ Người dùng: ' . $user->name)

@section('admin_content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.users.index') }}" class="btn btn-link btn-sm text-secondary text-decoration-none p-0">
        &larr; Quay lại danh sách người dùng
    </a>

    <!-- Lock / Unlock Actions -->
    @if($user->id !== Auth::id())
        @if($user->is_locked)
            <form action="{{ route('admin.users.unlock', $user) }}" method="POST"
                  data-confirm="true"
                  data-confirm-title="Mở khóa tài khoản"
                  data-confirm-message="Bạn có chắc muốn mở khóa tài khoản này?"
                  data-confirm-target-name="{{ $user->name }}"
                  data-confirm-target-meta="{{ $user->email }}"
                  data-confirm-description="Người dùng sẽ có thể đăng nhập và tiếp tục tương tác trên BlogMNM."
                  data-confirm-btn-text="Mở khóa tài khoản"
                  data-confirm-btn-class="btn-success"
                  data-confirm-type="success">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 fw-bold">
                    Mở khóa tài khoản
                </button>
            </form>
        @elseif(! $user->isAdmin())
            <form action="{{ route('admin.users.lock', $user) }}" method="POST"
                  data-confirm="true"
                  data-confirm-title="Khóa tài khoản"
                  data-confirm-message="Bạn có chắc muốn khóa tài khoản này?"
                  data-confirm-target-name="{{ $user->name }}"
                  data-confirm-target-meta="{{ $user->email }}"
                  data-confirm-description="Người dùng sẽ không thể đăng nhập hoặc thực hiện các chức năng yêu cầu tài khoản hoạt động cho đến khi được mở khóa."
                  data-confirm-btn-text="Khóa tài khoản"
                  data-confirm-btn-class="btn-danger"
                  data-confirm-type="danger">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 fw-bold">
                    Khóa tài khoản này
                </button>
            </form>
        @endif
    @endif
</div>

<!-- User Profile Card -->
<div class="card p-4 p-sm-5 border shadow-sm rounded-3 mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-4">
        <div class="rounded-3 brand-gradient text-white fw-bold fs-2 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 64px; height: 64px;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h2 class="h4 fw-bold text-body m-0">{{ $user->name }}</h2>
                <x-badge variant="status-draft" size="xs" class="text-uppercase">
                    {{ $user->role }}
                </x-badge>
                @if($user->is_locked)
                    <x-badge variant="status-rejected" size="xs">
                        Đang bị khóa
                    </x-badge>
                @else
                    <x-badge variant="status-published" size="xs">
                        Đang hoạt động
                    </x-badge>
                @endif
            </div>
            <p class="small text-secondary mt-1 mb-0">{{ $user->email }} &bull; Tham gia từ {{ $user->created_at->format('d/m/Y') }}</p>
            @if($user->bio)
                <p class="small text-body bg-body-tertiary p-3 rounded-3 border mt-3 mb-0 fst-italic">{{ $user->bio }}</p>
            @endif
        </div>
    </div>

    <!-- Quick Count Stats -->
    <div class="row g-3 text-center mt-4 pt-4 border-top">
        <div class="col-6 col-sm-3">
            <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.75rem;">Bài viết</span>
            <p class="h4 fw-bold text-body mt-1 mb-0">{{ number_format($user->posts_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.75rem;">Bình luận</span>
            <p class="h4 fw-bold text-body mt-1 mb-0">{{ number_format($user->comments_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.75rem;">Người theo dõi</span>
            <p class="h4 fw-bold text-body mt-1 mb-0">{{ number_format($user->followers_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="small fw-semibold text-secondary text-uppercase d-block" style="font-size: 0.75rem;">Đang theo dõi</span>
            <p class="h4 fw-bold text-body mt-1 mb-0">{{ number_format($user->following_count) }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- User Recent Posts -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border shadow-sm rounded-3 h-100">
            <h3 class="small fw-bold text-secondary text-uppercase tracking-wider mb-3">Bài viết gần đây</h3>
            @if($recentPosts->isEmpty())
                <p class="small text-secondary py-4 text-center mb-0">Người dùng chưa đăng bài viết nào.</p>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentPosts as $post)
                        <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between gap-3 bg-transparent">
                            <div class="min-w-0 flex-grow-1">
                                <a href="{{ route('admin.posts.show', $post) }}" class="small fw-bold text-body text-decoration-none text-truncate d-block">
                                    {{ $post->title }}
                                </a>
                                <span class="small text-secondary" style="font-size: 0.75rem;">{{ $post->category->name }} &bull; {{ $post->status }} &bull; {{ $post->created_at->format('d/m/Y') }}</span>
                            </div>
                            <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold flex-shrink-0" style="font-size: 0.75rem;">
                                Xem
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- User Recent Comments -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border shadow-sm rounded-3 h-100">
            <h3 class="small fw-bold text-secondary text-uppercase tracking-wider mb-3">Bình luận gần đây</h3>
            @if($recentComments->isEmpty())
                <p class="small text-secondary py-4 text-center mb-0">Người dùng chưa có bình luận nào.</p>
            @else
                <div class="list-group list-group-flush">
                    @foreach($recentComments as $comment)
                        <div class="list-group-item px-0 py-3 bg-transparent">
                            <p class="small text-body mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">"{{ $comment->body }}"</p>
                            <p class="small text-secondary mb-0" style="font-size: 0.75rem;">Trên bài: {{ $comment->post->title }} &bull; {{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
