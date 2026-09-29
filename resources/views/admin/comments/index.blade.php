@extends('layouts.admin')

@section('admin_title', 'Kiểm duyệt Bình luận (Comments)')

@section('admin_content')
<!-- Status Tabs -->
<div class="flex flex-wrap items-center gap-2 mb-6 border-b border-zinc-800/80 pb-4 no-scrollbar">
    <a
        href="{{ route('admin.comments.index') }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ empty($currentStatus) ? 'bg-white text-zinc-950 shadow-sm' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800' }}"
    >
        Tất cả ({{ number_format($statusCounts['all']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'pending' ? 'bg-amber-400 text-zinc-950 shadow-sm' : 'bg-zinc-900 text-amber-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Chờ duyệt ({{ number_format($statusCounts['pending']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'approved' ? 'bg-emerald-400 text-zinc-950 shadow-sm' : 'bg-zinc-900 text-emerald-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Đã duyệt ({{ number_format($statusCounts['approved']) }})
    </a>
    <a
        href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
        class="px-4 py-2 rounded-full text-xs font-bold transition {{ $currentStatus === 'spam' ? 'bg-rose-500 text-white shadow-sm' : 'bg-zinc-900 text-rose-400 hover:bg-zinc-800 border border-zinc-800' }}"
    >
        Spam ({{ number_format($statusCounts['spam']) }})
    </a>
</div>

<!-- Search Bar -->
<div class="bg-zinc-900/60 p-4 rounded-2xl border border-zinc-800/80 mb-6">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="flex gap-3">
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
                placeholder="Tìm nội dung bình luận hoặc tên người dùng..."
                class="w-full pl-9 pr-4 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-100 placeholder-zinc-500 transition"
            >
        </div>

        <button type="submit" class="px-5 py-2 text-xs font-bold text-zinc-950 bg-white hover:bg-zinc-200 rounded-xl transition shadow-xs">
            Tìm kiếm
        </button>
    </form>
</div>

<!-- Comments List Table -->
<div class="bg-zinc-900/50 rounded-2xl border border-zinc-800/80 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-zinc-300">
            <thead class="bg-zinc-950/80 text-[11px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-800/80">
                <tr>
                    <th class="px-6 py-3.5">Người bình luận</th>
                    <th class="px-6 py-3.5">Nội dung</th>
                    <th class="px-6 py-3.5">Bài viết</th>
                    <th class="px-6 py-3.5">Trạng thái</th>
                    <th class="px-6 py-3.5">Thời gian</th>
                    <th class="px-6 py-3.5 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60">
                @forelse($comments as $comment)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <!-- User -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.users.show', $comment->user) }}" class="font-bold text-zinc-100 hover:text-white block">
                                {{ $comment->user->name }}
                            </a>
                            <span class="text-xs text-zinc-500">{{ $comment->user->email }}</span>
                        </td>

                        <!-- Comment Body -->
                        <td class="px-6 py-4">
                            <p class="text-zinc-200 text-xs sm:text-sm line-clamp-3 leading-relaxed max-w-md">
                                "{{ $comment->body }}"
                            </p>
                            @if($comment->parent_id)
                                <span class="inline-block mt-1 text-[10px] text-zinc-500 italic">
                                    &lrcorner; Trả lời cho bình luận #{{ $comment->parent_id }}
                                </span>
                            @endif
                        </td>

                        <!-- Target Post -->
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.posts.show', $comment->post) }}" class="text-xs font-semibold text-zinc-300 hover:text-indigo-400 line-clamp-2 block max-w-xs">
                                {{ $comment->post->title }}
                            </a>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4">
                            @if($comment->status === 'approved')
                                <x-badge variant="status-published" size="xs">
                                    Đã duyệt
                                </x-badge>
                            @elseif($comment->status === 'pending')
                                <x-badge variant="status-pending" size="xs">
                                    Chờ duyệt
                                </x-badge>
                            @else
                                <x-badge variant="status-rejected" size="xs">
                                    Spam
                                </x-badge>
                            @endif
                        </td>

                        <!-- Time -->
                        <td class="px-6 py-4 text-xs text-zinc-500 whitespace-nowrap">
                            {{ $comment->created_at->diffForHumans() }}
                        </td>

                        <!-- Moderation Actions -->
                        <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                            @if($comment->status !== 'approved')
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold text-emerald-300 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 rounded-lg transition cursor-pointer">
                                        Duyệt
                                    </button>
                                </form>
                            @endif

                            @if($comment->status !== 'spam')
                                <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-semibold text-amber-300 bg-amber-950/60 hover:bg-amber-900/60 border border-amber-800/60 rounded-lg transition cursor-pointer">
                                        Spam
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Xóa bình luận này? Hành động không thể hoàn tác.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-zinc-500 hover:text-rose-400 rounded-lg transition cursor-pointer" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-zinc-500 text-sm">
                            Không có bình luận nào phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($comments->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800/80">
            {{ $comments->links() }}
        </div>
    @endif
</div>
@endsection
