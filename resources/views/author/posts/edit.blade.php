@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-[var(--color-text-secondary)] uppercase tracking-wider mb-1">
                <a href="{{ route('posts.stats') }}" class="hover:text-indigo-400 transition">Không gian tác giả</a>
                <span>&rsaquo;</span>
                <span class="text-indigo-400">Chỉnh sửa bài viết</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-[var(--color-text)] tracking-tight">
                    Chỉnh sửa bài viết
                </h1>
                <!-- Status Badge -->
                @if($post->status === 'published')
                    <x-badge variant="status-published" size="sm">
                        Đã xuất bản (Published)
                    </x-badge>
                @elseif($post->status === 'pending')
                    <x-badge variant="status-pending" size="sm">
                        Chờ duyệt (Pending)
                    </x-badge>
                @elseif($post->status === 'rejected')
                    <x-badge variant="status-rejected" size="sm">
                        Bị từ chối (Rejected)
                    </x-badge>
                @else
                    <x-badge variant="status-draft" size="sm">
                        Bản nháp (Draft)
                    </x-badge>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($post->status === 'published')
                <a href="{{ route('posts.show', $post->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-emerald-400 bg-emerald-950/40 border border-emerald-800/60 rounded-full hover:bg-emerald-900/60 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Xem bài live
                </a>
            @endif

            <a href="{{ route('posts.stats') }}" class="px-4 py-2 text-xs font-semibold text-[var(--color-text)] bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full hover:bg-[var(--color-surface-hover)] transition">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Success Feedback Message -->
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

    <!-- Rejection Alert / Resubmit Guidance -->
    @if($post->status === 'rejected')
        <div class="mb-6 p-5 rounded-2xl bg-rose-950/50 border border-rose-800/60 text-rose-200 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-rose-100 text-base mb-1">Bài viết đã bị Ban biên tập từ chối phê duyệt</h3>
                    <p class="text-rose-300 text-xs leading-relaxed">
                        Bạn có thể rà soát và chỉnh sửa nội dung bên dưới theo tiêu chuẩn biên tập, sau đó bấm <strong>"Gửi lại xét duyệt (Resubmit)"</strong> để gửi lại cho Ban biên tập.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Summary Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-800/60 text-rose-200 text-sm">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Có lỗi xảy ra khi lưu:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Main Column (Content Form) -->
        <div class="lg:col-span-2 space-y-6">
            <form id="edit-post-form" action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title & Excerpt -->
                <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)] space-y-4 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-[var(--color-text)] mb-1.5">
                            Tiêu đề bài viết <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $post->title) }}"
                            placeholder="Nhập tiêu đề bài viết..."
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
                            placeholder="Tóm tắt ngắn gọn nội dung hiển thị trong danh sách feed..."
                            class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('excerpt') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition"
                        >{{ old('excerpt', $post->excerpt) }}</textarea>
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
                        rows="18"
                        placeholder="Nội dung bài viết..."
                        required
                        class="w-full px-4 py-3 text-sm font-mono leading-relaxed bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('content') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] placeholder-[var(--color-text-secondary)] transition"
                    >{{ old('content', old('body', $post->body)) }}</textarea>
                    @error('content')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                    @error('body')
                        <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        <!-- Right Sidebar Column (Meta & Actions) -->
        <div class="space-y-6">
            <!-- Publishing Actions Box -->
            <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                <h2 class="text-sm font-bold text-[var(--color-text)] mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Hành động & Phê duyệt
                </h2>

                <div class="space-y-3">
                    <!-- Update Button -->
                    <button
                        type="submit"
                        form="edit-post-form"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-xs font-bold bg-[var(--color-text)] text-[var(--color-bg)] hover:opacity-90 active:scale-[0.98] shadow-md transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Lưu thay đổi
                    </button>

                    <!-- Submit / Resubmit for Review Button -->
                    @if(in_array($post->status, ['draft', 'rejected'], true))
                        <form action="{{ route('posts.submit', $post) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 transition cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $post->status === 'rejected' ? 'Gửi lại xét duyệt (Resubmit)' : 'Gửi xét duyệt (Submit)' }}
                            </button>
                        </form>
                    @elseif($post->status === 'pending')
                        <div class="p-3 rounded-xl bg-amber-950/40 border border-amber-800/60 text-xs text-amber-300 text-center font-medium">
                            Bài viết đang trong hàng đợi xét duyệt của Ban biên tập.
                        </div>
                    @endif

                    <!-- Delete Button Form -->
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác.');">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full text-xs font-semibold text-rose-400 hover:text-white bg-[var(--color-surface)] hover:bg-rose-600 border border-rose-900/60 hover:border-rose-600 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Xóa bài viết
                        </button>
                    </form>
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
                    form="edit-post-form"
                    required
                    class="w-full px-4 py-2.5 text-sm bg-[var(--color-input)] focus:bg-[var(--color-surface)] border @error('category_id') border-rose-500 @else border-[var(--color-border)] focus:border-indigo-500 @enderror rounded-xl focus:ring-1 focus:ring-indigo-500 text-[var(--color-text)] transition"
                >
                    <option value="">-- Chọn chuyên mục --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1.5 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail Upload & Current Image Preview -->
            <div class="bg-[var(--color-surface)] p-6 rounded-2xl border border-[var(--color-border)]">
                <label class="block text-sm font-bold text-[var(--color-text)] mb-2">
                    Ảnh đại diện (Thumbnail)
                </label>

                @if($post->thumbnail)
                    <div id="current-thumbnail-wrapper" class="mb-3">
                        <p class="text-xs text-[var(--color-text-secondary)] mb-1.5 font-medium">Ảnh hiện tại:</p>
                        <div class="relative rounded-xl overflow-hidden border border-[var(--color-border)] group">
                            <img
                                src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}"
                                alt="{{ $post->title }}"
                                class="w-full h-36 object-cover"
                            >
                        </div>
                    </div>
                @endif

                <div
                    id="drop-zone"
                    class="border-2 border-dashed border-[var(--color-border)] hover:border-zinc-500 rounded-xl p-4 text-center cursor-pointer transition bg-[var(--color-surface-hover)] relative"
                >
                    <input
                        type="file"
                        name="thumbnail"
                        id="thumbnail"
                        form="edit-post-form"
                        accept="image/jpeg,image/png,image/webp,image/jpg"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        onchange="previewThumbnail(event)"
                    >
                    <div id="upload-placeholder" class="space-y-2 py-3">
                        <svg class="mx-auto h-7 w-7 text-[var(--color-text-secondary)]" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-xs text-[var(--color-text)] font-medium">Chọn ảnh mới để thay thế</p>
                        <p class="text-[11px] text-[var(--color-text-secondary)]">JPG, PNG, WEBP tối đa 2MB</p>
                    </div>

                    <div id="preview-container" class="hidden">
                        <img id="image-preview" src="#" alt="Thumbnail preview" class="w-full h-36 object-cover rounded-lg mb-2 shadow-xs">
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
                    $currentTagIds = $post->tags->pluck('id')->all();
                    $selectedTags = old('tags', $currentTagIds);
                @endphp

                <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-1 no-scrollbar">
                    @foreach ($tags as $tag)
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer border transition select-none has-checked:bg-indigo-500/20 has-checked:text-indigo-400 has-checked:border-indigo-500/40 border-[var(--color-border)] bg-[var(--color-surface-hover)] text-[var(--color-text-secondary)] hover:border-zinc-500">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                form="edit-post-form"
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
            const currentWrapper = document.getElementById('current-thumbnail-wrapper');
            if (currentWrapper) {
                currentWrapper.classList.add('opacity-40');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
