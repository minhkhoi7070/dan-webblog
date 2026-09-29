@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-1">
                <a href="{{ route('posts.stats') }}" class="hover:text-indigo-400 transition">Không gian tác giả</a>
                <span>&rsaquo;</span>
                <span class="text-indigo-400">Soạn bài mới</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[var(--color-text)] tracking-tight">
                Tạo bài viết mới
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('posts.stats') }}" class="px-4 py-2 text-xs font-semibold text-[var(--color-text)] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full hover:bg-[var(--color-surface-hover)] transition">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-800/60 text-rose-200 text-sm">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Có lỗi xảy ra trong dữ liệu nhập:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Main Column (Content) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title & Excerpt -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)] space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-bold text-[var(--color-text)] mb-1.5">
                            Tiêu đề bài viết <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            placeholder="Nhập tiêu đề ấn tượng và súc tích..."
                            required
                            class="w-full px-4 py-3 text-base sm:text-lg font-semibold bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('title') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition"
                        >
                        @error('title')
                            <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block text-sm font-bold text-[var(--color-text)] mb-1.5">
                            Mô tả tóm tắt (Excerpt)
                        </label>
                        <textarea
                            name="excerpt"
                            id="excerpt"
                            rows="2"
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong danh sách feed (tối đa 500 ký tự)..."
                            class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('excerpt') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition"
                        >{{ old('excerpt') }}</textarea>
                        <p class="mt-1 text-xs text-[var(--color-text-secondary)]">Nếu bỏ trống, hệ thống sẽ tự động trích đoạn từ nội dung bài viết.</p>
                        @error('excerpt')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Body / Content -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                    <div class="flex items-center justify-between mb-2">
                        <label for="content" class="block text-sm font-bold text-[var(--color-text)]">
                            Nội dung chi tiết <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs text-[var(--color-text-secondary)]">Hỗ trợ định dạng văn bản</span>
                    </div>

                    <textarea
                        name="content"
                        id="content"
                        rows="16"
                        placeholder="Bắt đầu chia sẻ kiến thức của bạn ở đây..."
                        required
                        class="w-full px-4 py-3 text-sm font-mono leading-relaxed bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('content') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition"
                    >{{ old('content', old('body')) }}</textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                    @error('body')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Right Sidebar Column (Meta & Actions) -->
            <div class="space-y-6">
                <!-- Publishing Workflow Status Box -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                    <h2 class="text-sm font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Trạng thái xuất bản
                    </h2>

                    <div class="p-3.5 rounded-xl bg-[var(--color-surface-hover)] border border-[var(--color-border)] text-xs text-[var(--color-text-secondary)] mb-5 leading-relaxed">
                        <p class="font-semibold text-[var(--color-text)] mb-1">Quy trình kiểm duyệt:</p>
                        Bài viết mới sẽ được lưu dưới dạng <strong>Bản nháp (Draft)</strong>. Sau khi hoàn thiện, bạn có thể bấm <em>Gửi xét duyệt</em> để Ban biên tập thẩm định.
                    </div>

                    <div class="space-y-3">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] shadow-md transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Lưu bản nháp (Draft)
                        </button>

                        <a
                            href="{{ route('posts.stats') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-full text-xs font-semibold text-[var(--color-text-secondary)] hover:text-[var(--color-text)] bg-[var(--color-surface)] border border-[var(--color-border)] hover:bg-[var(--color-surface-hover)] transition"
                        >
                            Hủy bỏ
                        </a>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                    <label for="category_id" class="block text-sm font-bold text-[var(--color-text)] mb-2">
                        Chuyên mục <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="category_id"
                        id="category_id"
                        required
                        class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('category_id') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] transition"
                    >
                        <option value="">-- Chọn chuyên mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thumbnail Upload -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                    <label class="block text-sm font-bold text-[var(--color-text)] mb-2">
                        Ảnh đại diện (Thumbnail)
                    </label>

                    <div
                        id="drop-zone"
                        class="border-2 border-dashed border-[var(--color-border)] hover:border-zinc-500 rounded-xl p-4 text-center cursor-pointer transition bg-[var(--color-surface-hover)] relative"
                    >
                        <input
                            type="file"
                            name="thumbnail"
                            id="thumbnail"
                            accept="image/jpeg,image/png,image/webp,image/jpg"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            onchange="previewThumbnail(event)"
                        >
                        <div id="upload-placeholder" class="space-y-2 py-4">
                            <svg class="mx-auto h-8 w-8 text-[var(--color-text-secondary)]" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="text-xs text-[var(--color-text)] font-medium">Bấm để tải ảnh lên hoặc kéo thả vào đây</p>
                            <p class="text-[11px] text-[var(--color-text-secondary)]">JPG, PNG, WEBP tối đa 2MB</p>
                        </div>

                        <div id="preview-container" class="hidden">
                            <img id="image-preview" src="#" alt="Thumbnail preview" class="w-full h-40 object-cover rounded-lg mb-2 shadow-xs">
                            <p class="text-[11px] text-indigo-400 font-medium">Bấm vào để đổi ảnh khác</p>
                        </div>
                    </div>
                    @error('thumbnail')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags Selection -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                    <label class="block text-sm font-bold text-[var(--color-text)] mb-2">
                        Thẻ bài viết (Tags)
                    </label>
                    <p class="text-xs text-[var(--color-text-secondary)] mb-3">Chọn các từ khóa chủ đề liên quan:</p>

                    @php
                        $selectedTags = old('tags', []);
                    @endphp

                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-1 no-scrollbar">
                        @foreach ($tags as $tag)
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer border transition select-none has-checked:bg-indigo-500/20 has-checked:text-indigo-400 has-checked:border-indigo-500/40 border-[var(--color-border)] bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:border-zinc-500">
                                <input
                                    type="checkbox"
                                    name="tags[]"
                                    value="{{ $tag->id }}"
                                    class="hidden"
                                    @checked(in_array($tag->id, (array)$selectedTags))
                                >
                                <span>#{{ $tag->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('tags')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewThumbnail(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('preview-container').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
