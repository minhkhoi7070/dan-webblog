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

<div class="card p-3.5 p-sm-4 border-theme bg-theme-surface shadow-xs rounded-xl" id="tag-manager-component">
    <!-- Header with title & counter -->
    <div class="d-flex align-items-center justify-content-between mb-1">
        <label class="label-uppercase text-theme mb-0 d-block fw-bold">
            Thẻ bài viết (Tags)
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

<script>
(function() {
    const FORM_ID = '{{ $formId }}';
    const MAX_TAGS = 10;
    const initialSelectedIds = {!! $initialTagsJson !!};
    const allTags = {!! $allTagsJson !!};

    // Store in-memory maps
    const tagMap = new Map(); // id -> { id, name, slug }
    const selectedIds = new Set();

    allTags.forEach(tag => {
        tagMap.set(tag.id, tag);
    });

    initialSelectedIds.forEach(id => {
        if (tagMap.has(id)) {
            selectedIds.add(id);
        }
    });

    // Elements
    const chipsContainer = document.getElementById('selected-tags-chips');
    const hiddenInputsContainer = document.getElementById('hidden-tag-inputs');
    const searchInput = document.getElementById('tag-search-input');
    const addBtn = document.getElementById('tag-add-btn');
    const cloudContainer = document.getElementById('available-tags-cloud');
    const counterEl = document.getElementById('tag-selected-count');
    const limitAlert = document.getElementById('tag-limit-alert');
    const feedbackEl = document.getElementById('tag-input-feedback');
    const filterCountEl = document.getElementById('tag-filter-count');

    function cleanTagName(name) {
        if (!name) return '';
        return name.replace(/^#+/, '').trim();
    }

    function showFeedback(msg) {
        if (!feedbackEl) return;
        if (msg) {
            feedbackEl.textContent = msg;
            feedbackEl.classList.remove('d-none');
        } else {
            feedbackEl.textContent = '';
            feedbackEl.classList.add('d-none');
        }
    }

    function markFormDirty() {
        if (window.__postEditorSetDirty) {
            window.__postEditorSetDirty();
        }
    }

    function render() {
        // 1. Update Counter & Limit Alert
        if (counterEl) counterEl.textContent = selectedIds.size;
        if (limitAlert) {
            if (selectedIds.size >= MAX_TAGS) {
                limitAlert.classList.remove('d-none');
            } else {
                limitAlert.classList.add('d-none');
            }
        }

        // 2. Render Selected Chips
        chipsContainer.innerHTML = '';
        if (selectedIds.size === 0) {
            chipsContainer.innerHTML = `
                <span id="no-tags-selected-placeholder" class="small text-theme-muted fst-italic py-1 px-1" style="font-size: 11.5px;">
                    Chưa có thẻ nào được chọn.
                </span>
            `;
        } else {
            selectedIds.forEach(id => {
                const tag = tagMap.get(id);
                if (!tag) return;

                const chip = document.createElement('span');
                chip.className = 'd-inline-flex align-items-center gap-1.5 py-1 ps-2.5 pe-1.5 rounded-pill bg-theme-surface text-theme border border-theme shadow-xs';
                chip.style.fontSize = '12px';

                chip.innerHTML = `
                    <span class="fw-medium font-monospace">#${escapeHtml(tag.name)}</span>
                    <button type="button" class="btn btn-link text-theme-muted p-0 d-inline-flex align-items-center justify-content-center hover-danger"
                            style="width: 18px; height: 18px; border-radius: 999px; text-decoration: none;"
                            aria-label="Gỡ thẻ ${escapeHtml(tag.name)}"
                            title="Gỡ thẻ ${escapeHtml(tag.name)}">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;

                chip.querySelector('button').addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    removeTag(id);
                });

                chipsContainer.appendChild(chip);
            });
        }

        // 3. Render Hidden Inputs
        hiddenInputsContainer.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = id;
            if (FORM_ID) {
                input.setAttribute('form', FORM_ID);
            }
            hiddenInputsContainer.appendChild(input);
        });

        // 4. Render Available Tags Cloud
        filterCloud();
    }

    function filterCloud() {
        const query = cleanTagName(searchInput?.value || '').toLowerCase();
        cloudContainer.innerHTML = '';

        let visibleCount = 0;
        let totalCount = 0;

        tagMap.forEach(tag => {
            totalCount++;
            const tagNameLower = tag.name.toLowerCase();
            const tagSlugLower = tag.slug.toLowerCase();
            const matches = !query || tagNameLower.includes(query) || tagSlugLower.includes(query);

            if (!matches) return;

            visibleCount++;
            const isSelected = selectedIds.has(tag.id);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.fontSize = '12px';

            if (isSelected) {
                btn.className = 'btn btn-sm btn-editorial-primary rounded-pill py-0.5 px-2.5 small d-inline-flex align-items-center gap-1 shadow-xs';
                btn.innerHTML = `
                    <svg style="width: 11px; height: 11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>#${escapeHtml(tag.name)}</span>
                `;
                btn.title = `Nhấn để bỏ chọn #${tag.name}`;
            } else {
                btn.className = 'btn btn-sm btn-outline-theme rounded-pill py-0.5 px-2.5 small';
                btn.textContent = `#${tag.name}`;
                btn.title = `Nhấn để chọn #${tag.name}`;
            }

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTag(tag.id);
            });

            cloudContainer.appendChild(btn);
        });

        if (filterCountEl) {
            if (query) {
                filterCountEl.textContent = `${visibleCount}/${totalCount} thẻ`;
            } else {
                filterCountEl.textContent = '';
            }
        }
    }

    function toggleTag(id) {
        if (selectedIds.has(id)) {
            removeTag(id);
        } else {
            addTag(id);
        }
    }

    function addTag(id) {
        if (selectedIds.size >= MAX_TAGS) {
            if (limitAlert) limitAlert.classList.remove('d-none');
            showFeedback('Không thể chọn quá 10 thẻ.');
            return;
        }
        selectedIds.add(id);
        showFeedback('');
        markFormDirty();
        render();
    }

    function removeTag(id) {
        selectedIds.delete(id);
        showFeedback('');
        markFormDirty();
        render();
    }

    async function handleAddNewTag() {
        const raw = searchInput?.value || '';
        const clean = cleanTagName(raw);

        if (!clean) {
            showFeedback('Vui lòng nhập tên thẻ bài viết.');
            return;
        }

        if (clean.length > 50) {
            showFeedback('Tên thẻ không được dài quá 50 ký tự.');
            return;
        }

        // Check if already in selected
        for (const id of selectedIds) {
            const t = tagMap.get(id);
            if (t && t.name.toLowerCase() === clean.toLowerCase()) {
                showFeedback(`Thẻ #${t.name} đã được chọn.`);
                return;
            }
        }

        // Check if limit reached
        if (selectedIds.size >= MAX_TAGS) {
            if (limitAlert) limitAlert.classList.remove('d-none');
            showFeedback('Không thể chọn thêm thẻ (Tối đa 10 thẻ).');
            return;
        }

        // Check if exists in tagMap
        let existingId = null;
        for (const [id, t] of tagMap.entries()) {
            if (t.name.toLowerCase() === clean.toLowerCase() || t.slug.toLowerCase() === clean.toLowerCase()) {
                existingId = id;
                break;
            }
        }

        if (existingId !== null) {
            addTag(existingId);
            if (searchInput) searchInput.value = '';
            filterCloud();
            return;
        }

        // Create new tag via AJAX
        const originalText = addBtn ? addBtn.innerHTML : '';
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 11px; height: 11px;"></span>';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            const response = await fetch('{{ route("tags.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ name: clean }),
            });

            const data = await response.json();

            if (response.ok && data.success && data.tag) {
                const newTag = data.tag;
                tagMap.set(newTag.id, newTag);
                addTag(newTag.id);
                if (searchInput) searchInput.value = '';
                showFeedback('');
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Không thể tạo thẻ.');
                showFeedback(msg);
            }
        } catch {
            showFeedback('Lỗi kết nối khi tạo thẻ mới. Vui lòng thử lại.');
        } finally {
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.innerHTML = originalText;
            }
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            showFeedback('');
            filterCloud();
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleAddNewTag();
            } else if (e.key === ',') {
                e.preventDefault();
                handleAddNewTag();
            }
        });
    }

    if (addBtn) {
        addBtn.addEventListener('click', (e) => {
            e.preventDefault();
            handleAddNewTag();
        });
    }

    // Initial render
    render();
})();
</script>
