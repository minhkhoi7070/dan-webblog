@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-3 py-md-4">
    <article class="w-100 editorial-article-container bg-theme-surface border-0 border-sm-start border-sm-end border-sm-top border-sm-bottom border-theme rounded-0 rounded-sm-4 p-3 p-sm-4 p-md-5 mb-5 overflow-hidden">
        
        <!-- Top Navigation Bar -->
        <div class="mb-4 d-flex align-items-center justify-content-between gap-3">
            <a href="{{ route('posts.index') }}" class="small fw-semibold text-theme-secondary text-decoration-none d-inline-flex align-items-center gap-2 hover-accent" aria-label="Quay lại bảng tin">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>

            <!-- Category Pill -->
            <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
               class="category-pill bg-theme-surface-hover text-theme-secondary border border-theme hover-accent">
                {{ $post->category->name }}
            </a>
        </div>

        <!-- Article Headline (Title lớn chuẩn Editorial) -->
        <h1 class="editorial-article-title mb-4">
            {{ $post->title }}
        </h1>

        <!-- Author Banner with Follow Action -->
        <div class="d-flex align-items-center justify-content-between pb-4 border-theme-bottom mb-4 gap-3">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <a href="{{ route('authors.show', $post->user) }}" class="flex-shrink-0 text-decoration-none" aria-label="Hồ sơ {{ $post->user->name }}">
                    <x-avatar :user="$post->user" size="lg" />
                </a>
                <div class="min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('authors.show', $post->user) }}" class="fw-bold text-theme text-decoration-none text-truncate hover-accent">
                            {{ $post->user->name }}
                        </a>
                        <span class="text-theme-muted small opacity-50">&bull;</span>
                        <span class="small text-theme-muted flex-shrink-0" title="{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : $post->created_at->format('d/m/Y H:i') }}">
                            {{ $post->published_at ? $post->published_at->format('d M, Y') : $post->created_at->format('d M, Y') }}
                        </span>
                    </div>
                    <div class="small text-theme-muted text-truncate mt-0.5" style="font-size: 12px;">
                        {{ '@' . ($post->user->username ?? strtolower(str_replace(' ', '', $post->user->name))) }}
                        <span class="opacity-50 mx-1.5">&bull;</span>
                        <span>{{ number_format($post->views) }} lượt xem</span>
                    </div>
                </div>
            </div>

            <!-- Follow Action Button -->
            <div class="flex-shrink-0">
                <x-follow-button :author="$post->user" size="sm" />
            </div>
        </div>

        <!-- Hero / Featured Image -->
        @if($post->thumbnail)
            <div class="mb-4 mb-md-5 overflow-hidden border-theme rounded-4">
                <img src="{{ asset($post->thumbnail) }}"
                     alt="{{ $post->title }}"
                     class="img-fluid w-100"
                     style="max-height: 480px; object-fit: cover; aspect-ratio: 16/9;">
            </div>
        @endif

        <!-- Article Body Typography (Reading Experience) -->
        <div class="editorial-content mb-5 text-break">
            {!! nl2br(e($post->body)) !!}
        </div>

        <!-- Tags List -->
        @if($post->tags->isNotEmpty())
            <div class="pt-3 pb-3 border-theme-top d-flex flex-wrap gap-2 align-items-center mb-4">
                <span class="small fw-semibold text-theme-muted text-uppercase tracking-wider" style="font-size: 11px;">Thẻ:</span>
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="tag-pill">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Social Action Bar -->
        <div class="pt-3 pb-3 border-theme-top border-theme-bottom mb-4">
            <x-post-action-bar :post="$post" :show-labels="true" class="w-100" />
        </div>

        <!-- Related Posts (if any) -->
        @if($relatedPosts->isNotEmpty())
            <div class="mb-4 mb-md-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="small fw-bold text-uppercase tracking-wider text-theme-muted mb-0" style="font-size: 12px;">Bài viết cùng chuyên mục</h3>
                    <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}" class="small text-theme-muted text-decoration-none hover-accent">
                        Xem thêm &rarr;
                    </a>
                </div>
                <div class="row g-3">
                    @foreach($relatedPosts as $related)
                        <div class="col-12 col-sm-4">
                            <a href="{{ route('posts.show', $related->slug) }}" class="card p-3 border-theme bg-theme-surface text-decoration-none h-100 d-flex flex-column justify-content-between related-post-card rounded-xl">
                                <div>
                                    <span class="badge bg-theme-surface-hover text-accent border border-theme rounded-pill mb-2 px-2 py-0.5" style="font-size: 11px;">{{ $related->category->name }}</span>
                                    <h4 class="small fw-bold text-theme line-clamp-2 mb-2 lh-sm">
                                        {{ $related->title }}
                                    </h4>
                                </div>
                                <span class="small text-theme-muted mt-2" style="font-size: 11px;">
                                    {{ $related->published_at ? $related->published_at->diffForHumans() : $related->created_at->diffForHumans() }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Comments Thread Section -->
        <section id="comments" class="{{ $relatedPosts->isNotEmpty() ? 'mt-4 pt-4 border-theme-top' : '' }}">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-theme-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h5 fw-bold text-theme mb-0">Bình luận</h2>
                    <span class="comment-count-badge badge bg-theme-surface-hover text-theme border border-theme rounded-pill">
                        {{ $post->comments_count }}
                    </span>
                </div>
            </div>

            <!-- Comment Input Box (Authenticated) -->
            @auth
                <form action="{{ route('comments.store', $post->id) }}" method="POST" data-form="comment-form" class="mb-4">
                    @csrf
                    <div class="comment-error text-danger small mb-2 fw-medium"></div>
                    <div class="d-flex gap-3 align-items-start">
                        <x-avatar :user="auth()->user()" size="md" />
                        <div class="flex-grow-1">
                            <textarea name="body"
                                      rows="3"
                                      required
                                      placeholder="Trả lời hoặc chia sẻ góc nhìn của bạn..."
                                      class="form-control mb-2"></textarea>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill px-4">
                                    Đăng bình luận
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <!-- Guest Call to Action -->
                <div class="card p-4 border-theme bg-theme-surface-hover text-center mb-4 rounded-2xl">
                    <h4 class="h6 fw-bold text-theme mb-1">Tham gia thảo luận về bài viết</h4>
                    <p class="small text-theme-secondary mb-3">
                        Đăng nhập hoặc đăng ký tài khoản để bình luận và kết nối với cộng đồng.
                    </p>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-editorial-primary rounded-pill px-4">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-theme rounded-pill px-4">
                            Đăng ký
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Comments List Tree -->
            <div id="comments-list" class="d-flex flex-column gap-3">
                @forelse($post->comments as $comment)
                    <div id="comment-{{ $comment->id }}" class="comment-item p-3 p-sm-4">
                        <!-- Comment Header -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <x-avatar :user="$comment->user" size="sm" />
                                <div>
                                    <span class="fw-bold small text-theme">{{ $comment->user->name }}</span>
                                    <span class="small text-theme-muted ms-2" style="font-size: 11px;">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            @can('delete', $comment)
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="d-inline m-0"
                                      data-confirm="true"
                                      data-confirm-title="Xóa bình luận"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa bình luận này không?"
                                      data-confirm-target-name="{{ $comment->user->name }}"
                                      data-confirm-target-meta="{{ Str::limit($comment->body, 60) }}"
                                      data-confirm-description="Hành động này không thể hoàn tác. Toàn bộ các phản hồi bên dưới cũng sẽ bị xóa."
                                      data-confirm-btn-text="Xóa bình luận"
                                      data-confirm-btn-class="btn-danger"
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none small" title="Xóa">
                                        Xóa
                                    </button>
                                </form>
                            @endcan
                        </div>

                        <!-- Comment Body -->
                        <p class="small text-theme mb-2 ps-4 ms-2 lh-base">
                            {{ $comment->body }}
                        </p>

                        <!-- Reply Action Button -->
                        @auth
                            <div class="ps-4 ms-2">
                                <button type="button"
                                        data-action="toggle-reply"
                                        data-comment-id="{{ $comment->id }}"
                                        class="btn btn-sm btn-link p-0 text-theme-secondary text-decoration-none small hover-accent">
                                    Trả lời &crarr;
                                </button>
                            </div>

                            <!-- Inline Reply Form (hidden by default) -->
                            <form id="reply-box-{{ $comment->id }}"
                                  action="{{ route('comments.store', $post->id) }}"
                                  method="POST"
                                  data-form="comment-form"
                                  class="reply-form d-none mt-3 ps-4 ms-2">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="comment-error text-danger small mb-1 fw-medium"></div>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <textarea name="body"
                                              rows="2"
                                              required
                                              placeholder="Trả lời @ {{ $comment->user->name }}..."
                                              class="form-control form-control-sm"></textarea>
                                    <button type="submit" class="btn btn-sm btn-editorial-primary rounded-pill px-3 align-self-sm-end align-self-stretch flex-shrink-0">
                                        Gửi
                                    </button>
                                </div>
                            </form>
                        @endauth

                        <!-- Nested Replies -->
                        <div class="replies-container {{ $comment->replies && $comment->replies->isNotEmpty() ? 'mt-3 ps-3 ps-sm-4 border-theme-left d-flex flex-column gap-2' : '' }}">
                            @if($comment->replies && $comment->replies->isNotEmpty())
                                @foreach($comment->replies as $reply)
                                    <div id="comment-{{ $reply->id }}" class="reply-item p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <x-avatar :user="$reply->user" size="xs" />
                                                <span class="fw-semibold small text-theme">{{ $reply->user->name }}</span>
                                                <span class="small text-theme-muted" style="font-size: 11px;">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            @can('delete', $reply)
                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="d-inline m-0"
                                                      data-confirm="true"
                                                      data-confirm-title="Xóa phản hồi"
                                                      data-confirm-message="Bạn có chắc chắn muốn xóa phản hồi này không?"
                                                      data-confirm-target-name="{{ $reply->user->name }}"
                                                      data-confirm-target-meta="{{ Str::limit($reply->body, 60) }}"
                                                      data-confirm-description="Hành động này không thể hoàn tác."
                                                      data-confirm-btn-text="Xóa phản hồi"
                                                      data-confirm-btn-class="btn-danger"
                                                      data-confirm-type="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" style="font-size: 11px;">
                                                        Xóa
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                        <p class="small text-theme mb-0 ps-4 lh-base">
                                            {{ $reply->body }}
                                        </p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-theme-secondary small">
                        Chưa có bình luận nào cho bài viết này. Hãy là người đầu tiên tham gia thảo luận!
                    </div>
                @endforelse
            </div>
        </section>
    </article>
</div>
@endsection
