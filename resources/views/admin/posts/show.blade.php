@extends('layouts.admin')

@section('admin_title', 'Thẩm định Bài viết: ' . $post->title)

@section('admin_content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-indigo-600 transition">
        &larr; Quay lại danh sách bài viết
    </a>

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center gap-2">
        @if($post->status === 'published')
            <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl transition shadow-xs">
                Xem bài viết công khai &rarr;
            </a>
        @endif

        @if($post->status !== 'published')
            <!-- Approve Button Form -->
            <form action="{{ route('admin.posts.approve', $post) }}" method="POST" onsubmit="return confirm('Duyệt và xuất bản ngay bài viết này?');">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition cursor-pointer shadow-xs">
                    Duyệt & Xuất bản (Approve)
                </button>
            </form>
        @endif

        @if($post->status !== 'rejected')
            <!-- Reject Button Form with prompt -->
            <form action="{{ route('admin.posts.reject', $post) }}" method="POST" onsubmit="const reason = prompt('Nhập lý do từ chối bài viết:'); if(!reason) return false; this.rejection_reason.value = reason; return true;">
                @csrf
                <input type="hidden" name="rejection_reason" value="">
                <button type="submit" class="px-4 py-2 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-300 rounded-xl transition cursor-pointer">
                    Từ chối phê duyệt (Reject)
                </button>
            </form>
        @endif

        <!-- Delete Post Form -->
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('XÁC NHẬN: Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                Xóa bài viết
            </button>
        </form>
    </div>
</div>

<!-- Rejection Reason Banner if rejected -->
@if($post->status === 'rejected' && $post->rejection_reason)
    <div class="mb-6 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm">
        <h4 class="font-bold text-rose-900 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Lý do từ chối biên tập:
        </h4>
        <p class="text-rose-700 text-xs italic">{{ $post->rejection_reason }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content Area -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight mb-4">
                {{ $post->title }}
            </h1>

            @if($post->excerpt)
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 italic mb-6">
                    {{ $post->excerpt }}
                </div>
            @endif

            @if($post->thumbnail)
                <div class="mb-6 rounded-2xl overflow-hidden border border-gray-200 shadow-xs">
                    <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="" class="w-full max-h-96 object-cover">
                </div>
            @endif

            <!-- Article Body -->
            <div class="prose max-w-none text-gray-800 leading-relaxed font-sans text-sm sm:text-base border-t border-gray-100 pt-6 whitespace-pre-line">
                {{ $post->body }}
            </div>
        </div>
    </div>

    <!-- Right Sidebar Metadata -->
    <div class="space-y-6">
        <!-- Status & Reviewer Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Thông tin Xuất bản & Kiểm duyệt</h3>

            <div>
                <span class="text-xs text-gray-400 block mb-1">Trạng thái hiện tại:</span>
                @php
                    $statusBadges = [
                        'draft' => 'bg-slate-50 text-slate-700 border-slate-200',
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-300 ring-2 ring-amber-500/10 font-bold',
                        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold',
                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                    ];
                    $statusLabels = [
                        'draft' => 'Bản nháp (Draft)',
                        'pending' => 'Chờ thẩm định (Pending)',
                        'published' => 'Đã xuất bản (Published)',
                        'rejected' => 'Bị từ chối (Rejected)',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs border {{ $statusBadges[$post->status] ?? 'bg-gray-100 text-gray-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $post->status === 'published' ? 'bg-emerald-500' : ($post->status === 'pending' ? 'bg-amber-500' : ($post->status === 'rejected' ? 'bg-rose-500' : 'bg-slate-400')) }}"></span>
                    {{ $statusLabels[$post->status] ?? $post->status }}
                </span>
            </div>

            <div>
                <span class="text-xs text-gray-400 block mb-0.5">Tác giả:</span>
                <a href="{{ route('admin.users.show', $post->user) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600">
                    {{ $post->user->name }} ({{ $post->user->email }})
                </a>
            </div>

            <div>
                <span class="text-xs text-gray-400 block mb-0.5">Chuyên mục:</span>
                <span class="text-sm font-semibold text-gray-800">{{ $post->category->name }}</span>
            </div>

            @if($post->tags->isNotEmpty())
                <div>
                    <span class="text-xs text-gray-400 block mb-1.5">Thẻ bài viết:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($post->tags as $tag)
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1.5">
                <p>Khởi tạo: <strong>{{ $post->created_at->format('d/m/Y H:i') }}</strong></p>
                @if($post->published_at)
                    <p>Xuất bản: <strong>{{ $post->published_at->format('d/m/Y H:i') }}</strong></p>
                @endif
                @if($post->reviewer)
                    <p>Người kiểm duyệt: <strong>{{ $post->reviewer->name }}</strong></p>
                    <p>Thời điểm kiểm duyệt: <strong>{{ $post->reviewed_at?->format('d/m/Y H:i') }}</strong></p>
                @endif
            </div>
        </div>

        <!-- Rejection Box for quick reject -->
        @if($post->status !== 'rejected')
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Từ chối bài viết</h3>
                <form action="{{ route('admin.posts.reject', $post) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea
                        name="rejection_reason"
                        rows="3"
                        placeholder="Nhập lý do hoặc góp ý chỉnh sửa cho tác giả..."
                        class="w-full p-3 text-xs bg-gray-50 focus:bg-white border border-gray-200 focus:border-rose-500 rounded-xl focus:ring-2 focus:ring-rose-500/20 text-gray-900 transition"
                    ></textarea>
                    <button
                        type="submit"
                        class="w-full py-2 px-4 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition cursor-pointer"
                    >
                        Gửi lý do & Từ chối phê duyệt
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
