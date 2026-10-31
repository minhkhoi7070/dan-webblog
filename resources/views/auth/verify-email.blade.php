<x-guest-layout>
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-accent-subtle text-accent mb-3" style="width: 56px; height: 56px;">
            <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h1 class="h4 fw-bold text-theme mb-1 tracking-tight">
            Xác minh email
        </h1>
        <p class="small text-theme-secondary mb-2">
            Nhập mã 6 chữ số đã được gửi đến email của bạn:
        </p>

        @if(auth()->user())
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-surface-2 border border-theme small text-theme fw-medium user-select-all mb-2">
                <svg style="width: 15px; height: 15px;" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
                <span class="text-truncate" style="max-width: 280px;">{{ auth()->user()->email }}</span>
            </div>
        @endif
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success d-flex align-items-center gap-2 small mb-3 py-2.5 px-3 rounded-3" role="alert">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Mã xác thực OTP mới đã được gửi thành công đến hộp thư của bạn.</span>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning d-flex align-items-center gap-2 small mb-3 py-2.5 px-3 rounded-3" role="alert">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 small mb-3 py-2.5 px-3 rounded-3" role="alert">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Submit OTP -->
    <form method="POST" action="{{ route((request()->is('admin/*') ? 'admin.' : '').'verification.otp.verify') }}" class="mb-3" id="otp-verify-form">
        @csrf

        <x-otp-input name="otp" :autofocus="true" />

        <!-- Expiry countdown timer -->
        <div class="text-center small text-theme-secondary mb-3" id="otp-expiry-container" data-seconds="{{ $expiry }}" data-expires-at="{{ $expiresAtMs ?? '' }}" data-server-time="{{ $serverTimeMs ?? '' }}" data-has-otp="{{ $hasOtp ? '1' : '0' }}">
            <span class="d-inline-flex align-items-center gap-1.5">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @if($hasOtp)
                    <span>Mã hết hạn sau:</span>
                @endif
                <strong class="text-theme" id="otp-expiry-timer">{{ ! $hasOtp ? 'Chưa có mã OTP' : ($expiry === 0 ? '00:00 (Đã hết hạn)' : sprintf('%02d:%02d', intdiv($expiry, 60), $expiry % 60)) }}</strong>
            </span>
        </div>

        <button type="submit" class="btn btn-editorial-primary w-100 py-2.5 rounded-pill fw-semibold small d-flex align-items-center justify-content-center gap-2 mb-2">
            Xác minh
        </button>
    </form>

    <!-- Resend OTP Form with Cooldown -->
    <div class="d-flex align-items-center justify-content-center mb-3">
        <form method="POST" action="{{ route((request()->is('admin/*') ? 'admin.' : '').'verification.send') }}" class="m-0" id="otp-resend-form">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-link text-theme-secondary hover-accent text-decoration-none p-0 small"
                    id="otp-resend-btn"
                    data-cooldown="{{ $cooldown ?? 0 }}">
                <span id="otp-resend-text">Gửi lại mã OTP</span>
            </button>
        </form>
    </div>

    <!-- Footer Links: Change Email & Logout -->
    <div class="d-flex align-items-center justify-content-between pt-3 border-theme-top">
        <a href="{{ route('profile.edit') }}" class="small text-theme-secondary hover-accent text-decoration-none">
            Đổi địa chỉ email
        </a>

        <form method="POST" action="{{ route(request()->is('admin/*') ? 'admin.logout' : 'logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-link text-theme-secondary hover-accent text-decoration-none small p-0">
                Đăng xuất
            </button>
        </form>
    </div>

    <script>
    (function() {
        // Expiry countdown
        var expiryEl = document.getElementById('otp-expiry-timer');
        var expiryContainer = document.getElementById('otp-expiry-container');
        if (!expiryEl || !expiryContainer) return;

        var hasOtp = expiryContainer.dataset.hasOtp === '1';
        if (!hasOtp) return;

        var clientLoadTime = Date.now();
        var serverTimeRaw = parseInt(expiryContainer.dataset.serverTime || '', 10);
        var expiresAtRaw = parseInt(expiryContainer.dataset.expiresAt || '', 10);
        var initialSeconds = parseInt(expiryContainer.dataset.seconds || '0', 10);

        // Calculate clock offset between server and client:
        // currentServerTime = Date.now() + clockOffset
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

        // Resend cooldown
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
