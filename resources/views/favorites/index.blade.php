@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme shadow-lg overflow-hidden" style="border-radius: 1.5rem;">
        
        <!-- Header -->
        <div class="p-3 p-sm-4 border-theme-bottom sticky-top bg-theme-surface d-flex align-items-center justify-content-between" style="z-index: 1020;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                    <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="h6 fw-bold text-theme mb-0">
                        Bài viết đã lưu
                    </h1>
                    <p class="small text-theme-secondary mb-0" style="font-size: 11px;">
                        Danh sách các bài viết bạn đã bookmark ({{ $posts->total() }} bài viết)
                    </p>
                </div>
            </div>
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary text-decoration-none">
                Khám phá thêm
            </a>
        </div>

        <!-- Feed of Saved Posts -->
        <div class="d-flex flex-column">
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
                        <svg style="width: 24px; height: 24px;" class="text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                    </x-slot:icon>
                </x-empty-state>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="p-3 p-sm-4 border-theme-top bg-theme-surface d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
