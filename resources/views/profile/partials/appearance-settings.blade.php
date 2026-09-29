<section id="appearance">
    <header>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            <h2 class="text-lg font-bold text-[var(--color-text)]">
                {{ __('Giao diện hiển thị (Appearance)') }}
            </h2>
        </div>

        <p class="mt-1 text-sm text-[var(--color-text-secondary)]">
            {{ __('Chọn chế độ màu sắc hiển thị phù hợp với bạn. Thiết lập được lưu và áp dụng ngay lập tức trên toàn bộ ứng dụng.') }}
        </p>
    </header>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4" id="theme-radio-cards" role="radiogroup" aria-label="Lựa chọn giao diện">
        <!-- Dark Theme Card (Default) -->
        <label for="theme-option-dark"
               id="theme-card-dark"
               class="relative flex flex-col p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none group border-[var(--color-border)] hover:border-indigo-400/50 bg-[var(--color-surface)]">
            <input type="radio"
                   id="theme-option-dark"
                   name="theme_mode"
                   value="dark"
                   class="sr-only"
                   onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')">

            <!-- Card Header: Radio & Title -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="theme-radio-indicator w-5 h-5 rounded-full border-2 border-[var(--color-border)] flex items-center justify-center transition-colors">
                        <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 opacity-0 transition-opacity"></div>
                    </div>
                    <span class="font-bold text-sm text-[var(--color-text)]">{{ __('Tối (Dark)') }}</span>
                </div>
                <span class="theme-active-badge text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 opacity-0 transition-opacity">
                    {{ __('Đang dùng') }}
                </span>
            </div>

            <!-- Visual Mockup: Dark Preview -->
            <div class="w-full h-24 rounded-xl bg-black border border-zinc-800 p-2.5 flex flex-col gap-2 mb-3 pointer-events-none shadow-inner">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-2 rounded bg-zinc-800"></div>
                    <div class="w-4 h-4 rounded-full bg-indigo-600"></div>
                </div>
                <div class="h-10 rounded-lg bg-zinc-900 border border-zinc-800/80 p-2 flex flex-col justify-center gap-1.5">
                    <div class="w-3/4 h-2 rounded bg-zinc-700"></div>
                    <div class="w-1/2 h-1.5 rounded bg-zinc-800"></div>
                </div>
            </div>

            <p class="text-xs text-[var(--color-text-secondary)] leading-relaxed">
                {{ __('Giao diện tối chuẩn BlogMNM, dịu mắt, độ tương phản sâu và tập trung tối đa vào nội dung.') }}
            </p>
        </label>

        <!-- Light Theme Card -->
        <label for="theme-option-light"
               id="theme-card-light"
               class="relative flex flex-col p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none group border-[var(--color-border)] hover:border-indigo-400/50 bg-[var(--color-surface)]">
            <input type="radio"
                   id="theme-option-light"
                   name="theme_mode"
                   value="light"
                   class="sr-only"
                   onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')">

            <!-- Card Header: Radio & Title -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="theme-radio-indicator w-5 h-5 rounded-full border-2 border-[var(--color-border)] flex items-center justify-center transition-colors">
                        <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 opacity-0 transition-opacity"></div>
                    </div>
                    <span class="font-bold text-sm text-[var(--color-text)]">{{ __('Sáng (Light)') }}</span>
                </div>
                <span class="theme-active-badge text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 opacity-0 transition-opacity">
                    {{ __('Đang dùng') }}
                </span>
            </div>

            <!-- Visual Mockup: Light Preview -->
            <div class="w-full h-24 rounded-xl bg-white border border-zinc-200 p-2.5 flex flex-col gap-2 mb-3 pointer-events-none shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-2 rounded bg-zinc-200"></div>
                    <div class="w-4 h-4 rounded-full bg-indigo-600"></div>
                </div>
                <div class="h-10 rounded-lg bg-zinc-50 border border-zinc-200/80 p-2 flex flex-col justify-center gap-1.5">
                    <div class="w-3/4 h-2 rounded bg-zinc-800"></div>
                    <div class="w-1/2 h-1.5 rounded bg-zinc-300"></div>
                </div>
            </div>

            <p class="text-xs text-[var(--color-text-secondary)] leading-relaxed">
                {{ __('Giao diện sáng thanh lịch, độ tương phản cao, tươi mới và dễ nhìn trong môi trường đủ sáng.') }}
            </p>
        </label>
    </div>

    <!-- Live Sync Script for Settings Radios -->
    <script>
        (function() {
            function updateAppearanceUI() {
                var current = (window.BlogMNMTheme && window.BlogMNMTheme.getTheme()) || 'dark';

                var darkCard = document.getElementById('theme-card-dark');
                var lightCard = document.getElementById('theme-card-light');
                var darkInput = document.getElementById('theme-option-dark');
                var lightInput = document.getElementById('theme-option-light');

                if (!darkCard || !lightCard) return;

                if (current === 'dark') {
                    if (darkInput) darkInput.checked = true;
                    // Dark active
                    darkCard.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
                    darkCard.classList.remove('border-[var(--color-border)]');
                    darkCard.querySelector('.theme-radio-indicator').classList.add('border-indigo-500');
                    darkCard.querySelector('.theme-radio-indicator div').classList.remove('opacity-0');
                    darkCard.querySelector('.theme-active-badge').classList.remove('opacity-0');

                    // Light inactive
                    lightCard.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
                    lightCard.classList.add('border-[var(--color-border)]');
                    lightCard.querySelector('.theme-radio-indicator').classList.remove('border-indigo-500');
                    lightCard.querySelector('.theme-radio-indicator div').classList.add('opacity-0');
                    lightCard.querySelector('.theme-active-badge').classList.add('opacity-0');
                } else {
                    if (lightInput) lightInput.checked = true;
                    // Light active
                    lightCard.classList.add('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
                    lightCard.classList.remove('border-[var(--color-border)]');
                    lightCard.querySelector('.theme-radio-indicator').classList.add('border-indigo-500');
                    lightCard.querySelector('.theme-radio-indicator div').classList.remove('opacity-0');
                    lightCard.querySelector('.theme-active-badge').classList.remove('opacity-0');

                    // Dark inactive
                    darkCard.classList.remove('border-indigo-500', 'ring-2', 'ring-indigo-500/20');
                    darkCard.classList.add('border-[var(--color-border)]');
                    darkCard.querySelector('.theme-radio-indicator').classList.remove('border-indigo-500');
                    darkCard.querySelector('.theme-radio-indicator div').classList.add('opacity-0');
                    darkCard.querySelector('.theme-active-badge').classList.add('opacity-0');
                }
            }

            document.addEventListener('DOMContentLoaded', updateAppearanceUI);
            window.addEventListener('blogmnm:theme-changed', updateAppearanceUI);
            setTimeout(updateAppearanceUI, 50);
        })();
    </script>
</section>
