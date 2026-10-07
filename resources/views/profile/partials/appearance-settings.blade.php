<section id="appearance">
    <header class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                Giao diện hiển thị
            </h2>
        </div>
        <p class="small text-theme-muted mb-0" style="font-size: 13px;">
            Lựa chọn chế độ màu sắc phù hợp với bạn. Tùy chọn được lưu trên trình duyệt và áp dụng tức thì.
        </p>
    </header>

    <div class="row g-3" id="theme-radio-cards" role="radiogroup" aria-label="Lựa chọn giao diện">
        <!-- 1. Light Mode Card -->
        <div class="col-12 col-md-4">
            <label for="theme-option-light"
                   id="theme-card-light"
                   class="settings-option-card">
                <input type="radio"
                       id="theme-option-light"
                       name="theme_mode"
                       value="light"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')">

                <!-- Header: Icon, Label & Status Badge -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="16" height="16" class="text-warning flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span class="fw-semibold text-theme" style="font-size: 13.5px;">Sáng (Light)</span>
                    </div>
                    <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                        Đang dùng
                    </span>
                </div>

                <!-- Visual Mockup: Light Preview -->
                <div class="w-100 rounded-3 border p-2.5 d-flex flex-column gap-1.5 mb-3 shadow-2xs pointer-events-none" style="height: 84px; background-color: #FFFFFF; border-color: #E4E4E7 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="rounded" style="width: 50px; height: 6px; background-color: #A1A1AA;"></div>
                        <div class="rounded-circle" style="width: 12px; height: 12px; background-color: #6366f1;"></div>
                    </div>
                    <div class="rounded p-1.5 d-flex flex-column gap-1" style="background-color: #F4F4F5; border: 1px solid #E4E4E7;">
                        <div class="rounded" style="width: 80%; height: 5px; background-color: #18181B;"></div>
                        <div class="rounded opacity-50" style="width: 55%; height: 4px; background-color: #71717A;"></div>
                    </div>
                </div>

                <p class="small text-theme-muted mb-0" style="font-size: 11.5px; line-height: 1.5;">
                    Trang nhã, rõ ràng với nền sáng tự nhiên, thích hợp môi trường nhiều ánh sáng ban ngày.
                </p>
            </label>
        </div>

        <!-- 2. Dark Mode Card -->
        <div class="col-12 col-md-4">
            <label for="theme-option-dark"
                   id="theme-card-dark"
                   class="settings-option-card">
                <input type="radio"
                       id="theme-option-dark"
                       name="theme_mode"
                       value="dark"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')">

                <!-- Header: Icon, Label & Status Badge -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="16" height="16" class="text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <span class="fw-semibold text-theme" style="font-size: 13.5px;">Tối (Dark)</span>
                    </div>
                    <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                        Đang dùng
                    </span>
                </div>

                <!-- Visual Mockup: Dark Preview -->
                <div class="w-100 rounded-3 border p-2.5 d-flex flex-column gap-1.5 mb-3 shadow-2xs pointer-events-none" style="height: 84px; background-color: #0F0F0F; border-color: #27272A !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="rounded" style="width: 50px; height: 6px; background-color: #71717A;"></div>
                        <div class="rounded-circle" style="width: 12px; height: 12px; background-color: #818cf8;"></div>
                    </div>
                    <div class="rounded p-1.5 d-flex flex-column gap-1" style="background-color: #18181B; border: 1px solid #27272A;">
                        <div class="rounded" style="width: 80%; height: 5px; background-color: #F4F4F5;"></div>
                        <div class="rounded opacity-50" style="width: 55%; height: 4px; background-color: #A1A1AA;"></div>
                    </div>
                </div>

                <p class="small text-theme-muted mb-0" style="font-size: 11.5px; line-height: 1.5;">
                    Màu nền trầm `#0F0F0F` kết hợp thẻ nổi bật, tương phản đạt chuẩn bảo vệ mắt khi đọc đêm.
                </p>
            </label>
        </div>

        <!-- 3. System Mode Card -->
        <div class="col-12 col-md-4">
            <label for="theme-option-system"
                   id="theme-card-system"
                   class="settings-option-card">
                <input type="radio"
                       id="theme-option-system"
                       name="theme_mode"
                       value="system"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('system')">

                <!-- Header: Icon, Label & Status Badge -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <svg width="16" height="16" class="text-theme-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <span class="fw-semibold text-theme" style="font-size: 13.5px;">Hệ thống (System)</span>
                    </div>
                    <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                        Đang dùng
                    </span>
                </div>

                <!-- Visual Mockup: Split System Preview -->
                <div class="w-100 rounded-3 border overflow-hidden position-relative mb-3 shadow-2xs pointer-events-none" style="height: 84px; border-color: var(--color-border) !important;">
                    <div class="row g-0 h-100">
                        <!-- Half Light -->
                        <div class="col-6 p-2 d-flex flex-column gap-1" style="background-color: #FFFFFF; border-right: 1px dashed #A1A1AA;">
                            <div class="rounded" style="width: 35px; height: 5px; background-color: #A1A1AA;"></div>
                            <div class="rounded mt-1" style="width: 75%; height: 4px; background-color: #18181B;"></div>
                            <div class="rounded opacity-50" style="width: 50%; height: 3px; background-color: #71717A;"></div>
                        </div>
                        <!-- Half Dark -->
                        <div class="col-6 p-2 d-flex flex-column gap-1" style="background-color: #0F0F0F;">
                            <div class="rounded" style="width: 35px; height: 5px; background-color: #71717A;"></div>
                            <div class="rounded mt-1" style="width: 75%; height: 4px; background-color: #F4F4F5;"></div>
                            <div class="rounded opacity-50" style="width: 50%; height: 3px; background-color: #A1A1AA;"></div>
                        </div>
                    </div>
                </div>

                <p class="small text-theme-muted mb-0" style="font-size: 11.5px; line-height: 1.5;">
                    Tự động đồng bộ giao diện theo thiết lập Dark/Light Mode trên thiết bị macOS/Windows/iOS/Android.
                </p>
            </label>
        </div>
    </div>

    <!-- Live Sync Script for Appearance Radios -->
    <script>
        (function() {
            function updateAppearanceSettingsUI() {
                var mode = (window.BlogMNMTheme && window.BlogMNMTheme.getMode()) || 'dark';

                var cards = {
                    light: document.getElementById('theme-card-light'),
                    dark: document.getElementById('theme-card-dark'),
                    system: document.getElementById('theme-card-system')
                };

                var inputs = {
                    light: document.getElementById('theme-option-light'),
                    dark: document.getElementById('theme-option-dark'),
                    system: document.getElementById('theme-option-system')
                };

                ['light', 'dark', 'system'].forEach(function(key) {
                    var card = cards[key];
                    var input = inputs[key];
                    if (!card) return;

                    var isActive = (mode === key);
                    if (input) input.checked = isActive;

                    if (isActive) {
                        card.classList.add('active');
                        var badge = card.querySelector('.theme-active-badge');
                        if (badge) badge.classList.remove('opacity-0');
                    } else {
                        card.classList.remove('active');
                        var badge = card.querySelector('.theme-active-badge');
                        if (badge) badge.classList.add('opacity-0');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', updateAppearanceSettingsUI);
            window.addEventListener('blogmnm:theme-changed', updateAppearanceSettingsUI);
            setTimeout(updateAppearanceSettingsUI, 50);
        })();
    </script>
</section>
