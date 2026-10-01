@props([
    'author',
    'size' => 'sm',
])

@auth
    @if(auth()->id() !== $author->id)
        @php
            $isFollowing = auth()->user()->isFollowing($author);
            $sizeClass = $size === 'md' ? 'px-4 py-2 small' : 'px-3 py-1 small';
        @endphp
        <button type="button"
                data-action="follow"
                data-url="{{ route('authors.follow', $author->id) }}"
                data-author-id="{{ $author->id }}"
                aria-label="{{ $isFollowing ? 'Hủy theo dõi ' . $author->name : 'Theo dõi ' . $author->name }}"
                class="btn rounded-pill fw-semibold {{ $sizeClass }} {{ $isFollowing ? 'btn-outline-theme' : 'btn-primary' }}">
            {{ $isFollowing ? 'Đang theo dõi' : 'Theo dõi' }}
        </button>
    @endif
@else
    <a href="{{ route('login') }}"
       class="btn btn-primary rounded-pill fw-semibold {{ $size === 'md' ? 'px-4 py-2 small' : 'px-3 py-1 small' }}"
       title="Đăng nhập để theo dõi">
        Theo dõi
    </a>
@endauth
