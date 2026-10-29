<section id="reading">
    <header class="mb-4">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-surface-2 border border-theme text-accent p-1.5" style="width: 28px; height: 28px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </span>
            <h2 class="h5 fw-bold text-theme m-0">
                Trải nghiệm đọc
            </h2>
        </div>
    </header>

    <div class="d-flex flex-column gap-4">
        <!-- 1. Cỡ chữ bài viết (Hiển thị trên cả Desktop và Mobile) -->
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2.5">
            <label class="label-uppercase text-theme fw-semibold m-0" style="font-size: 11px; letter-spacing: 0.05em;">
                Cỡ chữ bài viết
            </label>

            <!-- Font Size Segmented Pill Buttons -->
            <div class="settings-segmented-group" id="reading-font-group" role="radiogroup" aria-label="Cỡ chữ bài viết">
                <button type="button"
                        class="settings-segmented-btn"
                        data-font="sm"
                        role="radio"
                        aria-label="Cỡ chữ Nhỏ (15px)"
                        title="Cỡ chữ Nhỏ: 15px">
                    Nhỏ
                </button>
                <button type="button"
                        class="settings-segmented-btn"
                        data-font="md"
                        role="radio"
                        aria-label="Cỡ chữ Tiêu chuẩn (17px)"
                        title="Cỡ chữ Tiêu chuẩn: 17px">
                    Tiêu chuẩn
                </button>
                <button type="button"
                        class="settings-segmented-btn"
                        data-font="lg"
                        role="radio"
                        aria-label="Cỡ chữ Lớn (20px)"
                        title="Cỡ chữ Lớn: 20px">
                    Lớn
                </button>
            </div>
        </div>

        <!-- 2. Độ rộng trang đọc (Chỉ hiển thị trên Desktop >= 768px; tự động 100% trên Mobile) -->
        <div class="d-none d-md-flex flex-row align-items-center justify-content-between gap-2.5 pt-1">
            <label class="label-uppercase text-theme fw-semibold m-0" style="font-size: 11px; letter-spacing: 0.05em;">
                Độ rộng trang đọc
            </label>

            <!-- Content Width Segmented Pill Buttons -->
            <div class="settings-segmented-group" id="reading-width-group" role="radiogroup" aria-label="Độ rộng trang đọc">
                <button type="button"
                        class="settings-segmented-btn"
                        data-width="standard"
                        role="radio"
                        aria-label="Độ rộng Tiêu chuẩn (680px)"
                        title="Độ rộng Tiêu chuẩn: tối đa 680px">
                    Tiêu chuẩn
                </button>
                <button type="button"
                        class="settings-segmented-btn"
                        data-width="comfortable"
                        role="radio"
                        aria-label="Độ rộng Thoáng (760px)"
                        title="Độ rộng Thoáng: tối đa 760px">
                    Thoáng
                </button>
                <button type="button"
                        class="settings-segmented-btn"
                        data-width="wide"
                        role="radio"
                        aria-label="Độ rộng Rộng (860px)"
                        title="Độ rộng Rộng: tối đa 860px">
                    Rộng
                </button>
            </div>
        </div>
    </div>
</section>
