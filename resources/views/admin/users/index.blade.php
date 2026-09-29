@extends('layouts.admin')

@section('admin_title', 'Quản lý Người dùng')

@section('admin_content')
<!-- Filter & Search Bar -->
<div class="bg-zinc-900/60 p-4 rounded-2xl border border-zinc-800/80 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <!-- Search Input -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm kiếm người dùng theo tên hoặc email..."
                class="w-full pl-9 pr-4 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-100 placeholder-zinc-500 transition"
            >
        </div>

        <!-- Role Filter -->
        <div class="w-full sm:w-44">
            <select
                name="role"
                onchange="this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-200 transition"
            >
                <option value="">-- Tất cả vai trò --</option>
                <option value="admin" @selected($roleFilter === 'admin')>Admin</option>
                <option value="author" @selected($roleFilter === 'author')>Author (Tác giả)</option>
                <option value="viewer" @selected($roleFilter === 'viewer')>Viewer (Độc giả)</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full sm:w-44">
            <select
                name="status"
                onchange="this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-200 transition"
            >
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" @selected($statusFilter === 'active')>Đang hoạt động</option>
                <option value="locked" @selected($statusFilter === 'locked')>Đã bị khóa</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2 text-xs font-bold text-zinc-950 bg-white hover:bg-zinc-200 rounded-xl transition shadow-xs">
            Lọc
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-zinc-900/50 rounded-2xl border border-zinc-800/80 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-zinc-300">
            <thead class="bg-zinc-950/80 text-[11px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-800/80">
                <tr>
                    <th class="px-6 py-3.5">Người dùng</th>
                    <th class="px-6 py-3.5">Vai trò</th>
                    <th class="px-6 py-3.5">Bài viết</th>
                    <th class="px-6 py-3.5">Bình luận</th>
                    <th class="px-6 py-3.5">Trạng thái</th>
                    <th class="px-6 py-3.5 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60">
                @forelse($users as $user)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <!-- User Identity -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$user" size="sm" />
                                <div class="min-w-0">
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-zinc-100 hover:text-white truncate block">
                                        {{ $user->name }}
                                    </a>
                                    <p class="text-xs text-zinc-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Role Badge -->
                        <td class="px-6 py-4">
                            <x-badge variant="status-draft" size="xs" class="uppercase">
                                {{ $user->role }}
                            </x-badge>
                        </td>

                        <!-- Posts Count -->
                        <td class="px-6 py-4 font-semibold text-zinc-200">
                            {{ number_format($user->posts_count) }}
                        </td>

                        <!-- Comments Count -->
                        <td class="px-6 py-4 font-semibold text-zinc-200">
                            {{ number_format($user->comments_count) }}
                        </td>

                        <!-- Account Status Badge -->
                        <td class="px-6 py-4">
                            @if($user->is_locked)
                                <x-badge variant="status-rejected" size="xs">
                                    Đã bị khóa
                                </x-badge>
                            @else
                                <x-badge variant="status-published" size="xs">
                                    Hoạt động
                                </x-badge>
                            @endif
                        </td>

                        <!-- Actions (Lock / Unlock / View) -->
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="px-3 py-1.5 text-xs font-semibold text-zinc-300 hover:text-white bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition">
                                Chi tiết
                            </a>

                            @if($user->id !== Auth::id())
                                @if($user->is_locked)
                                    <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="inline" onsubmit="return confirm('Mở khóa tài khoản người dùng \'{{ $user->name }}\'?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-emerald-300 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 rounded-lg transition cursor-pointer">
                                            Mở khóa
                                        </button>
                                    </form>
                                @elseif($user->isAdmin())
                                    <span class="text-xs text-indigo-400 font-medium">Quản trị viên</span>
                                @else
                                    <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="inline" onsubmit="return confirm('BẠN CÓ CHẮC CHẮN MUỐN KHÓA TÀI KHOẢN \'{{ $user->name }}\'?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-rose-300 bg-rose-950/60 hover:bg-rose-900/60 border border-rose-800/60 rounded-lg transition cursor-pointer">
                                            Khóa tài khoản
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs text-zinc-500 italic">Bạn</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-zinc-500 text-sm">
                            Không tìm thấy người dùng nào phù hợp với bộ lọc tìm kiếm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800/80">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
