<section id="appearance">
    <header>
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
            <h2 class="h5 fw-bold text-body m-0">
                {{ __('Giao diện hiển thị (Appearance)') }}
            </h2>
        </div>

        <p class="small text-secondary mt-1 mb-0">
            {{ __('Chọn chế độ màu sắc hiển thị phù hợp với bạn. Thiết lập được lưu và áp dụng ngay lập tức trên toàn bộ ứng dụng.') }}
        </p>
    </header>

    <div class="row g-3 mt-3" id="theme-radio-cards" role="radiogroup" aria-label="Lựa chọn giao diện">
        <!-- Dark Theme Card (Default) -->
        <div class="col-12 col-sm-6">
            <label for="theme-option-dark"
                   id="theme-card-dark"
                   class="card h-100 p-3 p-md-4 rounded-3 border cursor-pointer select-none text-decoration-none">
                <input type="radio"
                       id="theme-option-dark"
                       name="theme_mode"
                       value="dark"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('dark')">

                <!-- Card Header: Radio & Title -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="theme-radio-indicator rounded-circle border d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                            <div class="rounded-circle bg-primary opacity-0" style="width: 10px; height: 10px; transition: opacity 0.2s;"></div>
                        </div>
                        <span class="fw-bold small text-body">{{ __('Tối (Dark)') }}</span>
                    </div>
                    <span class="theme-active-badge badge bg-primary-subtle text-primary border border-primary-subtle opacity-0" style="transition: opacity 0.2s;">
                        {{ __('Đang dùng') }}
                    </span>
                </div>

                <!-- Visual Mockup: Dark Preview -->
                <div class="w-100 rounded-3 bg-black border border-secondary border-opacity-25 p-3 d-flex flex-column gap-2 mb-3 shadow-sm pointer-events-none" style="height: 100px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="bg-secondary rounded" style="width: 60px; height: 8px;"></div>
                        <div class="rounded-circle bg-primary" style="width: 14px; height: 14px;"></div>
                    </div>
                    <div class="rounded bg-dark border border-secondary border-opacity-25 p-2 d-flex flex-column justify-content-center gap-1">
                        <div class="bg-secondary rounded" style="width: 75%; height: 6px;"></div>
                        <div class="bg-secondary opacity-50 rounded" style="width: 50%; height: 5px;"></div>
                    </div>
                </div>

                <p class="small text-secondary mb-0" style="font-size: 0.8rem;">
                    {{ __('Giao diện tối chuẩn BlogMNM, dịu mắt, độ tương phản sâu và tập trung tối đa vào nội dung.') }}
                </p>
            </label>
        </div>

        <!-- Light Theme Card -->
        <div class="col-12 col-sm-6">
            <label for="theme-option-light"
                   id="theme-card-light"
                   class="card h-100 p-3 p-md-4 rounded-3 border cursor-pointer select-none text-decoration-none">
                <input type="radio"
                       id="theme-option-light"
                       name="theme_mode"
                       value="light"
                       class="d-none"
                       onchange="window.BlogMNMTheme && window.BlogMNMTheme.setTheme('light')">

                <!-- Card Header: Radio & Title -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="theme-radio-indicator rounded-circle border d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                            <div class="rounded-circle bg-primary opacity-0" style="width: 10px; height: 10px; transition: opacity 0.2s;"></div>
                        </div>
                        <span class="fw-bold small text-body">{{ __('Sáng (Light)') }}</span>
                    </div>
                    <span class="theme-active-badge badge bg-primary-subtle text-primary border border-primary-subtle opacity-0" style="transition: opacity 0.2s;">
                        {{ __('Đang dùng') }}
                    </span>
                </div>

                <!-- Visual Mockup: Light Preview -->
                <div class="w-100 rounded-3 bg-white border p-3 d-flex flex-column gap-2 mb-3 shadow-sm pointer-events-none" style="height: 100px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="bg-secondary bg-opacity-25 rounded" style="width: 60px; height: 8px;"></div>
                        <div class="rounded-circle bg-primary" style="width: 14px; height: 14px;"></div>
                    </div>
                    <div class="rounded bg-light border p-2 d-flex flex-column justify-content-center gap-1">
                        <div class="bg-dark rounded" style="width: 75%; height: 6px;"></div>
                        <div class="bg-secondary bg-opacity-50 rounded" style="width: 50%; height: 5px;"></div>
                    </div>
                </div>

                <p class="small text-secondary mb-0" style="font-size: 0.8rem;">
                    {{ __('Giao diện sáng thanh lịch, độ tương phản cao, tươi mới và dễ nhìn trong môi trường đủ sáng.') }}
                </p>
            </label>
        </div>
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
                    darkCard.classList.add('border-primary', 'shadow');
                    darkCard.querySelector('.theme-radio-indicator').classList.add('border-primary');
                    darkCard.querySelector('.theme-radio-indicator div').classList.remove('opacity-0');
                    darkCard.querySelector('.theme-active-badge').classList.remove('opacity-0');

                    // Light inactive
                    lightCard.classList.remove('border-primary', 'shadow');
                    lightCard.querySelector('.theme-radio-indicator').classList.remove('border-primary');
                    lightCard.querySelector('.theme-radio-indicator div').classList.add('opacity-0');
                    lightCard.querySelector('.theme-active-badge').classList.add('opacity-0');
                } else {
                    if (lightInput) lightInput.checked = true;
                    // Light active
                    lightCard.classList.add('border-primary', 'shadow');
                    lightCard.querySelector('.theme-radio-indicator').classList.add('border-primary');
                    lightCard.querySelector('.theme-radio-indicator div').classList.remove('opacity-0');
                    lightCard.querySelector('.theme-active-badge').classList.remove('opacity-0');

                    // Dark inactive
                    darkCard.classList.remove('border-primary', 'shadow');
                    darkCard.querySelector('.theme-radio-indicator').classList.remove('border-primary');
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
