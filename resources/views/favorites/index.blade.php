@extends('layouts.public')

@section('content')
<div class="w-100 d-flex justify-content-center px-0 px-sm-3 py-0 py-sm-4">
    <div class="w-100 feed-container card border-0 border-sm border-theme overflow-hidden bg-theme-surface shadow-xs" style="border-radius: var(--radius-xl, 16px);">
        
        <!-- Top Context Bar -->
        <div class="p-3 px-sm-4 border-theme-bottom d-flex align-items-center justify-content-between sticky-top bg-theme-surface" style="z-index: 1020;">
            <a href="{{ route('posts.index') }}" class="small text-theme-secondary hover-accent text-decoration-none d-inline-flex align-items-center gap-1.5 transition-colors">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại bảng tin
            </a>
            <span class="label-uppercase m-0 text-theme-muted">
                {{ $posts->total() }} bài viết
            </span>
        </div>

        <!-- Reading-Oriented Masthead -->
        <div class="p-4 p-sm-5 border-theme-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-surface-2 border border-theme text-warning flex-shrink-0" style="width: 44px; height: 44px;">
                    <svg style="width: 22px; height: 22px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-bold text-theme mb-1 tracking-tight" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                        Bài viết đã lưu
                    </h1>
                    <p class="small text-theme-secondary mb-0">
                        Danh sách lưu trữ cá nhân cho trải nghiệm đọc tập trung của bạn
                    </p>
                </div>
            </div>
        </div>

        <!-- Clear Saved State Indicator -->
        <div class="px-4 py-2.5 border-theme-bottom bg-surface-2 d-flex align-items-center justify-content-between">
            <span class="small text-theme-secondary d-inline-flex align-items-center gap-2" style="font-size: 12.5px;">
                <svg style="width: 15px; height: 15px;" class="text-warning flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                Tất cả bài viết bên dưới đang ở trạng thái <strong>Đã lưu</strong>
            </span>
            <span class="small text-theme-muted d-none d-sm-inline" style="font-size: 11.5px;">
                Nhấn dấu trang để gỡ bỏ
            </span>
        </div>

        <!-- Feed of Saved Posts -->
        <div class="d-flex flex-column">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <x-empty-state
                    title="Bạn chưa lưu bài viết nào"
                    description="Khi bạn tìm thấy bài viết hữu ích hoặc muốn đọc lại sau, hãy nhấn vào biểu tượng dấu trang để lưu vào đây."
                    :action-url="route('posts.index')"
                    action-label="Khám phá bài viết ngay"
                >
                    <x-slot:icon>
                        <svg style="width: 28px; height: 28px;" class="text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
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

