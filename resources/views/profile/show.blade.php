@extends('layouts.public')

@section('content')
<div class="w-full flex justify-center px-0 sm:px-4 py-0 sm:py-6">
    <div class="w-full max-w-[660px] min-h-screen sm:min-h-0 bg-[var(--color-surface)] border-0 sm:border border-[var(--color-border)] sm:rounded-3xl overflow-hidden shadow-xl p-6 sm:p-8">
        
        <!-- Social Header -->
        <div class="flex items-start justify-between gap-4 pb-6 border-b border-[var(--color-border)]">
            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[var(--color-text)] tracking-tight truncate">
                    {{ $user->name }}
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-medium text-[var(--color-text-secondary)]">
                        {{ '@' . ($user->username ?? strtolower(str_replace(' ', '', $user->name))) }}
                    </span>
                    <x-badge variant="status-draft" size="xs" class="capitalize">
                        {{ $user->role }}
                    </x-badge>
                </div>
                <p class="text-xs text-[var(--color-text-secondary)] mt-1">{{ $user->email }}</p>
            </div>

            <!-- Avatar -->
            <x-avatar :user="$user" size="2xl" class="shadow-lg shadow-indigo-600/10" />
        </div>

        <!-- Bio -->
        <p class="mt-4 text-sm text-[var(--color-text)] leading-relaxed">
            {{ $user->bio ?: 'Chưa cập nhật tiểu sử cá nhân. Hãy thêm vài dòng giới thiệu về bản thân bạn!' }}
        </p>

        <!-- Actions -->
        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('profile.edit') }}"
               class="flex-1 text-center px-4 py-2 rounded-full bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] text-xs font-bold transition shadow-sm">
                Chỉnh sửa trang cá nhân
            </a>
            <a href="{{ route('favorites.index') }}"
               class="px-4 py-2 rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] text-[var(--color-text)] text-xs font-semibold transition">
                Đã lưu
            </a>
            <a href="{{ route('activity.index') }}"
               class="px-4 py-2 rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] text-[var(--color-text)] text-xs font-semibold transition">
                Hoạt động
            </a>
        </div>

        <!-- Statistics Grid -->
        <div class="mt-8 pt-6 border-t border-[var(--color-border)]">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--color-text-secondary)] mb-4">Thống kê tương tác</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-4 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-center">
                    <span class="block text-2xl font-black text-[var(--color-text)]">{{ $user->comments_count }}</span>
                    <span class="text-[11px] text-[var(--color-text-secondary)] mt-1 block font-medium">Bình luận</span>
                </div>
                <div class="p-4 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-center">
                    <span class="block text-2xl font-black text-rose-500">{{ $user->liked_posts_count }}</span>
                    <span class="text-[11px] text-[var(--color-text-secondary)] mt-1 block font-medium">Đã thích</span>
                </div>
                <div class="p-4 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-center">
                    <span class="block text-2xl font-black text-amber-400">{{ $user->favorite_posts_count }}</span>
                    <span class="text-[11px] text-[var(--color-text-secondary)] mt-1 block font-medium">Đã lưu</span>
                </div>
                <div class="p-4 rounded-2xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-center">
                    <span class="block text-2xl font-black text-indigo-400">{{ $user->following_count }}</span>
                    <span class="text-[11px] text-[var(--color-text-secondary)] mt-1 block font-medium">Đang theo dõi</span>
                </div>
            </div>
        </div>

        <!-- Become Author CTA (for Viewers) -->
        @if($user->role === 'viewer')
            <div class="mt-8 pt-6 border-t border-[var(--color-border)]">
                <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-transparent border border-indigo-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-500/20 text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </span>
                            <h3 class="text-sm font-bold text-[var(--color-text)]">Trở thành tác giả BlogMNM</h3>
                        </div>
                        <p class="text-xs text-[var(--color-text-secondary)] mt-1.5 leading-relaxed">
                            Bắt đầu chia sẻ kiến thức, viết bài thảo luận công nghệ và tương tác với cộng đồng độc giả BlogMNM.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('author.become') }}" class="shrink-0 w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-md shadow-indigo-600/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Trở thành tác giả
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Account Shortcuts -->
        <div class="mt-8 pt-6 border-t border-[var(--color-border)] space-y-2">
            <a href="{{ route('favorites.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] border border-[var(--color-border)] transition group">
                <span class="text-sm font-medium text-[var(--color-text)]">Xem danh sách bài viết đã lưu</span>
                <span class="text-[var(--color-text-secondary)] group-hover:text-[var(--color-text)] transition-transform group-hover:translate-x-1">&rarr;</span>
            </a>
            <a href="{{ route('activity.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-[var(--color-surface)] hover:bg-[var(--color-surface-hover)] border border-[var(--color-border)] transition group">
                <span class="text-sm font-medium text-[var(--color-text)]">Xem toàn bộ nhật ký hoạt động</span>
                <span class="text-[var(--color-text-secondary)] group-hover:text-[var(--color-text)] transition-transform group-hover:translate-x-1">&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection
