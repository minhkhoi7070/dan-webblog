@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[760px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl p-4 sm:p-8">
        
        <!-- Top Back Bar -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>

            <!-- Category Pill -->
            <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}"
               class="text-xs font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text)] bg-[var(--color-surface-hover)] border border-[var(--color-border)] px-3 py-1 rounded-full transition">
                {{ $post->category->name }}
            </a>
        </div>

        <!-- Author Banner with Follow Action -->
        <div class="flex items-center justify-between pb-6 border-b border-[var(--color-border)] mb-6">
            <div class="flex items-center gap-3.5 min-w-0">
                <a href="{{ route('authors.show', $post->user) }}" class="shrink-0 group">
                    <x-avatar :user="$post->user" size="lg" class="group-hover:opacity-90 transition-opacity" />
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('authors.show', $post->user) }}" class="font-bold text-base text-[var(--color-text)] hover:underline truncate">
                            {{ $post->user->name }}
                        </a>
                        <span class="text-[var(--color-text-secondary)] text-xs opacity-60">&bull;</span>
                        <span class="text-xs text-[var(--color-text-secondary)] shrink-0">
                            {{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="text-xs text-[var(--color-text-secondary)] truncate">
                        {{ '@' . ($post->user->username ?? strtolower(str_replace(' ', '', $post->user->name))) }}
                        <span class="opacity-60 mx-1">&bull;</span>
                        <span>{{ number_format($post->views) }} lượt xem</span>
                    </div>
                </div>
            </div>

            <!-- Follow Action Button -->
            <div class="shrink-0">
                <x-follow-button :author="$post->user" size="sm" />
            </div>
        </div>

        <!-- Article Headline (Single H1) -->
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[var(--color-text)] tracking-tight leading-tight mb-6">
            {{ $post->title }}
        </h1>

        <!-- Hero / Featured Image -->
        @if($post->thumbnail)
            <div class="mb-8 rounded-2xl overflow-hidden border border-[var(--color-border)] bg-[var(--color-surface)] shadow-md">
                <img src="{{ asset($post->thumbnail) }}"
                     alt="{{ $post->title }}"
                     class="w-full h-auto max-h-[460px] object-cover">
            </div>
        @endif

        <!-- Article Body Typography -->
        <div class="text-[var(--color-text)] text-base sm:text-lg leading-relaxed space-y-5 selection:bg-indigo-500/40">
            {!! nl2br(e($post->body)) !!}
        </div>

        <!-- Tags List -->
        @if($post->tags->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-[var(--color-border)] flex flex-wrap gap-2 items-center">
                <span class="text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">Thẻ:</span>
                @foreach($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                       class="text-xs font-medium text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-3 py-1 rounded-full transition">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Social Action Bar -->
        <div class="mt-6 pt-5 border-t border-[var(--color-border)] flex items-center justify-between">
            <x-post-action-bar :post="$post" :show-labels="true" class="w-full" />
        </div>

        <!-- Author Biography Mini-Card -->
        <div class="mt-8 p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] flex items-center gap-4">
            <a href="{{ route('authors.show', $post->user) }}" class="shrink-0">
                <x-avatar :user="$post->user" size="xl" />
            </a>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="font-bold text-[var(--color-text)] text-base">{{ $post->user->name }}</h3>
                    <a href="{{ route('authors.show', $post->user) }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition shrink-0">
                        Xem hồ sơ &rarr;
                    </a>
                </div>
                <p class="text-xs text-[var(--color-text-secondary)] mt-1 line-clamp-2 leading-relaxed">
                    {{ $post->user->bio ?: 'Tác giả chia sẻ các bài viết và góc nhìn chuyên sâu trên nền tảng BlogMNM.' }}
                </p>
            </div>
        </div>

        <!-- Related Posts (if any) -->
        @if($relatedPosts->isNotEmpty())
            <div class="mt-10 pt-8 border-t border-[var(--color-border)]">
                <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--color-text-secondary)] mb-4">Bài viết cùng chuyên mục</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($relatedPosts as $related)
                        <a href="{{ route('posts.show', $related->slug) }}" class="p-4 rounded-2xl bg-[var(--color-surface-hover)] hover:opacity-90 border border-[var(--color-border)] transition flex flex-col justify-between group">
                            <div>
                                <span class="text-[11px] font-semibold text-indigo-400">{{ $related->category->name }}</span>
                                <h4 class="mt-1 text-xs sm:text-sm font-bold text-[var(--color-text)] group-hover:text-indigo-400 line-clamp-2">
                                    {{ $related->title }}
                                </h4>
                            </div>
                            <span class="mt-3 text-[11px] text-[var(--color-text-secondary)]">
                                {{ $related->published_at ? $related->published_at->diffForHumans() : $related->created_at->diffForHumans() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Comments Thread Section -->
        <section id="comments" class="mt-12 pt-8 border-t border-[var(--color-border)]">
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-[var(--color-border)]">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-lg font-bold text-[var(--color-text)]">Bình luận</h2>
                    <span class="comment-count-badge text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)]">
                        {{ $post->comments_count }}
                    </span>
                </div>
            </div>

            <!-- Comment Input Box (Authenticated) -->
            @auth
                <form action="{{ route('comments.store', $post->id) }}" method="POST" data-form="comment-form" class="mb-8">
                    @csrf
                    <div class="comment-error text-xs text-rose-400 mb-2 font-medium"></div>
                    <div class="flex gap-3.5 items-start">
                        <x-avatar :user="auth()->user()" size="md" />
                        <div class="flex-1">
                            <textarea name="body"
                                      rows="3"
                                      required
                                      placeholder="Trả lời hoặc chia sẻ góc nhìn của bạn..."
                                      class="w-full rounded-2xl border border-[var(--color-border)] bg-[var(--color-input)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm p-3.5 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition duration-150"></textarea>
                            <div class="mt-2.5 flex justify-end">
                                <button type="submit" class="px-5 py-2 rounded-full bg-[var(--color-text)] hover:opacity-90 text-[var(--color-bg)] text-xs font-bold transition shadow-sm">
                                    Đăng bình luận
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <!-- Guest Call to Action -->
                <div class="mb-8 p-6 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-center">
                    <h4 class="text-sm font-bold text-[var(--color-text)]">Tham gia thảo luận về bài viết</h4>
                    <p class="mt-1 text-xs text-[var(--color-text-secondary)] max-w-sm mx-auto">
                        Đăng nhập hoặc đăng ký tài khoản để bình luận và kết nối với cộng đồng.
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-2 text-xs font-bold text-[var(--color-bg)] bg-[var(--color-text)] hover:opacity-90 rounded-full transition shadow-sm">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2 text-xs font-bold text-[var(--color-text)] bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] border border-[var(--color-border)] rounded-full transition">
                            Đăng ký tài khoản
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Comments List Tree -->
            <div id="comments-list" class="space-y-4">
                @forelse($post->comments as $comment)
                    <div id="comment-{{ $comment->id }}" class="p-4 sm:p-5 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)]">
                        <!-- Comment Header -->
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$comment->user" size="sm" />
                                <div>
                                    <span class="font-bold text-sm text-[var(--color-text)]">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-[var(--color-text-secondary)] ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            @can('delete', $comment)
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-[var(--color-text-secondary)] hover:text-rose-400 transition" title="Xóa">
                                        Xóa
                                    </button>
                                </form>
                            @endcan
                        </div>

                        <!-- Comment Body -->
                        <p class="text-sm text-[var(--color-text)] leading-relaxed pl-11">
                            {{ $comment->body }}
                        </p>

                        <!-- Reply Action Button -->
                        @auth
                            <div class="pl-11 mt-2.5">
                                <button type="button"
                                        data-action="toggle-reply"
                                        data-comment-id="{{ $comment->id }}"
                                        class="text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition">
                                    Trả lời &crarr;
                                </button>
                            </div>

                            <!-- Inline Reply Form (hidden by default) -->
                            <form id="reply-box-{{ $comment->id }}"
                                  action="{{ route('comments.store', $post->id) }}"
                                  method="POST"
                                  data-form="comment-form"
                                  class="reply-form hidden mt-3 pl-11">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="comment-error text-xs text-rose-400 mb-1 font-medium"></div>
                                <div class="flex gap-2">
                                    <textarea name="body"
                                              rows="2"
                                              required
                                              placeholder="Trả lời @ {{ $comment->user->name }}..."
                                              class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] text-xs p-2.5 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                                    <button type="submit" class="self-end px-4 py-2 rounded-xl bg-[var(--color-text)] hover:opacity-90 text-[var(--color-bg)] text-xs font-bold transition shrink-0">
                                        Gửi
                                    </button>
                                </div>
                            </form>
                        @endauth

                        <!-- Nested Replies -->
                        <div class="replies-container {{ $comment->replies && $comment->replies->isNotEmpty() ? 'mt-4 pl-6 sm:pl-8 space-y-3 border-l border-[var(--color-border)]' : '' }}">
                            @if($comment->replies && $comment->replies->isNotEmpty())
                                @foreach($comment->replies as $reply)
                                    <div id="comment-{{ $reply->id }}" class="p-3.5 rounded-xl bg-[var(--color-surface)] border border-[var(--color-border)]">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <x-avatar :user="$reply->user" size="xs" />
                                                <span class="font-semibold text-xs text-[var(--color-text)]">{{ $reply->user->name }}</span>
                                                <span class="text-[11px] text-[var(--color-text-secondary)]">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            @can('delete', $reply)
                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa phản hồi này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[11px] text-[var(--color-text-secondary)] hover:text-rose-400 transition">
                                                        Xóa
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                        <p class="text-xs sm:text-sm text-[var(--color-text)] pl-8 leading-relaxed">
                                            {{ $reply->body }}
                                        </p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-[var(--color-text-secondary)] text-sm">
                        Chưa có bình luận nào cho bài viết này. Hãy là người đầu tiên tham gia thảo luận!
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
