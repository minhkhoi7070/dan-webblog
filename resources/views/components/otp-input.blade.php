@props(['name' => 'otp', 'autofocus' => true, 'value' => ''])

@php
    $initialValue = (string) old($name, $value);
@endphp

<div class="otp-component my-2" data-otp-component>
    <!-- Hidden input storing the full 6-digit OTP -->
    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $initialValue }}">

    <!-- 6 Input Slots -->
    <div class="d-flex align-items-center justify-content-center gap-1.5 gap-sm-2 user-select-none mb-2" role="group" aria-label="Mã xác thực OTP gồm 6 chữ số">
        @for ($i = 0; $i < 6; $i++)
            @php
                $digit = substr($initialValue, $i, 1);
            @endphp
            <input type="text"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   maxlength="1"
                   class="form-control text-center fw-bold text-theme bg-surface-2 border-theme otp-slot-box @error($name) is-invalid @enderror"
                   data-otp-slot="{{ $i }}"
                   aria-label="Chữ số thứ {{ $i + 1 }}"
                   autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                   style="width: 44px; height: 52px; font-size: 20px; border-radius: 10px; font-variant-numeric: tabular-nums; transition: border-color 0.15s, box-shadow 0.15s;"
                   value="{{ $digit !== false && $digit !== '' ? $digit : '' }}"
                   {{ $autofocus && $i === 0 ? 'autofocus' : '' }}>
        @endfor
    </div>

    <x-input-error :messages="$errors->get($name)" class="mt-2 text-center" />
</div>

<script>
(function() {
    function initOtpComponent() {
        document.querySelectorAll('[data-otp-component]').forEach(function(container) {
            if (container.dataset.otpInitialized) return;
            container.dataset.otpInitialized = 'true';

            var hiddenInput = container.querySelector('input[type="hidden"]');
            var slots = Array.from(container.querySelectorAll('[data-otp-slot]'));
            if (!hiddenInput || slots.length !== 6) return;

            function syncHidden() {
                var code = slots.map(function(slot) { return slot.value.replace(/\D/g, ''); }).join('');
                hiddenInput.value = code;
            }

            slots.forEach(function(slot, index) {
                // Focus styling
                slot.addEventListener('focus', function() {
                    slot.select();
                });

                // Input handling (Single digit entry & auto-advance)
                slot.addEventListener('input', function(e) {
                    var val = slot.value.replace(/\D/g, '');
                    if (val.length > 1) {
                        // In case browser autofills multiple digits
                        handlePasteString(val, index);
                        return;
                    }
                    slot.value = val;
                    syncHidden();

                    if (val.length === 1 && index < 5) {
                        slots[index + 1].focus();
                    }
                });

                // Keydown navigation (Backspace, Left/Right arrows)
                slot.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!slot.value && index > 0) {
                            slots[index - 1].focus();
                            slots[index - 1].value = '';
                            syncHidden();
                            e.preventDefault();
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        slots[index - 1].focus();
                        e.preventDefault();
                    } else if (e.key === 'ArrowRight' && index < 5) {
                        slots[index + 1].focus();
                        e.preventDefault();
                    }
                });

                // Paste handling
                slot.addEventListener('paste', function(e) {
                    e.preventDefault();
                    var pasteData = (e.clipboardData || window.clipboardData).getData('text');
                    handlePasteString(pasteData, index);
                });
            });

            function handlePasteString(str, startIndex) {
                var digits = String(str).replace(/\D/g, '').slice(0, 6);
                if (!digits) return;

                // If pasted 6 digits, fill from slot 0
                var start = (digits.length === 6) ? 0 : startIndex;
                for (var i = 0; i < digits.length && (start + i) < 6; i++) {
                    slots[start + i].value = digits[i];
                }
                syncHidden();

                var nextFocusIndex = Math.min(start + digits.length, 5);
                slots[nextFocusIndex].focus();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOtpComponent);
    } else {
        initOtpComponent();
    }
})();
</script>
