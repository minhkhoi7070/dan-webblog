@props([
    'tags' => collect(),
    'selectedTags' => [],
    'formId' => 'create-post-form',
])

@php
    $selectedTagIds = array_map('intval', (array) $selectedTags);
    $initialTagsJson = json_encode($selectedTagIds);
    $allTagsJson = json_encode($tags->map(fn($t) => [
        'id' => $t->id,
        'name' => $t->name,
        'slug' => $t->slug,
    ]));
@endphp

<div class="card p-4 border-theme bg-theme-surface shadow-xs rounded-xl"
     id="tag-manager-component"
     data-form-id="{{ $formId }}"
     data-initial-tags="{{ $initialTagsJson }}"
     data-all-tags="{{ $allTagsJson }}">
    <!-- Header with title & counter -->
    <div class="d-flex align-items-center justify-content-between mb-1">
        <label class="label-uppercase text-theme mb-0 d-flex align-items-center gap-1.5 fw-bold">
            <svg style="width: 14px; height: 14px; color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
            </svg>
            Thẻ bài viết
        </label>
        <span id="tag-counter" class="small text-theme-muted font-monospace" style="font-size: 11px;">
            (<span id="tag-selected-count">{{ count($selectedTagIds) }}</span>/10 thẻ)
        </span>
    </div>
    <p class="small text-theme-muted mb-3" style="font-size: 12px;">
        Gắn tối đa 10 thẻ giúp bài viết dễ dàng được tìm kiếm và phân loại chủ đề.
    </p>

    <!-- Exceeded limit alert -->
    <div id="tag-limit-alert" class="alert alert-warning py-2 px-2.5 small mb-3 @if(count($selectedTagIds) < 10) d-none @endif border border-warning border-opacity-50 rounded-3" role="alert" style="font-size: 12px;">
        <div class="d-flex align-items-center gap-1.5">
            <svg style="width: 15px; height: 15px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="fw-medium">Đã đạt giới hạn tối đa 10 thẻ cho mỗi bài viết.</span>
        </div>
    </div>

    <!-- Selected Tag Chips Container -->
    <div class="mb-3">
        <div class="small fw-semibold text-theme-muted mb-1.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
            Thẻ đã chọn:
        </div>
        <div id="selected-tags-chips" class="d-flex flex-wrap gap-1.5 p-2 rounded-3 bg-surface-2 border border-theme" style="min-height: 44px;">
            @php
                $initiallySelectedTags = $tags->whereIn('id', $selectedTagIds);
            @endphp
            @forelse($initiallySelectedTags as $selTag)
                <span class="d-inline-flex align-items-center gap-1.5 py-1 ps-2.5 pe-1.5 rounded-pill bg-theme-surface text-theme border border-theme shadow-xs" style="font-size: 12px;">
                    <span class="fw-medium font-monospace">#{{ $selTag->name }}</span>
                    <button type="button" class="btn btn-link text-theme-muted p-0 d-inline-flex align-items-center justify-content-center hover-danger"
                            style="width: 18px; height: 18px; border-radius: 999px; text-decoration: none;"
                            aria-label="Gỡ thẻ {{ $selTag->name }}"
                            title="Gỡ thẻ {{ $selTag->name }}">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @empty
                <span id="no-tags-selected-placeholder" class="small text-theme-muted fst-italic py-1 px-1" style="font-size: 11.5px;">
                    Chưa có thẻ nào được chọn.
                </span>
            @endforelse
        </div>
    </div>

    <!-- Hidden Inputs Container (synced in real-time) -->
    <div id="hidden-tag-inputs">
        @foreach($selectedTagIds as $selId)
            <input type="hidden" name="tags[]" value="{{ $selId }}" @if($formId) form="{{ $formId }}" @endif>
        @endforeach
    </div>

    <!-- Tag Search & Add New Tag Input -->
    <div class="mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-surface-2 border-theme text-theme-muted font-monospace" style="font-size: 12px;">#</span>
            <input
                type="text"
                id="tag-search-input"
                class="form-control form-control-sm form-control-editorial"
                placeholder="Tìm hoặc gõ thẻ mới (Enter hoặc dấu phẩy)..."
                autocomplete="off"
                style="font-size: 12px;"
            >
            <button
                type="button"
                id="tag-add-btn"
                class="btn btn-sm btn-editorial-primary px-3 fw-semibold"
                title="Thêm thẻ mới"
                style="font-size: 12px;"
            >
                + Thêm
            </button>
        </div>
        <div id="tag-input-feedback" class="small text-danger mt-1 d-none" style="font-size: 11px;"></div>
    </div>

    <!-- Available / Suggested Tags Cloud from Database -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-1.5">
            <span class="small fw-semibold text-theme-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                Gợi ý thẻ từ hệ thống:
            </span>
            <span id="tag-filter-count" class="small text-theme-muted" style="font-size: 11px;"></span>
        </div>

        <div id="available-tags-cloud" class="d-flex flex-wrap gap-1.5 overflow-y-auto no-scrollbar" style="max-height: 180px;">
            <!-- Buttons injected via JS -->
        </div>
    </div>

    @error('tags')
        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
    @enderror
    @error('tags.*')
        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
    @enderror
</div>
