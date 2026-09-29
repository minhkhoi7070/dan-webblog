@extends('layouts.admin')

@section('admin_title', 'Tạo Chuyên mục Mới')

@section('admin_content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-indigo-600 transition">
            &larr; Quay lại danh sách chuyên mục
        </a>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200/80 shadow-xs">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-bold text-gray-800 mb-1">
                    Tên chuyên mục <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    placeholder="VD: Trí tuệ Nhân tạo, DevOps, v.v..."
                    required
                    autofocus
                    class="w-full px-4 py-2.5 text-sm bg-gray-50 focus:bg-white border @error('name') border-rose-300 ring-1 ring-rose-500 @else border-gray-200 focus:border-indigo-500 @enderror rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-gray-900 transition"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-bold text-gray-800 mb-1">
                    Đường dẫn tĩnh (Slug)
                </label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug') }}"
                    placeholder="VD: tri-tue-nhan-tao (để trống nếu muốn tự sinh)"
                    class="w-full px-4 py-2.5 text-sm font-mono bg-gray-50 focus:bg-white border @error('slug') border-rose-300 ring-1 ring-rose-500 @else border-gray-200 focus:border-indigo-500 @enderror rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-gray-900 transition"
                >
                <p class="mt-1 text-xs text-gray-400">Nếu bỏ trống, hệ thống sẽ tự động tạo slug từ tên chuyên mục.</p>
                @error('slug')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition cursor-pointer"
                >
                    Tạo chuyên mục
                </button>
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 transition"
                >
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
