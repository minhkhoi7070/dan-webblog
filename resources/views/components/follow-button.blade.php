@props([
    'author',
    'size' => 'sm',
])

@auth
    @if(auth()->id() !== $author->id)
        @php
            $isFollowing = auth()->user()->isFollowing($author);
            $sizeClass = $size === 'md' ? 'px-5 py-2 text-sm' : 'px-4 py-1.5 text-xs';
        @endphp
        <button type="button"
                data-action="follow"
                data-url="{{ route('authors.follow', $author->id) }}"
                data-author-id="{{ $author->id }}"
                aria-label="{{ $isFollowing ? 'Hủy theo dõi ' . $author->name : 'Theo dõi ' . $author->name }}"
                class="inline-flex items-center justify-center font-semibold rounded-full transition-all duration-150 cursor-pointer {{ $sizeClass }} {{ $isFollowing ? 'border border-[var(--color-border)] bg-transparent text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:border-[var(--color-text-secondary)]' : 'bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 shadow-xs' }}">
            {{ $isFollowing ? 'Đang theo dõi' : 'Theo dõi' }}
        </button>
    @endif
@else
    <a href="{{ route('login') }}"
       class="inline-flex items-center justify-center font-semibold rounded-full bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 transition shadow-xs {{ $size === 'md' ? 'px-5 py-2 text-sm' : 'px-4 py-1.5 text-xs' }}"
       title="Đăng nhập để theo dõi">
        Theo dõi
    </a>
@endauth
