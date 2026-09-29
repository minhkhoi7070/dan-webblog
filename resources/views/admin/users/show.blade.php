@extends('layouts.admin')

@section('admin_title', 'Hồ sơ Người dùng: ' . $user->name)

@section('admin_content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-indigo-600 transition">
        &larr; Quay lại danh sách người dùng
    </a>

    <!-- Lock / Unlock Actions -->
    @if($user->id !== Auth::id())
        @if($user->is_locked)
            <form action="{{ route('admin.users.unlock', $user) }}" method="POST" onsubmit="return confirm('Mở khóa tài khoản người dùng \'{{ $user->name }}\'?');">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl transition cursor-pointer">
                    Mở khóa tài khoản
                </button>
            </form>
        @elseif(! $user->isAdmin())
            <form action="{{ route('admin.users.lock', $user) }}" method="POST" onsubmit="return confirm('XÁC NHẬN: Bạn có chắc chắn muốn khóa tài khoản người dùng \'{{ $user->name }}\'?');">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition cursor-pointer shadow-xs">
                    Khóa tài khoản này
                </button>
            </form>
        @endif
    @endif
</div>

<!-- User Profile Card -->
<div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl sm:text-2xl font-black text-gray-900">{{ $user->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $user->role }}
                </span>
                @if($user->is_locked)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Đang bị khóa
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Đang hoạt động
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $user->email }} &bull; Tham gia từ {{ $user->created_at->format('d/m/Y') }}</p>
            @if($user->bio)
                <p class="mt-3 text-xs text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100 italic">{{ $user->bio }}</p>
            @endif
        </div>
    </div>

    <!-- Quick Count Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-gray-100 text-center">
        <div>
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Bài viết</span>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($user->posts_count) }}</p>
        </div>
        <div>
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Bình luận</span>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($user->comments_count) }}</p>
        </div>
        <div>
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Người theo dõi</span>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($user->followers_count) }}</p>
        </div>
        <div>
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Đang theo dõi</span>
            <p class="text-xl font-black text-gray-900 mt-1">{{ number_format($user->following_count) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- User Recent Posts -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Bài viết gần đây</h3>
        @if($recentPosts->isEmpty())
            <p class="text-xs text-gray-400 py-6 text-center">Người dùng chưa đăng bài viết nào.</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($recentPosts as $post)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.posts.show', $post) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 truncate block">
                                {{ $post->title }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $post->category->name }} &bull; {{ $post->status }} &bull; {{ $post->created_at->format('d/m/Y') }}</span>
                        </div>
                        <a href="{{ route('admin.posts.show', $post) }}" class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition shrink-0">
                            Xem
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- User Recent Comments -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Bình luận gần đây</h3>
        @if($recentComments->isEmpty())
            <p class="text-xs text-gray-400 py-6 text-center">Người dùng chưa có bình luận nào.</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($recentComments as $comment)
                    <div class="py-3">
                        <p class="text-xs text-gray-800 line-clamp-2">"{{ $comment->body }}"</p>
                        <p class="text-[11px] text-gray-400 mt-1">Trên bài: {{ $comment->post->title }} &bull; {{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
