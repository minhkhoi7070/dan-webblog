@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-1">
                <span>Không gian tác giả</span>
                <span>&rsaquo;</span>
                <span class="text-indigo-400">Biên tập & Thống kê</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[var(--color-text)] tracking-tight">
                Quản lý bài viết của bạn
            </h1>
        </div>

        <a href="{{ route('posts.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] shadow-md transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Soạn bài viết mới
        </a>
    </div>

    <!-- Feedback messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-800/60 text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics Grid (All 8 Metrics) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-8">
        <!-- 1. Total Posts -->
        <a href="{{ route('posts.stats') }}" class="p-4 rounded-2xl border transition {{ empty($currentStatus) ? 'bg-[var(--color-surface-hover)] border-[var(--color-text)] ring-1 ring-[var(--color-text)]' : 'bg-[var(--color-surface)] border-[var(--color-border)] hover:bg-[var(--color-surface-hover)]' }}">
            <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Tổng bài</span>
            <p class="text-xl sm:text-2xl font-black text-[var(--color-text)] mt-1">{{ number_format($stats['total']) }}</p>
        </a>

        <!-- 2. Draft -->
        <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="p-4 rounded-2xl border transition {{ $currentStatus === 'draft' ? 'bg-[var(--color-surface-hover)] border-[var(--color-text)] ring-1 ring-[var(--color-text)]' : 'bg-[var(--color-surface)] border-[var(--color-border)] hover:bg-[var(--color-surface-hover)]' }}">
            <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Bản nháp</span>
            <p class="text-xl sm:text-2xl font-black text-[var(--color-text-secondary)] mt-1">{{ number_format($stats['draft']) }}</p>
        </a>

        <!-- 3. Pending -->
        <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="p-4 rounded-2xl border transition {{ $currentStatus === 'pending' ? 'bg-amber-950/40 border-amber-500/50 ring-1 ring-amber-500/50' : 'bg-[var(--color-surface)] border-[var(--color-border)] hover:bg-[var(--color-surface-hover)]' }}">
            <span class="text-[11px] font-semibold text-amber-400 uppercase tracking-wider block">Chờ duyệt</span>
            <p class="text-xl sm:text-2xl font-black text-amber-400 mt-1">{{ number_format($stats['pending']) }}</p>
        </a>

        <!-- 4. Published -->
        <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="p-4 rounded-2xl border transition {{ $currentStatus === 'published' ? 'bg-emerald-950/40 border-emerald-500/50 ring-1 ring-emerald-500/50' : 'bg-[var(--color-surface)] border-[var(--color-border)] hover:bg-[var(--color-surface-hover)]' }}">
            <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider block">Xuất bản</span>
            <p class="text-xl sm:text-2xl font-black text-emerald-400 mt-1">{{ number_format($stats['published']) }}</p>
        </a>

        <!-- 5. Rejected -->
        <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="p-4 rounded-2xl border transition {{ $currentStatus === 'rejected' ? 'bg-rose-950/40 border-rose-500/50 ring-1 ring-rose-500/50' : 'bg-[var(--color-surface)] border-[var(--color-border)] hover:bg-[var(--color-surface-hover)]' }}">
            <span class="text-[11px] font-semibold text-rose-400 uppercase tracking-wider block">Từ chối</span>
            <p class="text-xl sm:text-2xl font-black text-rose-400 mt-1">{{ number_format($stats['rejected']) }}</p>
        </a>

        <!-- 6. Views -->
        <div class="p-4 rounded-2xl bg-[var(--color-surface)] border border-[var(--color-border)]">
            <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Lượt xem</span>
            <p class="text-xl sm:text-2xl font-black text-indigo-400 mt-1">{{ number_format($stats['views']) }}</p>
        </div>

        <!-- 7. Likes -->
        <div class="p-4 rounded-2xl bg-[var(--color-surface)] border border-[var(--color-border)]">
            <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Lượt thích</span>
            <p class="text-xl sm:text-2xl font-black text-rose-500 mt-1">{{ number_format($stats['likes']) }}</p>
        </div>

        <!-- 8. Comments -->
        <div class="p-4 rounded-2xl bg-[var(--color-surface)] border border-[var(--color-border)]">
            <span class="text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider block">Bình luận</span>
            <p class="text-xl sm:text-2xl font-black text-purple-400 mt-1">{{ number_format($stats['comments']) }}</p>
        </div>
    </div>

    <!-- Posts Filter & Search Bar -->
    <div class="p-4 rounded-2xl bg-[var(--color-surface)] border border-[var(--color-border)] mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold pb-1 sm:pb-0 no-scrollbar">
                <a href="{{ route('posts.stats') }}" class="px-3.5 py-1.5 rounded-full transition {{ empty($currentStatus) ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
                    Tất cả ({{ $stats['total'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'draft']) }}" class="px-3.5 py-1.5 rounded-full transition {{ $currentStatus === 'draft' ? 'bg-[var(--color-text)] text-[var(--color-bg)] font-bold' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
                    Bản nháp ({{ $stats['draft'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'pending']) }}" class="px-3.5 py-1.5 rounded-full transition {{ $currentStatus === 'pending' ? 'bg-amber-400 text-zinc-950 font-bold' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
                    Chờ duyệt ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'published']) }}" class="px-3.5 py-1.5 rounded-full transition {{ $currentStatus === 'published' ? 'bg-emerald-400 text-zinc-950 font-bold' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
                    Đã xuất bản ({{ $stats['published'] }})
                </a>
                <a href="{{ route('posts.stats', ['status' => 'rejected']) }}" class="px-3.5 py-1.5 rounded-full transition {{ $currentStatus === 'rejected' ? 'bg-rose-500 text-white font-bold' : 'bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:text-[var(--color-text)]' }}">
                    Bị từ chối ({{ $stats['rejected'] }})
                </a>
            </div>

            <!-- Search form -->
            <form action="{{ route('posts.stats') }}" method="GET" class="relative max-w-xs w-full">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Tìm trong bài viết của bạn..."
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-[var(--color-input)] border border-[var(--color-border)] rounded-full text-[var(--color-text)] placeholder-[var(--color-text-secondary)] focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[var(--color-text-secondary)]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-[var(--color-surface)] rounded-2xl border border-[var(--color-border)] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[var(--color-text)]">
                <thead class="bg-[var(--color-surface-hover)] border-b border-[var(--color-border)] text-[11px] font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Bài viết</th>
                        <th class="px-6 py-4">Chuyên mục</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4">Chỉ số tương tác</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @forelse($posts as $post)
                        <tr class="hover:bg-[var(--color-surface-hover)] transition">
                            <!-- Title & Excerpt -->
                            <td class="px-6 py-4 max-w-xs sm:max-w-md">
                                <div class="flex items-center gap-3">
                                    @if($post->thumbnail)
                                        <img src="{{ asset($post->thumbnail) }}" alt="" class="w-12 h-12 rounded-xl object-cover shrink-0 bg-[var(--color-surface-hover)] border border-[var(--color-border)]">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] flex items-center justify-center shrink-0 font-bold text-xs border border-[var(--color-border)]">
                                            DOC
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('posts.edit', $post) }}" class="font-bold text-[var(--color-text)] hover:opacity-80 line-clamp-1 transition">
                                            {{ $post->title }}
                                        </a>
                                        <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">
                                            Cập nhật: {{ $post->updated_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-surface-hover)] text-[var(--color-text)] border border-[var(--color-border)]">
                                    {{ $post->category->name }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($post->status === 'published')
                                    <x-badge variant="status-published" size="xs">
                                        Đã xuất bản
                                    </x-badge>
                                @elseif($post->status === 'pending')
                                    <x-badge variant="status-pending" size="xs">
                                        Chờ duyệt
                                    </x-badge>
                                @elseif($post->status === 'rejected')
                                    <x-badge variant="status-rejected" size="xs">
                                        Bị từ chối
                                    </x-badge>
                                @else
                                    <x-badge variant="status-draft" size="xs">
                                        Bản nháp
                                    </x-badge>
                                @endif
                            </td>

                            <!-- Metrics -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-[var(--color-text-secondary)]">
                                <div class="flex items-center gap-3">
                                    <span title="Lượt xem">{{ number_format($post->views) }} views</span>
                                    <span>&bull;</span>
                                    <span title="Lượt thích">{{ $post->likers_count }} likes</span>
                                    <span>&bull;</span>
                                    <span title="Bình luận">{{ $post->comments_count }} cmt</span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Submit for review button (Available if draft or rejected) -->
                                    @if(in_array($post->status, ['draft', 'rejected'], true))
                                        <form action="{{ route('posts.submit', $post) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-500/20 text-indigo-400 hover:bg-indigo-500/30 border border-indigo-500/30 transition" title="Gửi xét duyệt">
                                                Gửi duyệt &rarr;
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Link -->
                                    <a href="{{ route('posts.edit', $post) }}" class="p-1.5 text-[var(--color-text-secondary)] hover:text-[var(--color-text)] transition" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-[var(--color-text-secondary)] hover:text-rose-400 transition" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-[var(--color-text-secondary)]">
                                Không tìm thấy bài viết nào. Hãy bấm "Soạn bài viết mới" để tạo bài viết đầu tiên!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
