@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[660px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl">
        
        <!-- Header -->
        <div class="p-5 border-b border-[var(--color-border)] sticky top-0 bg-[var(--color-surface)]/90 backdrop-blur-xl z-30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-[var(--color-text)]">
                        Bài viết đã lưu
                    </h1>
                    <p class="text-xs text-[var(--color-text-secondary)]">
                        Danh sách các bài viết bạn đã bookmark ({{ $posts->total() }} bài viết)
                    </p>
                </div>
            </div>
            <a href="{{ route('posts.index') }}" class="text-xs text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition">
                Khám phá thêm
            </a>
        </div>

        <!-- Feed of Saved Posts -->
        <div class="flex flex-col divide-y divide-[var(--color-border)]">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <x-empty-state
                    title="Bạn chưa lưu bài viết nào"
                    description="Khi bạn tìm thấy bài viết hữu ích, hãy bấm vào biểu tượng lưu để xem lại sau tại đây."
                    :action-url="route('posts.index')"
                    action-label="Khám phá bài viết ngay"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </x-slot:icon>
                </x-empty-state>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="p-5 border-t border-[var(--color-border)] bg-[var(--color-surface)]">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
