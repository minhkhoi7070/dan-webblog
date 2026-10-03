<section id="reading">
    <header class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0" style="font-family: var(--font-serif, 'Lora', Georgia, serif);">
                Trải nghiệm đọc
            </h2>
        </div>
        <p class="small text-theme-muted mb-0" style="font-size: 13px;">
            Tùy biến typography và không gian hiển thị bài viết để việc đọc đạt sự tập trung và thoải mái cao nhất.
        </p>
    </header>

    <div class="d-flex flex-column gap-4">
        <!-- 1. Font Size Control -->
        <div class="p-3.5 p-sm-4 rounded-xl border border-theme bg-surface-1">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                <div>
                    <h3 class="fw-semibold text-theme mb-0" style="font-size: 14px;">Cỡ chữ bài viết (Font Size)</h3>
                    <p class="small text-theme-muted mb-0" style="font-size: 12px;">Điều chỉnh kích thước chữ của phần nội dung đọc chính.</p>
                </div>

                <!-- Font Size Segmented Pill Buttons -->
                <div class="settings-segmented-group align-self-start align-self-sm-auto" id="reading-font-group" role="radiogroup" aria-label="Cỡ chữ">
                    <button type="button"
                            class="settings-segmented-btn"
                            data-font="sm"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setFontSize('sm')">
                        Nhỏ (15px)
                    </button>
                    <button type="button"
                            class="settings-segmented-btn"
                            data-font="md"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setFontSize('md')">
                        Tiêu chuẩn (17px)
                    </button>
                    <button type="button"
                            class="settings-segmented-btn"
                            data-font="lg"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setFontSize('lg')">
                        Lớn (20px)
                    </button>
                </div>
            </div>

            <!-- Interactive Live Preview Box (Readwise reader card) -->
            <div class="reading-preview-box mt-3" id="font-preview-box">
                <span class="label-uppercase text-theme-muted d-block mb-1.5" style="font-size: 10px; letter-spacing: 0.08em;">
                    Xem trước trực tiếp (Live Typography Preview)
                </span>
                <p class="mb-0" style="color: var(--color-text);">
                    "Mỗi trang viết chất lượng không chỉ trao gửi tri thức, mà còn mở ra chiều sâu tư tưởng mới cho độc giả. Trải nghiệm đọc tối giản và phông chữ chỉn chu giúp tâm trí hoàn toàn hòa nhập cùng nội dung."
                </p>
            </div>
        </div>

        <!-- 2. Content Width Control -->
        <div class="p-3.5 p-sm-4 rounded-xl border border-theme bg-surface-1">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                <div>
                    <h3 class="fw-semibold text-theme mb-0" style="font-size: 14px;">Độ rộng trang đọc (Content Width)</h3>
                    <p class="small text-theme-muted mb-0" style="font-size: 12px;">Khoảng rộng khung văn bản tối ưu cho thị giác của bạn.</p>
                </div>

                <!-- Content Width Segmented Pill Buttons -->
                <div class="settings-segmented-group align-self-start align-self-sm-auto" id="reading-width-group" role="radiogroup" aria-label="Độ rộng nội dung">
                    <button type="button"
                            class="settings-segmented-btn"
                            data-width="standard"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setWidth('standard')">
                        Tiêu chuẩn (680px)
                    </button>
                    <button type="button"
                            class="settings-segmented-btn"
                            data-width="comfortable"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setWidth('comfortable')">
                        Thoáng đãng (760px)
                    </button>
                    <button type="button"
                            class="settings-segmented-btn"
                            data-width="wide"
                            onclick="window.BlogMNMReading && window.BlogMNMReading.setWidth('wide')">
                        Rộng (860px)
                    </button>
                </div>
            </div>

            <!-- Content Width Mini Visual Meter -->
            <div class="p-3 rounded-lg border border-theme bg-surface-2 mt-3">
                <div class="d-flex align-items-center justify-content-between small text-theme-muted mb-2" style="font-size: 11.5px;">
                    <span>Khung văn bản minh họa:</span>
                    <span id="reading-width-label" class="fw-medium text-accent">Tiêu chuẩn (680px - Tập trung cao)</span>
                </div>
                <div class="w-100 rounded-pill bg-theme border border-theme p-1 d-flex justify-content-center overflow-hidden" style="height: 18px;">
                    <div id="reading-width-meter" class="h-100 rounded-pill bg-accent transition-all" style="width: 50%; opacity: 0.85;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Sync Script for Reading Preferences -->
    <script>
        (function() {
            function updateReadingSettingsUI() {
                var currentFont = (window.BlogMNMReading && window.BlogMNMReading.getFontSize()) || 'md';
                var currentWidth = (window.BlogMNMReading && window.BlogMNMReading.getWidth()) || 'standard';

                // Sync Font buttons
                var fontBtns = document.querySelectorAll('#reading-font-group .settings-segmented-btn');
                fontBtns.forEach(function(btn) {
                    var isCurrent = btn.getAttribute('data-font') === currentFont;
                    btn.classList.toggle('active', isCurrent);
                });

                // Sync Width buttons
                var widthBtns = document.querySelectorAll('#reading-width-group .settings-segmented-btn');
                widthBtns.forEach(function(btn) {
                    var isCurrent = btn.getAttribute('data-width') === currentWidth;
                    btn.classList.toggle('active', isCurrent);
                });

                // Update Meter width
                var meter = document.getElementById('reading-width-meter');
                var label = document.getElementById('reading-width-label');
                if (meter && label) {
                    if (currentWidth === 'standard') {
                        meter.style.width = '48%';
                        label.textContent = 'Tiêu chuẩn (680px - Tập trung cao)';
                    } else if (currentWidth === 'comfortable') {
                        meter.style.width = '70%';
                        label.textContent = 'Thoáng đãng (760px - Cân bằng)';
                    } else {
                        meter.style.width = '92%';
                        label.textContent = 'Rộng rãi (860px - Màn hình lớn)';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', updateReadingSettingsUI);
            window.addEventListener('blogmnm:reading-font-changed', updateReadingSettingsUI);
            window.addEventListener('blogmnm:reading-width-changed', updateReadingSettingsUI);
            setTimeout(updateReadingSettingsUI, 50);
        })();
    </script>
</section>
