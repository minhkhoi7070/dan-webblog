@extends('layouts.admin')

@section('admin_title', 'Hồ sơ Người dùng: ' . $user->name)

@section('admin_content')
<!-- Top Control Bar -->
<div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pb-3 border-theme-bottom">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-theme rounded-pill px-3 py-1 text-theme-secondary d-inline-flex align-items-center gap-1.5" style="font-size: 12.5px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Quay lại danh sách
    </a>

    <!-- Lock / Unlock Actions -->
    @if($user->id !== Auth::id())
        @if($user->is_locked)
            <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="m-0"
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
                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3.5 py-1 fw-semibold" style="font-size: 12.5px;">
                    Mở khóa tài khoản
                </button>
            </form>
        @elseif(! $user->isAdmin())
            <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="m-0"
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
                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3.5 py-1 fw-semibold" style="font-size: 12.5px;">
                    Khóa tài khoản
                </button>
            </form>
        @endif
    @endif
</div>

<!-- User Profile Card (High Density) -->
<div class="card p-4 p-sm-5 border-theme bg-theme-surface shadow-xs rounded-xl mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3.5">
        <x-avatar :user="$user" size="lg" class="border border-theme flex-shrink-0" />

        <div class="flex-grow-1 min-w-0">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                    {{ $user->name }}
                </h2>
                @if($user->role === 'admin')
                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill text-uppercase" style="font-size: 10.5px;">
                        Admin
                    </span>
                @elseif($user->role === 'author')
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill text-uppercase" style="font-size: 10.5px;">
                        Author
                    </span>
                @else
                    <span class="badge bg-surface-2 border border-theme text-theme-secondary rounded-pill text-uppercase" style="font-size: 10.5px;">
                        Viewer
                    </span>
                @endif

                @if($user->is_locked)
                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill" style="font-size: 10.5px;">
                        Đang bị khóa
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill" style="font-size: 10.5px;">
                        Hoạt động
                    </span>
                @endif
            </div>

            <p class="small text-theme-muted mb-0" style="font-size: 12.5px;">
                {{ $user->email }} &bull; Gia nhập ngày {{ $user->created_at->format('d/m/Y') }}
            </p>

            @if($user->bio)
                <div class="small text-theme-secondary bg-surface-2 p-3 rounded-xl border border-theme mt-3 mb-0 fst-italic" style="font-size: 13px; line-height: 1.6;">
                    "{{ $user->bio }}"
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Count Stats -->
    <div class="row g-3 text-center mt-3 pt-4 border-theme-top">
        <div class="col-6 col-sm-3">
            <span class="label-uppercase text-theme-muted d-block" style="font-size: 11px;">Bài viết</span>
            <p class="h4 fw-bold text-theme mt-1 mb-0">{{ number_format($user->posts_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="label-uppercase text-theme-muted d-block" style="font-size: 11px;">Bình luận</span>
            <p class="h4 fw-bold text-theme mt-1 mb-0">{{ number_format($user->comments_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="label-uppercase text-theme-muted d-block" style="font-size: 11px;">Người theo dõi</span>
            <p class="h4 fw-bold text-theme mt-1 mb-0">{{ number_format($user->followers_count) }}</p>
        </div>
        <div class="col-6 col-sm-3">
            <span class="label-uppercase text-theme-muted d-block" style="font-size: 11px;">Đang theo dõi</span>
            <p class="h4 fw-bold text-theme mt-1 mb-0">{{ number_format($user->following_count) }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- User Recent Posts -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
            <h3 class="label-uppercase text-theme-muted mb-3.5" style="font-size: 11px;">Bài viết gần đây</h3>
            @if($recentPosts->isEmpty())
                <p class="small text-theme-muted py-4 text-center mb-0">Người dùng chưa đăng bài viết nào.</p>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($recentPosts as $post)
                        <div class="p-2.5 rounded-lg border border-theme bg-surface-2 d-flex align-items-center justify-content-between gap-3">
                            <div class="min-w-0 flex-grow-1">
                                <a href="{{ route('admin.posts.show', $post) }}" class="small fw-semibold text-theme text-decoration-none hover-accent text-truncate d-block" style="font-size: 13px;">
                                    {{ $post->title }}
                                </a>
                                <span class="small text-theme-muted" style="font-size: 11px;">
                                    {{ $post->category->name }} &bull; {{ $post->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-sm btn-outline-theme rounded-pill px-2.5 py-1 text-theme-secondary flex-shrink-0" style="font-size: 11px;" aria-label="Xem bài viết {{ $post->title }}">
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
        <div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl h-100">
            <h3 class="label-uppercase text-theme-muted mb-3.5" style="font-size: 11px;">Bình luận gần đây</h3>
            @if($recentComments->isEmpty())
                <p class="small text-theme-muted py-4 text-center mb-0">Người dùng chưa có bình luận nào.</p>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($recentComments as $comment)
                        <div class="p-2.5 rounded-lg border border-theme bg-surface-2">
                            <p class="small text-theme mb-1 line-clamp-2">"{{ $comment->body }}"</p>
                            <p class="small text-theme-muted mb-0" style="font-size: 11px;">Trên bài: <a href="{{ route('admin.posts.show', $comment->post) }}" class="text-theme hover-accent text-decoration-none">{{ $comment->post->title }}</a> &bull; {{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

