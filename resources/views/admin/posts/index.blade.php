@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt & Quản lý Bài viết')

@section('admin_content')
<!-- Status Tabs -->
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-zinc-800/80 pb-4 no-scrollbar">
    <a
        href="{{ route('admin.posts.index') }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ empty($currentStatus) ? 'bg-white text-zinc-950 shadow-sm' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800' }}"
    >
        Tất cả ({{ number_format($statusCounts['all']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'pending']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'pending' ? 'bg-amber-400 text-zinc-950 shadow-sm' : 'bg-zinc-900 text-amber-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Chờ duyệt ({{ number_format($statusCounts['pending']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'published']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'published' ? 'bg-emerald-400 text-zinc-950 shadow-sm' : 'bg-zinc-900 text-emerald-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Đã xuất bản ({{ number_format($statusCounts['published']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'draft']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'draft' ? 'bg-white text-zinc-950 shadow-sm' : 'bg-zinc-900 text-zinc-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Bản nháp ({{ number_format($statusCounts['draft']) }})
    </a>
    <a
        href="{{ route('admin.posts.index', ['status' => 'rejected']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'rejected' ? 'bg-rose-500 text-white shadow-sm' : 'bg-zinc-900 text-rose-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Bị từ chối ({{ number_format($statusCounts['rejected']) }})
    </a>
</div>

<!-- Search & Category Filters -->
<div class="bg-zinc-900/60 p-4 rounded-2xl border border-zinc-800/80 mb-6">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif

        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm bài viết theo tiêu đề..."
                class="w-full pl-9 pr-4 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-100 placeholder-zinc-500 transition"
            >
        </div>

        <div class="w-full sm:w-56">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-200 transition"
            >
                <option value="">-- Tất cả chuyên mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($currentCategory == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-5 py-2 text-xs font-bold text-zinc-950 bg-white hover:bg-zinc-200 rounded-xl transition shadow-xs">
            Lọc
        </button>
    </form>
</div>

<!-- Posts Moderation Table -->
<div class="bg-zinc-900/50 rounded-2xl border border-zinc-800/80 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-zinc-300">
            <thead class="bg-zinc-950/80 text-[11px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-800/80">
                <tr>
                    <th class="px-6 py-3.5">Bài viết</th>
                    <th class="px-6 py-3.5">Tác giả</th>
                    <th class="px-6 py-3.5">Chuyên mục</th>
                    <th class="px-6 py-3.5">Trạng thái</th>
                    <th class="px-6 py-3.5">Tương tác</th>
                    <th class="px-6 py-3.5">Kiểm duyệt bởi</th>
                    <th class="px-6 py-3.5 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60">
                @forelse($posts as $post)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <!-- Post Info with Thumbnail -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-10 rounded-lg overflow-hidden bg-zinc-800 shrink-0 border border-zinc-700/60">
                                    @if($post->thumbnail)
                                        <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 max-w-xs">
                                    <a href="{{ route('admin.posts.show', $post) }}" class="font-bold text-zinc-100 hover:text-white line-clamp-1 block">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-xs text-zinc-500">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Author -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.users.show', $post->user) }}" class="font-semibold text-zinc-200 hover:text-white block">
                                {{ $post->user->name }}
                            </a>
                        </td>

                        <!-- Category -->
                        <td class="px-6 py-4 font-medium text-zinc-300">
                            {{ $post->category->name }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
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

                        <!-- Interactions -->
                        <td class="px-6 py-4 text-xs text-zinc-400">
                            <span title="Lượt xem">{{ number_format($post->views) }} views</span> &bull;
                            <span title="Lượt thích">{{ number_format($post->likers_count) }} likes</span> &bull;
                            <span title="Bình luận">{{ number_format($post->comments_count) }} cmt</span>
                        </td>

                        <!-- Reviewer Info -->
                        <td class="px-6 py-4 text-xs text-zinc-500">
                            @if($post->reviewer)
                                <span class="font-medium text-zinc-300">{{ $post->reviewer->name }}</span>
                                <span class="block text-[11px]">{{ $post->reviewed_at?->format('d/m H:i') }}</span>
                            @else
                                <span>&mdash;</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                            @if($post->status === 'pending')
                                <!-- Approve Action -->
                                <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="inline" onsubmit="return confirm('Duyệt và xuất bản ngay bài viết \'{{ $post->title }}\'?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs font-bold text-emerald-300 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 rounded-lg transition cursor-pointer">
                                        Duyệt
                                    </button>
                                </form>

                                <!-- Reject Action -->
                                <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="inline" onsubmit="const reason = prompt('Nhập lý do từ chối bài viết:'); if(!reason) return false; this.rejection_reason.value = reason; return true;">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="">
                                    <button type="submit" class="px-3 py-1 text-xs font-bold text-amber-300 bg-amber-950/60 hover:bg-amber-900/60 border border-amber-800/60 rounded-lg transition cursor-pointer">
                                        Từ chối
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.posts.show', $post) }}" class="px-2.5 py-1 text-xs font-semibold text-zinc-300 hover:text-white bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition">
                                Chi tiết
                            </a>

                            <!-- Delete Action -->
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn bài viết \'{{ $post->title }}\'?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-zinc-400 hover:text-rose-400 transition cursor-pointer" title="Xóa bài viết">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-zinc-500 text-sm">
                            Không có bài viết nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800/80">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
