@extends('layouts.public')

@section('content')
<div class="container-fluid py-4 author-comments-page" style="max-width: 1200px;"
     data-feed-shell
     data-feed-base-url="{{ route('author.comments.index') }}">
    <header class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 pb-3 mb-4 border-theme-bottom">
        <div>
            <p class="label-uppercase text-theme-muted small mb-1">Tác giả</p>
            <h1 class="h4 fw-bold text-theme mb-0">Bình luận trên bài viết của bạn</h1>
        </div>
        <a href="{{ route('posts.stats') }}" class="btn btn-sm btn-outline-theme rounded-pill px-3">Quản lý bài viết</a>
    </header>

    <div class="card p-3 border-theme bg-theme-surface shadow-xs rounded-xl mb-4 author-comments-filter-card">
        <form action="{{ route('author.comments.index') }}" method="GET" class="row g-2.5 align-items-end author-comments-filter-form" data-feed-filter-form>
            <div class="col-12 col-md-7">
                <label for="author-comment-search" class="form-label small text-theme-secondary mb-1" style="font-size: 12px;">Tìm bình luận, người viết hoặc bài viết</label>
                <input id="author-comment-search" type="search" name="q" value="{{ $search }}" maxlength="255" placeholder="Tìm bình luận..." class="form-control bg-surface-2 border-theme text-theme rounded-pill" style="height: 40px; font-size: 13.5px;" aria-describedby="author-comment-search-error">
                @error('q')
                    <p id="author-comment-search-error" class="text-danger small mb-0 mt-1" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-7 col-md-3">
                <label for="author-comment-status" class="form-label small text-theme-secondary mb-1" style="font-size: 12px;">Trạng thái</label>
                <select id="author-comment-status" name="status" class="form-select bg-surface-2 border-theme text-theme rounded-pill" style="height: 40px; font-size: 13px;">
                    <option value="">Tất cả</option>
                    <option value="approved" @selected($currentStatus === 'approved')>Đã duyệt</option>
                    <option value="pending" @selected($currentStatus === 'pending')>Chờ xử lý</option>
                    <option value="spam" @selected($currentStatus === 'spam')>Spam</option>
                </select>
            </div>
            <div class="col-5 col-md-2">
                <button type="submit" class="btn btn-editorial-primary rounded-pill w-100 d-inline-flex align-items-center justify-content-center fw-semibold" style="height: 40px; font-size: 13px;">Lọc</button>
            </div>
        </form>
    </div>

    <p id="feed-status" role="status" aria-live="polite" class="visually-hidden"></p>
    <div id="feed-error" role="alert" class="alert alert-danger mb-4 small" hidden>
        <span data-feed-error-message></span>
        <button type="button" data-feed-retry class="btn btn-sm btn-outline-theme ms-2">Thử lại</button>
    </div>

    @include('author.comments.partials.comment-results')
</div>
@endsection
