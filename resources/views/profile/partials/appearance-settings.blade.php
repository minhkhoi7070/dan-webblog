<section id="appearance">
    <header class="mb-3">
        <h2 class="macos-card-title mb-0">
            Giao diện hiển thị
        </h2>
    </header>

    <div class="row g-3" id="theme-radio-cards" role="radiogroup" aria-label="Lựa chọn giao diện">
        <!-- 1. Light Mode Card -->
        <div class="col-4">
            <label for="theme-option-light" id="theme-card-light" class="macos-appearance-tile w-100">
                <input type="radio"
                       id="theme-option-light"
                       name="theme_mode"
                       value="light"
                       class="d-none">

                <!-- macOS Mini Window Preview (Light) -->
                <div class="macos-appearance-preview macos-appearance-preview-light d-flex flex-column p-1.5">
                    <div class="d-flex align-items-center gap-1 mb-1">
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FF5F56;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FFBD2E;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #27C93F;"></span>
                    </div>
                    <div class="rounded-1 flex-fill" style="background: #F2F2F7; border: 0.5px solid rgba(0,0,0,0.06);"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between w-100 px-1 pt-0.5">
                    <span class="fw-medium text-truncate" style="font-size: 13px;">Sáng</span>
                    <span class="macos-appearance-check">
                        <svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
            </label>
        </div>

        <!-- 2. Dark Mode Card -->
        <div class="col-4">
            <label for="theme-option-dark" id="theme-card-dark" class="macos-appearance-tile w-100">
                <input type="radio"
                       id="theme-option-dark"
                       name="theme_mode"
                       value="dark"
                       class="d-none">

                <!-- macOS Mini Window Preview (Dark) -->
                <div class="macos-appearance-preview macos-appearance-preview-dark d-flex flex-column p-1.5">
                    <div class="d-flex align-items-center gap-1 mb-1">
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FF5F56;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FFBD2E;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #27C93F;"></span>
                    </div>
                    <div class="rounded-1 flex-fill" style="background: #2C2C2E; border: 0.5px solid rgba(255,255,255,0.08);"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between w-100 px-1 pt-0.5">
                    <span class="fw-medium text-truncate" style="font-size: 13px;">Tối</span>
                    <span class="macos-appearance-check">
                        <svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
            </label>
        </div>

        <!-- 3. System / Auto Mode Card -->
        <div class="col-4">
            <label for="theme-option-system" id="theme-card-system" class="macos-appearance-tile w-100">
                <input type="radio"
                       id="theme-option-system"
                       name="theme_mode"
                       value="system"
                       class="d-none">

                <!-- macOS Mini Window Preview (Split Auto) -->
                <div class="macos-appearance-preview macos-appearance-preview-system d-flex flex-column p-1.5 position-relative">
                    <div class="d-flex align-items-center gap-1 mb-1 position-relative" style="z-index: 2;">
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FF5F56;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #FFBD2E;"></span>
                        <span class="rounded-circle" style="width: 5px; height: 5px; background: #27C93F;"></span>
                    </div>
                    <div class="rounded-1 flex-fill" style="background: transparent;"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between w-100 px-1 pt-0.5">
                    <span class="fw-medium text-truncate" style="font-size: 13px;">Tự động</span>
                    <span class="macos-appearance-check">
                        <svg width="8" height="8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
            </label>
        </div>
    </div>

</section>
