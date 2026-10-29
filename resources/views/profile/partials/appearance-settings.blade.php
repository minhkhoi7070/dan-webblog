<section id="appearance">
    <header class="mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0">
                Giao diện hiển thị
            </h2>
        </div>
    </header>

    <div class="row g-2 g-sm-3" id="theme-radio-cards" role="radiogroup" aria-label="Lựa chọn giao diện">
        <!-- 1. Light Mode Card -->
        <div class="col-12 col-sm-4">
            <label for="theme-option-light"
                   id="theme-card-light"
                   class="settings-option-card d-flex align-items-center justify-content-between p-3 rounded-3 border border-theme bg-surface-1 cursor-pointer transition-colors w-100 m-0">
                <input type="radio"
                       id="theme-option-light"
                       name="theme_mode"
                       value="light"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')">

                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle p-2 bg-warning-subtle text-warning flex-shrink-0" style="width: 34px; height: 34px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </span>
                    <div>
                        <span class="fw-semibold text-theme d-block" style="font-size: 14px;">Giao diện sáng</span>
                        <span class="small text-theme-muted" style="font-size: 11.5px;">Light</span>
                    </div>
                </div>
                <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                    Đang dùng
                </span>
            </label>
        </div>

        <!-- 2. Dark Mode Card -->
        <div class="col-12 col-sm-4">
            <label for="theme-option-dark"
                   id="theme-card-dark"
                   class="settings-option-card d-flex align-items-center justify-content-between p-3 rounded-3 border border-theme bg-surface-1 cursor-pointer transition-colors w-100 m-0">
                <input type="radio"
                       id="theme-option-dark"
                       name="theme_mode"
                       value="dark"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')">

                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle p-2 bg-primary-subtle text-accent flex-shrink-0" style="width: 34px; height: 34px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </span>
                    <div>
                        <span class="fw-semibold text-theme d-block" style="font-size: 14px;">Giao diện tối</span>
                        <span class="small text-theme-muted" style="font-size: 11.5px;">Dark</span>
                    </div>
                </div>
                <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                    Đang dùng
                </span>
            </label>
        </div>

        <!-- 3. System Mode Card -->
        <div class="col-12 col-sm-4">
            <label for="theme-option-system"
                   id="theme-card-system"
                   class="settings-option-card d-flex align-items-center justify-content-between p-3 rounded-3 border border-theme bg-surface-1 cursor-pointer transition-colors w-100 m-0">
                <input type="radio"
                       id="theme-option-system"
                       name="theme_mode"
                       value="system"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('system')">

                <div class="d-flex align-items-center gap-2.5">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle p-2 bg-surface-2 text-theme-secondary flex-shrink-0" style="width: 34px; height: 34px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </span>
                    <div>
                        <span class="fw-semibold text-theme d-block" style="font-size: 14px;">Hệ thống</span>
                        <span class="small text-theme-muted" style="font-size: 11.5px;">Auto sync</span>
                    </div>
                </div>
                <span class="theme-active-badge badge bg-accent text-white rounded-pill opacity-0" style="font-size: 10px; transition: opacity 0.2s;">
                    Đang dùng
                </span>
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
