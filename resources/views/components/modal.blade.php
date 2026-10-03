@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$modalSize = match($maxWidth) {
    'sm' => 'modal-sm',
    'lg' => 'modal-lg',
    'xl', '2xl' => 'modal-lg',
    default => '',
};
@endphp

<div class="modal fade {{ $show ? 'show d-block' : '' }}" id="{{ $name }}" tabindex="-1" aria-labelledby="{{ $name }}-label" aria-hidden="{{ $show ? 'false' : 'true' }}">
    <div class="modal-dialog {{ $modalSize }} modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-theme bg-theme-surface shadow-lg p-3 p-sm-4" style="border-radius: 1.25rem;">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    if (!window._modalEventListenerBound) {
        window._modalEventListenerBound = true;
        window.addEventListener('open-modal', (e) => {
            const modalId = typeof e.detail === 'string' ? e.detail : e.detail?.name;
            const el = document.getElementById(modalId);
            if (el && window.bootstrap) {
                const modal = bootstrap.Modal.getOrCreateInstance(el);
                modal.show();
            }
        });
        window.addEventListener('close-modal', (e) => {
            const modalId = typeof e.detail === 'string' ? e.detail : e.detail?.name;
            const el = document.getElementById(modalId);
            if (el && window.bootstrap) {
                const modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            }
        });
    }
</script>
