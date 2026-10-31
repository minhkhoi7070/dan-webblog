<x-guest-layout>
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-accent-subtle text-accent mb-3" style="width: 56px; height: 56px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight">
            Nhập mã OTP đặt lại mật khẩu
        </h1>
        <p class="small text-theme-secondary mb-2">
            Mã OTP 6 chữ số đã được gửi đến hộp thư của bạn:
        </p>

        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-surface-2 border border-theme small text-theme fw-medium user-select-all mb-2">
            <svg style="width: 15px; height: 15px;" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
            </svg>
            <span class="text-truncate" style="max-width: 280px;">{{ $email }}</span>
        </div>
    </div>

    <!-- Status Alert -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    @if (session('warning'))
        <div class="alert alert-warning small mb-3 py-2 px-3 rounded-3" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger small mb-3 py-2 px-3 rounded-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- OTP Form -->
    <form method="POST" action="{{ route('password.otp.verify') }}" class="mb-3" id="otp-verify-form">
        @csrf

        <x-otp-input name="otp" :autofocus="true" />

        <!-- Expiry countdown timer -->
        <div class="text-center small text-theme-secondary mb-3" id="otp-expiry-container" data-seconds="{{ $expiry ?? 300 }}" data-expires-at="{{ $expiresAtMs ?? '' }}" data-server-time="{{ $serverTimeMs ?? '' }}">
            <span class="d-inline-flex align-items-center gap-1.5">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Mã hết hạn sau:</span>
                <strong class="text-theme" id="otp-expiry-timer">{{ ($expiry ?? 300) === 0 ? '00:00 (Đã hết hạn)' : sprintf('%02d:%02d', intdiv($expiry ?? 300, 60), ($expiry ?? 300) % 60) }}</strong>
            </span>
        </div>

        <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 rounded-pill fw-semibold small d-flex align-items-center justify-content-center gap-2 mb-2">
            Xác thực mã
        </button>
    </form>

    <!-- Resend OTP Form with Cooldown -->
    <div class="d-flex align-items-center justify-content-center mb-3">
        <form method="POST" action="{{ route('password.otp.resend') }}" class="m-0" id="otp-resend-form">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-link text-theme-secondary hover-accent text-decoration-none p-0 small"
                    id="otp-resend-btn"
                    data-cooldown="{{ $cooldown ?? 0 }}">
                <span id="otp-resend-text">Gửi lại mã OTP</span>
            </button>
        </form>
    </div>

    <div class="text-center pt-3 border-theme-top">
        <a href="{{ route('password.request') }}" class="small text-theme-secondary hover-accent text-decoration-none">
            &larr; Dùng địa chỉ email khác
        </a>
    </div>

    <script>
    (function() {
        var expiryEl = document.getElementById('otp-expiry-timer');
        var expiryContainer = document.getElementById('otp-expiry-container');
        if (!expiryEl || !expiryContainer) return;

        var clientLoadTime = Date.now();
        var serverTimeRaw = parseInt(expiryContainer.dataset.serverTime || '', 10);
        var expiresAtRaw = parseInt(expiryContainer.dataset.expiresAt || '', 10);
        var initialSeconds = parseInt(expiryContainer.dataset.seconds || '300', 10);

        var clockOffset = (!isNaN(serverTimeRaw) && serverTimeRaw > 0) ? (serverTimeRaw - clientLoadTime) : 0;
        var hasEpoch = (!isNaN(expiresAtRaw) && expiresAtRaw > 0);
        var fallbackDeadline = performance.now() + (initialSeconds * 1000);

        function formatTime(totalSec) {
            var m = Math.floor(totalSec / 60);
            var s = totalSec % 60;
            return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        }

        function calculateRemainingSeconds() {
            if (hasEpoch) {
                var currentServerNow = Date.now() + clockOffset;
                return Math.max(0, Math.ceil((expiresAtRaw - currentServerNow) / 1000));
            }
            return Math.max(0, Math.ceil((fallbackDeadline - performance.now()) / 1000));
        }

        var timerId = null;

        function updateExpiry() {
            if (!expiryEl || !expiryEl.isConnected) {
                if (timerId) clearInterval(timerId);
                return;
            }

            var remaining = calculateRemainingSeconds();
            if (remaining <= 0) {
                expiryEl.textContent = '00:00 (Đã hết hạn)';
                if (timerId) {
                    clearInterval(timerId);
                    timerId = null;
                }
                return;
            }

            expiryEl.textContent = formatTime(remaining);
        }

        updateExpiry();
        timerId = setInterval(updateExpiry, 1000);

        var resendBtn = document.getElementById('otp-resend-btn');
        var resendText = document.getElementById('otp-resend-text');
        var cooldown = parseInt(resendBtn?.dataset.cooldown || '0', 10);
        var cooldownTimerId = null;

        function updateCooldown() {
            if (!resendBtn || !resendBtn.isConnected) {
                if (cooldownTimerId) clearInterval(cooldownTimerId);
                return;
            }

            if (cooldown > 0) {
                resendBtn.disabled = true;
                resendBtn.classList.add('opacity-50', 'pe-none');
                if (resendText) resendText.textContent = 'Gửi lại mã (' + cooldown + 's)';
                cooldown--;
            } else {
                resendBtn.disabled = false;
                resendBtn.classList.remove('opacity-50', 'pe-none');
                if (resendText) resendText.textContent = 'Gửi lại mã OTP';
                if (cooldownTimerId) {
                    clearInterval(cooldownTimerId);
                    cooldownTimerId = null;
                }
            }
        }

        if (cooldown > 0) {
            updateCooldown();
            cooldownTimerId = setInterval(updateCooldown, 1000);
        }
    })();
    </script>
</x-guest-layout>
