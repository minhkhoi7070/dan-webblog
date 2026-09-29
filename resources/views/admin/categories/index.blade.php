@extends('layouts.admin')

@section('admin_title', 'Quản lý Chuyên mục (Categories)')

@section('admin_content')
<!-- Header Action Bar & Search -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex-1 max-w-md relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Tìm chuyên mục theo tên hoặc slug..."
            class="w-full pl-9 pr-4 py-2 text-xs bg-zinc-950 border border-zinc-800 focus:border-indigo-500 rounded-xl text-zinc-100 placeholder-zinc-500 transition shadow-xs"
        >
    </form>

    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-zinc-950 bg-white hover:bg-zinc-200 rounded-full shadow-md transition self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Thêm chuyên mục mới
    </a>
</div>

<!-- Categories Table -->
<div class="bg-zinc-900/50 rounded-2xl border border-zinc-800/80 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-zinc-300">
            <thead class="bg-zinc-950/80 text-[11px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-800/80">
                <tr>
                    <th class="px-6 py-3.5">Tên Chuyên mục</th>
                    <th class="px-6 py-3.5">Slug URL</th>
                    <th class="px-6 py-3.5">Số bài viết</th>
                    <th class="px-6 py-3.5">Ngày tạo</th>
                    <th class="px-6 py-3.5 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/60">
                @forelse($categories as $category)
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="px-6 py-4 font-bold text-zinc-100">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-zinc-400">
                            {{ $category->slug }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-zinc-200">
                            {{ number_format($category->posts_count) }}
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-500">
                            {{ $category->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 text-xs font-semibold text-zinc-300 hover:text-white bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 rounded-lg transition">
                                Sửa
                            </a>

                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa chuyên mục \'{{ $category->name }}\'? Hành động này không thể hoàn tác.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-400 hover:text-white bg-zinc-950 hover:bg-rose-600 border border-rose-900/60 hover:border-rose-600 rounded-lg transition cursor-pointer">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-zinc-500 text-sm">
                            Không tìm thấy chuyên mục nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-zinc-800/80">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
